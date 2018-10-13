<?php
$page_title="Read Message";
$page_header_title='Read Message Page';
include './includes/pref.php';
include HEADER;

require MYSQL;
$sql = "SELECT `from`, AES_DECRYPT(content, 'secret_key') as content from messages where `to`='" . $_SESSION['username']  . "';";
$z = $dbcl->query($sql);#sql -> query  z -> statement  dbcl -> database connection link object
  ?>

   <fieldset>
       <legend>Your Messages</legend>
    <?php 
    echo isset($general_msg)? $general_msg: ''; ?>
    <form action="read.php" > 
        <p>
            <b>The Messages :</b><hr>
            <?php 
            if ($z->num_rows > 0) {
                while($row = $z->fetch_assoc()){
                    echo '<br><b>' . $row['from'] . ': </b>' . $row['content'] . '<br>';
                }
            }
            ?>
        </p>
    </form>
    
</fieldset>
<?php include FOOTER; ?>
          


  
 
