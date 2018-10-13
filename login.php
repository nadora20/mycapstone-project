<?php
$page_title="Login";
$page_header_title='Login Page';
include './includes/pref.php';
include HEADER;
if(isset($_SESSION['user_id'])){
    header('Location: '.BASE_URL);
    exit();
}

if(filter_input(INPUT_POST, 'login')){
    require MYSQL;
    $errors=array();
    $safePOST=array_map('trim', filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING));
    $id=$dbcl->real_escape_string($safePOST['id']);   #id = email or username
    $pass=$dbcl->real_escape_string($safePOST['password']);
    $valid=TRUE;
    $query="SELECT AES_DECRYPT(pin,salt), active, user_id, username FROM users WHERE ";
    if(filter_var($id,FILTER_VALIDATE_EMAIL) && strlen($id)<=80){
        $query .="email = ? limit 1";
    }else if(len_check($id,5,50)){
        $query .="username = ? limit 1";
        }else{
        $errors['id']='Please enter a valid username/e-mail address.';
        $valid = FALSE;
        }
    
    if(!len_check($pass, 8, 20)){
        $errors['password']='Please enter a valid password';
        $valid = FALSE;
    }
    
    
    if($valid){ 
        $state = $dbcl->prepare($query);
        $state->bind_param('s',$id);
        $state->execute();
        $state->store_result();
        $state->bind_result($realPass,$active,$user_id,$username);
        $state->fetch();
        if($state->num_rows ==1){
            if($realPass==$pass){
                if($active==NULL){
                    $_SESSION = array(
                        'user_id'=>$user_id,
                        'username'=>$username
                    );
                    ?>
                    <script> location.replace("read.php"); </script>
                    <?php 
                    if (headers_sent()) {
                    die("Redirect failed. Please click on this link: <a href='/read.php'>Read</a>");
                        }else{
                    exit(header("Location: /read.php"));
}
                    exit();
                }else{$msg='<p>Your account is not active yet, Please <a href="activate.php">Activate your account</a>.</p>';}
            }else{$msg='<p>Password is incorrect! please, try again. Or <a href="reset_password.php">Reset your password</a></p>';
                /*$query1="SELECT email FROM users WHERE ";
                if(filter_var($id,FILTER_VALIDATE_EMAIL) && strlen($id)<=80){
                    $query1 .="email = ? limit 1";
                }else if(len_check($id,5,50)){
                    $query1 .="username = ? limit 1";}
                    $state = $dbcl->prepare($query1);
                    $state->bind_param('s',$id);
                    $state->execute();
                    $state->store_result();
                    $state->bind_result($id);
                    $state->fetch();
                $body = "Here's your valid password :-\n\n";
                $body .= $realPass;
                mail($id, 'Your Pass', $body, 'From: admin@mycapstone.tk');
                ?>
                <script> 
                    window.alert("Password is incorrect!, Your valid password has been sent to your email address!");                    
                    location.replace("login.php"); 
                </script>
                <?php*/
            }
    }else{
                $msg='<p>Invalid username (account not found)! please try, again.</p>';
            }
}}
?>

<fieldset>
    <legend>Login</legend>
    <?php echo isset($msg)?$msg:''; ?>
    <form action="login.php" method="POST">
        <p>
            <label for="id">E-mail/username: </label>
            <input type="text" name="id" value="<?php echo isset($id)?$id:''; ?>"/>
            <span>
                <?php echo isset($errors['id'])?$errors['id']:''; ?>
            </span>
        </p>
        <p>
            <label for="password">Password: </label>
            <input type="password" name="password" value="<?php echo isset($pass)?$pass:''; ?>"/>
            <span>
                <?php echo isset($errors['password'])?$errors['password']:''; ?>
            </span>
        </p>
        <p>
            <input type="submit" name="login" value="Sign In" />
        </p>
    </form>
</fieldset>
<?php include FOOTER; ?>