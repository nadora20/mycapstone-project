<?php

define('BASE_URL','https://mycapstone.tk/');
define('MYSQL','./includes/mysql_connection.php');
define('HEADER','./includes/header.php');
define('FOOTER','./includes/footer.php');

function len_check($val,$x,$y){
    $val_len =strlen($val);
    return ($val_len>=$x && $val_len <= $y)? TRUE:FALSE;
}


