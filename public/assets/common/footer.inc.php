<div id="footer_container">
    <div id="footer">
        <div class="section">
            <ul>
                <li>General enquiries</li>
                <li><a href="mailto:info@erekomethodistchurch.org">info@erekomethodistchurch.org</a></li>
                <li>Investors/Donations enquiries</li>
                <li><a href="mailto:info@methodist.com">investors@erekomethodistchurch.org</a></li>
            </ul>
        </div>
        <div class="section">
            <ul>
                <span>CONTACT DETAILS</span>
                <li>Ereko Methodist Church<br />18, Ereko Street,<br />Idumota,<br />Lagos, Nigeria. </li>
            </ul>
        </div>
        <div class="section">
            <ul>
                <span>Join Our Mailing List</span>
                <li>
                    <input type="text" placeholder="Your name" name="name" /><br />
                    <input type="text" placeholder="Your email address" name="email" /><br />
                    <p><a class="button" href="" style="margin-left:0.5em;">Subscribe</a></p>
                </li>
            </ul>
        </div>
    </div>
    <div style="clear:both" />
</div>
<?php foreach ($page_js_files as $page_js): ?>
    <script language="javascript" type="text/javascript" src="<?php echo WEB_ROOT; ?>/home/assets/js/<?php echo $page_js; ?>" /></script>
<?php endforeach; ?>
<?php
if (isset($msgArr)) {
    echo '<div id="notification"><a href="" class="close">Close</a>';

    $msg_tp = '';
    switch ($msgArr['type']) {
        case 0:
            $msg_tp = 'err';
            break;
        default:
            $msg_tp = 'success';
            break;
    }
    echo "<div class=\"{$msg_tp}\">"
    . $msgArr['msg']
    . '</div>';

    echo '<script type="text/javascript">$(function(){$(".close").click(function(e){e.preventDefault();$(this).parent("#notification").slideUp();});});</script></div>';
}
?>
<style>
    .fixed-dialog{
        position: fixed;
        top: 50px;
        left: 50px;
    }
</style>
<!--div class="underconstruction">
    <img src="/assets/images/underconstructn.jpg" width="224" height="148" alt="Website Under Construction" style="float:left" />Welcome to the Ereko Methodist Church website<br /><br />The website is currently under construction and only availabe for testing</div-->

<div id="dialog_box"></div>
<script type="text/javascript">
    $(function() {
        //$(".underconstruction").dialog({
        //    dialogClass: 'fixed-dialog',
        //    modal: true,
        //    buttons: {
        //        Ok: function() {
        //            $(this).dialog("close");
        //        }
        //    }
        //});
    });
</script>
</body>
</html>