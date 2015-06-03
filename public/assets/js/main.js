$(function() {
    var winHeight = $(window).height() * 0.95;
    //Initiate the home animation with jCarousel
    $('.jcarousel').jcarousel({
        wrap: 'both'
    }).jcarouselAutoscroll({
        interval: 5000,
        target: '+=1',
        autostart: true
    });
    $('.jcarousel-prev').jcarouselControl({
        target: '-=1'
    });
    $('.jcarousel-next').jcarouselControl({
        target: '+=1'
    });
    $('.jcarousel-pagination').jcarouselPagination({
        item: function(page) {
            return '<a href="#' + page + '">' + page + '</a>';
        }
    });
    //End jCarousel
    $(window).scroll(function(e) {
        $el = $('ul.main_nav');
        if ($(this).scrollTop() > 80 && $el.css('position') != 'fixed') {
            $el.addClass('stick');
        }
        if ($(this).scrollTop() < 80 && $el.css('position') == 'fixed') {
            $el.removeClass('stick');
        }
    });
    $('#main_content').height() > $('#r_sidebar').height()
        ? $('#r_sidebar').css("min-height", function() {
            return $('#main_content').height();
        })
        : $('#main_content').css("min-height", function() {
            return $('#r_sidebar').height();
        });
    validateFbChng();
    $("#feedback [name=submit]").click(function(e) {
        if (!validateFeedback()) {
            e.preventDefault();
            $('#slider_container').animate({scrollTop: 0}, 'slow');
        } else {
            $('#feedback').submit();
        }
    });
    startTicker = setInterval(function() {
        tick()
    }, 1500);
    $("#ticker").hover(function() {
        window.clearInterval(startTicker)
    }, function() {
        startTicker = setInterval(function() {
            tick()
        }, 1500);
    });
    //?page=reflection&q=1 alert($.urlParam('page'))
    $(".forModal").on('click',function(e) {
        $link = $(this);
        e.preventDefault();
        var classList = this.className.split(' ');
        $.ajax({
            "type":"POST", "url":'/assets/common/user_form.php', "data":{type:classList[1]}, "success":function(data){
                $('#dialog_box').html(data)
                var formHeight = $("form[name="+classList[1]+"]").height() * 1.5;
                formHeight = (winHeight>formHeight) ? formHeight : winHeight;
                $('#dialog_box').dialog({height:formHeight});
                doUsrForm();
            }
            , beforeSend: function () { $('#dialog_box').html('<div id="loading">Loading...</div>'); }
        });
        $("#dialog_box").dialog({
            dialogClass: 'fixed-dialog'
            , modal: true
            , title: $link.text()
            , resizable: false
            , closeOnEscape: false
            , width:400
            , close: function (ev, ui) { 
                $( "#dialog_box" ).dialog('close');
            }
        });
    });
    //alert(location.pathname.split('/'));
    $(".main_nav a").filter(function(){
        return this.href == location.href.replace(/#.*/, "");
        //return location.pathname;
    }).addClass("active");
});
var doUsrForm = function(){
        $('.doLogin').on('click',function(e){
            e.preventDefault();
            if(validateRegister()){
                $.ajax({
                    "type":"POST", "url":'/assets/common/user_form.php', "data":$(this).parents('form').serialize(), "success":function(data){
                       $('#dialog_box').html(data);
                    }
                    , beforeSend: function () { $('#dialog_box').html('<div id="loading">Loading...</div>'); }
                });
            }
        });        
    };
$.urlParam = function(name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results === null) {
        return null;
    } else {
        return results[1] || 0;
    }
};

var tick = function() {
    $('#ticker li:first').slideUp(function() {
        $(this).appendTo($('#ticker')).slideDown();
    });
    //We can change slideUp with any other animation that you like, just don't forget to revert effects on element after transition. E.g
    //$('#ticker li:first').animate({'opacity':0}, 200, function () { $(this).appendTo($('#ticker')).css('opacity', 1); });
};
var validateFbChng = function() {
    $("input[name=fullname]").change(function() {
        validName($("input[name=fullname]"), 'fullname', "Full name seems invalid");
    });
    $("input[name=email]").change(function() {
        isEmailValid($("input[name=email]"), 'email', "Email address invalid, pls check input");
    });
    $("input[name=subject]").change(function() {
        validName($("input[name=subject]"), 'subject', "Subject is invalid, pls check input");
    });
    $("textarea[name=msg]").change(function() {
        notEmpty($("input[name=msg]"), 'textarea_msg', "Message body cant be empty");
    });
};
var validateFeedback = function() {
    if (validName($("input[name=fullname]"), 'fullname', "The fullname you entered seems incorrect")) {
        if (isEmailValid($("input[name=email]"), 'email', "The email entered seems incorrect")) {
            if (validName($("input[name=subject]"), 'subject', "The subject seems incorrect")) {
                if (notEmpty($("textarea[name=msg]"), 'textarea_msg', "You have not entered a message")) {
                    return true;
                }
            }
        }
    }
    return false;
};
var validateRegister = function() {
    if (validName($("input[name=fst_nm]"), 'fst_nm', "The Firstname you entered seems incorrect")) {
        if (validName($("input[name=mdl_nm]"), 'lst_nm', "The Middlename seems incorrect", true)) {
            if (validName($("input[name=lst_nm]"), 'phn_no', "The Lastname entered seems incorrect")) {
                if (isPhone($("input[name=phn_no]"), 'eml_adr', "The Phone No. entered seems incorrect", true)) {
                    if (isEmailValid($("input[name=eml_adr]"), 'eml_adr', "The Email Address entered seems incorrect")) {
                        return true;
                    }
                }
            }
        }
    }
    return false;
};
var zebraTable = function() {
    $("table.my_tables tr:even").addClass('alt');
    $("table.my_tables tr").mouseover(function() {
        $(this).addClass("over");
    }).mouseout(function() {
        $(this).removeClass("over");
    });
    if ($("table.my_tables").hasClass('vertColNm')) {
        $("table.my_tables").find('td:first-child').addClass('vertColNm');
    }
};
