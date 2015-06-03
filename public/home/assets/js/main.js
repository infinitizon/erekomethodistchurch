/* 
 Created on : Aug 28, 2014, 10:43:18 AM
 Author     : infinitizon
 */

$(function() {
    $("ul.main_nav a").on('click', function(e) {
        e.preventDefault();
        var data = $.param({getPage: 1, link: $(this).attr('href')});
        $.ajax({
            "type": "POST", "url": 'assets/common/ajax.inc.php', "data": data, "success": function(data) {
                $('#content').html(data);
                enCkeditor();enUploadify();update();deleteRec();createRec();paging();enDate();
            }
            , beforeSend: function() {
                $('#content').html('<div id="loading">Loading...</div>');
            }
        });
    });
    $("ul.main_nav >li > a").on('click', function(e) {
        e.preventDefault();
        $("ul.main_nav ul").slideUp();  //slide up all the link lists
        //slide down the link list below this clicked - only if its closed
        if (!$(this).next().is(":visible")) {
            $(this).next().slideDown();
        }
    });
    $('.icon-user').on('click', function(e) {
        e.preventDefault();
        $('div#user_dets').toggle();
    });
    $('.icon-power-off').on('click', function(e) {
        $('div#user_dets').hide();
    });
    $(document).mouseup(function(e) {
        var container = $("div#user_dets");
        if (!container.is(e.target) && container.has(e.target).length === 0) { // if the target of the click isn't the container nor a descendant of the container
            container.hide();
        }
    });
});
var enCkeditor = function() {
    $('textarea.ckeditor').ckeditor();
};
var enDate = function(){
    $( "input[name*='date']" ).datepicker({ changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd',yearRange: '-100:+0'});
}
var enUploadify = function() {
    $('#file_upload').uploadify({
        'formData': {
            'upload_tp': $('select[name=upload_tp]').val()
        }
        , 'fileSizeLimit' : '0'
        , 'swf': '/assets/images/uploadify/uploadify.swf'
        , 'uploader': 'assets/common/ajax.inc.php'
        , 'onUploadSuccess': function(file, data, response) {
            $('#queue').html(data);
        }
    });
};
var update = function(){
    $('a.update').on('click',function(e){
        e.preventDefault();
         $.ajax({
            "type": "POST", "url": 'assets/common/ajax.inc.php', "data": $(this).parents('form').serializeArray(), "success": function(data) {
                $('#content').html(data);
                update();enDate();
            }
            , beforeSend: function() {
                $('#content').html('<div id="loading">Loading...</div>');
            }
        });
    });
};
var deleteRec =function(){
    $('a.delete').on('click',function(e){
        e.preventDefault();
        if(confirm('Are you sure you want to delete this record?')){
            $.ajax({
               "type": "POST", "url": 'assets/common/ajax.inc.php', "data":$(this).siblings('input[name=dets]').serialize(), "success": function(data) {
                   $('#content').html(data);
                   update();enCkeditor();enDate();
               }
               , beforeSend: function() {
                   $('#content').html('<div id="loading">Loading...</div>');
               }
           });
       }
    });
};
var createRec = function(){
    $('a.create').on('click',function(e){
        e.preventDefault();
        $.ajax({
            "type": "POST", "url": 'assets/common/ajax.inc.php', "data":$(this).siblings('input[name=dets]').serialize(), "success": function(data) {
                $('#content').html(data);
                update();enCkeditor();enDate();createRec();
            }
            , beforeSend: function() {
                $('#content').html('<div id="loading">Loading...</div>');
            }
        });
    });
};
var paging = function(){
    $('a[currentpage]').on('click',function(e){
        e.preventDefault();
        var data = $(this).attr('href').substring(1);
        $.ajax({
            "type": "POST", "url": 'assets/common/ajax.inc.php', "data":data, "success": function(data) {
                $('#content').html(data);
                update();enCkeditor();paging();enDate();createRec();deleteRec();
            }
            , beforeSend: function() {
                $('#content').html('<div id="loading">Loading...</div>');
            }
        });
    });
};