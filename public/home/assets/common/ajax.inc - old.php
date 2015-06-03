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
$tblTitle = array('PARTYROLE_ID'=>'Party Role ID.', 'PARTY_ID'=>'Party ID.', 'PARTYTITLE_ID'=>'Title', 'CUSTOMERID_'=>'ERP Customer ID', 'LEGACYCUSTOMERID_'=>'iBroker Customer ID', 'ST_ACC_NOS'=>'ST Account No.', 'LEGACYSTACCOUNTNUMBER_'=> 'iBroker ST Account #', 'LEGACYMMACCOUNTNUMBER_'=>'iBroker MM Account #', 'LEGACYTBILLSACCOUNTNUMBER_'=>'iBroker TBills Account #', 'TOTALASSETVALUE_'=>'Total Asset Value (NGN)', 'TYPE_ID'=>'Type ID.', 'FIRSTNAME_'=>'First Name', 'MIDDLENAME_'=>'Middle Name', 'LASTNAME_'=>'Last Name', 'INITIALS_'=>'Initials', 'GENDER_ID'=>'Gender', 'MARITALSTATUSTYPE_ID'=>'Marital Status', 'MAIDENNAME_'=>'Maiden Name', 'DATEOFBIRTH_'=>'Date Of Birth', 'PRIMARYPHONENO_'=>'Phone No. (Mobile)', 'WORKPHONENUMBER_'=>'Phone No. (Other)', 'PRIMARYEMAILADDRESS_'=>'Email Address', 'ADDRESSLINE1_'=>'Address Line 1', 'ADDRESSLINE2_'=>'Address Line 2', 'ADDRESSCITY_'=>'City', 'ADDRESSCOUNTRY_ID'=>'Country', 'ADDRESSSTATE_ID'=>'State', 'COUNTRY_ID'=>'Nationality', 'KINRELATIONSHIPTYPE_ID'=>'Next Of Kin Type', 'KINFIRSTNAME_'=>'Kin First Name', 'KINLASTNAME_'=>'Kin Last Name', 'KINOTHERNAMES_'=>'Kin Other Names', 'KINCONTACTPHONENUMBER_'=>'Kin Contact Phone No.', 'KINADDRESS_'=>'Kin Address', 'FULLNAME_'=>'Full Name');
//Array to hold column titles
$classNm = array('PARTYROLE_ID'=>'', 'PARTY_ID'=>'', 'PARTYTITLE_ID'=>'', 'CUSTOMERID_'=>'', 'LEGACYCUSTOMERID_'=>'combobox', 'LEGACYSTACCOUNTNUMBER_'=> 'combobox', 'LEGACYMMACCOUNTNUMBER_'=>'combobox', 'LEGACYTBILLSACCOUNTNUMBER_'=>'combobox', 'TOTALASSETVALUE_'=>'', 'ST_ACC_NOS'=>'', 'TYPE_ID'=>'', 'FIRSTNAME_'=>'combobox', 'MIDDLENAME_'=>'combobox', 'LASTNAME_'=>'combobox', 'INITIALS_'=>'', 'GENDER_ID'=>'', 'MARITALSTATUSTYPE_ID'=>'', 'MAIDENNAME_'=>'combobox', 'DATEOFBIRTH_'=>'combobox date', 'PRIMARYPHONENO_'=>'combobox phone', 'WORKPHONENUMBER_'=>'combobox phone', 'PRIMARYEMAILADDRESS_'=>'combobox', 'ADDRESSLINE1_'=>'combobox', 'ADDRESSLINE2_'=>'combobox', 'ADDRESSCITY_'=>'combobox', 'ADDRESSSTATE_ID'=>'', 'ADDRESSCOUNTRY_ID'=>'', 'COUNTRY_ID'=>'', 'KINRELATIONSHIPTYPE_ID'=>'', 'KINFIRSTNAME_'=>'combobox', 'KINLASTNAME_'=>'combobox', 'KINOTHERNAMES_'=>'combobox', 'KINCONTACTPHONENUMBER_'=>'combobox phone', 'KINADDRESS_'=>'combobox', 'FULLNAME_'=>'combobox');
$hiddenFields = array('PARTYROLE_ID','TYPE_ID','INITIALS_','FULLNAME_','PARTY_ID');
$planeTxt = array('CUSTOMERID_','TOTALASSETVALUE_', 'ST_ACC_NOS');
//For customer type individual, restructure the arrays
if(@$_POST['cust_type'] == '4'){
	$tblTitle = array('PARTYROLE_ID'=>'Party Role ID.', 'PARTY_ID'=>'Party ID.', 'PARTYTITLE_ID'=>'Title', 'CUSTOMERID_'=>'ERP Customer ID', 'LEGACYCUSTOMERID_'=>'iBroker Customer ID', 'ST_ACC_NOS'=>'ST Account No.', 'LEGACYSTACCOUNTNUMBER_'=> 'iBroker ST Account #', 'LEGACYMMACCOUNTNUMBER_'=>'iBroker MM Account #', 'LEGACYTBILLSACCOUNTNUMBER_'=>'iBroker TBills Account #', 'TOTALASSETVALUE_'=>'Total Asset Value (NGN)', 'TYPE_ID'=>'Type ID.', 'FULLNAME_'=>'Name', 'PRIMARYPHONENO_'=>'Contact Phone Number', 'WORKPHONENUMBER_'=>'Phone No. (Other)', 'PRIMARYEMAILADDRESS_'=>'Email Address', 'ADDRESSLINE1_'=>'Address Line 1', 'ADDRESSLINE2_'=>'Address Line 2', 'ADDRESSCITY_'=>'City', 'ADDRESSCOUNTRY_ID'=>'Country', 'ADDRESSSTATE_ID'=>'State');
	if(($key = array_search('FULLNAME_', $hiddenFields)) !== false) {
		unset($hiddenFields[$key]);
	}
}
//This is where where display the Autocomplete search.
if(isset($_POST['search_term'])){
	$msc=microtime(true);
	$results = $cust->_doCustSearch($_POST['search_term'], false);
	$msc=microtime(true)-$msc;
	$count = count($results);
	if ( $count ){
		
		$custs = $count > 30 ? '<div style="color:#999;">About ' : '';
		$custs .= $count. ' results ('.round($msc, 3).' seconds)</div>';
		$custs .= '<form method="post" action="" style="width:70%;"><table class="my_tables display clickable">';
		$custs .= '<thead><tr><th></th><th>Customer Name</th><th>Customer ID</th><th>Customer Type</th><th>Total Portfolio Value (NGN)</th></tr></thead>';
		$custs .= '<input type="hidden" name="clean_cust" value="1" />';
		$custs .= '<tbody>';
		foreach($results as $customer){
			$custs .= '<tr>
						<td><input type="checkbox" name="cust_id[]" value="'.$customer["PARTYROLE_ID"].'" data-role-type="'.$customer["TYPE_ID"].'" /></td>
						<td>'.$customer["FULLNAME_"].'</td>
						<td>'.$customer["CUSTOMERID_"].'</td>
						<td>'.$customer["custTpNm"].'</td>
						<td style="text-align:right;">'.$fxns->_formatMoney($customer["TOTALASSETVALUE_"], true).'</td>
					</tr>';
		}
		$custs .= '</tbody></table><a href="" class="button clean_cust" style="float:right;margin-top:.5%;" >Merge Customers</a></form>';
	}else{
		$custs = '<div style="font-size:1.2em;">';
		$custs .= 'Your search - <strong>'.$_POST['search_term'].'</strong> - did not match any customer.<br /><br />';
		$custs .= 'Suggestions:';
		$custs .= '<ul>
						<li>Make sure that all words are spelled correctly.</li>
						<li>Try different keywords.</li>
						<li>Try more general keywords.</li>
						<li>Try fewer keywords.</li>
					</ul></div>';
	}
	echo $custs;
}
//This builds the tables columns for the selected customers
if(isset($_POST['clean_cust'])){
	if(!@$_POST['cust_id']) echo "You have not selected any action to perform.<br />Please select an action and try again";
	else{
		//var_dump( $_POST['cust_id'] );
		//exit();
		$selected = implode('\',\'',$_POST['cust_id']);
		$selected = "'".$selected."'" ;
		$selectedCustomers = $cust->_getCusts($selected);
		array_unshift($selectedCustomers, $tblTitle);
		$table = '<form method="post" action="">';
		$table .= '<table  class="my_tables vertColNm"><tr><td></td>';
		for($i = 1; $i < count($selectedCustomers); $i++){
			if(!isset($_POST['plain'])){
				$table .= '<td><input type="radio" name="checkID" value="'.$selectedCustomers[$i]['PARTYROLE_ID'].'" /></td>';
				$table .= '<input type="hidden" name="checkIDs[]" value="'.$selectedCustomers[$i]['PARTYROLE_ID'].'" />';
			}
		}
		$table .= "</tr>";
		foreach($selectedCustomers[0] as $key => $values){
			$table .= "<tr>";
			$sql_getCombo = "SELECT ";
			if($key =='DATEOFBIRTH_'){
				$sql_getCombo .= " DATE_FORMAT(DATEOFBIRTH_, '%b-%d-%Y') AS DATEOFBIRTH_ ";
			}elseif(strpos($key ,'PHONE') !== false){
				$sql_getCombo .= " SUBSTRING_INDEX({$key}, '-', -1) {$key} ";
			}else{
				$sql_getCombo .= " {$key} ";
			}
			$sql_getCombo .= " FROM MIGRATION_ WHERE PARTYROLE_ID IN ({$selected})";
			for($i = 0; $i < count($selectedCustomers); $i++){
				if (in_array($key, $hiddenFields)) {
					$table .= !empty($_POST['plain'])?'':'<td><input type="hidden" name="'.$key.'" value="'.$selectedCustomers[$i][$key].'" /></td>';
				}else{
					if ($i == 0){
						$table .= '<td>'.$selectedCustomers[$i][$key].'</td>';
					}else{
						if(isset($_POST['plain'])){
							if($key == 'PARTYTITLE_ID'){
								$sql = "SELECT DESCRIPTION_ FROM PARTYTITLE_ WHERE PARTYTITLE_ID = '{$selectedCustomers[$i][$key]}'";
								$table .= "<td>".$fxns->_getLOVDsc($sql, 'DESCRIPTION_').'</td>';
							}elseif($key == 'GENDER_ID'){
								$sql = "SELECT DESCRIPTION_ FROM GENDER_ WHERE GENDER_ID = '{$selectedCustomers[$i][$key]}'";
								$table .= "<td>".$fxns->_getLOVDsc($sql, 'DESCRIPTION_').'</td>';
							}elseif($key == 'MARITALSTATUSTYPE_ID'){
								$sql = "SELECT DESCRIPTION_ FROM MARITALSTATUSTYPE_ WHERE MARITALSTATUSTYPE_ID = '{$selectedCustomers[$i][$key]}'";
								$table .= "<td>".$fxns->_getLOVDsc($sql, 'DESCRIPTION_').'</td>';
							}elseif($key == 'ADDRESSCOUNTRY_ID'){
								$sql = "SELECT NAME_ FROM GEOGRAPHICBOUNDARY_ WHERE TYPE_ID = (SELECT GEOGRAPHICBOUNDARYTYPE_ID FROM GEOGRAPHICBOUNDARYTYPE_ WHERE NAME_ = 'Country') AND GEOGRAPHICBOUNDARY_ID = '{$selectedCustomers[$i][$key]}'";
								$table .= "<td>".$fxns->_getLOVDsc($sql, 'NAME_').'</td>';
							}elseif($key == 'ADDRESSSTATE_ID'){
								$sql = "SELECT NAME_ FROM GEOGRAPHICBOUNDARY_ WHERE TYPE_ID = (SELECT GEOGRAPHICBOUNDARYTYPE_ID FROM GEOGRAPHICBOUNDARYTYPE_ WHERE NAME_ = 'State') AND GEOGRAPHICBOUNDARY_ID = '{$selectedCustomers[$i][$key]}'";
								$table .= "<td>".(empty($selectedCustomers[$i][$key]) ? '' : $fxns->_getLOVDsc($sql, 'NAME_')).'</td>';
							}elseif($key == 'COUNTRY_ID'){
								$sql = "SELECT NATIONALITY_ FROM GEOGRAPHICBOUNDARY_ WHERE TYPE_ID = (SELECT GEOGRAPHICBOUNDARYTYPE_ID FROM GEOGRAPHICBOUNDARYTYPE_ WHERE NAME_ = 'Country') AND GEOGRAPHICBOUNDARY_ID = '{$selectedCustomers[$i][$key]}'";
								$table .= "<td>".$fxns->_getLOVDsc($sql, 'NATIONALITY_').'</td>';
							}elseif($key == 'KINRELATIONSHIPTYPE_ID'){
								$sql_kin = "SELECT NAME_ FROM FAMILYRELATIONSHIPTYPE_ WHERE FAMILYRELATIONSHIPTYPE_ID = '{$selectedCustomers[$i][$key]}'";
								$ans = (empty($selectedCustomers[$i][$key])) ? '' : $fxns->_getLOVDsc($sql_kin, 'NAME_');
								$table .= '<td>'.$ans.'</td>';
							}else{
								$table .= '<td>'.$selectedCustomers[$i][$key].'</td>';
							}
						}else{
							if($key == 'PARTYTITLE_ID'){
								$sql_getTitle = "SELECT PARTYTITLE_ID, DESCRIPTION_ FROM PARTYTITLE_";
								$table .= "<td>".$fxns->_getLOVs($sql_getTitle, $key, "DESCRIPTION_", $key, $classNm[$key], "--Select Title--", $selectedCustomers[$i][$key])."</td>";
							}elseif($key == 'GENDER_ID'){
								$sql_getGender = "SELECT GENDER_ID, DESCRIPTION_ FROM GENDER_";
								$table .= "<td>".$fxns->_getLOVs($sql_getGender, $key, "DESCRIPTION_", $key, $classNm[$key], "--Select Gender--", $selectedCustomers[$i][$key])."</td>";
							}elseif($key == 'MARITALSTATUSTYPE_ID'){
								$sql_getMaritalStat = "SELECT MARITALSTATUSTYPE_ID, DESCRIPTION_ FROM MARITALSTATUSTYPE_";
								$table .= "<td>".$fxns->_getLOVs($sql_getMaritalStat, $key, "DESCRIPTION_", $key, $classNm[$key], "--Select Marital Status--", $selectedCustomers[$i][$key])."</td>";
							}elseif($key == 'ADDRESSCOUNTRY_ID'){
								$sql_getCountry = "SELECT GEOGRAPHICBOUNDARY_ID, NAME_ FROM GEOGRAPHICBOUNDARY_
	WHERE TYPE_ID = (SELECT GEOGRAPHICBOUNDARYTYPE_ID FROM GEOGRAPHICBOUNDARYTYPE_ WHERE NAME_ = 'Country') ORDER BY NAME_";
								$table .= "<td>".$fxns->_getLOVs($sql_getCountry, "GEOGRAPHICBOUNDARY_ID", "NAME_", $key, $classNm[$key], "--Select Country--", $selectedCustomers[$i][$key])."</td>";
							}elseif($key == 'ADDRESSSTATE_ID'){
								$chosenState = isset($selectedCustomers[$i][$key]) ? $selectedCustomers[$i][$key]: '0';
								$sql_getStates = "SELECT GEOGRAPHICBOUNDARY_ID, NAME_ FROM GEOGRAPHICBOUNDARY_
	WHERE TYPE_ID = (SELECT GEOGRAPHICBOUNDARYTYPE_ID FROM GEOGRAPHICBOUNDARYTYPE_ WHERE NAME_ = 'State') AND COUNTRY_STATES_ID =".$selectedCustomers[$i]['ADDRESSCOUNTRY_ID'];
								$table .= "<td>".$fxns->_getLOVs($sql_getStates, "GEOGRAPHICBOUNDARY_ID", "NAME_", $key, $classNm[$key], "--Select State--", $selectedCustomers[$i][$key])."</td>";
							}elseif($key == 'COUNTRY_ID'){
								$sql_getNationality = "SELECT GEOGRAPHICBOUNDARY_ID, NATIONALITY_ FROM GEOGRAPHICBOUNDARY_
	WHERE TYPE_ID = (SELECT GEOGRAPHICBOUNDARYTYPE_ID FROM GEOGRAPHICBOUNDARYTYPE_ WHERE NAME_ = 'Country') ORDER BY NATIONALITY_";
								$table .= "<td>".$fxns->_getLOVs($sql_getNationality, "GEOGRAPHICBOUNDARY_ID", "NATIONALITY_", $key, $classNm[$key], "--Select Nationality--", $selectedCustomers[$i][$key])."</td>";
							}elseif($key == 'KINRELATIONSHIPTYPE_ID'){
								$sql_getKinType = "SELECT FAMILYRELATIONSHIPTYPE_ID, NAME_ FROM FAMILYRELATIONSHIPTYPE_";
								$table .= "<td>".$fxns->_getLOVs($sql_getKinType, "FAMILYRELATIONSHIPTYPE_ID", "NAME_", $key, $classNm[$key], "--Select Relationship--", $selectedCustomers[$i][$key])."</td>";
							}elseif(strpos($key ,'PHONE') !== false){
								$phone = explode("-", $selectedCustomers[$i][$key]);
								$sql_callingCode = "SELECT DISTINCT CONCAT('+',COUNTRYCALLINGCODE_) CODE, CONCAT(NAME_,' +',COUNTRYCALLINGCODE_) NAMES_ FROM GEOGRAPHICBOUNDARY_
	WHERE TYPE_ID = (SELECT GEOGRAPHICBOUNDARYTYPE_ID FROM GEOGRAPHICBOUNDARYTYPE_ WHERE NAME_ = 'Country') ORDER BY NAME_";
								$table .= "<td>"
											.$fxns->_getLOVs($sql_callingCode, "CODE", "NAMES_", $key.'CODE', "", "--Select Calling Code--", $phone[0])
											."-"
											.$fxns->_getLOVs($sql_getCombo, $key, $key, $key, $classNm[$key], NULL, @$phone[1])
											."</td>";
							}elseif (in_array($key, $planeTxt)) {
								if($key == 'TOTALASSETVALUE_') $table .= '<td><div style="width:200px; text-align:right;">'.$fxns->_formatMoney($selectedCustomers[$i][$key], true).'</div></td>';
								else $table .= "<td>".$selectedCustomers[$i][$key].'<input type="hidden" name="'.$key.'" value="'.$selectedCustomers[$i][$key].'" /></td>';
							}else{
								$table .= "<td>".$fxns->_getLOVs($sql_getCombo, $key, $key, $key, $classNm[$key], NULL, $selectedCustomers[$i][$key])."</td>";
							}
						}
					}
				}
			}
			$table .= "</tr>";
		}
		$table .= '</table>';
		$table .= isset($_POST['plain']) ? '' :'<a href="" class="button preview" style="float:right;margin-top:-10px;" >Preview >></a>';
		$table .= '</form>';
		echo $table;
	}
}
if(isset($_POST['checkID'])){
	$result = array($_POST);
	array_unshift($result, $tblTitle);
	$table = '<form method="post" action=""><table  class="my_tables vertColNm">';
	$table .= "<tr><td></td>";
	foreach($result[1] as $key => $values){
		$table .= "<tr>";
		for($i = 0; $i < count($result); $i++){
			if (in_array($key, $hiddenFields)) {
				if ($i == 0){
						$table .= ($key=='FULLNAME_') ? '<td>Full Name</td>' : '<td></td>';
				}elseif($result[$i]['cust_type'] == '5'){ // If customer is individual customer
					$fullNm = ucwords(strtolower($result[$i]['LASTNAME_']))
							.' '.ucwords(strtolower($result[$i]['FIRSTNAME_']))
							.' '.ucwords(strtolower($result[$i]['MIDDLENAME_']));
					$table .= ($key=='FULLNAME_') 
								? '<td>'.$fullNm.'<input type="hidden" name="'.$key.'" value="'.$fullNm.'" /></td>' 
								: '<td><input type="hidden" name="'.$key.'" value="'.$result[$i][$key].'" /></td>';
				}elseif($result[$i]['cust_type'] == '4'){ // If customer is individual customer
					$table .= '<td><input type="hidden" name="'.$key.'" value="'.$result[$i][$key].'" /></td>';
				}
			}else{
				if ($i == 0 && $key != 'checkIDs' && $key != 'checkID' && $key != 'cust_type'){
						$table .= (strpos($key ,'_CODE') !== false) ? '' : '<td>'.$result[$i][$key].'</td>';
				}else{
					if($key == 'checkIDs'){
						foreach($result[1][$key] as $checkIDs){
							$table .= '<td><input type="hidden" name="checkIDs[]" value="'.$checkIDs.'" /></td>';
						}
					}elseif($key == 'checkID'){
						$table .= '';
					}elseif($key == 'cust_type'){
						$table .= '<td><input type="hidden" name="'.$key.'" value="'.@$_POST['cust_type'].'" /></td>';
					}elseif(strpos($key ,'PHONE') !== false && strpos($key ,'_CODE') === false){
						$code = $key.'CODE';
						$phone = (!empty($result[$i][$code]) && !empty($result[$i][$key])) ? $result[$i][$code].'-'.$result[$i][$key] : "";
						$table .= "<td>".$phone.'<input type="hidden" name="'.$key.'" value="'.$phone.'" /></td>';
					}elseif(strpos($key ,'_CODE') !== false){
						$table .= "";
					}elseif($key == 'PARTYTITLE_ID'){
						$sql = "SELECT DESCRIPTION_ FROM PARTYTITLE_ WHERE PARTYTITLE_ID = '{$result[$i][$key]}'";
						$table .= "<td>".$fxns->_getLOVDsc($sql, 'DESCRIPTION_').'<input type="hidden" name="'.$key.'" value="'.$result[$i][$key].'" /></td>';
					}elseif($key == 'GENDER_ID'){
						$sql = "SELECT DESCRIPTION_ FROM GENDER_ WHERE GENDER_ID = '{$result[$i][$key]}'";
						$table .= "<td>".$fxns->_getLOVDsc($sql, 'DESCRIPTION_').'<input type="hidden" name="'.$key.'" value="'.$result[$i][$key].'" /></td>';
					}elseif($key == 'MARITALSTATUSTYPE_ID'){
						$sql = "SELECT DESCRIPTION_ FROM MARITALSTATUSTYPE_ WHERE MARITALSTATUSTYPE_ID = '{$result[$i][$key]}'";
						$table .= "<td>".$fxns->_getLOVDsc($sql, 'DESCRIPTION_').'<input type="hidden" name="'.$key.'" value="'.$result[$i][$key].'" /></td>';
					}elseif($key == 'ADDRESSCOUNTRY_ID'){
						$sql = "SELECT NAME_ FROM GEOGRAPHICBOUNDARY_ WHERE TYPE_ID = (SELECT GEOGRAPHICBOUNDARYTYPE_ID FROM GEOGRAPHICBOUNDARYTYPE_ WHERE NAME_ = 'Country') AND GEOGRAPHICBOUNDARY_ID = '{$result[$i][$key]}'";
						$table .= "<td>".$fxns->_getLOVDsc($sql, 'NAME_').'<input type="hidden" name="'.$key.'" value="'.$result[$i][$key].'" /></td>';
					}elseif($key == 'ADDRESSSTATE_ID'){
						$sql = "SELECT NAME_ FROM GEOGRAPHICBOUNDARY_ WHERE TYPE_ID = (SELECT GEOGRAPHICBOUNDARYTYPE_ID FROM GEOGRAPHICBOUNDARYTYPE_ WHERE NAME_ = 'State') AND GEOGRAPHICBOUNDARY_ID = '{$result[$i][$key]}'";
						$table .= "<td>".(empty($result[$i][$key]) ? '' : $fxns->_getLOVDsc($sql, 'NAME_')).'<input type="hidden" name="'.$key.'" value="'.$result[$i][$key].'" /></td>';
					}elseif($key == 'COUNTRY_ID'){
						$sql = "SELECT NATIONALITY_ FROM GEOGRAPHICBOUNDARY_ WHERE TYPE_ID = (SELECT GEOGRAPHICBOUNDARYTYPE_ID FROM GEOGRAPHICBOUNDARYTYPE_ WHERE NAME_ = 'Country') AND GEOGRAPHICBOUNDARY_ID = '{$result[$i][$key]}'";
						$table .= "<td>".$fxns->_getLOVDsc($sql, 'NATIONALITY_').'<input type="hidden" name="'.$key.'" value="'.$result[$i][$key].'" /></td>';
					}elseif($key == 'KINRELATIONSHIPTYPE_ID'){
						$sql = "SELECT NAME_ FROM FAMILYRELATIONSHIPTYPE_ WHERE FAMILYRELATIONSHIPTYPE_ID = '{$result[$i][$key]}'";
						$table .= "<td>".(empty($result[$i][$key]) ? '' : $fxns->_getLOVDsc($sql, 'NAME_')).'<input type="hidden" name="'.$key.'" value="'.$result[$i][$key].'" /></td>';
					}else{
						$table .= '<td>'.$result[$i][$key].'<input type="hidden" name="'.$key.'" value="'.$result[$i][$key].'" /></td>';
					}
				}
			}
		}
		$table .= "</tr>";
	}
	$table .= '</table><input type="hidden" name="mergeNow" value="1"/><a href="" class="button merge" style="float:right;color:#FFF;" >Clean Customers</a></form>';
	echo $table;
}
if(isset($_POST['mergeNow'])){
	if(!isset($_SESSION['user'])){
		echo '<div style="font-size:1.5em; color:#600; margin-top:2em;">You have arrived here illegally and cannot perform this action.</div>';
		exit();
	}
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
		if($key != 'checkIDs' && $key != 'mergeNow' && $key != 'ST_ACC_NOS' && $key != 'cust_type'){
			$sql_merge .= ($key=='DATEOFBIRTH_')? " $key = '".date("Y-m-d H:i:s", strtotime($value))."'," :" $key = '$value', ";
		}		
	}
	$nmCln = ($_POST['FIRSTNAME_']!='' && strlen($_POST['FIRSTNAME_'])>3 && $_POST['LASTNAME_']!='' && strlen($_POST['LASTNAME_']) > 3)?1:0;
	$emlCln = ($_POST['PRIMARYEMAILADDRESS_'] != '') ? 1 : 0;
	$phnCln = ($_POST['PRIMARYPHONENO_'] != '') ? 1 : 0;
	$adrCln = ($_POST['ADDRESSLINE1_']!='' && $_POST['ADDRESSCITY_']!='' && $_POST['ADDRESSSTATE_ID']!='' && $_POST['ADDRESSCOUNTRY_ID']!='')?1:0;
	$custMap = ($_POST['LEGACYCUSTOMERID_'] != '') ? 1 : 0;
	$stMap = ($_POST['LEGACYSTACCOUNTNUMBER_'] != '') ? 1 : 0;

	$sql_merge .= "ISNAMEVALID_=$nmCln, ISEMAILVALID_=$emlCln, ISPHONENOVALID_=$phnCln, ISVALIDADDRESS_=$adrCln, ";
	$sql_merge .= "LEGACYCUSTOMERID_=$custMap, ISSTACCOUNTMAPPED_=$stMap, ";
	$sql_merge .= "VERSION_=VERSION_+1, LASTUPDATEBY_ = '".$_SESSION['user']."' WHERE PARTYROLE_ID = ".$_POST['PARTYROLE_ID'];
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
			$sql_updt_party .= " $value='".date("Y-m-d H:i:s", strtotime($_POST[$value]))."', ";
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
	$_SESSION['sql_merge'] = $sql_merge;
	$_SESSION['sql_dupe'] = $sql_dupe;
	$_SESSION['sql_updt_party'] = $sql_updt_party;
	$_SESSION['sql_updt_partyRole'] = $sql_updt_partyRole;
	$_SESSION['sql_updt_partyRole_dupe'] = $sql_updt_partyRole_dupe;
	$_SESSION['sql_updt_acc'] = $sql_updt_acc;

/*********** Debugging *************/ /*
	echo $_SESSION['sql_merge']." <br /><br />";
	echo $_SESSION['sql_dupe']." <br /><br />";
	echo $_SESSION['sql_updt_party'] ." <br /><br />";
	echo $_SESSION['sql_updt_partyRole']." <br /><br />";
	echo $_SESSION['sql_updt_partyRole_dupe']." <br /><br />";
	echo $_SESSION['sql_updt_acc'] ." <br /><br />";
*/
	$selectedCustomers = $cust->_getCusts("'".$toBMergedVals."'");
	for($i = 0; $i < count($selectedCustomers); $i++){
		if(!isset($selectedCustomers[$i]['ST_ACC_NOS'])){
			if(($key = array_search($selectedCustomers[$i]['PARTYROLE_ID'], $toBMergedArr)) !== false) {
				unset($toBMergedArr[$key]);
			}
		}
	}
	$message = "";
	if(empty($toBMergedArr)){
		$doCustMerge = doCustMerge();
		if($doCustMerge['result'] == 'Success')
			$message .= "Customer information edited successfully!<blockquote>Enter search term to perform another cleanup</blockquote>";
		else
			$message .= $doCustMerge['msg'];
	}
	$table = '';
	if(count($toBMergedArr)+1 > 1){
		$message .= count($toBMergedArr)+1 ." of the customers have different accounts on them and might need to be merged!";
		$message .= '<blockquote style="color:#600; font-style:italic;">Note: if you do not merge accounts, customer information will also not be merged.</blockquote>';
		$survivingCustomers = $cust->_getCusts($_POST['PARTYROLE_ID']);
		array_unshift($survivingCustomers, $tblTitle);
		$planeTxt = array('FULLNAME_','TOTALASSETVALUE_', 'ST_ACC_NOS');
		$table .= '<p>ST Account Information on Selected Customer</p><form method="post" action=""><table  class="my_tables">';
		$table .= "<tr>";
		for($i = 0; $i < count($survivingCustomers); $i++){
			$table .= "<tr>";
			foreach($planeTxt as $values){
				$table .= ($i == 0) ? "<th>".$survivingCustomers[$i][$values]."</th>" : "<td>".$survivingCustomers[$i][$values]."</td>";
			}
			$table .= '</tr>';
		}
		$table .= '</table><input type="hidden" name="survivor" value="'.$survivingCustomers[1]['ST_ACC_NOS'].'" />';
		$toBMergedVals = implode('\',\'',$toBMergedArr);
		$selectedCustomers = $cust->_getCusts("'".$toBMergedVals."'");
		array_unshift($selectedCustomers, $tblTitle);
		$table .= '<p>Select ST Accounts found on duplicate customers to be merged with selected customer</p><table  class="my_tables">';
		$table .= "<tr><td></td>";
		for($i = 0; $i < count($selectedCustomers); $i++){
			$table .= "<tr>";
			$table .= ($i == 0)?"<th></th>":'<td><input type="checkbox" name="ST_ACC_NOS[]" value="'.$selectedCustomers[$i]['ST_ACC_NOS'].'" /></td>';
			foreach($planeTxt as $values){
				$table .= ($i == 0) ? "<th>".$selectedCustomers[$i][$values]."</th>" : "<td>".$selectedCustomers[$i][$values]."</td>";
			}
			$table .= "</tr>";
		}
		$table .= '</table><a href="" class="button mergeCustAcc" style="float:right;" >Merge Customer Accounts</a></form>';
	}
	echo $message. $table;
}
if(isset($_POST['survivor'])){
	$doCustMerge = doCustMerge();
	$message = "";$error=0;$success=0;
	if($doCustMerge['result'] == 'Success'){
		$message .= "Customers merged successfully!";
		//Customer info merge successful, try account merge
		$targetAcc = $cust->_getCustsAccs($_POST['survivor'], false);
		$ST_ACC_NOS = array_filter(($_POST['ST_ACC_NOS']));
		$cleanOthers = "'".implode("','", $ST_ACC_NOS)."'";
		$sourceAccs = $cust->_getCustsAccs($cleanOthers);
		
		$tot_autrole  = count($_SESSION['user_dets']['authrole']);
		$rand_autrole = rand(0, $tot_autrole);
		
		for($i = 0; $i < count($ST_ACC_NOS); $i++){
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
		else $message .="Accounts also merged successfully.<br /><br /><br />You can perform another merge by entering your search term.";
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
		$dbo->commit();
		return array('result' => 'Success');
	} catch (Exception $e) {
		$dbo->rollBack();
		return array('result' => 'Failure', 'msg' => "The Transaction batch failed due to the following reason:<blockquote>" . $e->getMessage() . "</blockquote>");
	}
}
?>

