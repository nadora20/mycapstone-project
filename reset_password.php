<?php

$page_title="Reset";
$page_header_title='Reset Page';
include './includes/pref.php';
include HEADER;
if(isset($_SESSION['user_id'])){
    header('Location: '.BASE_URL);
    exit();
}

$display_form=TRUE;

if(filter_input(INPUT_POST,'reset')){
    require MYSQL;
    $un = $e =FALSE; #un -> username    e -> email
    $errors = array();
    $safeData = array_map('trim', filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING));
    
    if(filter_var($safeData['id'],FILTER_VALIDATE_EMAIL) #E-mail validation
            && strlen($safeData['id']) <= 80){
        $e = $dbcl ->real_escape_string($safeData['id']);
    }else if(len_check($safeData['id'], 5, 50)){  #username validation
        $un = $dbcl->real_escape_string($safeData['id']);
    }else{
        $_SESSION['msg']='Please provide a valid Username/E-mail !';
    }
    

    if ($un || $e){
        $taken = FALSE;
        $query = "SELECT username, email FROM users WHERE username = ? || email = ?";
        $state = $dbcl->prepare($query);  #state -> statement   dbcl -> database connection link object
        $state->bind_param('ss',$un, $e);
        $state->execute();
        $state->bind_result($cun , $ce);
        $state->fetch();
        if($un == $cun&& $un==TRUE){ #username  current-username
            $taken=TRUE;
        }else if ($e == $ce && $e==TRUE){ #email  current email
            $taken = TRUE;
        }else{
            $_SESSION['msg']='Username/E-mail not exist, Please provide your true Username/E-mail !';
            header('location:'.BASE_URL.'reset_password.php');
            exit();
        }
        
        if($taken){
            require MYSQL;
            $errors=array();
            $safePOST=array_map('trim', filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING));
            $id=$dbcl->real_escape_string($safePOST['id']);
            $query="SELECT AES_DECRYPT(pin,salt) FROM users WHERE ";
            if(filter_var($id,FILTER_VALIDATE_EMAIL) && strlen($id)<=80){
                $query .="email = ? limit 1";
            }else if(len_check($id,5,50)){
                $query .="username = ? limit 1";
            }else{
                $errors['id']='Please enter a valid username/e-mail address.';
            }
            $state = $dbcl->prepare($query);
            $state->bind_param('s',$id);
            $state->execute();
            $state->store_result();
            $state->bind_result($realPass);
            $state->fetch();

            $query1="SELECT email FROM users WHERE ";
                if(filter_var($id,FILTER_VALIDATE_EMAIL) && strlen($id)<=80){
                    $query1 .="email = ? limit 1";
                }else if(len_check($id,5,50)){
                    $query1 .="username = ? limit 1";}
                $state1 = $dbcl->prepare($query1);
                $state1->bind_param('s',$id);
                $state1->execute();
                $state1->store_result();
                $state1->bind_result($id);
                $state1->fetch();
                $body = "Here's your valid password :-\n\n";
                $body .= $realPass;
                mail($id, 'Your Pass', $body, 'From: admin@mycapstone.tk');
                $_SESSION['msg']='<p>Your Password has been sent to your e-mail address.</p>';
                header('location:'.BASE_URL.'reset_password.php');
                exit();
                }
        $state->close();
        unset($state);
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

<form action="reset_password.php" method="POST">
    <p>
        <label for="id">Username/E-mail: </label>
        <input type="text" name="id" value="<?php echo isset($id)?$id:''; ?>"/>
    </p>
    <p>
        <input type="submit" name="reset" value="Send Password" />
    </p>
</form>    

<?php } 

include FOOTER; ?>
}