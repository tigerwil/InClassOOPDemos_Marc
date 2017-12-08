<!-- Admin Index Page Template Content -->
<div class="container">
    <h1 class="mt-4 mb-3">Admin Dashboard</h1>
    <!-- mwilliams:  breadcrumb navigation -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active">Admin</li>            
    </ol>
    <!-- end breadcrumb -->
    <?php
        //ONLY AUTHORIZED USER CAN VIEW ARTICLE
        if (!empty($_SESSION['user_id']) && $_SESSION['admin']){
            //we found a user who is logged in - store their id in variable
            $adminUser = $_SESSION['user_id'];
            
        }else{
            $adminUser = null;
        }
        
        //var_dump($_SESSION);
        //var_dump($adminUser);
        
        if(empty($adminUser)){
            //not an admin user - show message
            echo '<div class="alert alert-warning" role="alert">
                    <strong>Admins Only</strong>
                    <p>You must be logged in as an administrator!</p>
                  </div>';
        }else{
        
    
    ?>
    
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <h4 class="card-header bg-dark text-light">Categories</h4>
                <div class="card-body">
                    <p class="card-text text-center"><i class="fa fa-folder-open fa-3x" aria-hidden="true"></i></p>
                </div>
                <a class="card-footer text-primary clearfix" href="admin-categories.php">
                    <span class="float-left">Manage Categories</span>
                    <span class="float-right">
                        <i class="fa fa-angle-right"></i>
                    </span>
                </a>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <h4 class="card-header bg-dark text-light">Articles</h4>
                <div class="card-body">
                    <p class="card-text text-center"><i class="fa fa-file-text fa-3x" aria-hidden="true"></i></p>
                </div>
                <a class="card-footer text-primary clearfix" href="admin-articles.php">
                    <span class="float-left">Manage Articles</span>
                    <span class="float-right">
                        <i class="fa fa-angle-right"></i>
                    </span>
                </a>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <h4 class="card-header bg-dark text-light">Users</h4>
                <div class="card-body">
                    <p class="card-text text-center"><i class="fa fa-users fa-3x" aria-hidden="true"></i></p>
                </div>
                <a class="card-footer text-primary clearfix" href="admin-users.php">
                    <span class="float-left">Manage Users</span>
                    <span class="float-right">
                        <i class="fa fa-angle-right"></i>
                    </span>
                </a>
            </div>
        </div>   
    </div>
    
        <?php } ?>
    
</div>
