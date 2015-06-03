<?php
/*
 * Include necessary files
 */
include_once 'core/init.inc.php';
$fxns = new Functions($dbo);
/*
 * Set up the page title and CSS files
 */
if (isset($_POST['gallery'])) { ###  If we are coming from Profiles	
    if (isset($_POST['gallery_id']) && !isset($_POST['currentpage'])) {
        $getGallery = "SELECT sn, url, title, detail"
                . ", (select min(sn) from gallery where sn > :sn and type={$_POST['type']}) next"
                . ", (select max(sn) from gallery where sn < :sn and type={$_POST['type']}) prev"
                . ", (SELECT COUNT(*) FROM gallery WHERE TYPE={$_POST['type']}) totalGallery "
                . " FROM gallery WHERE sn = :sn";
        $getGallery = $fxns->_execQuery($getGallery, true, false, $params = array(':sn' => $_POST['gallery_id']));
        $gallerys = "<div><a href=\"\" class=\"gallery\"> Back to Gallery</a></div>
                        <div style=\"float:left; text-align:center;\">
                            <img src=\"{$getGallery['url']}\" title=\"{$getGallery['title']}\" style=\"max-width:500px;\" />
			</div>".html_entity_decode($getGallery['detail']);
        $next = (empty($getGallery['next'])) ? "" : "<a href=\"\" class=\"gallery\" id=\"" . ($getGallery['next']) . "\" style=\"float:right;\">Next Image >></a>";
        ;
        $prev = (empty($getGallery['prev'])) ? "" : "<a href=\"\" class=\"gallery\" id=\"" . ($getGallery['prev']) . "\" style=\"float:left;\"><< Previous Image</a>";
        $gallerys .= "<div style=\"clear:left;\">{$prev}{$next}</div><div style=\"clear:left;\">&nbsp;</div>";
        echo "<h1>".(($_POST['type']==1)?'Photo Gallery':($_POST['type']==2?'Video Gallery':'Testimonies'))."</h1>" . $gallerys;
    } else {
        //var_dump($_POST);exit;
        $getNewsCount = "SELECT COUNT(*) galleryCount FROM gallery WHERE TYPE={$_POST['type']}";
        $rowsperpage = 9;
        $getNewsCount = $fxns->_execQuery($getNewsCount, true, false);
        $preparePaging = $fxns->_preparePaging($getNewsCount['galleryCount'], $rowsperpage, @$_POST['currentpage']);

        $getGallery = "SELECT sn, url, title FROM gallery WHERE TYPE={$_POST['type']}
					LIMIT {$preparePaging['offset']}, {$rowsperpage}";
        $getGallery = $fxns->_execQuery($getGallery);
        $gallerys = "<ul class=\"".(($_POST['type']==2) ? "videoThumb":"galleryThumb")."\">";
        foreach ($getGallery as $assocKey => $details) {
            $gallerys .= "<li>".
                            (($_POST['type']==2)
                            ?"<iframe width=\"300\" height=\"250\" src=\"{$details['url']}\" frameborder=\"0\" allowfullscreen></iframe><br /><span>{$details['title']}</span>"
                            :"<a href=\"\" class=\"gallery\" id=\"{$details['sn']}\">
                                <img src=\"{$details['url']}\" style=\"float:left;\" /><br /><span>{$details['title']}</span>
                            </a>")
                        ."</li>";
        }
        $gallerys .= "</ul>";
        $gallerys .= "<div style=\"clear:left;\">&nbsp;</div>";
        $gallerys .= $fxns->_buildPagingLinks($range = 5, $preparePaging['currentpage'], $preparePaging['totalpages'], array('div' => 'phpPaging', 'link' => 'gallery'));

        echo "<h1>".(($_POST['type']==1)?'Photo Gallery':($_POST['type']==2?'Video Gallery':'Testimonies'))."</h1>" . $gallerys;
    }
} elseif (isset($_POST['profile'])) { ###  If we are coming from Profiles	
    if (isset($_POST['profile_id']) && !isset($_POST['currentpage'])) {
        $getProfile = "SELECT sn, name, title, profile, image, (SELECT COUNT(*) FROM profiles) totalProfiles FROM profiles WHERE sn = :sn";
        $getProfile = $fxns->_execQuery($getProfile, true, false, $params = array(':sn' => $_POST['profile_id']));
        $profiles = "<div><a href=\"\" class=\"profile\"> Back to Profiles</a></div>
					<div style=\"float:left; text-align:center;\">
						<img src=\"{$getProfile['image']}\" title=\"{$getProfile['name']}\" />
					</div>{$getProfile['profile']}";
        $next = ($getProfile['sn'] + 1 > $getProfile['totalProfiles']) ? "" : "<a href=\"\" class=\"profile\" id=\"" . ($getProfile['sn'] + 1) . "\" style=\"float:right;\">Next Profile >></a>";
        ;
        $prev = ($getProfile['sn'] - 1 == 0) ? "" : "<a href=\"\" class=\"profile\" id=\"" . ($getProfile['sn'] - 1) . "\" style=\"float:left;\"><< Previous Profile</a>";
        $profiles .= "<div style=\"clear:left;\">{$prev}{$next}</div><div style=\"clear:left;\">&nbsp;</div>";
        echo "<h1>Profile of {$getProfile['title']}<br /><br />({$getProfile['name']})</h1>" . $profiles;
    } else {
        $getNewsCount = "SELECT COUNT(*) profilesCount FROM profiles";
        $rowsperpage = 5;
        $getNewsCount = $fxns->_execQuery($getNewsCount, true, false);
        $preparePaging = $fxns->_preparePaging($getNewsCount['profilesCount'], $rowsperpage, @$_POST['currentpage']);

        $getProfile = "SELECT sn, name, title, profile, image FROM profiles
					LIMIT {$preparePaging['offset']}, {$rowsperpage}";
        $getProfile = $fxns->_execQuery($getProfile);
        $profiles = "<ul class=\"profileTmb\">";
        foreach ($getProfile as $assocKey => $details) {
            $readMoreLink = "<a href=\"\" class=\"profile\" id=\"{$details['sn']}\">";
            $profile = $fxns->_readMore(strip_tags ($details['profile']), 150, $readMoreLink);
            $profiles .= "<li>{$readMoreLink}
								<img src=\"{$details['image']}\" style=\"float:left;\" />{$details['title']}<br />{$details['name']}
							</a><br />{$profile}
						</li>";
        }
        $profiles .= "</ul>";
        $profiles .= $fxns->_buildPagingLinks($range = 5, $preparePaging['currentpage'], $preparePaging['totalpages'], array('div' => 'phpPaging', 'link' => 'profile'));

        echo "<h1>Officers' Profiles</h1>" . $profiles;
    }
} elseif (isset($_POST['lectionary'])) { ###  If we are coming from Lectionary	
    $file = WEB_ROOT . str_replace(" ", "_", $_POST['lectionary']);
    $file_headers = @get_headers($file);
    if ($file_headers[0] == 'HTTP/1.1 404 Not Found' && !@fopen($file, "r")) {
        echo 0;
    } else {
        echo 1;
    }
} elseif (isset($_POST['societies'])) { ###  If we are coming from Societies
    $tblTitle = array('sn' => '', 'association' => 'Association', 'president' => 'President', 'secretary' => 'Secretary', 'treasurer' => 'Treasurer', 'started' => 'Founded', 'yearAnniversary' => 'Anniversary Date');

    $getNewsCount = "SELECT COUNT(*) societiesCount FROM societies";
    $rowsperpage = 10;
    $getNewsCount = $fxns->_execQuery($getNewsCount, true, false);
    $preparePaging = $fxns->_preparePaging($getNewsCount['societiesCount'], $rowsperpage, @$_POST['currentpage']);

    $getSocieties = "SELECT sn, association, president, secretary, treasurer, started, yearAnniversary FROM societies
					LIMIT {$preparePaging['offset']}, {$rowsperpage}";
    $getSocieties = $fxns->_execQuery($getSocieties);
    $societies = $fxns->_buildTable($getSocieties, false, $tblTitle);

    $societies .= $fxns->_buildPagingLinks($range = 5, $preparePaging['currentpage'], $preparePaging['totalpages'], array('div' => 'phpPaging', 'link' => 'societies'));
    echo "<h1>Our Societies</h1>" . $societies;
} elseif (isset($_POST['news'])) {  ###  If we are coming from News	
    if (isset($_POST['news_id']) && !isset($_POST['currentpage'])) {
        $getNews = "SELECT news_id, news_title, news_detail, image, (SELECT COUNT(*) FROM news) allNews 
						FROM news WHERE news_id=:news_id";
        $getNews = $fxns->_execQuery($getNews, true, false, $params = array(':news_id' => $_POST['news_id']));
        $news = "<div><a href=\"\" class=\"news\"> Back to News</a></div>
					<div style=\"float:left; text-align:center;\">";
        $news .=!empty($getNews['image']) ? "<img src=\"{$getNews['image']}\" title=\"{$getNews['news_title']}\" />" : '';
        $news .= "</div>".html_entity_decode($getNews['news_detail']);
        $next = ($getNews['news_id'] + 1 > $getNews['allNews']) ? "" : "<a href=\"\" class=\"news\" id=\"" . ($getNews['news_id'] + 1) . "\" style=\"float:right;\">Next News >></a>";
        ;
        $prev = ($getNews['news_id'] - 1 == 0) ? "" : "<a href=\"\" class=\"news\" id=\"" . ($getNews['news_id'] - 1) . "\" style=\"float:left;\"><< Previous News</a>";
        $news .= "<div style=\"clear:left;\">{$prev}{$next}</div><div style=\"clear:left;\">&nbsp;</div>";
        echo "<h1>{$getNews['news_title']}</h1>" . $news;
    } else {
        $getNewsCount = "SELECT COUNT(*) newsCount FROM news";
        $rowsperpage = 5;
        $getNewsCount = $fxns->_execQuery($getNewsCount, true, false);
        $preparePaging = $fxns->_preparePaging($getNewsCount['newsCount'], $rowsperpage, @$_POST['currentpage']);
        $getNews = "SELECT  news_id, news_title, image, news_detail, date_created 
					FROM news ORDER BY date_created DESC
					LIMIT {$preparePaging['offset']}, {$rowsperpage}";
        $getNews = $fxns->_execQuery($getNews);
        $news = "<ul class=\"profileTmb\">";
        foreach ($getNews as $assocKey => $details) {
            $readMoreLink = "<a href=\"\" class=\"news\" id=\"{$details['news_id']}\">";
            $newsLink = $fxns->_readMore(html_entity_decode ($details['news_detail']), 150, $readMoreLink);
            $news .= "<li>{$readMoreLink}";
            $news .= "<img src=\"" . (!empty($details['image']) ? $details['image'] : '/assets/images/news/none.png') . "\" style=\"float:left;\" />";
            $news .= "</a>";
            $news .= "<span style=\"float:right; font-size:0.7em;\">Created: {$details['date_created']}</span>";
            $news .= "{$readMoreLink}{$details['news_title']}</a><br />{$newsLink}</li>";
        }
        $news .= "</ul>";
        $news .= $fxns->_buildPagingLinks($range = 5, $preparePaging['currentpage'], $preparePaging['totalpages'], array('div' => 'phpPaging', 'link' => 'news'));
        echo "<h1>News and Events</h1>" . $news;
    }
} elseif (isset($_POST['reflection'])) {  ###  If we are coming from Reflection
    $reflection = "<div id=\"reflect2day\">";
    $reflection .= "<p><input type=\"text\" name=\"search_term\" class=\"search\" placeholder=\"Enter your search term and press enter to search...\" value=\"" . @$_POST['search_term'] . "\" /><i class=\"icon-search icon-2x searchSubmit\"></i></p>";
    if (isset($_POST['search']) && empty($_POST['q'])) {  //Was there a search
        $search_term = explode('*', $_POST['search_term']);
        $sqlSearchCount = "SELECT COUNT(*) count FROM messages WHERE type=1 AND ";
        $sqlSearchQuery = "SELECT * FROM messages WHERE type=1 AND ";
        $sqlBuildSearchQry = $fxns->_buildSearchQry($sqlSearchCount, $sqlSearchQuery, $search_term, array('date', 'theme', 'passage', 'text', 'content'));

        $rowsperpage = 5;
        $sqlSearchCount = $fxns->_execQuery($sqlBuildSearchQry['sqlSearchCount'], true, false);
        $preparePaging = $fxns->_preparePaging($sqlSearchCount['count'], $rowsperpage, @$_POST['currentpage']);
        $sqlSearchQuery = $sqlBuildSearchQry['sqlSearchQuery'] . " LIMIT {$preparePaging['offset']}, {$rowsperpage}";
        $getSearch = $fxns->_execQuery($sqlSearchQuery);

        if (isset($_POST['reflection_id']) && !isset($_POST['currentpage'])) {
            $getReflection = "SELECT * FROM messages WHERE sn=:reflection_id AND type=1";
            $getReflection = $fxns->_execQuery($getReflection, true, false, $params = array(':reflection_id' => $_POST['reflection_id']));
            $reflection .= "<div id=\"reflect2day\"><p><span style=\"float:right;\" class=\"text\">{$getReflection['date']}</span>";
            $reflection .= "<strong><em>Theme:</em></strong> <span class=\"text\">{$getReflection['theme']}</span><br />";
            $reflection .= "<strong><em>Passage:</strong></em> {$getReflection['passage']} &nbsp;|&nbsp; <strong><em>Text:</strong></em> <span class=\"text\">{$getReflection['text']}</span></p>";
            $reflection .= html_entity_decode ($getReflection['content']);

            $reflection .= "<div class=\"fb-comments\" data-href=\"" . WEB_ROOT . "/resources/daily-reflection?q={$getReflection['sn']}\" data-numposts=\"5\" data-colorscheme=\"light\"></div>";
        } else {
            $reflection .= "<ul class=\"profileTmb\">";
            foreach ($getSearch as $assocKey => $details) {
                $readMoreLink = "<a href=\"\" class=\"reflection\" id=\"{$details['sn']}\" search='1' search_term=\"" . @$_POST['search_term'] . "\">";
                $newsLink = $fxns->_readMore(str_replace(array("<p>", '</p>'), '', $details['content']), 250, $readMoreLink);
                $reflection .= "<li><span style=\"float:right; font-size:small;\">Date: {$details['date']}</span>
				<strong><em>Theme: </em></strong><span>{$details['theme']}</span><br />";
                $reflection .= "{$newsLink}</li>";
            }
            $reflection .= "</ul>";
            $reflection .= $fxns->_buildPagingLinks($range = 5, $preparePaging['currentpage'], $preparePaging['totalpages'], array('div' => 'phpPaging', 'link' => 'reflection'));
        }
    } else {  ##### Default: i.e There is no search
        $sqlGetToday = "SELECT * FROM messages WHERE type=1 ";
        $sqlGetToday .= empty($_POST['q']) ? " AND `date` = curdate()" : " AND `sn` = {$_POST['q']}";
        $getReflection = $fxns->_execQuery($sqlGetToday, true, false);
        if (empty($getReflection)) {
            $reflection .= "No Reflection for today";
        } else {
            $reflection .= "<p><span style=\"float:right;\" class=\"text\">{$getReflection['date']}</span>" . (!isset($_POST['q']) ? '' : "<h3>Reflection for today:</h3>");
            $reflection .= "<strong><em>Theme:</em></strong> <span class=\"text\">{$getReflection['theme']}</span><br />";
            $reflection .= "<strong><em>Passage:</strong></em> {$getReflection['passage']} &nbsp;|&nbsp; <strong><em>Text:</strong></em> <span class=\"text\">{$getReflection['text']}</span></p>";
            $reflection .= html_entity_decode ($getReflection['content']);
            $reflection .= "<div class=\"fb-comments\" data-href=\"" . WEB_ROOT . "/resources/daily-reflection?q={$getReflection['sn']}\" data-numposts=\"5\" data-colorscheme=\"light\"></div>";
        }
        ##### End Default: 
    }
    $reflection .= "<br /><br />How to use the search:<ul>";
    $reflection .= "<li><span><u>Search by Date:</u></span> If you know the date for the reflection,just enter it e.g 2014-01-28.</li>";
    $reflection .= "<li><span><u>Search by Scripture:</u></span> You can search for a reflection by scripture e.g John 3:16.</li>";
    $reflection .= "<li><span><u>Search by Theme:</u></span> If you're looking for a particular topic/theme enter it. You'll be linked to lectionaries that will give you biblical insight about the topic you've chosen. e.g Arise and shine</li>";
    $reflection .= "<li><span><u>Search by Content:</u></span>If you know a part of the content just enter it.</li>";
    $reflection .= "<li><span><u>Search by Annotation:</u></span> Annotations help to refine your search. You simplify the search and get closer to your search by using annotations. E.g date*Jan 1, 2013 will return a very accurate match; scripture*John 1:1 will return all scriptures matching such; e.t.c</li>";
    $reflection .= "</ul><div id=\"overlay\"><div><img src=\"/assets/images/loading.gif\" /><br />Loading...</div></div></div>";
    echo $reflection;
} elseif (isset($_POST['bibleStudy'])) {  ###  If we are coming from Reflection
    $bibleStudy = "<div id=\"reflect2day\">";
    $bibleStudy .= "<p><input type=\"text\" name=\"search_term\" class=\"search\" placeholder=\"Enter your search term and press enter to search...\" value=\"" . @$_POST['search_term'] . "\" /><i class=\"icon-search icon-2x searchSubmit\"></i></p>";
    if (isset($_POST['search']) && empty($_POST['q'])) {  //Was there a search
        $search_term = explode('*', $_POST['search_term']);
        $sqlSearchCount = "SELECT COUNT(*) count FROM messages WHERE type=2 AND (";
        $sqlSearchQuery = "SELECT * FROM messages WHERE type=2 AND (";
        $sqlBuildSearchQry = $fxns->_buildSearchQry($sqlSearchCount, $sqlSearchQuery, $search_term, array('date', 'theme', 'passage', 'text', 'content'));

        $rowsperpage = 5;
        $sqlSearchCount = $fxns->_execQuery(")" . $sqlBuildSearchQry['sqlSearchCount'], true, false);
        $preparePaging = $fxns->_preparePaging($sqlSearchCount['count'], $rowsperpage, @$_POST['currentpage']);
        $sqlSearchQuery = $sqlBuildSearchQry['sqlSearchQuery'] . ") LIMIT {$preparePaging['offset']}, {$rowsperpage}";
        $getSearch = $fxns->_execQuery($sqlSearchQuery);

        if (isset($_POST['bibleStudy_id']) && !isset($_POST['currentpage'])) {
            $getBibleStudy = "SELECT *, WEEK(`date`) week FROM messages WHERE sn=:bibleStudy_id AND type=2";
            $getBibleStudy = $fxns->_execQuery($getBibleStudy, true, false, $params = array(':bibleStudy_id' => $_POST['bibleStudy_id']));
            $bibleStudy .= "<div id=\"reflect2day\"><p><span style=\"float:right;\" class=\"text\">Published on week: {$getBibleStudy['week']}</span>";
            $bibleStudy .= "<strong><em>Topic:</em></strong> <span class=\"text\">{$getBibleStudy['theme']}</span><br />";
            $bibleStudy .= "<strong><em>Text:</strong></em> <span class=\"text\">{$getBibleStudy['text']}</span></p>";
            $bibleStudy .= html_entity_decode ($getBibleStudy['content']);

            $bibleStudy .= "<div class=\"fb-comments\" data-href=\"" . WEB_ROOT . "/resources/bible-study?q={$getBibleStudy['sn']}\" data-numposts=\"5\" data-colorscheme=\"light\"></div>";
        } else {
            $bibleStudy .= "<ul class=\"profileTmb\">";
            foreach ($getSearch as $assocKey => $details) {
                $readMoreLink = "<a href=\"\" class=\"bibleStudy\" id=\"{$details['sn']}\" search='1' search_term=\"" . @$_POST['search_term'] . "\">";
                $newsLink = $fxns->_readMore(str_replace(array("<p>", '</p>', '<h3>', '</h3>'), '', $details['content']), 250, $readMoreLink);
                $bibleStudy .= "<li><span style=\"float:right; font-size:small;\">Date: {$details['date']}</span>
								<strong><em>Theme: </em></strong><span>{$details['theme']}</span><br />";
                $bibleStudy .= "{$newsLink}</li>";
            }
            $bibleStudy .= "</ul>";
            $bibleStudy .= $fxns->_buildPagingLinks($range = 5, $preparePaging['currentpage'], $preparePaging['totalpages'], array('div' => 'phpPaging', 'link' => 'bibleStudy'));
        }
    } else {  ##### Default: i.e There is no search
        $sqlGetToday = "SELECT *, WEEK(`date`) week FROM messages WHERE type=2 ";
        $sqlGetToday .= empty($_POST['q']) ? " AND `date` BETWEEN DATE_ADD(CURDATE(), INTERVAL 1-DAYOFWEEK(CURDATE()) DAY)
    AND DATE_ADD(CURDATE(), INTERVAL 7-DAYOFWEEK(CURDATE()) DAY)" : " AND `sn` = {$_POST['q']}";
        $getBibleStudy = $fxns->_execQuery($sqlGetToday, true, false);
        if (empty($getBibleStudy)) {
            $bibleStudy .= "No bible study this week";
        } else {
            $bibleStudy .= "<p><span style=\"float:right;\" class=\"text\">Week: {$getBibleStudy['week']}</span>" . (!isset($_POST['q']) ? '' : "<h3>Bible study this week:</h3>");
            $bibleStudy .= "<strong><em>Topic:</em></strong> <span class=\"text\">{$getBibleStudy['theme']}</span><br />";
            $bibleStudy .= "<strong><em>Text:</strong></em> <span class=\"text\">{$getBibleStudy['text']}</span></p>";
            $bibleStudy .= html_entity_decode ($getBibleStudy['content']);
            $bibleStudy .= "<div class=\"fb-comments\" data-href=\"" . WEB_ROOT . "/resources/bible-study?q={$getBibleStudy['sn']}\" data-numposts=\"5\" data-colorscheme=\"light\"></div>";
        }
        ##### End Default: 
    }
    $bibleStudy .= "<br /><br />How to use the search:<ul>";
    $bibleStudy .= "<li><span><u>Search by Date:</u></span> If you know the date for the Bible Study,just enter it e.g 2014-01-28.</li>";
    $bibleStudy .= "<li><span><u>Search by Scripture:</u></span> You can search for a study by scripture e.g John 3:16.</li>";
    $bibleStudy .= "<li><span><u>Search by Theme:</u></span> If you're looking for a particular topic/theme enter it. You'll be linked to studies that will give you biblical insight about the topic you've chosen. e.g Arise and shine</li>";
    $bibleStudy .= "<li><span><u>Search by Content:</u></span>If you know a part of the content just enter it.</li>";
    $bibleStudy .= "<li><span><u>Search by Annotation:</u></span> Annotations help to refine your search. You simplify the search and get closer to your search by using annotations. E.g date*Jan 1, 2013 will return a very accurate match; scripture*John 1:1 will return all scriptures matching such; e.t.c</li>";
    $bibleStudy .= "</ul><div id=\"overlay\"><div><img src=\"/assets/images/loading.gif\" /><br />Loading...</div></div></div>";
    echo $bibleStudy;
} elseif (isset($_POST['sermon'])) {  ###  If we are coming from Reflection
    $sermon = "<div id=\"reflect2day\">";
    $sermon .= "<p><input type=\"text\" name=\"search_term\" class=\"search\" placeholder=\"Enter your search term and press enter to search...\" value=\"" . @$_POST['search_term'] . "\" /><i class=\"icon-search icon-2x searchSubmit\"></i></p>";
    if (isset($_POST['search']) && empty($_POST['q'])) {  //Was there a search
        $search_term = explode('*', $_POST['search_term']);
        $sqlSearchCount = "SELECT COUNT(*) count FROM messages WHERE type=3 AND (";
        $sqlSearchQuery = "SELECT * FROM messages WHERE type=3 AND (";
        $sqlBuildSearchQry = $fxns->_buildSearchQry($sqlSearchCount, $sqlSearchQuery, $search_term, array('date', 'theme', 'passage', 'text', 'content'));

        $rowsperpage = 5;
        $sqlSearchCount = $fxns->_execQuery(")" . $sqlBuildSearchQry['sqlSearchCount'], true, false);
        $preparePaging = $fxns->_preparePaging($sqlSearchCount['count'], $rowsperpage, @$_POST['currentpage']);
        $sqlSearchQuery = $sqlBuildSearchQry['sqlSearchQuery'] . ") LIMIT {$preparePaging['offset']}, {$rowsperpage}";
        $getSearch = $fxns->_execQuery($sqlSearchQuery);

        if (isset($_POST['sermon_id']) && !isset($_POST['currentpage'])) {
            $getSermon = "SELECT * FROM messages WHERE sn=:sermon_id AND type=3";
            $getSermon = $fxns->_execQuery($getSermon, true, false, $params = array(':sermon_id' => $_POST['sermon_id']));
            $sermon .= "<div id=\"reflect2day\"><p><span style=\"float:right;\" class=\"text\">Published for month: {$getSermon['date']}</span>";
            $sermon .= "<strong><em>Text:</strong></em> <span class=\"text\">{$getSermon['text']}</span></p>";
            $sermon .= html_entity_decode ($getSermon['content']);

            $sermon .= "<div class=\"fb-comments\" data-href=\"" . WEB_ROOT . "/resources/john-wesley-sermon?q={$getSermon['sn']}\" data-numposts=\"5\" data-colorscheme=\"light\"></div>";
        } else {
            $sermon .= "<ul class=\"profileTmb\">";
            foreach ($getSearch as $assocKey => $details) {
                $readMoreLink = "<a href=\"\" class=\"bibleStudy\" id=\"{$details['sn']}\" search='1' search_term=\"" . @$_POST['search_term'] . "\">";
                $newsLink = $fxns->_readMore(str_replace(array("<p>", '</p>', '<h3>', '</h3>'), '', $details['content']), 250, $readMoreLink);
                $sermon .= "<li><span style=\"float:right; font-size:small;\">Date: {$details['date']}</span>
								<!--strong><em>Theme: </em></strong><span>{$details['theme']}</span--><br />";
                $sermon .= "{$newsLink}</li>";
            }
            $sermon .= "</ul>";
            $sermon .= $fxns->_buildPagingLinks($range = 5, $preparePaging['currentpage'], $preparePaging['totalpages'], array('div' => 'phpPaging', 'link' => 'bibleStudy'));
        }
    } else {  ##### Default: i.e There is no search
        $sqlGetToday = "SELECT * FROM messages WHERE type=3 ";
        $sqlGetToday .= empty($_POST['q']) ? " AND MONTH(`date`) = MONTH(NOW()) AND YEAR(`date`)=YEAR(NOW()) " : " AND `sn`={$_POST['q']}";
        $getSermon = $fxns->_execQuery($sqlGetToday, true, false);
        if (empty($getSermon)) {
            $sermon .= "No bible study this week";
        } else {
            $sermon .= "<p><span style=\"float:right;\" class=\"text\">{$getSermon['date']}</span>" . (!isset($_POST['q']) ? '' : "<h3>Sermon this month:</h3>");
            $sermon .= "<strong><em>Text:</strong></em> <span class=\"text\">{$getSermon['text']}</span></p>";
            $sermon .= html_entity_decode ($getSermon['content']);
            $sermon .= "<div class=\"fb-comments\" data-href=\"" . WEB_ROOT . "/resources/john-wesley-sermon?q={$getSermon['sn']}\" data-numposts=\"5\" data-colorscheme=\"light\"></div>";
        }
        ##### End Default: 
    }
    $sermon .= "<br /><br />How to use the search:<ul>";
    $sermon .= "<li><span><u>Search by Date:</u></span> If you know the date for the Sermon,just enter it e.g 2014-01-28.</li>";
    $sermon .= "<li><span><u>Search by Scripture:</u></span> You can search for a sermon by scripture e.g John 3:16.</li>";
    $sermon .= "<li><span><u>Search by Theme:</u></span> If you're looking for a particular topic/theme enter it. You'll be linked to sermon that will give you biblical insight about the topic you've chosen. e.g Arise and shine</li>";
    $sermon .= "<li><span><u>Search by Content:</u></span>If you know a part of the content just enter it.</li>";
    $sermon .= "<li><span><u>Search by Annotation:</u></span> Annotations help to refine your search. You simplify the search and get closer to your search by using annotations. E.g date*Jan 1, 2013 will return a very accurate match; scripture*John 1:1 will return all scriptures matching such; e.t.c</li>";
    $sermon .= "</ul><div id=\"overlay\"><div><img src=\"/assets/images/loading.gif\" /><br />Loading...</div></div></div>";
    echo $sermon;
}