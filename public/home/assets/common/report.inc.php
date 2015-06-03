<?php
/*
* Include necessary files
*/
include_once 'core/init.inc.php';
require_once('FirePHPCore/FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
$firephp->setEnabled(true);

$cust = new Customer($dbo);
$fxns = new Functions($dbo);

$custMigTitle = array('PARTYROLE_ID'=>'Party Role ID.', 'CUSTOMERID_'=>'ERP Customer ID', 'custTpNm'=>'Customer Type', 'FULLNAME_'=>'Customer Name', 'CUSTOMERMIGRATIONSTATUS_'=>'Status');
$hiddenFields = array('PARTYROLE_ID','PARTY_ID');
$whatToLook = array('Not Migrated'=>0, 'Ready For Migration'=>1, 'Migrated'=>2);

if(isset($_POST['accType']) && !isset($_POST['chartTp'])){
	foreach( $_POST['accType'] as $val) {
		@$migrated .= $val.'ACCOUNTMIGRATEDCOUNT_ + ';
		@$ready .= $val.'ACCOUNTREADYFORMIGRATION_ + ';
		@$legacy .= 'LEGACY'.$val.'ACCOUNTCOUNT_ + ';
	}
	$migrated = '('.substr($migrated, 0, strrpos( $migrated, "+") ) . ')';
	$ready = '('.substr($ready, 0, strrpos( $ready, "+") ) . ')';
	$legacy = '('.substr($legacy, 0, strrpos( $legacy, "+") ) . ')';
	$pieData = "SELECT $migrated migrated_, $ready ready_ , ($legacy - $migrated) 'Not Migrated'
				FROM MIGRATIONREPORT_ 
				WHERE CREATED_=(SELECT MAX(CREATED_) FROM MIGRATIONREPORT_)";
	$data = $fxns->_execQuery($pieData, true, false);
	############  Build Chart lines   ####################
	$migratedSql = "SELECT IFNULL(CREATED_, NOW())CREATED_, IFNULL($migrated, 0) AccMigrated_
				FROM MIGRATIONREPORT_";
	$migratedSql = $fxns->_execQuery($migratedSql, true);
    $migratedLine = $fxns->_buildChartLine($migratedSql);
	$readyMigratn = "SELECT IFNULL(CREATED_, NOW())CREATED_, IFNULL($ready,0)accReady
		  FROM MIGRATIONREPORT_";
	$readyMigratn = $fxns->_execQuery($readyMigratn, true);
	$readyMigratnLine = $fxns->_buildChartLine($readyMigratn);	
	$accMigLine = "callJqPlotlineChat('accMigLine'
					  , ''
					  , [$migratedLine,$readyMigratnLine]
					  , ['{$migratedSql[0]['CREATED_']}', '1 day']
					  , [{label:'Migrated'},{label:'Ready for Migrated'}]
				  )";
			
	############  :End Build Chart lines   ####################
	
	$accMigPie = "callJqPlotPieChat('accMigPie'
							, ''
							,[
								['Migrated: &nbsp;&nbsp;{$data['migrated_']}',{$data['migrated_']}]
								, ['Ready For Migration: &nbsp;&nbsp;{$data['ready_']}',{$data['ready_']}]
								, ['Not Migrated: &nbsp;&nbsp;{$data['Not Migrated']}',{$data['Not Migrated']}]
							 ]
						)";
	echo "<div id=\"accMigPie\" class=\"chart\"></div>
			<div id=\"accMigLine\" style=\"float:left; margin-left:150px; width:600px;\"></div>
			<div id=\"report_results\" style=\"clear:both; width:85%; margin:auto; top:30px; border:1px solid #CCC;\">
				<div class=\"migrate\" style=\"width:95%; margin:auto;\">
					<div style=\"clear:both;\">&nbsp;</div>
				</div>
			</div>
			<div style=\"clear:both;\">&nbsp;</div>
			<script type=\"text/javascript\">".$accMigPie.';'.$accMigLine.';'.'</script>';
}elseif(isset($_POST['dataQuality']) && !isset($_POST['chartTp'])){
	foreach( $_POST['dataQuality'] as $val) {
		@$clean .= 'IS'.$val.'VALID_ =1 AND ';
		@$howMany +=1;
		@$cleanTot .= 'ERPCLEAN'.$val.'COUNT_ , ';
	}
	$clean = '('.substr($clean, 0, strrpos( $clean, "AND") ) . ')';
	$cleanTot = '('.substr($cleanTot, 0, strrpos( $cleanTot, ",") ) . ')';
	if($howMany > 1) $cleanTot = " LEAST$cleanTot ";
	
	$pieData = "SELECT COUNT(*) clean, ((SELECT COUNT(*) FROM MIGRATION_) - COUNT(*)) 'Not clean'
				FROM MIGRATION_ 
				WHERE $clean";
	$data = $fxns->_execQuery($pieData, true, false);
	$dataQualityPie = "callJqPlotPieChat('dataQualityPie'
							, 'Correct',[['Correct: &nbsp;&nbsp;{$data['clean']}',{$data['clean']}]
							, ['Not Correct: &nbsp;&nbsp;{$data['Not clean']}',{$data['Not clean']}]]
						);";
	############  Build Chart lines   ####################
	$dataQualitySQL = "SELECT IFNULL(CREATED_, NOW())CREATED_, IFNULL($cleanTot,0) AccMigrated_
				FROM MIGRATIONREPORT_";
	$dataQualitySQL = $fxns->_execQuery($dataQualitySQL, true);
    $migratedLine = $fxns->_buildChartLine($dataQualitySQL);

	$dirty = "SELECT IFNULL(CREATED_, NOW())CREATED_, IFNULL((ERPCUSTOMERCOUNT_ - $cleanTot),0) 'Dirty Names'
			FROM MIGRATIONREPORT_";
	$dirty = $fxns->_execQuery($dirty, true);
	$dirtyLine = $fxns->_buildChartLine($dirty);				
	$dataQualityLine = "callJqPlotlineChat('dataQualityLine'
					  , ''
					  , [$migratedLine,$dirtyLine]
					  , ['{$dataQualitySQL[0]['CREATED_']}', '1 day']
					  , [{label:'Migrated'},{label:'Clean'}]
				  )";
	//var_dump($dataQualityLine);exit;
	############  :End Build Chart lines   ####################
	echo "<div id=\"dataQualityPie\" class=\"chart\"></div>
			<div id=\"dataQualityLine\" style=\"float:left; margin-left:150px; width:600px;\"></div>
			<div id=\"report_results\" style=\"clear:both; width:85%; margin:auto; top:30px; border:1px solid #CCC;\">
				<div class=\"migrate\" style=\"width:95%; margin:auto;\">
					<div style=\"clear:both;\">&nbsp;</div>
				</div>
			</div>
			<div style=\"clear:both;\">&nbsp;</div>
			<script type=\"text/javascript\">".$dataQualityPie.';'.$dataQualityLine.';'.'</script>';
}else{
	$chartTp = $fxns->_multiexplode ($_POST['chartTp'], array("-", ":"));
	if(isset($_POST['accType'])){
		$accType = array_shift ($_POST['accType']);
		$firstVal = $accType."ACCOUNTMIGRATIONSTATUS_";
		$accType = ($accType=='ST')? 'ERPST_ACC_NOS': 'LEGACY'.$accType.'ACCOUNTNUMBER_';
		$custMigTitle = array('PARTYROLE_ID'=>'Party Role ID.', 'FULLNAME_'=>'Customer Name', $accType=>'Account Number', $firstVal=>'Status');
		
		foreach( $_POST['accType'] as $val) {
			@$extraClause .= " AND ".$val.'ACCOUNTMIGRATIONSTATUS_ = '.$whatToLook[$chartTp[1]];
		}
	}elseif(isset($_POST['dataQuality'])){
		$dataQuality = array_shift($_POST['dataQuality']);
		$firstVal = 'IS'.$dataQuality."VALID_";
		$whatToLook = array('Not Correct'=>0, 'Correct'=>1);
		foreach( $_POST['dataQuality'] as $val) {
			@$extraClause .= " AND ".'IS'.$val.'VALID_ = '.$whatToLook[$chartTp[1]];
		}
	}
	
	echo drawTable($chartTp, $custMigTitle, @$firstVal, @$extraClause);
}
function drawTable($chartTp, $MigTitle, $FirstTypeToLook='STACCOUNTMIGRATIONSTATUS_', $extraClause=''){
	$whereToLook = array('custMigPie'=> array('type'=>'CUSTOMERMIGRATIONSTATUS_'
										, 'title'=>$MigTitle)
						, 'accMigPie'=> array('type'=>$FirstTypeToLook
										, 'title'=>$MigTitle)
						, 'dataQualityPie'=> array('type'=>$FirstTypeToLook
										, 'title'=>$MigTitle));
	global $cust, $fxns, $custMigTitle, $hiddenFields, $whatToLook;				
	$getCustByStatus = $cust->_getCustsByStatus($whereToLook[$chartTp[0]]['type'], $whatToLook[$chartTp[1]], $extraClause);
	$numRows = count($getCustByStatus);
	//return var_dump($whereToLook);
	
	array_unshift($getCustByStatus, $whereToLook[$chartTp[0]]['title']);
	$numCols = count($getCustByStatus[0]);
	$table = '<form method="post" action="">';
	//echo $fxns->_buildTable($custsReadyForMigration, $isVertical=false, $classNm="clickable");
	$table = '<table  class="my_tables clickable">';
	for($i = 0; $i < count($getCustByStatus); $i++){
		$table .= "<tr>";
		foreach($getCustByStatus[0] as $key => $values){
			if ($i == 0) {
				 $table .=in_array($key, $hiddenFields) ? "<th>&nbsp;</th>" :"<th>".$getCustByStatus[$i][$key]."</th>";
			}else{
				if($key=='PARTYROLE_ID'){
					$table .= '<td><input type="checkbox" name="cust_id[]" value="'.$getCustByStatus[$i]["PARTYROLE_ID"].'" data-role-type="'.$getCustByStatus[$i]["TYPE_ID"].'" /></td>';
				}elseif($key==$whereToLook[$chartTp[0]]['type']){
					switch($getCustByStatus[$i][$whereToLook[$chartTp[0]]['type']]){
						case 1: $table .='<td>Ready For Migration</td>'; break;
						case 2: $table .='<td>Migrated</td>'; break;
						default:  $table .='<td>Not Migrated</td>'; break;
					}
				}else{
					$table .="<td>".$getCustByStatus[$i][$key]."</td>";
				}
			}
		}
		$table .= "</tr>";
	}
	if($numRows == 0){
		$table .= "<tr><td colspan=\"$numCols\" style=\"background:#CCC; text-align:center\">No records found</td></tr>";					
	}
	
	$table .= '</table>';
	$table .= ($whatToLook[$chartTp[1]] ==1)?'<a href="" class="button migrate" style="float:right;color:#FFF;" >Migrate</a>' : '';
	$table .= '</form><div style="clear:both;">&nbsp;</div>';
	return $table;
}
?>