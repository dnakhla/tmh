//namespace
var tmh = (function($){
//lets decide where we're gonna output the result here and store it
// in real life we'd be using templating like handlebars
var $output = $('#output'); 

//DOM binding until I get around to learning Backbone
$('.ajax-action').bind('click', function(e){
    e.preventDefault();
    var api_request = $(this).data('action');
    switch (api_request){
        case 'add_dan':
            $('#call').hide();
            $.getJSON('/index.php/api/'+ api_request, tmh.user.add_dan_cb);
            break;
        case 'compare_with_dan':
            $('#call').hide();
            $.getJSON('/index.php/api/'+ api_request, tmh.user.compared_with_dan);
            break;
        case 'call_dan':
            var widget = 'Put your info in below and call Dan for free<br><object type="application/x-shockwave-flash" data="https://clients4.google.com/voice/embed/webCallButton" width="230" height="85"><param name="movie" value="https://clients4.google.com/voice/embed/webCallButton" /><param name="wmode" value="transparent" /><param name="FlashVars" value="id=100e6e77a7abc48bf280ad7ed649196c00a637f2&style=0" /></object>';
            $('#call').html(widget).fadeIn();
            break;
    }
    
});

//object models which contain our CBs
var user = {
 update_dom: function (json){
    $('.user_picture').attr('src',json.result.icon);
 },
 add_dan_cb: function (json){
    //call back for add_dan as defined in the DOM data attribute
    if (json.status === 'error'){
        $output.addClass('alert-error');
        $output.html('Something went wrong in adding Dan to your friends: <br/>'+json.message);
    }else{
        $output.addClass('alert-success');
        $output.removeClass('alert-error');
        $output.html('Horray! you and Dan are now Friends on Rdio!');
    }
},
compared_with_dan: function (json){
    console.log(json);
    //again handlebars or some template would come in handy here
    //i would never acutally put all this text here
    //since both scenerios call this long script, lets put it in a function
    function print_list(json){
        $output.append('<br/><br/>Dan Listens to: <ul>');
        $.each(json.Dan, function (index, article) {
            $output.append('<li>'+ article.name + '<small> (<a href='+article.shortUrl+'>'+article.shortUrl+'</a>)</small></li>');
        });
        $output.append('</ul>');
        $output.append('<br/><br/>You Listen to: <ul>');
        $.each(json.You, function (index, article) {
          $output.append('<li>'+ article.name + '<small> (<a href='+article.shortUrl+'>'+article.shortUrl+'</a>)</small></li>');
      });
        $output.append('</ul>');
    }
    if (json.Common.length < 1){
        $output.addClass('alert-error');
        $output.html('<i class="icon-music"></i> You guys don\'t listen to the same people :( ');
        print_list(json);

    }else{
        $output.removeClass('alert-error');
        $output.addClass('alert-success');
        $output.html('You guys listen to the same people! <ul>');
        $.each(json.Common, function (index, article) {
          $output.append('<li>'+ article.name + '<small> (<a href='+article.shortUrl+'>'+article.shortUrl+'</a>)</small></li>');
      });
        $output.append('</ul>');
        print_list(json);
    }
}

}


//alias the return 
return {
    user: user 
}

})(jQuery);//pass jquery back as a param


//docready calls
$(document).ready( $.getJSON('/index.php/api/get_current_user', tmh.user.update_dom) )

