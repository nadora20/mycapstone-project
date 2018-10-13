<?php
$page_title="Activate";
$page_header_title='Activate Page';
include './includes/pref.php';
include HEADER;
if(isset($_SESSION['user_id'])){
    header('Location: '.BASE_URL);
    exit();
}

$display_form=TRUE;

$x = filter_input(INPUT_GET, 'x' ,FILTER_VALIDATE_EMAIL);
$y = filter_input(INPUT_GET, 'y', FILTER_SANITIZE_STRING);
if($x && strlen($y)==32){
    require MYSQL;
    $email = $dbcl->real_escape_string($x);
    $query ="SELECT active FROM users WHERE email = ? limit 1";
    $state = $dbcl->prepare($query);
    $state->bind_param('s',$email);
    $state->execute();
    $state->store_result();
    if($state->num_rows == 1){
        $state->bind_result($active);
        $state->fetch();
        if($active==$y){
            $query = "UPDATE users SET active = NULL WHERE email = ? limit 1";
            $state=$dbcl->prepare($query);
            $state->bind_param('s',$email);
            $state->execute();
            if($state->affected_rows ==1){
                $msg = '<p>Yuor account is now active, You may now <a href="login.php">Log in.</a></p>';
                $display_form = FALSE;
            }else{
                $msg = '<p>Your account could not be activated, Please re-check the link in mail.</p>';
            }
        }else if($active == NULL){ #active=NULL  =  account activated
            $msg = '<p>Your account is already activated!</p>';
        }else{
            $msg = '<p>Your account could not be activated, Please re-check the link in mail.</p>';
        }
    }else{
            $msg = '<p>Your account could not be activated, Please re-check the link in mail.</p>';
    }
    $state->close();unset($state);$dbcl->close(); unset($dbcl);
}
else if (filter_input(INPUT_POST,'activate')){
    require MYSQL;
    function send($id,$dbcl){
        $active=substr(sha1(uniqid(rand())),-32);
        $query="UPDATE users SET active = ? WHERE email = ? limit 1";
        $state = $dbcl->prepare($query);
        $state->bind_param('ss',$active,$id);
        $state->execute();
        if($state->affected_rows==1){
            $body = "To activate your account, please click on this link:\n\n";
            $body .= BASE_URL.'activate.php?x='.urlencode($id).'&y='.$active;
            mail($id, 'Registration Confirmation', $body, 'From: admin@mycapstone.tk');
            return TRUE;
        }else{
            return FALSE;
        }
    }
    function is_exist($id, $dbcl,$username){
        $query="SELECT email, active FROM users";
        if(!$username){
            $query .=" WHERE email = ?";
        }else{
            $query .=" WHERE username = ?";
        }
        $query .=" limit 1";
        $state=$dbcl->prepare($query);
        $state->bind_param('s',$id);
        $state->execute();
        $state->store_result();
        $state->bind_result($email,$active);
        $state->fetch();
        if($state->num_rows==1){
            if($username){
                return array(TRUE, $active,$email);
            }
            return array(TRUE,$active);
        }
        return FALSE;
    }
    
    function send_activation_link($id,$dbcl,$username){
        $result=is_exist($id,$dbcl,$username);
        if($result[0]){
            if($result[1] != NULL){
                $id = $username?$result[2]:$id;
                if( send( $id,$dbcl)){
                    $_SESSION['msg']='<p>A confirmation email has been sent to your e-mail address</p>';
                    header('location:'.BASE_URL.'activate.php');
                    exit();
                }else{
                    return '<p>System error occured.</p>';
                }
            }else{
                return '<p> Your account is already activated!</p>';
            }
        }else{
            return '<p>Please try again. Invalid username/E-mail!</p>';
        }
    }
    
    $id= $dbcl ->real_escape_string(
            trim(filter_input(INPUT_POST,'id',FILTER_SANITIZE_STRING)));
    if(filter_var($id,FILTER_VALIDATE_EMAIL) && strlen($id)<=80){
        $msg = send_activation_link($id, $dbcl,FALSE);
    }else if (len_check($id,5,50)){
        $msg= send_activation_link($id, $dbcl,TRUE);
    }else{
        $msg = '<p> Please, enter a valid username/e-mail! </p>';
        $dbcl->close();
        unset($dbcl);
    }
}



if (isset($_SESSION['msg'])){
    echo $_SESSION['msg'];
    unset ($_SESSION['msg']);
    session_destroy();
    setcookie(session_name(),'',time()-3600);
}
echo isset($msg)?$msg:'';

if ($display_form){
    ?>

<form action="activate.php" method="POST">
    <p>
        <label for="id">E-mail/username: </label>
        <input type="text" name="id" value="<?php echo isset($id)?$id:''; ?>"/>
    </p>
    <p>
        <input type="submit" name="activate" value="Send activation link" />
    </p>
</form>    

<?php } 

include FOOTER; ?>
}