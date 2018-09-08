$(function(){
    tagbox.tagbox(".tagbox");
    
    $(document).on('change', '#media-media_file', function(){
        if(this.files[0]){
           
            
            if($("#media-name").val() == '') $("#media-name").val($(this).val().split(/(\\|\/)/g).pop());
        }else{
            
        }
    });
    //--
    //start form
    var bar = $('.bar');
    var percent = $('.percent');
    // var status = $('#status');
    $('form').submit(function(e){
//        e.preventDefault();
////        if($("#media-name").val() === '' || $("#media-media_file").val() == '' || ($("#media-name"))){
////            return false;
////        }
        $("#loading").show();
        $('form').ajaxForm({
        data:{
            type:"1",
            submit: '1'
        },
        forceSync: true,
        beforeSerialize: function() { 
            $(".tag-input").each(function(){
               $(this).val($(this).tagvalue());
               $(this).hide();
            });
            // return false to cancel submit                  
        },
        beforeSend: function () {
            // status.empty();
            var percentVal = '0%';
            bar.width(percentVal);
            percent.html(percentVal);
            $(percent).parent().show();
            console.log("work");
            //set tag value
            //set tags label into textinput
            // var tags = [];
            // $(".tag").each(function(){
            //     tags.push($(this).text());
            // });
            // $(".tagname").val(tags.join(";")+";");


        },
        uploadProgress: function (event, position, total, percentComplete) {
            var percentVal = percentComplete + '%';
            bar.width(percentVal);
            percent.html(percentVal);
            //console.log(percentVal, position, total);
        },
        success: function (responseText) {
            var percentVal = '100%';
            bar.width(percentVal);
            percent.html(percentVal);
            window.location = responseText;
        },
        error: function(xhr){
            bar.width(0);
            percent.html(xhr.responseText);
            errorPopUp(xhr.responseText);
            $("#loading").hide();
        },
        complete: function (xhr) {
            // status.html(xhr.responseText);

            $(".tag-input").val('').show();
            //restore tag value to tag chip
            $(".tag").each(function(){
                $(this).tagvalue($(this).val());
                $(this).prop({disabled:false});
            });
            $("#loading").hide();
        }
    });
    });
    
    //--end form
    function errorPopUp(data){
        $("#error-messages").prependTo(".content");
        $('#error-messages').html(data).stop().fadeIn().animate({opacity: 1.0}, 4000).fadeOut('slow');
    }
    function successPopUp(data){
        $("#success-messages").prependTo(".content");
        $('#success-messages').html(data).stop().fadeIn().animate({opacity: 1.0}, 4000).fadeOut('slow');
    }
    
    
});


