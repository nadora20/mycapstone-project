<?php
$page_title='Register';
$page_header_title='Register Page';
include './includes/pref.php';
include HEADER;

if(isset($_SESSION['user_id'])){
    header('location: '.BASE_URL);
    exit();
}

if(filter_input(INPUT_POST,'register')){
    require MYSQL;
    $un = $p = $e =FALSE; #un -> username   p -> password   e -> email
    $errors = array();
    $safeData = array_map('trim', filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING));
    
    if(filter_var($safeData['email'],FILTER_VALIDATE_EMAIL) #E-mail validation
            && strlen($safeData['email']) <= 80){
        $e = $dbcl ->real_escape_string($safeData['email']);
    }else{
        $errors['email']='Please provide a valid email address!';
    }
    
    if(len_check($safeData['username'], 5, 50)){  #username validation
        $un = $dbcl->real_escape_string($safeData['username']);
    }else{
        $errors['username']='Please enter your username, should be within 5-50 characters';
    }
    
    if(len_check($safeData['password'], 8, 20)){  #password validation
        if($safeData['password']==$safeData['cpassword']){
            $p =$dbcl ->real_escape_string($safeData['password']);
        }else{$errors ['cpassword']='Please make sure to enter the same password';}
        
    }else{
        $errors['password']='Please enter your password, should be within 8-20 characters';
    }
    
    if ($un && $p && $e){
        $taken = FALSE;
        $query = "SELECT username, email FROM users WHERE username = ? || email = ?";
        $state = $dbcl->prepare($query);  #state -> statement   dbcl -> database connection link object
        $state->bind_param('ss',$un, $e);
        $state->execute();
        $state->bind_result($cun , $ce);
        $state->fetch();
        if($un == $cun){ #username  current-username
            $taken=TRUE;
            $errors['username']='this username is already registered, please try another one';
        }
        if ($e == $ce){ #email  current email
            $taken = TRUE;
            $errors['email']='This email is already registered, You can <a href="login.php">Log In</a> or <a href="reset_password.php">Reset Password</a>';
        }
        if(!$taken){
            $query="INSERT INTO users"
                    ."(username,email,salt,active,pin)"
                    ."VALUES"
                    ."(?,?,?,?,AES_ENCRYPT(?,?))";
            $salt = substr(md5(uniqid(rand())),-20);
            $active = substr(sha1(uniqid(rand())),-32);
            $state = $dbcl->prepare($query);
            $state ->bind_param('ssssss',$un,$e,$salt,$active,$p,$salt);
            $state ->execute();
            
            if($state->affected_rows==1){
                $body = "Thank you for registering. To activate your account, please click on this link :\n\n";
                $body .=BASE_URL.'activate.php?x='.urldecode($e).'&y='.$active;
                mail ($e, 'Registration Confirmation',$body,'From: admin@mycapstone.tk');
            $state->close();
            unset($state);
            $dbcl->close();
            unset($dbcl);
            ?>
            <script> 
            window.alert("Thank you for registring, A confirmation email has been sent to your e-mail address");
            location.replace("login.php");
            </script>
            <?php
            exit();
            }else{
                $general_msg='<p>System error occured, Registration unsuccessful'.$state->error;
            }
        }
        $state->close();
        unset($state);
        $dbcl->close();
        unset($dbcl);
    }
}
?>
<fieldset>
    <legend>Register</legend>
    <?php if (isset($_SESSION['msg'])){
        echo $_SESSION['msg'];
        unset($_SESSION['msg']);
        session_destroy();
        setcookie(session_name(),'',time()-3600);
    }
    echo isset($general_msg)? $general_msg: ''; ?>
    <form action="register.php" method="POST">
        <p>
            <label for="username">Username: </label>
            <input type="text" name="username" value="<?php
            echo isset($safeData['username'])?$safeData['username']:'';
            ?>"/>
            <span>
                <?php echo isset($errors['username'])?$errors['username']:''; ?>
            </span>
        </p>
        <p>
            <label for="email">E-mail: </label>
            <input type="text" name="email" value="<?php
            echo isset($safeData['email'])?$safeData['email']:'';
            ?>"/>
            <span>
                <?php echo isset($errors['email'])?$errors['email']:''; ?>
            </span>
        </p>
        <p>
            <label for="password">Password: </label>
            <input type="password" name="password" value="<?php
            echo isset($safeData['password'])?$safeData['password']:'';
            ?>"/>
            <span>
                <?php echo isset($errors['password'])?$errors['password']:''; ?>
            </span>
        </p>
        <p>
            <label for="cpassword">Confirm Password: </label>
            <input type="password" name="cpassword" value="<?php
            echo isset($safeData['cpassword'])?$safeData['cpassword']:'';
            ?>"/>
            <span>
                <?php echo isset($errors['cpassword'])?$errors['cpassword']:''; ?>
            </span>
        </p>
        <p>
            <input type="submit" name="register" value="Register" />
        </p>
    </form>
    
</fieldset>

<?php include FOOTER; ?>

