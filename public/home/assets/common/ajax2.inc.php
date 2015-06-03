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
if(isset($_REQUEST['term'])){
	$results = $cust->_doCustSearch($_REQUEST['term'], 4);
	echo json_encode($results);
}
//Array to hold column titles
if(isset($_POST['getStateCtyID'])){
	$sql_getStateOptLOV = "SELECT GEOGRAPHICBOUNDARY_ID, NAME_ FROM GEOGRAPHICBOUNDARY_
WHERE TYPE_ID = (SELECT GEOGRAPHICBOUNDARYTYPE_ID FROM GEOGRAPHICBOUNDARYTYPE_ WHERE NAME_ = 'State') AND COUNTRY_STATES_ID = ".$_POST['getStateCtyID'];
	$states = $fxns->_getStateOptLOV($sql_getStateOptLOV, "GEOGRAPHICBOUNDARY_ID", "NAME_", "--Select State--", NULL);
	echo $states;
}
$tblTitle = array('sn'=>'S/N','PARTYROLE_ID'=>'Party Role ID.', 'PARTY_ID'=>'Party ID.', 'PARTYTITLE_ID'=>'Title', 'CUSTOMERID_'=>'ERP Customer ID', 'LEGACYCUSTOMERID_'=>'iBroker Customer ID', 'ERPST_ACC_NOS'=>'ST Account No.', 'LEGACYSTACCOUNTNUMBER_'=> 'iBroker ST Account #', 'LEGACYMMACCOUNTNUMBER_'=>'iBroker MM Account #', 'LEGACYTBILLSACCOUNTNUMBER_'=>'iBroker TBills Account #', 'TOTALASSETVALUE_'=>'Total Asset Value (NGN)', 'TYPE_ID'=>'Type ID.', 'FIRSTNAME_'=>'First Name', 'MIDDLENAME_'=>'Middle Name', 'LASTNAME_'=>'Last Name', 'INITIALS_'=>'Initials', 'GENDER_ID'=>'Gender', 'MARITALSTATUSTYPE_ID'=>'Marital Status', 'MAIDENNAME_'=>'Maiden Name', 'DATEOFBIRTH_'=>'Date Of Birth', 'PRIMARYPHONENO_'=>'Phone No. (Mobile)', 'WORKPHONENUMBER_'=>'Phone No. (Other)', 'PRIMARYEMAILADDRESS_'=>'Email Address', 'ADDRESSLINE1_'=>'Address Line 1', 'ADDRESSLINE2_'=>'Address Line 2', 'ADDRESSCITY_'=>'City', 'ADDRESSCOUNTRY_ID'=>'Country', 'ADDRESSSTATE_ID'=>'State', 'COUNTRY_ID'=>'Nationality', 'KINRELATIONSHIPTYPE_ID'=>'Next Of Kin Type', 'KINFIRSTNAME_'=>'Kin First Name', 'KINLASTNAME_'=>'Kin Last Name', 'KINOTHERNAMES_'=>'Kin Other Names', 'KINCONTACTPHONENUMBER_'=>'Kin Contact Phone No.', 'KINADDRESS_'=>'Kin Address', 'FULLNAME_'=>'Full Name');
//Array to hold column titles
$classNm = array('PARTYROLE_ID'=>'', 'PARTY_ID'=>'', 'PARTYTITLE_ID'=>'', 'CUSTOMERID_'=>'', 'LEGACYCUSTOMERID_'=>'combobox', 'LEGACYSTACCOUNTNUMBER_'=> 'combobox', 'LEGACYMMACCOUNTNUMBER_'=>'combobox', 'LEGACYTBILLSACCOUNTNUMBER_'=>'combobox', 'TOTALASSETVALUE_'=>'', 'ERPST_ACC_NOS'=>'', 'TYPE_ID'=>'', 'FIRSTNAME_'=>'combobox', 'MIDDLENAME_'=>'combobox', 'LASTNAME_'=>'combobox', 'INITIALS_'=>'', 'GENDER_ID'=>'', 'MARITALSTATUSTYPE_ID'=>'', 'MAIDENNAME_'=>'combobox', 'DATEOFBIRTH_'=>'combobox date', 'PRIMARYPHONENO_'=>'combobox phone', 'WORKPHONENUMBER_'=>'combobox phone', 'PRIMARYEMAILADDRESS_'=>'combobox', 'ADDRESSLINE1_'=>'combobox', 'ADDRESSLINE2_'=>'combobox', 'ADDRESSCITY_'=>'combobox', 'ADDRESSSTATE_ID'=>'', 'ADDRESSCOUNTRY_ID'=>'', 'COUNTRY_ID'=>'', 'KINRELATIONSHIPTYPE_ID'=>'', 'KINFIRSTNAME_'=>'combobox', 'KINLASTNAME_'=>'combobox', 'KINOTHERNAMES_'=>'combobox', 'KINCONTACTPHONENUMBER_'=>'combobox phone', 'KINADDRESS_'=>'combobox', 'FULLNAME_'=>'combobox');
$hiddenFields = array('PARTYROLE_ID','TYPE_ID','INITIALS_','FULLNAME_','PARTY_ID');
$planeTxt = array('CUSTOMERID_','TOTALASSETVALUE_', 'ERPST_ACC_NOS');
//For customer type individual, restructure the arrays
if(@$_POST['cust_type'] == '4'){
	$tblTitle = array('PARTYROLE_ID'=>'Party Role ID.', 'PARTY_ID'=>'Party ID.', 'PARTYTITLE_ID'=>'Title', 'CUSTOMERID_'=>'ERP Customer ID', 'LEGACYCUSTOMERID_'=>'iBroker Customer ID', 'ERPST_ACC_NOS'=>'ST Account No.', 'LEGACYSTACCOUNTNUMBER_'=> 'iBroker ST Account #', 'LEGACYMMACCOUNTNUMBER_'=>'iBroker MM Account #', 'LEGACYTBILLSACCOUNTNUMBER_'=>'iBroker TBills Account #', 'TOTALASSETVALUE_'=>'Total Asset Value (NGN)', 'TYPE_ID'=>'Type ID.', 'FULLNAME_'=>'Name', 'PRIMARYPHONENO_'=>'Contact Phone Number', 'WORKPHONENUMBER_'=>'Phone No. (Other)', 'PRIMARYEMAILADDRESS_'=>'Email Address', 'ADDRESSLINE1_'=>'Address Line 1', 'ADDRESSLINE2_'=>'Address Line 2', 'ADDRESSCITY_'=>'City', 'ADDRESSCOUNTRY_ID'=>'Country', 'ADDRESSSTATE_ID'=>'State');
	if(($key = array_search('FULLNAME_', $hiddenFields)) !== false) {
		unset($hiddenFields[$key]);
	}
}
//This builds the tables columns for the selected customers
if(isset($_POST['get_merger'])){
	$surviving = "SELECT ms.sn, ms.PARTYROLE_ID, ms.PARTY_ID, ms.CUSTOMERID_, ms.TYPE_ID, ms.ERPST_ACC_NOS, ms.ERPIMACCOUNTNUMBER_, ms.LEGACYSTACCOUNTNUMBER_, ms.LEGACYMMACCOUNTNUMBER_, ms.LEGACYTBILLSACCOUNTNUMBER_, ms.LEGACYCUSTOMERID_, ms.PARTYTITLE_ID, ms.FIRSTNAME_, ms.MIDDLENAME_, ms.LASTNAME_, ms.INITIALS_, ms.GENDER_ID, ms.MARITALSTATUSTYPE_ID, ms.MAIDENNAME_, DATE_FORMAT(ms.DATEOFBIRTH_, '%b-%d-%Y') AS DATEOFBIRTH_, ms.PRIMARYEMAILADDRESS_, ms.COUNTRY_ID, ms.PRIMARYPHONENO_, ms.WORKPHONENUMBER_, ms.ADDRESSLINE1_, ms.ADDRESSLINE2_, ms.ADDRESSCITY_, ms.ADDRESSSTATE_ID, ms.ADDRESSCOUNTRY_ID, ms.KINRELATIONSHIPTYPE_ID, ms.KINFIRSTNAME_, ms.KINLASTNAME_, ms.KINOTHERNAMES_, KINCONTACTPHONENUMBER_, ms.KINADDRESS_, ms.LOCALE_, ms.FULLNAME_, (SELECT m.TOTALASSETVALUE_ FROM MIGRATION_ m WHERE m.PARTYROLE_ID=ms.PARTYROLE_ID) TOTALASSETVALUE_, ms.DUPLICATE_IDS FROM migrationstatus_ ms WHERE sn=".$_POST['id'];
	$surviving = $fxns->_execQuery($surviving, true, false);
	if($surviving['DUPLICATE_IDS'] == 4){
		$tblTitle = array('PARTYROLE_ID'=>'Party Role ID.', 'PARTY_ID'=>'Party ID.', 'PARTYTITLE_ID'=>'Title', 'CUSTOMERID_'=>'ERP Customer ID', 'LEGACYCUSTOMERID_'=>'iBroker Customer ID', 'ERPST_ACC_NOS'=>'ST Account No.', 'LEGACYSTACCOUNTNUMBER_'=> 'iBroker ST Account #', 'LEGACYMMACCOUNTNUMBER_'=>'iBroker MM Account #', 'LEGACYTBILLSACCOUNTNUMBER_'=>'iBroker TBills Account #', 'TOTALASSETVALUE_'=>'Total Asset Value (NGN)', 'TYPE_ID'=>'Type ID.', 'FULLNAME_'=>'Name', 'PRIMARYPHONENO_'=>'Contact Phone Number', 'WORKPHONENUMBER_'=>'Phone No. (Other)', 'PRIMARYEMAILADDRESS_'=>'Email Address', 'ADDRESSLINE1_'=>'Address Line 1', 'ADDRESSLINE2_'=>'Address Line 2', 'ADDRESSCITY_'=>'City', 'ADDRESSCOUNTRY_ID'=>'Country', 'ADDRESSSTATE_ID'=>'State');
		if(($key = array_search('FULLNAME_', $hiddenFields)) !== false) {
			unset($hiddenFields[$key]);
		}
	}
	$dupIDs = str_replace(',',"','", $surviving['DUPLICATE_IDS']);
	$others = "SELECT '' sn, m.PARTYROLE_ID, m.PARTY_ID, m.CUSTOMERID_, m.TYPE_ID, m.ERPST_ACC_NOS, m.ERPIMACCOUNTNUMBER_, m.LEGACYSTACCOUNTNUMBER_, m.LEGACYMMACCOUNTNUMBER_, m.LEGACYTBILLSACCOUNTNUMBER_, m.LEGACYCUSTOMERID_, m.PARTYTITLE_ID, m.FIRSTNAME_, m.MIDDLENAME_, m.LASTNAME_, m.INITIALS_, m.GENDER_ID, m.MARITALSTATUSTYPE_ID, m.MAIDENNAME_, DATE_FORMAT(m.DATEOFBIRTH_, '%b-%d-%Y') AS DATEOFBIRTH_, m.PRIMARYEMAILADDRESS_, m.COUNTRY_ID, m.PRIMARYPHONENO_, m.WORKPHONENUMBER_, m.ADDRESSLINE1_, m.ADDRESSLINE2_, m.ADDRESSCITY_, m.ADDRESSSTATE_ID, m.ADDRESSCOUNTRY_ID, m.KINRELATIONSHIPTYPE_ID, m.KINFIRSTNAME_, m.KINLASTNAME_, m.KINOTHERNAMES_, m.KINCONTACTPHONENUMBER_, m.KINADDRESS_, m.LOCALE_, m.FULLNAME_, m.TOTALASSETVALUE_ FROM migration_ m WHERE PARTYROLE_ID IN ('".$dupIDs. "')";

	$all4Pending = $fxns->_execQuery($others, true, true);
	array_unshift($all4Pending, $surviving);
	array_unshift($all4Pending, $tblTitle);
	$selected = "'".$surviving['PARTYROLE_ID']."','".$dupIDs."'"; //Forms the id of all selected customer
	$table = '<form method="post" action="" id="rejectForm">';
	$table .= '<table  class="my_tables vertColNm">';
	foreach($all4Pending[0] as $key => $values){
		$table .= "<tr>";
			$sql_getCombo = "SELECT ";
			if($key =='DATEOFBIRTH_'){
				$sql_getCombo .= " DATE_FORMAT(DATEOFBIRTH_, '%b-%d-%Y') AS DATEOFBIRTH_ ";
			}elseif(strpos($key ,'PHONE') !== false){
				$sql_getCombo .= " SUBSTRING_INDEX({$key}, '-', -1) {$key} ";
			}else{
				$sql_getCombo .= " {$key} ";
			}
			$sql_getComboMIG = $sql_getCombo." FROM MIGRATION_ WHERE PARTYROLE_ID IN ('{$dupIDs}')";
			$sql_getComboSTAT = " UNION ALL ". $sql_getCombo."FROM MIGRATIONSTATUS_ WHERE PARTYROLE_ID IN('{$surviving['PARTYROLE_ID']}')";
			$sql_getCombo = $sql_getComboMIG.$sql_getComboSTAT;
			for($i = 0; $i < count($all4Pending); $i++){
				if (in_array($key, $hiddenFields)) {
					$table .= '<td><input type="hidden" name="'.$key.'" value="'.$all4Pending[$i][$key].'" /></td>';
				}else{
					if ($i == 0){
						$table .= '<td>'.$all4Pending[$i][$key].'</td>';
					}else{
							if($key == 'sn'){
								$table .= '<td><input type="radio" name="checkID" value="'.$all4Pending[$i]['PARTYROLE_ID'].'" ';
								$table .= (!empty($all4Pending[$i]['sn'])? 'checked="checked"' : '') . ' /></td>';
								$table .= '<input type="hidden" name="checkIDs[]" value="'.$all4Pending[$i]['PARTYROLE_ID'].'" />
								<input type="hidden" name="sn[]" value="'.$all4Pending[$i]['sn'].'" />';
							}elseif($key == 'PARTYTITLE_ID'){
								$sql_getTitle = "SELECT PARTYTITLE_ID, DESCRIPTION_ FROM PARTYTITLE_";
								$table .= "<td>".$fxns->_getLOVs($sql_getTitle, $key, "DESCRIPTION_", $key, $classNm[$key], "--Select Title--", $all4Pending[$i][$key])."</td>";
							}elseif($key == 'GENDER_ID'){
								$sql_getGender = "SELECT GENDER_ID, DESCRIPTION_ FROM GENDER_";
								$table .= "<td>".$fxns->_getLOVs($sql_getGender, $key, "DESCRIPTION_", $key, $classNm[$key], "--Select Gender--", $all4Pending[$i][$key])."</td>";
							}elseif($key == 'MARITALSTATUSTYPE_ID'){
								$sql_getMaritalStat = "SELECT MARITALSTATUSTYPE_ID, DESCRIPTION_ FROM MARITALSTATUSTYPE_";
								$table .= "<td>".$fxns->_getLOVs($sql_getMaritalStat, $key, "DESCRIPTION_", $key, $classNm[$key], "--Select Marital Status--", $all4Pending[$i][$key])."</td>";
							}elseif($key == 'ADDRESSCOUNTRY_ID'){
								$sql_getCountry = "SELECT GEOGRAPHICBOUNDARY_ID, NAME_ FROM GEOGRAPHICBOUNDARY_
	WHERE TYPE_ID = (SELECT GEOGRAPHICBOUNDARYTYPE_ID FROM GEOGRAPHICBOUNDARYTYPE_ WHERE NAME_ = 'Country') ORDER BY NAME_";
								$table .= "<td>".$fxns->_getLOVs($sql_getCountry, "GEOGRAPHICBOUNDARY_ID", "NAME_", $key, $classNm[$key], "--Select Country--", $all4Pending[$i][$key])."</td>";
							}elseif($key == 'ADDRESSSTATE_ID'){
								$chosenState = isset($selectedCustomers[$i][$key]) ? $selectedCustomers[$i][$key]: '0';
								$sql_getStates = "SELECT GEOGRAPHICBOUNDARY_ID, NAME_ FROM GEOGRAPHICBOUNDARY_
	WHERE TYPE_ID = (SELECT GEOGRAPHICBOUNDARYTYPE_ID FROM GEOGRAPHICBOUNDARYTYPE_ WHERE NAME_ = 'State') AND COUNTRY_STATES_ID =".$all4Pending[$i]['ADDRESSCOUNTRY_ID'];
								$table .= "<td>".$fxns->_getLOVs($sql_getStates, "GEOGRAPHICBOUNDARY_ID", "NAME_", $key, $classNm[$key], "--Select State--", $all4Pending[$i][$key])."</td>";
							}elseif($key == 'COUNTRY_ID'){
								$sql_getNationality = "SELECT GEOGRAPHICBOUNDARY_ID, NATIONALITY_ FROM GEOGRAPHICBOUNDARY_
	WHERE TYPE_ID = (SELECT GEOGRAPHICBOUNDARYTYPE_ID FROM GEOGRAPHICBOUNDARYTYPE_ WHERE NAME_ = 'Country') ORDER BY NATIONALITY_";
								$table .= "<td>".$fxns->_getLOVs($sql_getNationality, "GEOGRAPHICBOUNDARY_ID", "NATIONALITY_", $key, $classNm[$key], "--Select Nationality--", $all4Pending[$i][$key])."</td>";
							}elseif($key == 'KINRELATIONSHIPTYPE_ID'){
								$sql_getKinType = "SELECT FAMILYRELATIONSHIPTYPE_ID, NAME_ FROM FAMILYRELATIONSHIPTYPE_";
								$table .= "<td>".$fxns->_getLOVs($sql_getKinType, "FAMILYRELATIONSHIPTYPE_ID", "NAME_", $key, $classNm[$key], "--Select Relationship--", $all4Pending[$i][$key])."</td>";
							}elseif(strpos($key ,'PHONE') !== false){
								$phone = explode("-", $all4Pending[$i][$key]);
								$sql_callingCode = "SELECT DISTINCT CONCAT('+',COUNTRYCALLINGCODE_) CODE, CONCAT(NAME_,' +',COUNTRYCALLINGCODE_) NAMES_ FROM GEOGRAPHICBOUNDARY_
	WHERE TYPE_ID = (SELECT GEOGRAPHICBOUNDARYTYPE_ID FROM GEOGRAPHICBOUNDARYTYPE_ WHERE NAME_ = 'Country') ORDER BY NAME_";
								$table .= "<td>"
											.$fxns->_getLOVs($sql_callingCode, "CODE", "NAMES_", $key.'CODE', "", "--Select Calling Code--", $phone[0])
											."-"
											.$fxns->_getLOVs($sql_getCombo, $key, $key, $key, $classNm[$key], NULL, @$phone[1])
											."</td>";
							}elseif (in_array($key, $planeTxt)) {
								if($key == 'TOTALASSETVALUE_') $table .= '<td><div style="width:200px; text-align:right;">'.$fxns->_formatMoney($all4Pending[$i][$key], true).'</div></td>';
								else $table .= "<td>".$all4Pending[$i][$key].'<input type="hidden" name="'.$key.'" value="'.$all4Pending[$i][$key].'" /></td>';
							}else{
								$table .= "<td>".$fxns->_getLOVs($sql_getCombo, $key, $key, $key, $classNm[$key], NULL, $all4Pending[$i][$key])."</td>";
							}
					}
				}
			}
		$table .= "</tr>";
	}
	$table .= "</table>";
	$table .= '<a href="" class="button preview" style="color:#FFF; float:right;margin:-5px 10px;">Preview >></a>';
	if (!in_array("MIGRATION_OFFICER", $_SESSION['user_dets']['authrole']) ){
		$table .= '<a href="" class="failBtn reject" style="background:#900; color:#FFF; float:right;margin:-5px 10px;" >Reject</a>';
		$table .= '<div id="rejectRsn" style="display:none;"><table cellpadding="10" ><tr><td valign="middle"><label for="rsn">Reason for rejecting:</td><td><textarea name="rejectRsn" id="rsn" style="width:280px; height:100px;"></textarea></td></tr></table><a href="" class="failBtn rejected" style="background:#900; color:#FFF; float:right;margin:-5px 10px;" >Reject</a></div>
		';
	}
	$table .= '</form>';
	echo $table;
	//var_dump($all4Pending);
}

if(isset($_POST['rsn'])){
	$sn = array_filter($_POST['sn']);
	$sql_rejectMerge = "UPDATE MIGRATIONSTATUS_ SET STATUS_=2, REJECTREASON_='{$_POST['rsn']}' WHERE sn = {$sn[0]}";
	$rejectMerge = $fxns->_execQuery($sql_rejectMerge, false, false);
	$message = NULL;
	if(is_array( $rejectMerge)){
		$message .= $rejectMerge['msg'];
	}else{
		$message .= "Customer merge rejected successfully! ";
	}
	echo "<div style=\"position:absolute;width:192px;margin:5% 10%;text-align:center;\">".$message."</div>";
}
if(isset($_POST['mergeNow'])){
	if(!isset($_SESSION['user'])){
		echo '<div style="font-size:1.5em; color:#600; margin-top:2em;">You have arrived here illegally and cannot perform this action.</div>';
		exit();
	}
	if (in_array("MIGRATION_OFFICER", $_SESSION['user_dets']['authrole']) ){
		$toBMergedArr = array_unique($_POST['checkIDs']);		
		if (in_array($_POST['PARTYROLE_ID'], $toBMergedArr)) {
			unset($toBMergedArr[array_search($_POST['PARTYROLE_ID'],$toBMergedArr)]);
		}
		$toBMergedVals = implode(',',$toBMergedArr);
	
		##########	Check if record already exists
		$sql_recExist = "SELECT COUNT(*) count FROM MIGRATIONSTATUS_ 
						WHERE PARTYROLE_ID = {$_POST['PARTYROLE_ID']} AND DUPLICATE_IDS='{$toBMergedVals}'";
		$sql_recExist = $fxns->_execQuery($sql_recExist, true, false );
		$sql_submitApprove = NULL;
		if($sql_recExist['count'] > 0 ){
			$sql_submitApprove .= "UPDATE MIGRATIONSTATUS_ SET ";
			foreach($_POST as $key => $value){
				if($key != 'checkIDs' && $key != 'mergeNow' && $key != 'cust_type'){
					$sql_submitApprove .= ($key=='DATEOFBIRTH_') ? " $key=".(empty($value) ?"NULL,":"'". date("Y-m-d H:i:s", strtotime($value))."',")
														: (($key == 'ERPST_ACC_NOS') ? "ERPST_ACC_NOS='$value', ": " $key = '$value', ");
				}		
			}
			$sql_submitApprove .= "STATUS_=0 WHERE PARTYROLE_ID = {$_POST['PARTYROLE_ID']}";
		}else{
			$sql_submitApprove .= "INSERT INTO MIGRATIONSTATUS_ ";
			$cols = "(";	$vals = "(";
			foreach($_POST as $key => $value){
				if($key != 'checkIDs' && $key != 'mergeNow' && $key != 'cust_type'){
					$cols .= ($key == 'ERPST_ACC_NOS') ? "ERPST_ACC_NOS, " : $key.", ";
					$vals .= ($key=='DATEOFBIRTH_')? (empty($value) ?"NULL,":"'". date("Y-m-d H:i:s", strtotime($value))."',") : "'$value', ";
				}		
			}
			$cols .= "STATUS_, DUPLICATE_IDS, LASTUPDATEBY_)";	$vals .= "0, '{$toBMergedVals}', '{$_SESSION['user']}')";
			$sql_submitApprove .= $cols." VALUES ".$vals;
		}
		$insertAppr = $fxns->_execQuery($sql_submitApprove, false, false);
		$message = NULL;
		if(is_array( $insertAppr)){
			$message .= $insertAppr['msg'];
		}else{
			$message .= "Customer(s) update submitted for approval ";
		}
		echo "<div style=\"position:absolute;width:192px;margin:5% 10%;text-align:center;\">".$message."</div>";

	}else{
	$partyRole = ($_POST['cust_type'] == 4) 
					? array('CUSTOMERID_')
					:array('CUSTOMERID_', 'KINRELATIONSHIPTYPE_ID', 'KINFIRSTNAME_', 'KINLASTNAME_', 'KINOTHERNAMES_',
					   'KINCONTACTPHONENUMBER_', 'KINADDRESS_');
	$party = ($_POST['cust_type'] == 4) 
				? array('PARTYTITLE_ID', 'PRIMARYEMAILADDRESS_', 'PRIMARYPHONENO_', 'WORKPHONENUMBER_', 'ADDRESSLINE1_',
			   		'ADDRESSLINE2_', 'ADDRESSCITY_', 'ADDRESSCOUNTRY_ID', 'ADDRESSSTATE_ID', 'FULLNAME_')
				: array('PARTYTITLE_ID', 'FIRSTNAME_', 'MIDDLENAME_', 'LASTNAME_', 'INITIALS_', 'GENDER_ID', 'MARITALSTATUSTYPE_ID',
			   		'MAIDENNAME_', 'DATEOFBIRTH_', 'PRIMARYEMAILADDRESS_', 'COUNTRY_ID', 'PRIMARYPHONENO_', 'WORKPHONENUMBER_'
					, 'ADDRESSLINE1_', 'ADDRESSLINE2_', 'ADDRESSCITY_', 'ADDRESSCOUNTRY_ID', 'ADDRESSSTATE_ID', 'FULLNAME_');
	/*****Query to update surviving customer in migration table***********/
	$sql_merge = "UPDATE MIGRATION_ SET ";
	foreach($_POST as $key => $value){
		if($key != 'checkIDs' && $key != 'mergeNow' && $key != 'ERPST_ACC_NOS' && $key != 'cust_type'){
			$sql_merge .= ($key=='DATEOFBIRTH_')? " $key=".(empty($value) ?"NULL,":"'". date("Y-m-d H:i:s", strtotime($value))."',") :" $key = '$value', ";
		}		
	}
	$nmCln = ($_POST['FIRSTNAME_']!='' && strlen($_POST['FIRSTNAME_'])>3 && $_POST['LASTNAME_']!='' && strlen($_POST['LASTNAME_']) > 3)?1:0;
	$emlCln = ($_POST['PRIMARYEMAILADDRESS_'] != '') ? 1 : 0;
	$phnCln = ($_POST['PRIMARYPHONENO_'] != '') ? 1 : 0;
	$adrCln = ($_POST['ADDRESSLINE1_']!='' && $_POST['ADDRESSCITY_']!='' && $_POST['ADDRESSSTATE_ID']!='' && $_POST['ADDRESSCOUNTRY_ID']!='')?1:0;
	$custMap = ($_POST['LEGACYCUSTOMERID_'] != '') ? 1 : 0;
	$stMap = ($_POST['LEGACYSTACCOUNTNUMBER_'] != '') ? 1 : 0;
	$mmMap = ($_POST['LEGACYMMACCOUNTNUMBER_'] != '') ? 1 : 0;
	$tbillsMap = ($_POST['LEGACYTBILLSACCOUNTNUMBER_'] != '') ? 1 : 0;

	$sql_merge .= "ISNAMEVALID_=$nmCln, ISEMAILVALID_=$emlCln, ISPHONENOVALID_=$phnCln, ISADDRESSVALID_=$adrCln, ";
	$sql_merge .= "ISCUSTOMERMAPPED_=$custMap, ";
	$sql_merge .= "ISCUSTOMERMAPPED_=$custMap, MMACCOUNTMIGRATIONSTATUS_=$mmMap, TBILLSACCOUNTMIGRATIONSTATUS_=$tbillsMap, ";
	$sql_merge .= "VERSION_=VERSION_+1, LASTUPDATEBY_ = '".$_SESSION['user']."' WHERE PARTYROLE_ID = ".$_POST['PARTYROLE_ID'];
/*ALTER TABLE MIGRATION_ ADD ISCUSTOMERMAPPED_ bit(1) DEFAULT 0 AFTER ISVALIDADDRESS_;
ALTER TABLE MIGRATION_ ADD CUSTOMERMIGRATIONSTATUS_ TINYINT(1) DEFAULT 0 AFTER DATEMARKEDDUPLICATE_;
ALTER TABLE MIGRATION_ ADD STACCOUNTMIGRATIONSTATUS_  TINYINT(1) DEFAULT 0 AFTER CUSTOMERMIGRATIONSTATUS_;
ALTER TABLE MIGRATION_ ADD MMACCOUNTMIGRATIONSTATUS_  TINYINT(1) DEFAULT 0 AFTER STACCOUNTMIGRATIONSTATUS_;
ALTER TABLE MIGRATION_ ADD TBILLSACCOUNTMIGRATIONSTATUS_  TINYINT(1) DEFAULT 0 AFTER MMACCOUNTMIGRATIONSTATUS_;
	/*************Ends here **********************/
	/*****Query to update mark non-customer as duplicates in migration table***********/
	$toBMergedArr = array_unique($_POST['checkIDs']);		
	$uniqueCheckIDs = $toBMergedArr;		
	if (in_array($_POST['PARTYROLE_ID'], $toBMergedArr)) {
		unset($toBMergedArr[array_search($_POST['PARTYROLE_ID'],$toBMergedArr)]);
	}
	$toBMergedVals = implode('\',\'',$toBMergedArr);
	$sql_dupe = "UPDATE MIGRATION_ SET ";
	$sql_dupe .= "ISDUPLICATE_ = 1, DUPLICATE_ID={$_POST['PARTYROLE_ID']}, DATEMARKEDDUPLICATE_='".date("Y-m-d H:i:s")."'";
	$sql_dupe .= ", VERSION_=VERSION_+1, LASTUPDATEBY_ = '".$_SESSION['user']."'";
	$sql_dupe .= " WHERE PARTYROLE_ID IN ('".$toBMergedVals."')";
	/************ Mark duplicate ends here **********************/
	/*****Query to update surviving customer in partyrole table***********/
	$sql_updt_partyRole = "UPDATE PARTYROLE_ SET ";
	$sql_updt_partyRole .= "LEGACYNUMBER_ = '$_POST[LEGACYCUSTOMERID_]', ";
	foreach($partyRole as $value){
		$sql_updt_partyRole .= "$value = ".(($_POST[$value] == '') ? "NULL" : "'".$_POST[$value]."'").",";
	}
	$sql_updt_partyRole = substr($sql_updt_partyRole, 0, strrpos( $sql_updt_partyRole, ",") );
	$sql_updt_partyRole .= " WHERE PARTYROLE_ID = ".$_POST['PARTYROLE_ID'];

	$sql_updt_partyRole_dupe = "UPDATE PARTYROLE_ SET ";
	$sql_updt_partyRole_dupe .= "ISDUPLICATE_ = 1, DUPLICATEID_={$_POST['PARTYROLE_ID']}";
	$sql_updt_partyRole_dupe .= " WHERE PARTYROLE_ID IN ('".$toBMergedVals."')";
	/*****Query to update surviving customer in partyrole table***********/
	$sql_updt_party = "UPDATE PARTY_ SET ";
	foreach($party as $value){
		if($value=='DATEOFBIRTH_'){
			$sql_updt_party .= " $value=".(empty($_POST[$value]) ?"NULL,":"'". date("Y-m-d H:i:s", strtotime($_POST[$value]))."',");
		}else{
		 	$sql_updt_party .= "$value= ".(($_POST[$value] == '') ? "NULL" : "'".$_POST[$value]."'").",";
		}
	}
	$sql_updt_party = substr($sql_updt_party, 0, strrpos( $sql_updt_party, ",") );
	$sql_updt_party .= " WHERE PARTY_ID = ". $_POST['PARTY_ID'];

	$sql_updt_acc = "UPDATE ACCOUNT_ SET 
				`MONEYMARKETNUMBER_`='{$_POST['LEGACYMMACCOUNTNUMBER_']}', `TREASURYBILLNUMBER_`='{$_POST['LEGACYTBILLSACCOUNTNUMBER_']}'
				, `BROKERAGENUMBER_` = '{$_POST['LEGACYSTACCOUNTNUMBER_']}'";
	$sql_updt_acc .= " WHERE `CUSTOMER_PORTFOLIO_ID` = {$_POST['PARTYROLE_ID']}" ;
	/************  party and partyrole updates ends here **********************/
	/************  Update the migrationstatus to remove currently processed **********************/
	$sql_updt_migratnStat = "UPDATE MIGRATIONSTATUS_ SET `STATUS_`='1'";
	$sql_updt_migratnStat .= " WHERE `PARTYROLE_ID` = {$_POST['PARTYROLE_ID']} AND DUPLICATE_IDS='".str_replace("','",",",$toBMergedVals)."' AND STATUS_=0" ;
	/************  End: Update the migrationstatus **********************/
	$_SESSION['sql_merge'] = $sql_merge;
	$_SESSION['sql_dupe'] = $sql_dupe;
	$_SESSION['sql_updt_party'] = $sql_updt_party;
	$_SESSION['sql_updt_partyRole'] = $sql_updt_partyRole;
	$_SESSION['sql_updt_partyRole_dupe'] = $sql_updt_partyRole_dupe;
	$_SESSION['sql_updt_acc'] = $sql_updt_acc;
	$_SESSION['sql_updt_migratnStat'] = $sql_updt_migratnStat;

/*********** Debugging *************
	echo $_SESSION['sql_merge']." <br /><br />";
	echo $_SESSION['sql_dupe']." <br /><br />";
	echo $_SESSION['sql_updt_party'] ." <br /><br />";
	echo $_SESSION['sql_updt_partyRole']." <br /><br />";
	echo $_SESSION['sql_updt_partyRole_dupe']." <br /><br />";
	echo $_SESSION['sql_updt_acc'] ." <br /><br />";
	echo $_SESSION['sql_updt_migratnStat'] ." <br /><br />";
	var_dump($_POST);
	exit;
/*********** :End Debugging *************/ 
	$all4Pending = $cust->_getCusts("'".$toBMergedVals."'");
	for($i = 0; $i < count($all4Pending); $i++){
		if(!isset($all4Pending[$i]['ERPST_ACC_NOS'])){
			if(($key = array_search($all4Pending[$i]['PARTYROLE_ID'], $toBMergedArr)) !== false) {
				unset($toBMergedArr[$key]);
			}
		}
	}
	$message = "";
	if(empty($toBMergedArr)){
		$doCustMerge = doCustMerge();
		if($doCustMerge['result'] == 'Success')
			$message .= "Customer information edited successfully!<blockquote>Click on another line item to approve merge.</blockquote>";
		else
			$message .= $doCustMerge['msg'];
	}
	$table = '';
	if(count($toBMergedArr)+1 > 1){
		$message .= count($toBMergedArr)+1 ." of the customers have different accounts on them and might need to be merged!";
		$message .= '<blockquote style="color:#600; font-style:italic;">Note: if you do not merge accounts, customer information will also not be merged.</blockquote>';
		$survivingCustomers = $cust->_getCusts($_POST['PARTYROLE_ID']);
		array_unshift($survivingCustomers, $tblTitle);
		$planeTxt = array('FULLNAME_','TOTALASSETVALUE_', 'ERPST_ACC_NOS');
		$table .= '<p>ST Account Information on Selected Customer</p><form method="post" action=""><table  class="my_tables">';
		$table .= "<tr>";
		for($i = 0; $i < count($survivingCustomers); $i++){
			$table .= "<tr>";
			foreach($planeTxt as $values){
				$table .= ($i == 0) ? "<th>".$survivingCustomers[$i][$values]."</th>" : "<td>".$survivingCustomers[$i][$values]."</td>";
			}
			$table .= '</tr>';
		}
		$table .= '</table><input type="hidden" name="survivor" value="'.$survivingCustomers[1]['ERPST_ACC_NOS'].'" />';
		$toBMergedVals = implode('\',\'',$toBMergedArr);
		$all4Pending = $cust->_getCusts("'".$toBMergedVals."'");
		array_unshift($all4Pending, $tblTitle);
		$table .= '<p>Select ST Accounts found on duplicate customers to be merged with selected customer</p><table  class="my_tables">';
		$table .= "<tr><td></td>";
		for($i = 0; $i < count($all4Pending); $i++){
			$table .= "<tr>";
			$table .= ($i == 0)?"<th></th>":'<td><input type="checkbox" name="ERPST_ACC_NOS[]" value="'.$all4Pending[$i]['ERPST_ACC_NOS'].'" /></td>';
			foreach($planeTxt as $values){
				$table .= ($i == 0) ? "<th>".$all4Pending[$i][$values]."</th>" : "<td>".$all4Pending[$i][$values]."</td>";
			}
			$table .= "</tr>";
		}
		$table .= '</table><a href="" class="button mergeCustAcc" style="float:right; color:#FFF;" >Merge Customer Accounts</a></form>';
	}
	echo $message. $table;
	}
}
if(isset($_POST['survivor'])){
	$doCustMerge = doCustMerge();
	$message = "";$error=0;$success=0;
	if($doCustMerge['result'] == 'Success'){
		$message .= "Customers merged successfully!";
		//Customer info merge successful, try account merge
		$targetAcc = $cust->_getCustsAccs($_POST['survivor'], false);
		$ERPST_ACC_NOS = array_filter(($_POST['ERPST_ACC_NOS']));
		$cleanOthers = "'".implode("','", $ERPST_ACC_NOS)."'";
		$sourceAccs = $cust->_getCustsAccs($cleanOthers);
		
		$tot_autrole  = count($_SESSION['user_dets']['authrole']);
		$rand_autrole = rand(0, $tot_autrole);
		
		for($i = 0; $i < count($ERPST_ACC_NOS); $i++){
			$xml_acc_merge = '<?xml version="1.0" encoding="UTF-8"?><TRANSACTION currentLocale="'.$_SESSION['user_dets']['locale'][0].'" currentRole="'.$_SESSION['user_dets']['authrole'][$rand_autrole].'"><UPDATE  entity="AccountMerge" id="new:1398252424497:2424198645685703680"><PROPERTY bidirectional="false" composite="false" path="sourceAccount" type="FinancialAccount"><ENTITY idref="'.$sourceAccs[$i]['ACCOUNT_ID'].'" /></PROPERTY><PROPERTY bidirectional="false" composite="false" path="targetAccount" type="FinancialAccount"><ENTITY  idref="'.$targetAcc['ACCOUNT_ID'].'" /></PROPERTY><PROPERTY bidirectional="false" composite="false" path="status" type="DataMergingStatus"><ENTITY  idref="2" /></PROPERTY><PROPERTY  path="locale" type="String" value="'.$_SESSION['user_dets']['locale'][0].'" /><PROPERTY path="entryDate" type="Date" value="'.date('m/d/Y').'" /></UPDATE><ENTITYSPEC name="AccountMerge"><PROPERTY path="id" /></ENTITYSPEC></TRANSACTION>';
			$URL = "http://192.168.2.14:8082/jbi";
			$headers = array('Content-Type: application/xml',
							 'Pragma: no-cache',
							 'Cache Control: no-cache',
							 'Authorization: Basic '.$_SESSION['credentials']
							 );
			$output =  $fxns->_consumeService($URL, $xml_acc_merge, $headers);
			$query_response = simplexml_load_string($output);
			if($query_response->ERRORS){
				$error++;
			}else{
				$success++;
			}
	
		}
		if($error) $message .= "Error while trying to perform the account merge. Transaction rolled back.<br /><br />Contact admin to complete.";
		else $message .="<br />Accounts also merged successfully.<br /><br /><br />Click on another line item to approve merge.";
	}else{
		$message .= $doCustMerge['msg'];
	}
	echo $message;
}
//Function to execute customer merge queries. 
function doCustMerge(){
	global $dbo, $fxns;
	try {  
		$dbo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$dbo->beginTransaction();
			$fxns->_execQuery($_SESSION['sql_merge'], false, false);
			$fxns->_execQuery($_SESSION['sql_dupe'], false, false);
			$fxns->_execQuery($_SESSION['sql_updt_party'], false, false);
			$fxns->_execQuery($_SESSION['sql_updt_partyRole'], false, false);
			$fxns->_execQuery($_SESSION['sql_updt_partyRole_dupe'], false, false);
			$fxns->_execQuery($_SESSION['sql_updt_acc'], false, false);
			$fxns->_execQuery($_SESSION['sql_updt_migratnStat'], false, false);
		$dbo->commit();
		return array('result' => 'Success');
	} catch (Exception $e) {
		$dbo->rollBack();
		return array('result' => 'Failure', 'msg' => "The Transaction batch failed due to the following reason:<blockquote>" . $e->getMessage() . "</blockquote>");
	}
}
?>

