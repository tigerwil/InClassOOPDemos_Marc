<ul class="navbar-nav ml-auto">
    <?php
    //Convert above static menu to a dynamic menu using an array of labels and pages
    //to allow us to dynamically set the active menu item based on the current page the user 
    //is currently visiting
    $pages = array(
        'Home' => '/InClassOOPDemos_2017/',
        'About' => '/InClassOOPDemos_2017/about.php',
        'Services' => '/InClassOOPDemos_2017/services.php',
        'Contact' => '/InClassOOPDemos_2017/contact.php',
    );

    //Find out which page user is viewing
    $this_page = $_SERVER['REQUEST_URI'];
    // =========== test =================//
    //echo $this_page;
    //exit();
    // ========= end test ===============//    
    //loop the array and check if array page matches $this_page
    foreach ($pages as $k => $v):
        echo '<li class="nav-item';

        if ($this_page == $v) {
            echo ' active">';
        } else {
            echo '">';
        }
        echo '<a class="nav-link" href="' . $v . '">' . $k . '</a>
            </li>';
    endforeach;
    ?>
    <?php 
        //find the word article in links
        $article = strpos($this_page, 'article');      
        //var_dump($article);
        //if($article>0)echo ' active';
    ?>
    <li class="nav-item <?php if($article) echo 'active';?> dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownBlog" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Articles
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownBlog">
            <a class="dropdown-item" href="articles.php">All Articles</a>
            <a class="dropdown-item" href="#">Database Security</a>
            <a class="dropdown-item" href="#">Web Security</a>
        </div>
    </li>
</ul>