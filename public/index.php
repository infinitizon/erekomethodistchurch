<?php
/*
 * Include necessary files
 */
include_once 'core/init.inc.php';
$fxns = new Functions($dbo);
/*
 * Set up the page title and CSS files
 */
$page_title = ":: Home &rsaquo;&rsaquo; Ereko Methodist Church ::";
$common_css_files = array('jquery-ui-11/jquery-ui.min.css', 'jcarousel.css', 'main.css', 'common.css');
$page_css_files = array();
$font_awesome_files = array('font-awesome.css');
$general_js_files = array('jquery-1-11.min.js', 'jquery-ui-11/jquery-ui.min.js', 'validator.js'
                            , 'main.js', 'jcarousel/jquery.jcarousel.min.js');
$page_js_files = array();
/*
 * Perform feedback submit
 */
if (@$_POST['submit'] && @$_POST['submit'] == 'Submit Feedback') {
    try {
        $stmtFB = $dbo->prepare("INSERT INTO contacts (name, email, subject, msg)
					VALUES (:name, :email, :subject, :msg)");
        $stmtFB->execute(array(':name' => $_POST['fullname'], ':email' => $_POST['email'], ':subject' => $_POST['subject'], ':msg' => $_POST['msg']));
        $msgArr = array('msg' => "Thank you for your feedback. We would get back to you as necessary."
            , 'type' => 1);
    } catch (PDOException $e) {
        $msgArr = array('msg' => "Feedback entry failed:<br />" . $e->getMessage() . ".<br />Please try again later!"
            , 'type' => 0);
    }
}
/*
 * Include the header
 */
include_once 'assets/common/header.inc.php';

/*
 * 	Get page content and details
 */
$page = (substr($_SERVER['REQUEST_URI'], 1) == "") ? "/" : "/" . substr($_SERVER['REQUEST_URI'], 1);
$page = strtok($page, '?');
//echo $page;
$page_details = $dbo->query("SELECT page_label, page_url, content FROM pages WHERE page_url = '{$page}'");
while ($page_detail = $page_details->fetch(PDO::FETCH_ASSOC)) {
    extract($page_detail);
}
?>
<div id="slider_container">
    <div id="social_like">
        <iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Ferekomethodistchurch&amp;width=50&amp;layout=box_count&amp;action=like&amp;show_faces=false&amp;share=false&amp;height=65" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:65px; width:50px;" allowTransparency="true"></iframe><br /><br />
        <!-- Place this tag where you want the +1 button to render. -->
        <div class="g-plusone" data-size="tall" data-annotation="none"></div>

        <!-- Place this tag after the last +1 button tag. -->
        <script type="text/javascript">
            (function() {
                var po = document.createElement('script');
                po.type = 'text/javascript';
                po.async = true;
                po.src = 'https://apis.google.com/js/platform.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(po, s);
            })();
        </script>
    </div>
    <?php if ($page == '/') { ?>
    <div class="jcarousel-wrapper">
        <div class="jcarousel">
            <ul>
                <li><img src="<?php echo WEB_ROOT; ?>/assets/images/slider/1.jpg" /></li>
                <li><a href="<?php echo WEB_ROOT; ?>/misc/hymn"><img src="<?php echo WEB_ROOT; ?>/assets/images/slider/2.jpg" /></a></li>
                <li><img src="<?php echo WEB_ROOT; ?>/assets/images/slider/3.jpg" /></li>
                <li><a href="<?php echo WEB_ROOT; ?>/resources"><img src="<?php echo WEB_ROOT; ?>/assets/images/slider/4.jpg" /></a></li>
            </ul>
        </div>
        <!-- Controls -->
        <a class="jcarousel-prev" href="#">&lsaquo;</a>
        <a class="jcarousel-next" href="#">&rsaquo;</a>
        <!-- Pagination -->
        <!--p class="jcarousel-pagination"></p-->
    </div>
    <?php } ?>
</div>
<div id="contents">
    <div id="r_sidebar">
        <img src="/assets/images/vision_mission.gif" style="width:290px;" />
        <?php
            $sqlGetActiveVid = "SELECT url FROM gallery WHERE type=2 AND active=1";//type 2 = video
            $getVideo = $fxns->_execQuery($sqlGetActiveVid, TRUE, FALSE);
        ?>
        <iframe width="300" height="250" src="<?php echo $getVideo['url']; ?>" frameborder="0" allowfullscreen></iframe>
        <iframe src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2Ferekomethodistchurch&amp;width&amp;height=258&amp;colorscheme=light&amp;show_faces=true&amp;header=false&amp;stream=false&amp;show_border=true" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:258px;" allowTransparency="true"></iframe>
        <div class="news">
            <h2>IN THE NEWS</h2>
            <?php
            $getNews = "SELECT news_id, news_title, image, news_detail, date_created FROM news";
            $getNews = $fxns->_execQuery($getNews);
            $news = "<ul id=\"ticker\">";
            foreach ($getNews as $assocKey => $details) {
                $readMoreLink = "<a href=\"" . WEB_ROOT . "/news-and-events\" id=\"{$details['news_id']}\">";
                $newsLink = $fxns->_readMore(strip_tags(html_entity_decode($details['news_detail'])), 50, $readMoreLink);
                $news .= "<li>{$readMoreLink}";
                $news .= "<img src=\"" . (!empty($details['image']) ? $details['image'] : 'none.png') . "\" style=\"float:left; width:20px; height:20px; margin:0 5px;\" />";
                $news .= "{$details['news_title']}</a><br />{$newsLink}</li>";
            }
            $news .= "</ul>";
            echo $news;
            ?>
        </div>
    </div>
    <div id="main_content">
        <div id="content">
            <?php                      
            if($page=='/misc/hymn')
                echo '<embed src="'.WEB_ROOT.'/assets/downloads/hymn/'.date('Y').'/'.date('m').'/'.date('j').'.mp3" autostart="true" hidden="false" height="40" width="400"></embed>';
            else
                echo html_entity_decode($content);
            ?>
        </div>
    </div>        
</div>
<?php
/*
 * Include the footer
 */
include_once 'assets/common/footer.inc.php';
