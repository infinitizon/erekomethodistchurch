<?php
/*
 * Include necessary files
 */
include_once 'core/init.inc.php';
if (@$_COOKIE['rem_emp']) {
    header('Location: home/');
}

if (isset($_POST['btn_submit']) && $_POST['btn_submit'] == 'Log In') {
    $stmt_chk_user = "SELECT *
	                  FROM t_wb_pro_usr 
			WHERE usr_nm=:user_name AND usr_pass=:pass AND active=1";
    $stmt = $dbo->prepare($stmt_chk_user);
    $stmt->execute(array(':user_name' => $_POST['user_name'], ':pass' => sha1($salt . md5($_POST['password'] . $salt))));

    if ($stmt->rowCount()) { //Check if a record is found.
        while ($row = $stmt->fetch()) {
            $_SESSION['r_k'] = $row['r_k'];
            $_SESSION['usr_nm'] = $row['usr_nm'];
            $_SESSION['usr_tp'] = $row['usr_tp'];
            $_SESSION['user_dets']['name']['firstname'] = $row['fst_nm'];
            $_SESSION['user_dets']['name']['lastname'] = $row['lst_nm'];
        }
        if (isset($_POST['rem_me']))
            setcookie("rem_emp", "yes", time() + (60 * 60 * 15), '/');  //If rem_me is set from post, set a cookie to 15days.

        header('Location: ' . $_POST['goto']);     // Allow login
    }else {
        extract($_POST);
        $err_msg = "Username. or password incorrect.<br />Please try again!";
    }
} elseif (isset($_POST['btn_submit']) && $_POST['btn_submit'] == 'Reset Password') {
    try {
        /// Update the email in the db
        $stmt_updt_pass = "UPDATE t_wb_pro_usr SET usr_pass=:pass WHERE eml_adr=:email AND active=1";
        $stmt = $dbo->prepare($stmt_updt_pass);
        $stmt->execute(array(':pass' => sha1($salt . md5($_POST['new_pass'] . $salt)), ':email' => $_POST['email']));
        $success_msg = "Password reset was successful.<br />You may now login with your new password.";
    } catch (exception $e) {
        $err_msg = $e->getMessage();
    }
} elseif (isset($_POST['btn_submit']) && $_POST['btn_submit'] == 'Get New Password') {
    //Check if Matric no or email entered exist in the db
    $stmt_chk_user = "SELECT eml_adr FROM t_wb_pro_usr WHERE usr_nm=:user_name_email OR eml_adr=:user_name_email AND active=1 ";
    $stmt = $dbo->prepare($stmt_chk_user);
    $stmt->execute(array(':user_name_email' => $_POST['user_name_email']));

    if ($stmt->rowCount()) {
        //If Matric no or email exists, send an email with a link to the user's email
        while ($row = $stmt->fetch()) {
            $dbemail = $row['eml_adr'];
        }
        $time = time();
        $hash = md5($dbemail . $time . $salt);
        $email = urlencode($dbemail);
        $link = WEB_ROOT . "/signin?action=rp&email=$email&t=$time&h=$hash";
        $body = "You (or someone else) requested that the password be reset on your admin account.<br /><br />";
        $body .= "If this was a mistake, just ignore this email and nothing will happen.<br /><br />";
        $body .= "<a href='$link'>Click here</a> to reset your password.<br /><br /><br />";
        $body .= "If you are unable to click the link, simply copy the link below into your browser:<br /><br />";
        $body .= "<a href='$link'>$link</a>";

        require("phpmailer-master/class.phpmailer.php");
        try {
            $mail = new PHPMailer(true); //New instance, with exceptions enabled
            $mail->IsSMTP();                           // tell the class to use SMTP
            $mail->SMTPAuth = true;                  // enable SMTP authentication
            $mail->IsSendmail();  // tell the class to use Sendmail
            $mail->AddReplyTo("info@erekomethodistchurch.org", "Ereko Methodist Church");
            $mail->From = "info@erekomethodistchurch.org";
            $mail->FromName = "Ereko Methodist Church";
            $mail->AddAddress($dbemail);
            $mail->Subject = "[Ereko Methodist Church] - Password reset";
            $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
            $mail->WordWrap = 80; // set word wrap
            $mail->MsgHTML($body);
            $mail->IsHTML(true); // send as HTML
            $mail->Send();
            $success_msg = 'Check your e-mail for the confirmation link.';
        } catch (phpmailerException $e) {
            $err_msg = $e->errorMessage();
        }
    } else { //Matric no or email DOES NOT exist
        extract($_POST);
        $err_msg = "Username or Email does not exist or is inactive.<br />Please try again or contact your administrator";
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href='/assets/images/favicon.gif' rel='SHORTCUT ICON' />

        <link rel="stylesheet" type="text/css" href="assets/css/signin.css"/>
        <title>Sign in &rsaquo; Ereko Methodist Church</title>
    </head>

    <body><!---Start body-->
        <div id="box_holder">

            <div id="logo_holder">
                <a href="<?php echo WEB_ROOT; ?>">
                    <img src="/assets/images/logo.png" align="absmiddle" width="120" height="50" alt="Ministry of Science" />
                </a>
            </div><!-- logo_holder ends -->
            <div id="note" style="text-align:center; font-size:11px;text-shadow:1px 1px 1px #FFF; font-weight:bold;">
                <?php
                switch (@$_GET['action']) {
                    case 'get_pass':
                        echo "Please enter your User name or email address. You will receive a link to create a new password via email.";
                        break;
                    default:
                        echo "";
                        break;
                }
                echo isset($err_msg) ? "<div class='err'>" . $err_msg . '</div>' : '';
                echo isset($success_msg) ? "<div class='success'>" . $success_msg . '</div>' : '';
                ?>
            </div>
            <div id="login_holder">
<?php require ("assets/common/login_form.php"); ?>
            </div><!-- login_holder ends -->

            <div class="others">
                <?php
                switch (@$_GET['action']) {
                    case 'get_pass':
                        echo "<a href='" . WEB_ROOT . "/signin'>Login</a>";
                        break;
                    case 'rp':
                        echo "<a href='" . WEB_ROOT . "/signin'>Login</a>";
                        break;
                    default:
                        echo "<a href='" . WEB_ROOT . "/signin?action=get_pass'>Forgotten password</a>";
                        break;
                }
                ?>
            </div><!-- others ends -->   
        </div><!-- box_holder ends -->
<?php if (@$_GET['message'] && @$_GET['type']): ?>
            <div id="notification">
                <div class="close">x</div>
                <div class="<?php echo @$_GET['type']; ?>" ><?php echo @$_GET['message']; ?></div>
            </div>
<?php endif; ?>
        <script type="text/javascript" src="<?php echo WEB_ROOT; ?>/assets/js/jquery-1-11.min.js"></script>
        <script type="text/javascript" src="<?php echo WEB_ROOT; ?>/assets/js/jquery-ui-11/jquery-ui.min.js"></script>
<?php if (@$err_msg) echo "<script type='text/javascript'>\$(function(){\$('#login_holder').effect('shake',{times:4},100);})</script>"; ?>
        <script type="text/javascript" src="<?php echo WEB_ROOT; ?>/assets/js/common.js"></script>
        <script type="text/javascript" src="<?php echo WEB_ROOT; ?>/assets/js/signin.js"></script>
    </body>
</html>