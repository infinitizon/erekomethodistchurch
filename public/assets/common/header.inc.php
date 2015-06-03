<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href='<?php echo WEB_ROOT; ?>/assets/images/favicon.png' rel='SHORTCUT ICON' />
        <title><?php echo $page_title; ?></title>
        <?php foreach ($common_css_files as $css): ?>
            <link rel="stylesheet" type="text/css" media="screen,projection" href="<?php echo WEB_ROOT; ?>/assets/css/<?php echo $css; ?>" />
        <?php endforeach; ?>
        <?php foreach ($font_awesome_files as $font_awesome): ?>
            <link rel="stylesheet" type="text/css" media="screen,projection" href="<?php echo WEB_ROOT; ?>/assets/fontawesome/css/<?php echo $font_awesome; ?>" />
        <?php endforeach; ?>
        <?php foreach ($page_css_files as $page_css): ?>
            <link rel="stylesheet" type="text/css" media="screen,projection" href="<?php echo WEB_ROOT; ?>/home/assets/css/<?php echo $page_css; ?>" />
        <?php endforeach; ?>
        <?php foreach ($general_js_files as $gen_files): ?>
            <script language="javascript" type="text/javascript" src="<?php echo WEB_ROOT; ?>/assets/js/<?php echo $gen_files; ?>" /></script>
        <?php endforeach; ?>
    </head>

    <body>
        <div id="fb-root"></div>
        <script>(function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id))
                    return;
                js = d.createElement(s);
                js.id = id;
                js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&appId=1468318270089362&version=v2.0";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));</script>

        <div style="padding:3px; background:#333;">
            <div style="width:1000px; margin:auto;">
                <ul class="top_nav">
                    <li><a href="/signin" class="signIn">Sign In</a></li>
                    <li><a href="" class="forModal register">Register</a>
                </ul>
                <div id="marqueeInfo">
                    <marquee scrollamount="2"><?php
                        $sqlGetQuote = "SELECT * FROM prayerfocusmemoryverse WHERE active = 1";
                        $sqlGetQuote = $fxns->_execQuery($sqlGetQuote);
                        $quote = NULL;
                        foreach ($sqlGetQuote as $key => $value) {
                            if ($value['type'] == '1') {
                                $quote .= "<span>Prayer Focus - {$value['title']}:</span> &ldquo;{$value['detail']}&rdquo; ";
                            } else {
                                $quote .= "&nbsp;<span>Memory Verse:</span> &ldquo;{$value['detail']}&rdquo;";
                            }
                        }
                        echo $quote;
                        ?>
                    </marquee>
                </div>
                <div id="social">
                    <a href="https://www.facebook.com/erekomethodistchurch"><img src="<?php echo WEB_ROOT; ?>/assets/images/social/facebook.jpg" alt="facebook" /></a>&nbsp;
                    <img src="<?php echo WEB_ROOT; ?>/assets/images/social/twitter.jpg" alt="twitter" />&nbsp;
                    <img src="<?php echo WEB_ROOT; ?>/assets/images/social/google.jpg" alt="google plus" />&nbsp;
                </div>
            </div>
            <div style="clear:both;"></div>
        </div>
        <div class="top_rept">
            <div class="top">
                <?php
                $main_mnu = new Functions();
                echo $main_mnu->_mainMenu();
                ?>
                <a href="/"><img src="<?php echo WEB_ROOT; ?>/assets/images/logo.png" alt="Methodist Church Nigeria" style="padding:5px 0;" /></a>
            </div>
        </div>
