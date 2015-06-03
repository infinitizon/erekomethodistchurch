// JavaScript Document

$(function(){
	//
	if($.fn.editable){
		$.fn.editable.defaults.mode = 'inline' 
		$('.txtEdit').editable({
			type: 'text', url: '/students/assets/common/ajax'
		});
		$('.txtAreaEdit').editable({
			type: 'textarea', url: '/students/assets/common/ajax'
		});
	}
	$('#navigation').find('li:has(ul)').click( function(event) {
		if (this == event.target) {
			$(this).toggleClass('expanded');
			$(this).children('ul').toggle('medium');
		}
		return false;
	}).addClass('collapsed').children('ul').hide();

	$('table.my_tables tr:even').addClass('alt'); 
	$("table.my_tables tr").mouseover(function(){
		$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");
	});
	$("a.delete").on("click",function(event){
		return confirm($(this).attr('data'));
	});
	//Close any notification area showing
	setTimeout(closeNotification, 5000);
	$("div#notification .close").click(function(){
		closeNotification();
	});
});
var closeNotification = function(){
	if ( $('div#notification').css('display') == 'block' )
		$('div#notification').slideUp('slow');
}
function passwordStrength(password){
	/**************  Function to help get password strength  **********/
	var desc = new Array();	
	desc[0] = "Very Weak";
	desc[1] = "Weak";
	desc[2] = "Better";
	desc[3] = "Medium";
	desc[4] = "Strong";
	desc[5] = "Strongest";
	
	var score   = 0;
	//if password bigger than 6 give 1 point
	if (password.length > 6) score++;
	//if password has both lower and uppercase characters give 1 point      
	if ( ( password.match(/[a-z]/) ) && ( password.match(/[A-Z]/) ) ) score++;
	//if password has at least one number give 1 point
	if (password.match(/\d+/)) score++;
	//if password has at least one special caracther give 1 point
	if ( password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) ) score++;
	//if password bigger than 12 give another 1 point
	if (password.length > 12) score++;
	$("#passDesc").html(desc[score]);
	document.getElementById("passStrength").className = "strength" + score;
}
