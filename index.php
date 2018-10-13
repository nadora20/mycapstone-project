<?php
$page_title='Home';
$page_header_title='Home Page';
include'./includes/pref.php';
include HEADER;
?>
<h3>
    Welcome <?php echo isset($_SESSION['username'])?'[ '.$_SESSION['username'].' ]':' Guest '?>
</h3>

<?php include FOOTER;?>