<?php 
session_start();
$logged=FALSE;
if(isset($_SESSION['user_id'])){ $logged =TRUE; }
 ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $page_title; ?></title>
        <link rel="stylesheet" href="css/style.css"/>
    </head>
    <body>
        <header>
            <h1><?php echo $page_header_title; ?></h1>
            <h2>Welcome to capstone messaging center</h2>
            <nav>
                <ul>
                    <li>
                        <a href="index.php">HOME</a>
                    </li>
                    <?php if($logged){ ?>
                    <li>
                        <a href="logout.php">Logout</a>
                    </li>
                    <li>
                        <a href="send.php">Send Message</a>
                    </li>
                    <li>
                        <a href="read.php">Read Message</a>
                    </li>
                    <li>
                        <a href="down.php">Get Database</a>
                    </li>
                    <?php } else { ?>
                    <li>
                        <a href="register.php">Register</a>
                    </li>
                    <li>
                        <a href ="login.php">Login</a>
                    </li>
                    <?php } ?>
                </ul>
            </nav>
        </header>
        <div id="content">