<?php

define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_NAME', 'capstonedb');

$dbcl = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

if($dbcl->connect_error){
    die('Could not connect to MySQL database:'.$dbcl->connect_error );
}else{
    $dbcl->set_charset('utf8');
}

