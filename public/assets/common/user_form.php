<?php
/*
* Include necessary files
*/
include_once 'core/init.inc.php';
$fxns = new Functions($dbo);
if(isset($_POST['formType']) && @$_POST['formType']==='register'){
    $sql_insertMember = "INSERT INTO t_wb_pro_usr("
            . "usr_ttl, fst_nm, mdl_nm, lst_nm, phn_no, eml_adr, pry_adr_ln1, pry_adr_ln2, pry_adr_cty, pry_adr_sta, pry_adr_ctr, crt_tm )"
            . "VALUES (:usr_ttl, :fst_nm, :mdl_nm, :lst_nm, :phn_no, :eml_adr, :pry_adr_ln1, :pry_adr_ln2, :pry_adr_cty, :pry_adr_sta, :pry_adr_ctr, NOW())";
    $result = $fxns->_execQuery($sql_insertMember, false, false, array(
        ':usr_ttl'=>$_POST['usr_ttl'], ':fst_nm'=>$_POST['fst_nm'], ':mdl_nm'=>$_POST['mdl_nm'], ':lst_nm'=>$_POST['lst_nm']
        , ':phn_no'=>$_POST['phn_no'], ':eml_adr'=>$_POST['eml_adr'], ':pry_adr_ln1'=>$_POST['pry_adr_ln1']
        , ':pry_adr_ln2'=>$_POST['pry_adr_ln2'], ':pry_adr_cty'=>$_POST['pry_adr_cty'], ':pry_adr_sta'=>$_POST['pry_adr_sta']
        , ':pry_adr_ctr'=>$_POST['pry_adr_ctr']
    ));
    if(!is_array($result) && $result===1){
        echo "Your registration has been received successfully.";
    }else{
        echo $result['msg'];
    }
    exit;
}

if($_POST['type'] === 'signIn'){
?>
<form action="" method="post" name="<?php echo @$_POST['type']; ?>">
    <table width="100%">
        <input type="hidden" name="formType" value="<?php echo @$_POST['type']; ?>">
        <tr><td colspan="2"><input type="text" name="usr_nm" class="text_input"  /></td></tr>
        <tr><td colspan="2"><input type="password" name="usr_pass" class="text_input"  /></td></tr>
        <tr><td><input type="checkbox" name="rem_me" />Remember me?</td><td class="float_right"><a href="" class="button doLogin" style="color: #FFF;">Sign In</a></td></tr>
    </table>
</form>
<?php
    }elseif($_POST['type'] === 'register'){
 ?>
<form action="" method="post" name="<?php echo @$_POST['type']; ?>">
    <table width="100%">
        <input type="hidden" name="formType" value="<?php echo @$_POST['type']; ?>">
        <tr>
            <td><?php 				
                    $sql_getTitles = "SELECT val_id, val_dsc FROM t_wb_lov
                                        WHERE def_id='00-TTL'";
                    echo $fxns->_getLOVs($sql_getTitles, "val_id", "val_dsc", 'usr_ttl', '', "--Select Title--", @$_POST['usr_ttl']);
                ?></td>

        </tr>
        <tr><td><input type="text" name="fst_nm" class="text_input" placeholder="Enter your First name" /></td></tr>
        <tr><td><input type="text" name="mdl_nm" class="text_input" placeholder="Enter your Middle name" /></td></tr>
        <tr><td><input type="text" name="lst_nm" class="text_input" placeholder="Enter your Last  name" /></td></tr>
        <tr><td><input type="text" name="phn_no" class="text_input" placeholder="Enter your Phone No." /></td></tr>
        <tr><td><input type="text" name="eml_adr" class="text_input" placeholder="Enter your Email Address" /></td></tr>
        <tr><td><textarea name="pry_adr_ln1"class="text_input text_area" placeholder="Residential Address Line 1"></textarea></td></tr>
        <tr><td><textarea name="pry_adr_ln2"class="text_input text_area" placeholder="Residential Address Line 2"></textarea></td></tr>
        <tr>
            <td><?php 				
                    $sql_getCountry = "SELECT val_id, val_dsc FROM t_wb_lov
                                        WHERE def_id='CTC-CTR'";
                    echo $fxns->_getLOVs($sql_getCountry, "val_id", "val_dsc", 'pry_adr_ctr', '', "--Select Country--", @$_POST['pry_adr_ctr']);
                ?></td>
        </tr>
        <tr><td><?php 				
                    $sql_getState = "SELECT val_id, val_dsc FROM t_wb_lov
                                        WHERE def_id='CTC-STA'";
                    echo $fxns->_getLOVs($sql_getState, "val_id", "val_dsc", 'pry_adr_sta', '', "--Select State--", @$_POST['pry_adr_sta']);
                ?></td></tr>
        <tr><td><input type="text" name="pry_adr_cty" class="text_input" placeholder="Residential City" /></td></tr>
        <tr><td class="float_right"><a href="" class="button doLogin" style="color: #FFF;">Submit Registration</a></td></tr>
    </table>
</form>
<?php
    }