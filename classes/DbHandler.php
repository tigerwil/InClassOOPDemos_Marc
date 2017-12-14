<?php

/*
 * DbHandler.php
 * Class to handle all database operations
 * This class will have all the CRUD methods:
 * C - Create
 * R - Read
 * U - Update
 * D - Delete
 */

class DbHandler {

    //private variable to hold the connection
    private $conn;

    //Constructor object - will run automatically when class is instantiated
    function __construct() {
        //Initialize the database 
        require_once dirname(__FILE__ . '/DbConnect.php');
        //Open db Connection
        try {
            $db = new DbConnect();
            $this->conn = $db->connect();
        } catch (Exception $ex) {
            $this::dbConnectError($ex->getCode());
        }
    }

//End of constructor
    //Create a static function called dbConnectError
    //A static function can be called without instantiating the class
    //in other words no need to use the new keyword
    private static function dbConnectError($code) {
        switch ($code) {
            case 1045:
                echo "A database access error has occured!";
                break;
            case 2002:
                echo "A database server error has occured!";
                break;
            default:
                echo "A server error has occured!";
                break;
        }//end of swith        
    }

//End of dbConnectError function

    /**
     * getCategoryList() function
     * Get a list of categories for creating menu
     */
    public function getCategoryList() {
        $sql = "SELECT id, category,Summary.total 
                FROM categories JOIN (SELECT COUNT(*) AS total, 
                                      category_id
                                      FROM pages
                                      GROUP BY category_id) AS Summary
                WHERE categories.id = Summary.category_id
                ORDER BY category";
        try {
            $stmt = $this->conn->query($sql);
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //Create an array to hold success|failure
            //data|message
            $data = array('error' => false,
                'items' => $categories
            );
        } catch (PDOException $ex) {
            $data = array('error' => true,
                'message' => $ex->getMessage()
            );
        }//end of try catch
        //Return data back to calling environment
        return $data;
    }

//end of getCategoryList Method

    /**
     * getPopularList() method 
     * Get a list of the 3 most popular articles based on history
     * of pages visited
     */
    public function getPopularList() {
        $sql = "SELECT COUNT(*)AS num, page_id, pages.title, 
                       CONCAT(LEFT(pages.description,30),'...') AS description
              FROM history JOIN pages ON pages.id = history.page_id
              WHERE type = 'page'
              GROUP BY page_id
              ORDER BY 1 DESC
              LIMIT 3";

        try {
            $stmt = $this->conn->query($sql);
            $popular = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //Create an array to hold success|failure
            //data|message
            $data = array('error' => false,
                'items' => $popular
            );
        } catch (PDOException $ex) {
            $data = array('error' => true,
                'message' => $ex->getMessage()
            );
        }//end of try catch
        //Return data back to calling environment
        return $data;
    }

//End of getPopularList

    public function getArticle($id) {
        try {
            //Prepare our sql query with $id param coming from 
            //outside environment
            $stmt = $this->conn->prepare("SELECT title,description,content
                                        FROM pages 
                                        WHERE id=:id");
            //Bind our parameter
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            //Execute the query
            $stmt->execute();

            //Fetch the data as an associative array
            $page = $stmt->fetchAll(PDO::FETCH_ASSOC);
    

            //Return array of data items
            $data = array(
                'error' => false,
                'items' => $page
            );
        } catch (PDOException $ex) {
            $data = array('error' => true,
                'message' => $ex->getMessage()
            );
        }//end of try catch
        //Return final data array
        return $data;
    }

//end of getArticle

    public function getArticles() {

        //build our sql query
        $sql = "SELECT id, title, description FROM pages ORDER BY title";

        try {
            $stmt = $this->conn->query($sql);
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //Return array of data items
            $data = array(
                'error' => false,
                'items' => $articles
            );
        } catch (PDOException $ex) {
            $data = array('error' => true,
                'message' => $ex->getMessage()
            );
        }//end of try catch
        //Return final data array
        return $data;
    }

//end of getArticles

    public function createUser($email, $password, $first_name, $last_name) {
        //First check if user already exists in table
        if (!$this->isUserExists($email)) {
            //User does not exist - continue
            //Generate password hash'
            $password_hash = PassHash::hash($password);

            //Generate random activation code
            $active = md5(uniqid(rand(), true));

            //Insert user in database using prepared statement
            //Note:  set date_expires to yesterday (until they activate account)
            $stmt = $this->conn->prepare("INSERT INTO users(email,pass,first_name,last_name,date_expires,active)
                                          VALUES(:email, :pass, :fname, :lname, SUBDATE(NOW(), INTERVAL 1 DAY), :active)");

            //Bind parameters
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':pass', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':fname', $first_name, PDO::PARAM_STR);
            $stmt->bindValue(':lname', $last_name, PDO::PARAM_STR);
            $stmt->bindValue(':active', $active, PDO::PARAM_STR);

            //Execute statement
            $result = $stmt->execute();

            //Prepare array for result
            if ($result) {
                // User successfully inserted
                $data = array(
                    'error' => false,
                    'message' => 'USER_CREATE_SUCCESS',
                    'active' => $active
                );
            } else {
                // Failed to create user
                $data = array(
                    'error' => true,
                    'message' => 'USER_CREATE_FAIL',
                );
            }
        } else {
            //User already exists - return error and message
            $data = array('error' => true,
                'message' => 'USER_ALREADY_EXISTS'
            );
        }

        //Return one final data array
        return $data;
    }

//End of createUser

public function activateUser($email, $active) {
    if ($this->isUserExists($email)) {
        //User exists in database - update table (date_expires and active)      
        $stmt = $this->conn->prepare("UPDATE users SET active=NULL, 
                                     date_expires=ADDDATE(date_expires, INTERVAL 1 YEAR)
                                     WHERE email=:email AND active = :active");

        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':active', $active, PDO::PARAM_STR);


        $result = $stmt->execute();
        $count = $stmt->rowCount();

        //Check for successfull update
        if ($count > 0) {
            //User successfully activated
            $data = array('error' => false,
                'message' => 'USER_ACTIVE_SUCCESS');
        } else {
            //Failed to activate user
            $data = array('error' => true,
                'message' => 'USER_ACTIVE_FAIL');
        }
    } else {
        //Account does not exist in database
        $data = array('error' => true,
            'message' => 'USER_ACTIVE_FAIL');
    }
    return $data;
}
//End activateUser

public function checkLogin($email, $password) {
    // fetching user by email
    //var_dump($email);
    //var_dump($password);
    //var_dump(PassHash::hash($password));
    //exit();
    //1. Check if email exists

    $stmt = $this->conn->prepare("SELECT COUNT(*) from users WHERE email = :email");
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $num_rows = $stmt->fetchColumn();
    //var_dump($num_rows);
    //exit();
    if ($num_rows > 0) {
        //2. Actual query
        $stmt = $this->conn->prepare("SELECT pass from users WHERE email = :email");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);

        if (PassHash::check_password($row->pass, $password)) {
            // User password is correct
            return TRUE;
        } else {
            // user password is incorrect
            return FALSE;
        }
    } else {
        // user not existed with the email
        return FALSE;
    }
}//End checkLogin

public function getUserByEmail($email) {
    try {
        $stmt = $this->conn->prepare("SELECT id, type, email, first_name, last_name, 
                                     IF(date_expires>=NOW(),true,false) as notexpired,
                                     IF(type='admin',true,false)as admin
                                     FROM users WHERE email = :email");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //return $user;
            $data = array('error' => false,
                'items' => $user);
            return $data;
        } else {
            return NULL;
        }
    } catch (PDOException $e) {
        return NULL;
    }
}
//End getUserByEmail

//====================USER FAVORITES =======================//

public function addFavorite($userid,$pageid){
/* REPLACE INTO statment
     * This works exactly like INSERT, except that if an old row in the
     * table has the same value as a new row for a PK or UQ index, 
     * the old row is deleted before the new row is inserted.
     * Note:  this statement may return more than one row affected when 
     * it deletes + inserts
*/
    $stmt=$this->conn->prepare(
            "REPLACE INTO user_favorites (user_id, page_id)
             VALUES(:userid,:pageid)"
            );

    $stmt->bindValue(':userid',$userid,PDO::PARAM_INT);
    $stmt->bindValue(':pageid',$pageid,PDO::PARAM_INT);    
    $stmt->execute();
    $count=$stmt->rowCount();

    //check for success|failure
    if($count>0){
        //success
        //$response['message'] = 'FAVORITE_ADD_SUCCESS';
        return true;
    }else{
        //failure
        //$response['message'] = 'FAVORITE_ADD_FAIL';
        return false;
    }

    //return final response
    //return $response;        
}//End addFavorite

public function delFavorite($userid,$pageid){
   $stmt=$this->conn->prepare(
           "DELETE FROM user_favorites 
            WHERE user_id = :userid AND
                  page_id = :pageid"
           );

   $stmt->bindValue(':userid',$userid,PDO::PARAM_INT);
   $stmt->bindValue(':pageid',$pageid,PDO::PARAM_INT);    
   $stmt->execute();
   $count=$stmt->rowCount();

   //check for success|failure
   if($count>0){
       //success
       //$response['message'] = 'FAVORITE_DELETE_SUCCESS';
       return true;
   }else{
       //failure
       //$response['message'] = 'FAVORITE_DELETE_FAIL';
       return false;
   }

   //return final response
   //return $response;        
}//End delFavorite


public function getFavorite($userid, $pageid){
    try{
//        $stmt=$this->conn->prepare("SELECT pages.id, title, category
//                                    FROM categories JOIN pages
//                                            ON categories.id = pages.id
//                                    JOIN user_favorites
//                                            ON user_favorites.page_id = pages.id
//                                    WHERE user_id = :userid AND page_id = :pageid
//                                    ORDER BY title;");
        $stmt=$this->conn->prepare("SELECT user_id, page_id
                                    FROM user_favorites
                                    WHERE user_id = :userid AND page_id = :pageid");        
        $stmt->bindValue(':userid',$userid, PDO::PARAM_INT);
        $stmt->bindValue(':pageid',$pageid, PDO::PARAM_INT);
        $stmt->execute();
        $favorites = $stmt->fetchAll(PDO::FETCH_OBJ);

        $data = array('error'=>false,
                      'items'=>$favorites);
    } catch (PDOException $ex) {
        $data = array('error'=>true,
                      'message'=>$ex->getMessage());
    }
    return $data;

}//End of getFavorites

//==================== END USER FAVORITES ==================//

private function isUserExists($email) {
    $stmt = $this->conn->prepare("SELECT COUNT(*)
                                FROM users 
                                WHERE email=:email");

    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $num_rows = $stmt->fetchColumn();

    //return true or false
    return $num_rows > 0;
}
//end of isUserExists 

//========================= ADMIN ONLY ==============================//
    public function getUsers() {
        try {
            //Prepare our sql query with $id param coming from 
            //outside environment
            $sql="SELECT id,type,email,first_name,last_name,
                                                IF(date_expires<=NOW(),true,false) AS notexpired,
                                                CASE WHEN active IS NOT NULL
                                                 THEN 'Not Active'
                                                 ELSE 'Active'
                                                End AS active
                                         FROM users";
            
            $stmt = $this->conn->query($sql);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);    

            //Return array of data items
            $data = array(
                'error' => false,
                'items' => $users
            );
        } catch (PDOException $ex) {
            $data = array('error' => true,
                'message' => $ex->getMessage()
            );
        }//end of try catch
        //Return final data array
        return $data;
    }

//========================= END ADMIN  ==============================//
}
//End of Class

