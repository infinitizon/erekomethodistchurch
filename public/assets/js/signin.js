// JavaScript Document

$(function(){
	$("#new_pass").keyup(function(){
		if($(this).val() != '')
		passwordStrength($(this).val());   //Call the password strength function
	});
	$("#confirm_new_pass").keyup(function(){
		if($(this).val() != $("#new_pass").val()){
			$(".passDesc").html('Mismatch');
			document.getElementById("passStrength").className = "strength1";
		}else{
			passwordStrength($(this).val());	
		}
	});
})
