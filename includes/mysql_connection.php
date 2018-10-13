<?php

define('DB_USER', 'u595095753_root');
define('DB_PASSWORD', '123698745');
define('DB_HOST', 'localhost');
define('DB_NAME', 'u595095753_cap');

$dbcl = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

if($dbcl->connect_error){
    die('Could not connect to MySQL database:'.$dbcl->connect_error );
}else{
    $dbcl->set_charset('utf8');
}

