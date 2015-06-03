<?php
if(!isset($_SESSION['usr_nm'])){
	$this_url = "http".(!empty($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	
	// Redirect to login page passing current URL
	header('Location: '.WEB_ROOT.'/signin?return_url=' . urlencode($this_url));
	exit;
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link href='/assets/images/favicon.gif' rel='SHORTCUT ICON' />

<title><?php echo $page_title; ?></title>

<meta name="title" content="Leave management system for the Ministry of science and computer, Lagos State, Nigeria.">
<meta name="keywords" content="Leave Management System, Leave, Ministry of science, Ministry of computer, Lagos State, Nigeria.">
<meta name="description" content="A governmental application for managing leave applications in the Lagos State Ministry of Science and Tech">
<meta name="author" content="Abimbola Hassan">

<?php foreach ( $common_css_files as $css ): ?>
	<link rel="stylesheet" type="text/css" media="screen,projection" href="<?php echo WEB_ROOT; ?>/assets/css/<?php echo $css; ?>" />
<?php endforeach; ?>
<?php foreach ( $font_awesome_files as $font_awesome ): ?>
  <link rel="stylesheet" type="text/css" media="screen,projection" href="<?php echo WEB_ROOT; ?>/assets/fontawesome/css/<?php echo $font_awesome; ?>" />
<?php endforeach; ?>
<?php foreach ( $page_css_files as $page_css ): ?>
  <link rel="stylesheet" type="text/css" media="screen,projection" href="<?php echo WEB_ROOT; ?>/home/assets/css/<?php echo $page_css; ?>" />
<?php endforeach; ?>

</head>
<body>
    <div id="l_sidebar">
        <?php
        $main_mnu = new Functions();
        echo $main_mnu->_mainMenu();
        ?>
        <ul class="main_nav">
            <li><a href="misc"><span style="float:right;">&nbsp;&nbsp; &#9661;</span>Miscellanous</a>
                <ul>
                    <li><a href="/misc/hymn">Hymn</a></li>
                    <li><a href="/misc/images">Images</a></li>
                    <li><a href="/misc/prayerfocusmemoryverse">Prayer Focus/ Mem Verse</a></li>
                    <li><a href="/misc/registered">Registered Members</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <div id="gen_container">
        <div style="float:right; margin:5px;">
            <ul class="top-menu">
                <li><a href="<?php echo WEB_ROOT; ?>/home/"><i class="icon-home icon-2x awesome-ico"></i></a></li>
                <li><a href="" class="user_dets"><i class="icon-user icon-2x awesome-ico"></i></a>
                    <div id="user_dets">
                        <div style="padding:10px;">
                            <strong><?php echo @$_SESSION['user_dets']['name']['firstname'] . ' ' . @$_SESSION['user_dets']['name']['lastname']; ?></strong>
                        </div>
                        <div style="padding:10px; background:#F5F5F5; border-top:1px solid #C4C4C4;">
                            <a href="?logout=yes" style="color:#FFF; float:right;"><i class="icon-power-off icon-2x" style="color: #900;"></i></a>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                </li>
                
            </ul>
        </div>
        <div style="clear:right;"></div>
        
        <!--li style="position:relative">
                    <a href="" class="user_dets"><i class="icon-user icon-large awesome-ico"></i><a>
                    <div id="user_dets">
                        <div style="padding:10px;">
                            <strong><?php echo @$_SESSION['user_dets']['name']['firstname'] . ' ' . @$_SESSION['user_dets']['name']['lastname']; ?></strong>
                        </div>
                        <div style="padding:10px; background:#F5F5F5; border-top:1px solid #C4C4C4;">
                            <a href="?logout=yes" style="background:#900; color:#FFF; float:right;"><i class="icon-power-off awesome-ico"></i></a>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                </li-->