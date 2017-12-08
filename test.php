<?php
include 'includes/header.php';
?>
<div class='container'>
    <h1 class="mt-4 mb-3">Password Tester</h1>

    <?php
        if($_POST){
         
            if (!empty($_POST['password'])){
                   var_dump($_POST);
                $password = $_POST['password'];
                $password_hash = PassHash::hash($password);
                echo $password_hash;
            }
        }
    
    ?>
    <form class="form-inline" method="post" action="test.php">
        <div class="form-group mx-sm-3">
            <label for="password" class="sr-only">Password:</label>
            <input type="text" class="form-control" 
                   id="password" name="password"                    
                   placeholder="Test your password"
                   value="<?php if (isset($_POST['password'])) echo $_POST['password']; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Test</button>
    </form>
</div>
<?php
include 'includes/footer.php';

