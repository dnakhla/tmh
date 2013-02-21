tmh = (function($){
var $output = $('#output');



//DOM binding until I get around to learning Backbone
$('.ajax-action').bind('click', function(e){
    e.preventDefault();
    var api_request = $(this).data('action');
    switch (api_request){
    case 'add_dan':
        $.getJSON('/index.php/api/'+ api_request, tmh.user.add_dan_cb);
        break;
    case 'similiar_taste':
        $.getJSON('/index.php/api/'+ api_request, tmh.user.add_dan_cb);
        break;
    }
    
});


//object models
var user = {
 add_dan_cb: function (json){
    console.log(json);
    //call back for add_dan as defined in the DOM data attribute
    if (json.status === 'error'){
        $output.addClass('alert-error');
        $output.html('Something went wrong in adding Dan to your friends: <br/>');
        $output.append(json.message);
    }else{
        $output.addClass('alert-success');
        $output.html('Horray! you and Dan are Friends on Rdio');
    }
}
}

//alias the return 
return {
    user: user 
}

})(jQuery);

