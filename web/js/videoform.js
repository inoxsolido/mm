$(function(){
    tagbox.tagbox(".tagbox");
    
    $(document).on('change', '#media-media_file', function(){
        if(this.files[0]){
            var $source = $("#video-src");
            $source[0].src = URL.createObjectURL(this.files[0]);
            $source.parent()[0].load();
            $(this).parents('.input-group').before($("#video-preview-box"));
            $("#video-preview-box").show();
            $("#media-thumbnail_file").parent().before($("#btnusethumbnail"));
            $("#btnusethumbnail").show();
            
            if($("#media-name").val() == '') $("#media-name").val($(this).val().split(/(\\|\/)/g).pop());
        }else{
            $(this).parents('.input-group').find('.file-caption-name').text("");
            $("#btnusethumbnail").hide();
        }
    });
    $("#close-video-preview").click(function(){
        $("#video-preview-box").hide();
        $("#media-media_file").val("").change();
        $("#video-el")[0].pause();
        $("#video-src")[0].src = "";
        $("#video-el")[0].load();
    });
    $("#close-thumbnail-preview").click(function(){
        $("#thumbnail-preview-box").hide();
        $("#media-thumbnail_file").val("");//clear file input
        $("#img-el").parent().find(".file-caption-name").text("");
        $("#thumbnail_from_video").val("");
    });
    // Get handles on the video and canvas elements
    var video = document.querySelector('video');
    var canvas = document.querySelector('canvas');
    // Get a handle on the 2d context of the canvas element
    var context = canvas.getContext('2d');
    // Define some vars required later
    var w, h, ratio;

    // Add a listener to wait for the 'loadedmetadata' state so the video's dimensions can be read
    video.addEventListener('loadedmetadata', function() {
            // Calculate the ratio of the video's width to height
            ratio = video.videoWidth / video.videoHeight;
            // Define the required width as 100 pixels smaller than the actual video's width
            w = video.videoWidth - 100;
            // Calculate the height based on the video's width and the ratio
            h = parseInt(w / ratio, 10);
            // Set the canvas width and height to the values just calculated
            canvas.width = w;
            canvas.height = h;			
    }, false);

    // Takes a snapshot of the video
    function snap() {
            if(!$("#media-media_file").val()){ errorPopUp('Please choose file before.'); return false;}
            // Define the size of the rectangle that will be filled (basically the entire element)
            context.fillRect(0, 0, w, h);
            // Grab the image from the video
            context.drawImage(video, 0, 0, w, h);
            
            var canvas = $("#thumbnail-canvas")[0];
            $("#media-thumbnail_file").val("");//clear file input
            $("#img-el").parent().find(".file-caption-name").text("");
            $("#thumbnail_from_video").val(canvas.toDataURL('image/jpeg',0.75)); //put file to hidden input
            $("#img-el")[0].src = $("#thumbnail_from_video").val();
            $("#thumbnail-preview-box").show();
    }

    $("#btnusethumbnail").click(snap);
    //---
    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $("#img-el")[0].src = e.target.result;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
    $(document).on("change", "#media-thumbnail_file", function(){
        if(this.files[0]){
            $("#thumbnail_from_video").val("");
            readURL(this);
            $("#thumbnail-preview-box").show();
        }else{
            
        }
    });
    //--
    //start form
    var bar = $('.bar');
    var percent = $('.percent');
    // var status = $('#status');
    $('form').submit(function(e){
        e.preventDefault();
        $("#loading").show();
        $('form').ajaxForm({
        data:{
            type:"1",
            submit: '1'
        },
        forceSync: true,
        beforeSerialize: function($form, options) { 
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
            successPopUp('Upload success');
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
    
    
    
});


