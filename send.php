<?php
$page_title='Send';
$page_header_title='Send Page';
include './includes/pref.php';
include HEADER;

if(filter_input(INPUT_POST,'send')){
    require MYSQL;
    $un =FALSE; #un -> username 
    $errors = array();
    $safeData = array_map('trim', filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING));
    
   
    if(len_check($safeData['username'], 5, 50)){  #username validation
        $un = $dbcl->real_escape_string($safeData['username']);
    }else{
        $errors['username']='Please enter a valid username, should be within 5-50 characters';
    }
   
    
    if ($un){
        $exist = TRUE;
        $query = "SELECT username FROM users WHERE username = ?";
        $state = $dbcl->prepare($query);  #state -> statement   dbcl -> database connection link object
        $state->bind_param('s',$un);
        $state->execute();
        $state->bind_result($cun);
        $state->fetch();
        if($un != $cun){ #username  current-username
            $exist=FALSE;
            $errors['username']='this username is not exsit, please try another one';
        }
        
        if($exist){
            require MYSQL;
            $un = $_POST['username'];
            $msg = $_POST['msg'];
            $query1 = "INSERT INTO `messages` (`to`, `from`, `content`) VALUES ('" . $un . "', '" . $_SESSION['username'] . "', AES_ENCRYPT('" . $msg . "','secret_key'));";
            if($dbcl->query($query1) == TRUE){
            $dbcl->close();
            unset($dbcl);
        ?>
            <script> 
            window.alert("Message was sent!");
            location.replace("send.php");
            </script>
        <?php
    exit();
    }else{
        $_SESSION['msg']='<p>Error!</p>';
    }}
        $state->close();
        unset($state);
        $dbcl->close();
        unset($dbcl);
    }
}
?>
<fieldset>
    <legend>Send Message</legend>
    <?php 
    echo isset($general_msg)? $general_msg: ''; ?>
    <form action="send.php" method="POST">
        <p>
            <label for="username">To Username: </label>
            <input type="text" name="username" value=""/>
            <span>
                <?php echo isset($errors['username'])?$errors['username']:''; ?>
            </span>
        </p>
        <p>
            <label for="msg">Message: </label>
            <input type="text" name="msg" value=""/> 
            <span>
                <?php echo isset($errors['msg'])?$errors['msg']:''; ?>
            </span>
        </p>
        <p>
            <input type="submit" name="send" value="Send" />
        </p>
    </form>
    
</fieldset>
<?php include FOOTER; ?>