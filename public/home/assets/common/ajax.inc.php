<?php

/*
 * Include necessary files
 */
include_once 'core/init.inc.php';
require_once('FirePHPCore/FirePHP.class.php');
//$firephp = FirePHP::getInstance(true);
//$firephp->setEnabled(true);

$fxns = new Functions($dbo);
$output = NULL;

//var_dump($_POST);exit;
if(isset($_POST['getPage'])){
    $output .= "<form action=\"\" method=\"post\">";
    $link = $_POST['link'];
    $news = NULL;
    $timestamp = time();
    $token = md5('unique_salt' . $timestamp);
    $tableNm = array('/news-and-events'=>'news', '/resources/daily-reflection'=>"messages WHERE type='1'"
                    , '/about-us/profile'=>'profiles', '/about-us/church-societies'=>'societies'
                    , '/resources/bible-study'=>"messages WHERE type='2'"
                    , '/resources/john-wesley-sermon'=>"messages WHERE type='3'"
                    , '/about-us/photo-gallery'=>"gallery WHERE type='1'"
                    , '/resources/videos'=>"gallery WHERE type='2'"
                    , '/resources/testimonies'=>"gallery WHERE type='3'"
                    , '/misc/prayerfocusmemoryverse'=>"prayerfocusmemoryverse"
                    );
    $uploads = array('/about-us/our-lectionary'=>'lectionary', '/misc/hymn'=>'hymn', '/misc/images'=>'images');
    if(array_key_exists($link, $tableNm)){
        echo buildTable($tableNm, $link, @$_POST['currentpage']);
    }elseif(in_array($link, array('/misc/registered'))){
        $sqlSearchQuery = "SELECT r_k,usr_nm,usr_pass,usr_ttl,fst_nm,mdl_nm,lst_nm,phn_no,eml_adr"
                . " pry_adr_ln1,pry_adr_ln2,pry_adr_cty,pry_adr_sta,pry_adr_ctr,usr_tp,active,crt_tm,updt_tm"
                . " FROM t_wb_pro_usr WHERE r_k<>1";
        $tblTtl = array('r_k'=>'Row Key','usr_nm'=>'User Name','usr_pass'=>'Password','usr_ttl'=>'Title'
                        ,'fst_nm'=>'First Name','mdl_nm'=>'Middle Name','lst_nm'=>'Last Name','phn_no'=>'Phone No.'
                        ,'eml_adr'=>'Email Address','pry_adr_ln1'=>'Address Line 1','pry_adr_ln2'=>'Address Line 2'
                        ,'pry_adr_cty'=>'City','pry_adr_sta'=>'State','pry_adr_ctr'=>'Country','usr_tp'=>'User type'
                        ,'active'=>'Active','crt_tm'=>'Create Time','updt_tm'=>'Last Updated');
        $sqlGetTitles = $sqlSearchQuery." LIMIT 0";        
        $sqlSearchCount = "SELECT COUNT(*) count FROM t_wb_pro_usr WHERE r_k<>1";;
        $rowsperpage = 10;
        $sqlSearchCount = $fxns->_execQuery($sqlSearchCount, true, false);
        $preparePaging = $fxns->_preparePaging($sqlSearchCount['count'], $rowsperpage, @$_POST['currentpage']);
        $sqlSearchQuery = $sqlSearchQuery . " LIMIT {$preparePaging['offset']}, {$rowsperpage}";
        $getNews = $fxns->_execQuery($sqlSearchQuery);
        $sqlGetTitles = $fxns->_getResultHeaders($sqlGetTitles);
        $remove = array('r_k','usr_pass');
        $tblNm = "t_wb_pro_usr";
        $cr8Input = "<input forW=\"create\" type=\"hidden\" name=\"dets\" value=\"\$det=array('t'=>'{$tblNm}', 't_cond'=>array('type'=>'create'));\">";
        $news .= "<div style=\"margin:5px;text-align:right;\">{$cr8Input}<a href=\"{$tblNm}\" class=\"goodBtn create\">Create New</a></div>";
        $news .= "<table border=\"1\" rules=\"all\">";
        array_unshift($getNews, $tblTtl);
        if(count($getNews)>0){
            foreach($getNews as $key => $details){
                $news .= "<tr>";
                foreach($sqlGetTitles as $t_key=> $t_val){
                    if(in_array($t_val,$remove)){
                        $news .= "";
                    }elseif($key===0){
                        $news .= "<th>{$details[$t_val]}</th>";
                    }else{
                        $news .= "<td>{$details[$t_val]}</td>";
                    }
                }
                $EditInput = "<input forW=\"edit\" type=\"hidden\" name=\"dets\" value=\"\$det=array('t'=>'{$tblNm}', 't_u'=>array('{$sqlGetTitles[0]}'=>'{$details[$sqlGetTitles[0]]}'), 't_cond'=>array('type'=>'create'));\">";
                $delInput = "<input forW=\"delete\" type=\"hidden\" name=\"dets\" value=\"\$det=array('t'=>'{$tblNm}', 't_u'=>array('{$sqlGetTitles[0]}'=>'{$details[$sqlGetTitles[0]]}'), 't_cond'=>array('type'=>'delete'));\">";
                $news .= ($key===0)?"":"<td>{$EditInput}<a href=\"\" class=\"create icon-edit success\">edit</a></td><td>{$delInput}<a href=\"\" class=\"delete icon-remove err\">delete</a></td></tr>";
            }
            $webLink = "";
            $param = "getPage=1&link={$link}";
            $news .= "<tr><td colspan=".(count($sqlGetTitles)+2)." style=\"text-align:center;\">".$fxns->_buildPagingLinks($range = 5, $preparePaging['currentpage'], $preparePaging['totalpages'], array('div' => 'phpPaging', 'link' => 'success','param'=>$param),$webLink)."</td></tr>";
       }
        $news .= "<table>";
        echo $news;

    }elseif(array_key_exists($link, $uploads)){
        $output .= "<table>";
        $output .= "<tr><td><h2>Uploads</h2></td><td>&nbsp;</td></tr>";  
        $output .= "<tr><td colspan=\"2\">Note: Uploads are sent immediately you select the file"
                . "<br />They can however be cancelld using the the x-button."
                . "<br /><span style=\"color:#900;\">MAXIMUM FILE SIZE IS 10MB. Files less than 2MB are best for the server</span></td></tr>";        
        $output .= "<tr><td colspan=\"2\">&nbsp;</td></tr>";  
        $output .= "<tr><td>Upload type</td><td>"
                . "<select name=\"upload_tp\">";
        foreach($uploads as $key => $name){
            if(is_array($name)){
                $output .= "";
                foreach($name as $options){
                    $output .= "<option> {$name}</option>";                    
                }
            }else{
                $output .= "<option".(($key===$link)? " selected=\"selected\"": "" ). "> {$name}</option>";
            }
        }
        $output .= "</select></td></tr>";        
        $output .= "<tr><td colspan=\"2\"><div id=\"queue\"></div><input id=\"file_upload\" name=\"file_upload\" type=\"file\" /></td></tr>";        
        $output .= "</table>";  
    }else{
        //var_dump($_POST);exit;
        $getContent = "SELECT * FROM pages WHERE page_url='{$link}'";
        $content = $fxns->_execQuery($getContent, TRUE, FALSE);
        $output .= "<input type=\"hidden\" name=\"dets\" value=\"\$det=array('t'=>'pages'"
                                                                    . ", 't_u'=>array('page_id'=>'{$content['page_id']}')"
                                                                    . ", 't_cond'=>array('type'=>'update'"
                                                                                    . ", 'col'=>array("
                                                                                                //. "'page_label'=>'page_label',"
                                                                                                . "'page_url'=>'page_url',"
                                                                                                . "'site_position'=>'site_position',"
                                                                                                . "'content'=>'editor1')));\">";
        $output .= "<table>";
        $output .= "<tr><td width=\"30%\"><h2>{$content['page_label']}</h2></td><td><a href=\"\" class=\"goodBtn update\" style=\"float:right;\">Update</a></td></tr>";        
        $output .= "<tr><td>Permanent Link</td><td>".WEB_ROOT."".$content['page_url']."</td><tr>";
        $output .= "<tr><td colspan=\"2\">&nbsp;</td><tr>";
        $output .= "<tr><td>Page Label</td><td><input type=\"text\" name=\"page_label\" value=\"".$content['page_label']."\" /></td><tr>";
        $output .= "<tr><td>Page URL</td><td><input type=\"text\" name=\"page_url\" value=\"".$content['page_url']."\" /></td><tr>";
        $sqlDMNYN = "SELECT `val_id`, `val_dsc` FROM `t_wb_lov` WHERE `def_id`='00-YSN'";
        $output .= "<tr><td>Show in Menu</td><td>".$fxns->_getLOVs($sqlDMNYN, 'val_id', 'val_dsc', 'active', '', NULL, $content['site_position']==1)."</td><tr>";
        $output .= "<tr><td colspan=\"2\"><textarea class=\"ckeditor\" id=\"editor1\" name=\"editor1\">{$content['content']}</textarea></td></tr>";        
        $output .= "</table>";  
    }
    $output .= "</form>";
    echo $output;
}elseif(isset($_POST['dets'])){
    eval($_POST['dets']);
    $sqlUpdate = NULL; $sqlCondition = NULL;$createLnk =NULL;
    
    //Some tables to be updated first
    $updTblFst = array(
                        'prayerfocusmemoryverse' => '', 'gallery'=>''
                    );
    if(array_key_exists($det['t'], $updTblFst) && @$_POST['active']=='1'){
        //var_dump($_POST);exit;
        $sqlUpdFst = "UPDATE {$det['t']} SET active =0 WHERE type={$_POST['type']}";
        $result = $fxns->_execQuery($sqlUpdFst, FALSE, FALSE);
        if($result['result']=='Failure'){
            echo "<blockquote>".$result['msg']."</blockquote>";
            exit;
        }
    }
    //End: Some tables to be updated first.
    
    if($det['t_cond']['type']==='update'){
        $sqlUpdate .= "UPDATE {$det['t']} SET ";
        foreach($det['t_cond']['col'] as $key => $cols){
            $sqlUpdate .= " {$key} = '".  htmlspecialchars($_POST[$cols], ENT_QUOTES, 'UTF-8') ."' , ";
        }
        $sqlUpdate = substr($sqlUpdate, 0, strrpos( $sqlUpdate, ",") );
        if(!empty($det['t_u'])){
            $sqlCondition .= " WHERE ";            
            foreach($det['t_u'] as $key => $cols){
                $sqlCondition .= " {$key} = '{$cols}' AND ";
            }
            $sqlCondition = substr($sqlCondition, 0, strrpos( $sqlCondition, "AND") );
        }
        $createLnk = buildTable($det['t'], '', NULL);
    }elseif($det['t_cond']['type']==='insert'){
        $createLnk .= "<input forW=\"create\" type=\"hidden\" name=\"dets\" value=\"\$det=array('t'=>'{$det['t']}', 't_cond'=>array('type'=>'create'));\">";
        $sqlCols = NULL;$sqlVals = NULL;
        $sqlUpdate .= "INSERT INTO {$det['t']}";
        foreach($det['t_cond']['col'] as $key => $cols){
            $sqlCols .= " {$key}, ";
            $sqlVals .= "'{$_POST[$cols]}' , ";
        }
        $sqlCols = substr($sqlCols, 0, strrpos( $sqlCols, ",") );
        $sqlVals = substr($sqlVals, 0, strrpos( $sqlVals, ",") );
        $sqlUpdate .= "({$sqlCols}) VALUES ({$sqlVals})";
    }elseif($det['t_cond']['type']==='delete'){
        $sqlUpdate .= "DELETE FROM {$det['t']} ";
        if(!empty($det['t_u'])){
            $sqlCondition .= " WHERE ";            
            foreach($det['t_u'] as $key => $cols){
                $sqlCondition .= " {$key} = '{$cols}' AND ";
            }
            $sqlCondition = substr($sqlCondition, 0, strrpos( $sqlCondition, "AND") );
        }

    }elseif($det['t_cond']['type']==='create'){  //For insert/Edit Screens
        echo buildScreen($det['t'],@$det['t_u']);
        exit;
    }
    $query = $sqlUpdate.$sqlCondition;
    $result = $fxns->_execQuery($query, FALSE, FALSE);
    if($result==1){
        echo "Transaction Successful<br />";//.@$createLnk;
    }elseif($result['result']=='Failure'){
        echo "<blockquote>".$result['msg']."</blockquote>";
    }
}elseif(isset($_POST['upload_tp']) && !empty($_FILES)){
    //var_dump($_FILES); exit;
    $targetFolder = array('lectionary'=>array('folder'=>'/assets/downloads/lectionary'
                                                ,'type'=>array('pdf'))
                        ,'hymn'=>array('folder'=>'/assets/downloads/hymn/'.date('Y').'/'.date('m')
                                        ,'type'=>array('mp3'))
                        ,'images'=>array('folder'=>'/assets/images'
                                        ,'type'=>array('jpg','jpeg','gif','png'))
                    );
    $tempFile = $_FILES['Filedata']['tmp_name'];
    $targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder[$_POST['upload_tp']]['folder'];
    $targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];
    //If targetPath does not exist, create it.
    if (!file_exists($targetPath)) {
        mkdir($targetPath, 0777, true);
    }
    // Validate the file type
    $fileTypes = $targetFolder[$_POST['upload_tp']]['type']; // File extensions
    $fileParts = pathinfo($_FILES['Filedata']['name']);
    
    if (in_array($fileParts['extension'],$fileTypes)) {
        move_uploaded_file($tempFile,$targetFile);
        echo '<blockquote style="color:#063">File uploaded successfully<br />File URL is '.WEB_ROOT.$targetFolder[$_POST['upload_tp']]['folder']."/".rawurlencode($_FILES['Filedata']['name'])."</blockquote>";
    } else {
        echo '<blockquote style="color:#900">Error: Invalid file type.  File is discarded</blockquote>';
    }
}
function buildTable($tableNm, $link='', $currentpage){
    
    global $dbo,$fxns ;
    $news=NULL;
        $sqlSearchQuery = "SELECT * FROM ".(($link=='')?$tableNm:$tableNm[$link]);
        $sqlGetTitles = $sqlSearchQuery." LIMIT 0";        
        $sqlSearchCount = "SELECT COUNT(*) count FROM ".(($link=='')?$tableNm:$tableNm[$link]);
        $rowsperpage = 10;
        $sqlSearchCount = $fxns->_execQuery($sqlSearchCount, true, false);
        $preparePaging = $fxns->_preparePaging($sqlSearchCount['count'], $rowsperpage, @$currentpage);
        $sqlSearchQuery = $sqlSearchQuery . " LIMIT {$preparePaging['offset']}, {$rowsperpage}";
        $getNews = $fxns->_execQuery($sqlSearchQuery);
        $sqlGetTitles = $fxns->_getResultHeaders($sqlGetTitles);
        
        $tblNm = strtok($tableNm[$link],  ' ');
        $cr8Input = "<input forW=\"create\" type=\"hidden\" name=\"dets\" value=\"\$det=array('t'=>'{$tblNm}', 't_cond'=>array('type'=>'create'));\">";
        $news .= "<div style=\"margin:5px;text-align:right;\">{$cr8Input}<a href=\"{$link}\" class=\"goodBtn create\">Create New</a></div>";
        $news .= "<table border=\"1\" rules=\"all\" style=\"min-width:60%;\">";
        $news .= "<tr>";
        foreach($sqlGetTitles as $key => $values){
            $news .= "<th>".ucwords(str_replace("_", " ", $values))."</th>";
        }
        $news .= "</tr>";
        if(count($getNews)>0){
            foreach($getNews as $key => $details){
                $reduceDets = array('news_detail','content','profile');
                $news .= "<tr>";
                foreach($sqlGetTitles as $t_key=> $t_val){
                    if(in_array($t_val,$reduceDets)){
                        $detail = $fxns->_readMore(strip_tags($details[$t_val]), 60, NULL);
                        $news .= "<td>{$detail}</td>";
                    }else{
                        $news .= "<td>{$details[$t_val]}</td>";
                    }
                    $EditInput = "<input forW=\"edit\" type=\"hidden\" name=\"dets\" value=\"\$det=array('t'=>'{$tblNm}', 't_u'=>array('{$sqlGetTitles[0]}'=>'{$details[$sqlGetTitles[0]]}'), 't_cond'=>array('type'=>'create'));\">";
                    $delInput = "<input forW=\"delete\" type=\"hidden\" name=\"dets\" value=\"\$det=array('t'=>'{$tblNm}', 't_u'=>array('{$sqlGetTitles[0]}'=>'{$details[$sqlGetTitles[0]]}'), 't_cond'=>array('type'=>'delete'));\">";
                }
                $news .= "<td>{$EditInput}<a href=\"\" class=\"create icon-edit success\">edit</a></td><td>{$delInput}<a href=\"\" class=\"delete icon-remove err\">delete</a></td></tr>";
            }
            $webLink = "";
            $param = "getPage=1&link={$link}";
            $news .= "<tr><td colspan=".(count($sqlGetTitles)+2)." style=\"text-align:center;\">".$fxns->_buildPagingLinks($range = 5, $preparePaging['currentpage'], $preparePaging['totalpages'], array('div' => 'phpPaging', 'link' => 'success','param'=>$param),$webLink)."</td></tr>";
       }else{
           $news .= "<tr><td colspan=\"".count($sqlGetTitles)."\" style=\"text-align:center;\">No records found!</td></tr>";
       }
        $news .= "<table>";
        return $news;

}
function buildScreen($table,$editField = array()){
    global $dbo,$fxns ;
    if(!empty($editField)){
        $getEdit = "SELECT * FROM {$table} ";
        $getCond = " WHERE ";$getCond2="";
        foreach($editField as $key => $cols){
            $getCond .= " {$key} = '{$cols}' AND ";
            $getCond2 .= " '{$key}' => '{$cols}', ";
        }
        $getCond = substr($getCond, 0, strrpos( $getCond, "AND") );
        $getCond2 = substr($getCond2, 0, strrpos( $getCond2, ",") );
        $getEdit .= $getCond;
        $getEdit = $fxns->_execQuery($getEdit, TRUE, FALSE);
    }
    $output = "<form action=\"\" method=\"post\">";
    $output .= "<table>";        
    if($table==='profiles'){
        $output .= "<input type=\"hidden\" name=\"dets\" "
                . "value=\"\$det=array('t'=>'{$table}'"
                                    . ", 't_u'=>array(".@$getCond2.")"
                                    . ", 't_cond'=>array('type'=>'".(!empty($editField)?'update':'insert')."'"
                                    . ", 'col'=>array('name'=>'name', 'title'=>'title', 'date'=>'date','profile'=>'profile','image'=>'image')));\">";
        $output .= "<tr><td>&nbsp</h2></td><td>"
                . (!empty($editField)? "<a href=\"\" class=\"goodBtn update\" style=\"float:right;\">Update</a>":"<a href=\"\" class=\"goodBtn update\" style=\"float:right;\">Create Entry</a>")
                . "</td></tr>";        
        $output .= "<tr><td>Name</td><td><input type=\"text\" name=\"name\" value=\"".@$getEdit['name']."\" /></td><tr>";
        $output .= "<tr><td>Title</td><td><input type=\"text\" name=\"title\" value=\"".@$getEdit['title']."\" /></td><tr>";
        $output .= "<tr><td>Date</td><td><input type=\"text\" name=\"date\" value=\"".@$getEdit['date']."\" /></td><tr>";
        $output .= "<tr><td>Profile</td><td><textarea class=\"ckeditor\" id=\"editor1\" name=\"profile\">".@$getEdit['profile']."</textarea></td><tr>";
        $output .= "<tr><td>Image Url</td><td><input type=\"text\" name=\"image\" value=\"".@$getEdit['image']."\" /></td><tr>";
    }elseif($table==='prayerfocusmemoryverse'){
        $output .= "<input type=\"hidden\" name=\"dets\" "
                . "value=\"\$det=array('t'=>'{$table}'"
                                    . ", 't_u'=>array(".@$getCond2.")"
                                    . ", 't_cond'=>array('type'=>'".(!empty($editField)?'update':'insert')."'"
                                    . ", 'col'=>array('title'=>'title', 'detail'=>'detail', 'type'=>'type','active'=>'active','date_created'=>'date_created')));\">";
        $output .= "<tr><td>&nbsp</h2></td><td>"
                . (!empty($editField)? "<a href=\"\" class=\"goodBtn update\" style=\"float:right;\">Update</a>":"<a href=\"\" class=\"goodBtn update\" style=\"float:right;\">Create Entry</a>")
                . "</td></tr>";        
        $output .= "<tr><td>Title</td><td><input type=\"text\" name=\"title\" value=\"".@$getEdit['title']."\" /></td><tr>";
        $output .= "<tr><td>Detail</td><td><input type=\"text\" maxlength=\"200\" placeholder=\"Maximum length is 200 characters\" name=\"detail\" value=\"".@$getEdit['detail']."\" /></td><tr>";
        $sqlPMTTp = "SELECT `val_id`, `val_dsc` FROM `t_wb_lov` WHERE `def_id`='00-PMT'";
        $output .= "<tr><td>Type</td><td>".$fxns->_getLOVs($sqlPMTTp, 'val_id', 'val_dsc', 'type', '', NULL, @$getEdit['type'])."</td><tr>";
        $sqlDMNYN = "SELECT `val_id`, `val_dsc` FROM `t_wb_lov` WHERE `def_id`='00-YSN'";
        $output .= "<tr><td>Active</td><td>".$fxns->_getLOVs($sqlDMNYN, 'val_id', 'val_dsc', 'active', '', NULL, @$getEdit['active'])."</td><tr>";
        $output .= "<tr><td>Date Created</td><td><input type=\"text\" name=\"date_created\" value=\"".@$getEdit['date_created']."\" /></td><tr>";
    }elseif($table==='societies'){
        $output .= "<input type=\"hidden\" name=\"dets\" "
                . "value=\"\$det=array('t'=>'{$table}'"
                                    . ", 't_u'=>array(".@$getCond2.")"
                                    . ", 't_cond'=>array('type'=>'".(!empty($editField)?'update':'insert')."'"
                                    . ", 'col'=>array('association'=>'association', 'president'=>'president', 'secretary'=>'secretary','treasurer'=>'treasurer','started'=>'started','yearAnniversary'=>'yearAnniversary')));\">";
        $output .= "<tr><td>&nbsp</h2></td><td>"
                . (!empty($editField)? "<a href=\"\" class=\"goodBtn update\" style=\"float:right;\">Update</a>":"<a href=\"\" class=\"goodBtn update\" style=\"float:right;\">Create Entry</a>")
                . "</td></tr>";        
        $output .= "<tr><td>Association</td><td><input type=\"text\" name=\"association\" value=\"".@$getEdit['association']."\" /></td><tr>";
        $output .= "<tr><td>President</td><td><input type=\"text\" name=\"president\" value=\"".@$getEdit['president']."\" /></td><tr>";
        $output .= "<tr><td>Secretary</td><td><input type=\"text\" name=\"secretary\" value=\"".@$getEdit['secretary']."\" /></td><tr>";
        $output .= "<tr><td>Treasurer</td><td><input type=\"text\" name=\"treasurer\" value=\"".@$getEdit['treasurer']."\" /></td><tr>";
        $output .= "<tr><td>Started</td><td><input type=\"text\" name=\"started\" value=\"".@$getEdit['started']."\" /></td><tr>";
        $output .= "<tr><td>Anniversary</td><td><input type=\"text\" name=\"yearAnniversary\" value=\"".@$getEdit['yearAnniversary']."\" /></td><tr>";
    }elseif($table==='news'){
        $output .= "<input type=\"hidden\" name=\"dets\" "
                . "value=\"\$det=array('t'=>'{$table}'"
                                    . ", 't_u'=>array(".@$getCond2.")"
                                    . ", 't_cond'=>array('type'=>'".(!empty($editField)?'update':'insert')."'"
                                    . ", 'col'=>array('news_title'=>'news_title', 'image'=>'image', 'news_detail'=>'news_detail')));\">";
        $output .= "<tr><td>&nbsp</h2></td><td>"
                . (!empty($editField)? "<a href=\"\" class=\"goodBtn update\" style=\"float:right;\">Update</a>":"<a href=\"\" class=\"goodBtn update\" style=\"float:right;\">Create Entry</a>")
                . "</td></tr>";        
        $output .= "<tr><td>Title</td><td><input type=\"text\" name=\"news_title\" value=\"".@$getEdit['news_title']."\" /></td><tr>";
        $output .= "<tr><td>Image Url</td><td><input type=\"text\" name=\"image\" value=\"".@$getEdit['image']."\" /></td><tr>";
        $output .= "<tr><td>Detail</td><td><textarea class=\"ckeditor\" id=\"editor1\" name=\"news_detail\">".@$getEdit['news_detail']."</textarea></td><tr>";
    }elseif($table==='messages'){
        $output .= "<input type=\"hidden\" name=\"dets\" "
                . "value=\"\$det=array('t'=>'{$table}'"
                                    . ", 't_u'=>array(".@$getCond2.")"
                                    . ", 't_cond'=>array('type'=>'".(!empty($editField)?'update':'insert')."'"
                                    . ", 'col'=>array('type'=>'type', 'date'=>'date', 'theme'=>'theme', 'passage'=>'passage', 'text'=>'text', 'content'=>'content')));\">";
        $output .= "<tr><td>&nbsp</h2></td><td>"
                . (!empty($editField)? "<a href=\"\" class=\"goodBtn update\" style=\"float:right;\">Update</a>":"<a href=\"\" class=\"goodBtn update\" style=\"float:right;\">Create Entry</a>")
                . "</td></tr>";
        $sqlMsgTp = "SELECT `val_id`, `val_dsc` FROM `t_wb_lov` WHERE `def_id`='MSG-TP'";
        $output .= "<tr><td>Type</td><td>".$fxns->_getLOVs($sqlMsgTp, 'val_id', 'val_dsc', 'type', '', NULL, @$getEdit['type'])."</td><tr>";
        $output .= "<tr><td>Date</td><td><input type=\"text\" name=\"date\" value=\"".@$getEdit['date']."\" /></td><tr>";
        $output .= "<tr><td>Theme</td><td><input type=\"text\" name=\"theme\" value=\"".@$getEdit['theme']."\" /></td><tr>";
        $output .= "<tr><td>Passage</td><td><input type=\"text\" name=\"passage\" value=\"".@$getEdit['passage']."\" /></td><tr>";
        $output .= "<tr><td>Text</td><td><input type=\"text\" name=\"text\" value=\"".@$getEdit['text']."\" /></td><tr>";
        $output .= "<tr><td>Detail</td><td><textarea class=\"ckeditor\" id=\"editor1\" name=\"content\">".@$getEdit['content']."</textarea></td><tr>";
    }elseif($table==='gallery'){
        $output .= "<input type=\"hidden\" name=\"dets\" "
                . "value=\"\$det=array('t'=>'{$table}'"
                                    . ", 't_u'=>array(".@$getCond2.")"
                                    . ", 't_cond'=>array('type'=>'".(!empty($editField)?'update':'insert')."'"
                                    . ", 'col'=>array('type'=>'type', 'title'=>'title', 'url'=>'url', 'active'=>'active', 'create_date'=>'create_date', 'detail'=>'detail')));\">";
        $output .= "<tr><td>&nbsp</h2></td><td>"
                . (!empty($editField)? "<a href=\"\" class=\"goodBtn update\" style=\"float:right;\">Update</a>":"<a href=\"\" class=\"goodBtn update\" style=\"float:right;\">Create Entry</a>")
                . "</td></tr>";
        $sqlGalTp = "SELECT `val_id`, `val_dsc` FROM `t_wb_lov` WHERE `def_id`='GAL-TP'";
        $output .= "<tr><td>Gallery Type</td><td>".$fxns->_getLOVs($sqlGalTp, 'val_id', 'val_dsc', 'type', '', NULL, @$getEdit['type'])."</td><tr>";
        $output .= "<tr><td>Title</td><td><input type=\"text\" name=\"title\" value=\"".@$getEdit['title']."\" /></td><tr>";
        $output .= "<tr><td>URL</td><td><input type=\"text\" name=\"url\" value=\"".@$getEdit['url']."\" /></td><tr>";
        $sqlDMNYN = "SELECT `val_id`, `val_dsc` FROM `t_wb_lov` WHERE `def_id`='00-YSN'";
        $output .= "<tr><td>Active</td><td>".$fxns->_getLOVs($sqlDMNYN, 'val_id', 'val_dsc', 'active', '', NULL, @$getEdit['active'])."</td><tr>";
        $output .= "<tr><td>Detail</td><td><textarea class=\"ckeditor\" id=\"editor1\" name=\"detail\">".@$getEdit['detail']."</textarea></td><tr>";
        $output .= "<tr><td>Create Date</td><td><input type=\"text\" name=\"create_date\" value=\"".@$getEdit['create_date']."\" /></td><tr>";
    }elseif($table==='t_wb_pro_usr'){
        $output .= "<input type=\"hidden\" name=\"dets\" "
                . "value=\"\$det=array('t'=>'{$table}'"
                                    . ", 't_u'=>array(".@$getCond2.")"
                                    . ", 't_cond'=>array('type'=>'".(!empty($editField)?'update':'insert')."'"
                                    . ", 'col'=>array('usr_ttl'=>'usr_ttl', 'fst_nm'=>'fst_nm', 'mdl_nm'=>'mdl_nm'"
                                    . ", 'lst_nm'=>'lst_nm', 'phn_no'=>'phn_no', 'eml_adr'=>'eml_adr', 'pry_adr_ln1'=>'pry_adr_ln1', 'pry_adr_ln2'=>'pry_adr_ln2'"
                                    . ", 'pry_adr_cty'=>'pry_adr_cty', 'pry_adr_sta'=>'pry_adr_sta', 'pry_adr_ctr'=>'pry_adr_ctr', 'usr_tp'=>'usr_tp', 'active'=>'active')));\">";
        $output .= "<tr><td>&nbsp</h2></td><td>"
                . (!empty($editField)? "<a href=\"\" class=\"goodBtn update\" style=\"float:right;\">Update</a>":"<a href=\"\" class=\"goodBtn update\" style=\"float:right;\">Create Entry</a>")
                . "</td></tr>";
        $output .= "<tr><td>User Name</td><td><input type=\"text\" name=\"usr_nm\" value=\"".@$getEdit['usr_nm']."\" /></td><tr>";
                    $sql_getTitles = "SELECT val_id, val_dsc FROM t_wb_lov
                                        WHERE def_id='00-TTL'";
        $output .= "<tr><td>Title</td><td>".$fxns->_getLOVs($sql_getTitles, "val_id", "val_dsc", 'usr_ttl', '', "--Select Title--", @$getEdit['usr_ttl'])."</td><tr>";
        $output .= "<tr><td>First Name</td><td><input type=\"text\" name=\"fst_nm\" value=\"".@$getEdit['fst_nm']."\" /></td><tr>";
        $output .= "<tr><td>Middle Name</td><td><input type=\"text\" name=\"mdl_nm\" value=\"".@$getEdit['mdl_nm']."\" /></td><tr>";
        $output .= "<tr><td>Last Name</td><td><input type=\"text\" name=\"lst_nm\" value=\"".@$getEdit['lst_nm']."\" /></td><tr>";
        $output .= "<tr><td>Phone No</td><td><input type=\"text\" name=\"phn_no\" value=\"".@$getEdit['phn_no']."\" /></td><tr>";
        $output .= "<tr><td>Email Address</td><td><input type=\"text\" name=\"eml_adr\" value=\"".@$getEdit['eml_adr']."\" /></td><tr>";
        $output .= "<tr><td>Address Line 1</td><td><input type=\"text\" name=\"pry_adr_ln1\" value=\"".@$getEdit['pry_adr_ln1']."\" /></td><tr>";
        $output .= "<tr><td>Address Line 2</td><td><input type=\"text\" name=\"pry_adr_ln2\" value=\"".@$getEdit['pry_adr_ln2']."\" /></td><tr>";
        $output .= "<tr><td>City</td><td><input type=\"text\" name=\"pry_adr_cty\" value=\"".@$getEdit['pry_adr_cty']."\" /></td><tr>";
                    $sql_getState = "SELECT val_id, val_dsc FROM t_wb_lov
                                        WHERE def_id='CTC-STA'";
        $output .= "<tr><td>State</td><td>".$fxns->_getLOVs($sql_getState, "val_id", "val_dsc", 'pry_adr_sta', '', "--Select State--", @$getEdit['pry_adr_sta'])."</td><tr>";
                        $sql_getCountry = "SELECT val_id, val_dsc FROM t_wb_lov
                                        WHERE def_id='CTC-CTR'";
        $output .= "<tr><td>Country</td><td>".$fxns->_getLOVs($sql_getCountry, "val_id", "val_dsc", 'pry_adr_ctr', '', "--Select Country--", @$getEdit['pry_adr_ctr'])."</td><tr>";
        $output .= "<tr><td>User Type</td><td><input type=\"text\" name=\"usr_tp\" value=\"".@$getEdit['usr_tp']."\" /></td><tr>";
        $sqlDMNYN = "SELECT `val_id`, `val_dsc` FROM `t_wb_lov` WHERE `def_id`='00-YSN'";
        $output .= "<tr><td>Active</td><td>".$fxns->_getLOVs($sqlDMNYN, 'val_id', 'val_dsc', 'active', '', NULL, @$getEdit['active'])."</td><tr>";
    }    
    $output .= "</table>";        
    $output .= "</form>";
    return $output;
}
