<!-- Admin Users Page Template Content -->
<div class="container">
    <h1 class="mt-4 mb-3">Manage Users</h1>

    <!-- mwilliams:  breadcrumb navigation -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item"><a href="admin.php">Admin</a></li>
        <li class="breadcrumb-item active">Manage Users</li>            
    </ol>
    <!-- end breadcrumb -->
    <?php
        $data = $dbh->getUsers();
        //var_dump($data);
        if($data['error']==false){
            //No error -get items
            $users = $data['items'];
            //var_dump($users);
             echo '    
                <table class="table table-striped table-hover table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Type</th>
                            <th scope="col">Email</th>
                            <th scope="col">First Name</th>
                            <th scope="col">Last Name</th>
                            <th scope="col">Expired</th>
                            <th scope="col">Active</th>
                        </tr>
                    </thead>
                    <tbody>';
                    foreach($users as $user){
                        $id = $user['id'];
                        $type = $user['type'];
                        $email = $user['email'];
                        $last_name = $user['last_name'];
                        $first_name = $user['first_name'];
                        $expired = $user['notexpired'];
                        $active = $user['active'];
                        
                        if($expired){
                            $icon = '<i class="fa fa-thumbs-down" aria-hidden="true"></i>';
                        }else{
                            $icon = '<i class="fa fa-thumbs-up" aria-hidden="true"></i>';
                        }

                        //create a tr for each record
                        echo "<tr>
                                <th scope='row'><img style='width:25%' class='rounded-circle' src='images/users/$id.jpg'></th>
                                <th scope='row'>$type</th>
                                <th scope='row'>$email</th>
                                <th scope='row'>$first_name</th>
                                <th scope='row'>$last_name</th>
                                <th scope='row'>$icon</th>
                                <th scope='row'>$active</th>
                             </tr>";

                    }//end of foreach 
                echo '</tbody></table>';  //finish table  
        }
    ?>
    
</div>