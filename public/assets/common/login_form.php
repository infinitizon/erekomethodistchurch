<?php
if (@$_GET['action'] == 'get_pass') {
    ?>
    <form action="<?php echo WEB_ROOT . '/signin?action=get_pass'; ?>" method="POST">
        <table width="100%" border="0">
            <tr><td colspan="2"><label>User Name Or Email<br /><input type="text" name="user_name_email" id="user_name_email" class="login_ctrl text_input" value="<?php echo @$user_name_email ?>" /></label></td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
                <td align="left">&nbsp;</td>
                <td align="right"><input type="submit" name="btn_submit" id="btn_submit" class="button" value="Get New Password" /></td></tr>
        </table>
    </form>
    <?php
} elseif (@$_GET['action'] == 'rp') {
    $hash = md5(urldecode($_GET['email']) . $_GET['t'] . $salt);

    if (time() - $_GET['t'] > (60 * 60 * 24 * 2) || $hash != $_GET['h']) {
        echo "<div class='err'>This password recovery attempt is invalid or expired.<br />Please try resetting all over.</div><br />";
        echo "<div class='others'><a href='" . WEB_ROOT . "/?action=get_pass'>Reset Password again</a></div><br />";
    } else {
        ?>
        <form action="signin" method="POST">
            <table width="100%" border="0">
                <tr><td colspan="2"><label>New password<br /><input type="password" name="new_pass" id="new_pass" class="new_pass text_input login_ctrl" value="" /></label></td></tr>
                <tr><td colspan="2"><label>Confirm new password<br /><input type="password" name="confirm_new_pass" id="confirm_new_pass" class="confirm_new_pass text_input login_ctrl" value="" /></label></td></tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td colspan="2">
                        <div id="passStrength" class="passStrength strength0"><div id="passDesc" class="passDesc">Password not entered</div></div>
                        <span style="color:#666; font-size:0.85em; padding-top:10px;">Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ & ).</span>
                    </td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr><td align="right" colspan="2"><input type="submit" name="btn_submit" id="btn_submit" class="button" value="Reset Password" /></td></tr>
            </table>
            <input type="hidden" name="email" id="email" value="<?php echo urldecode($_GET['email']); ?>" />
        </form>
        <?php
    }
} else {
    ?>
    <form action="signin" method="POST">
        <?php $return_url = isset($_GET['return_url']) ? urldecode($_GET['return_url']) : 'home/'; // Fetch URL to redirect to  ?>
        <table width="100%" border="0">
            <tr><td colspan="2"><label>Username.<br /><input type="text" name="user_name" id="user_name" class="login_ctrl text_input" value="<?php echo @$user_name ?>" /></label></td></tr>
            <tr><td colspan="2"><label>Password<br /><input type="password" name="password" id="password" class="login_ctrl text_input" value="" /></label></td></tr>
            <tr><input type="hidden" name="goto" value="<?php echo $return_url ?>" /></tr>
            <tr>
                <td align="left"><label><input type="checkbox" name="rem_me" id="rem_me" />Remember Me</label></td>
                <td align="right"><input type="submit" name="btn_submit" id="btn_submit" class="button" value="Log In" /></td></tr>
        </table>
    </form>
<?php } ?>