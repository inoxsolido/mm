<?php
use richardfan\widget\JSRegister;
/* @var $this yii\web\View */
use yii\helpers\Url;
\app\assets\PlyrAsset::register($this);
?>

<div id="player-backdrop" class="player-back-drop" style="display:none;"></div>
<div id="player" class="player" style="display:none;">
    <div class="player-header">
        <div class="player-icon">
            <i style="line-height:50px;font-size:20px;" class="glyphicon glyphicon-folder-open"></i>
        </div>
        <div class="player-title">

            <a href="#" class="player-album">album_name</a>
            <span>&nbsp;-&nbsp;</span>
            <span class="player-name">media_name</span>

        </div>
        <div class="player-close">
            <i style="line-height:50px;font-size:20px" class="fa fa-close"></i>
        </div>
    </div>
    <div class="player-content">
        <div class="player-left"><a href="#" style="display:table-cell; vertical-align: middle;"><i class="glyphicon glyphicon-backward"></i></a></div>
        <div class="player-media"><div class="player-media-wrapper"></div></div>
        <div class="player-right"><a href="#" style="display:table-cell; vertical-align: middle;"><i class="glyphicon glyphicon-forward"></i></a></div>
    </div>
    <div class="player-footer">
        <div class="player-tag-container">
            <div class="">
                <div class="player-tag-icon">
                    <i style="color:white;font-size:20px;" class="glyphicon glyphicon-tags"></i>
                    <span>&nbsp;&nbsp;</span>
                </div>
                <span class="player-tag">
                <a href="#">tag</a>
                <a href="#">tag</a>
                <a href="#">tag</a>
                <a href="#">tag</a>
                <a href="#">tag</a>
                <a href="#">tag</a>
                </span>
            </div>
        </div>
        <div class="player-download">
            <a href="#" class="btn btn-lg btn-default">Download</a>
        </div>
    </div>
</div>
<?php JSRegister::begin(['position'=> \yii\web\View::POS_READY]); ?>
<script>
    window.url_suggest_word = "<?=Url::to(['search/suggest-word'])?>";
    //lib
    var controls = {controls: [
        'play-large', // The large play button in the center
//        'restart', // Restart playback
//        'rewind', // Rewind by the seek time (default 10 seconds)
        'play', // Play/pause playback
//        'fast-forward', // Fast forward by the seek time (default 10 seconds)
        'progress', // The progress bar and scrubber for playback and buffering
        'current-time', // The current time of playback
        'duration', // The full duration of the media
        'mute', // Toggle mute
        'volume', // Volume control
//        'captions', // Toggle captions
//        'settings', // Settings menu
//        'pip', // Picture-in-picture (currently Safari only)
//        'airplay', // Airplay (currently Safari only)
//        'download', // Show a download button with a link to either the current source or a custom URL you specify in your options
        'fullscreen', // Toggle fullscreen
    ]};
    var controls = {controls: { muted: true, volume: true } };
    var media_set = [];  
    $("body").on("click", ".previewable", function(){
        /* step 
         * 1 check type
         * 2 collect .previewable infomation
         * 3 find this position
         * 4 create mediaElement
         * 5 put next and prevous to button
         * 6 put mediaElement to template
         * 7 show up !
         */
        let this_class = $(this).attr('class').split(" ");
        this_class = this_class.map(function(v){return "."+v;});
        let selector = this_class.join("");
        let full_link = $(this).data('flink');
        let poster = $(this).data('poster');
        let type = $(this).data('type');
        let id = $(this).data('id');
        let tags = $(this).data('tags');
        let album_name = $(this).data('album_name');
        let album_link = $(this).data('album_link');
        let name = $(this).data('name');
        let this_context = $(this).context;
        let $previewable = $(selector);
        var media_set = [];//reset
        //get position
        let prev,next,pos = 0;
        $previewable.each(function(){
            media_set.push($(this).data('id'));
        });
        pos = media_set.indexOf(id);
        prev = pos - 1,next = pos + 1;
        let mediaElement = '';
        switch(type){
            case 1:
                mediaElement = $('<video id="media-player" controls volume src="'+full_link+'" poster="'+poster+'">Your browser does not support the video tag.</video>');
                break;
            case 2:
                mediaElement = $('<div/>').append($('<img/>',{src: full_link}));
                break;
            case 3:
                mediaElement = $('<audio id="media-player" controls src="'+full_link+'" poster="'+poster+'"></audio>');
                break;
            case 4:
                mediaElement = $('<span><i class="fa fa-file-o" style="font-size:200px"></i></span>');
                break;
            case 5:
                mediaElement = $('<span><i class="fa fa-file-o" style="font-size:200px"></i></span>');
                break;
        }
        if(mediaElement === '') return false;
        
        //name
        $(".player-name").text(name);
        //album
        $player_album = $(".player-album");
        if(album_name != ''){
            $player_album.attr({href:album_link}).text(album_name).show();
            $player_album.next("span").show();
        }
        else{
            $player_album.next("span").hide();
            $player_album.attr({href:"#"}).text("").hide();
            
        }
        //tag
        $(".player-tag > *").remove();
        let splited_tag = String(tags).split(",");
        $.each(splited_tag, function(){
            $(".player-tag").append($("<a/>",{href:"<?= Url::to(['search/index','omediatag'=>1,'oalbumtag'=>1]) ?>&q="+this}).text(this));
        });
        
        //put prev, next btn
        if(prev < 0){ // disable prev btn
            $(".player-left > a").eq(0).attr({href: "javascript:;"});
            $(".player-left").hide();
        }else{
            $(".player-left > a").eq(0).attr({href: "javascript:;"}).data({id:media_set[prev]});
            $(".player-left").show();
        }
        
 
        if(next >= media_set.length){//disable next btn
            $(".player-right > a").eq(0).attr({href: "javascript:;"});
            $(".player-right").hide();
        }else{
            $(".player-right > a").eq(0).attr({href: "javascript:;"}).data({id:media_set[next]});
            $(".player-right").show();
        }
        $("#player").find(".player-media-wrapper").html(mediaElement);
        $("#player-backdrop").show();
        $("#player").show();
        Plyr.setup("#media-player", controls);
        $(".plyr__volume").removeAttr("hidden");
        $(".plyr__control").removeAttr("hidden");
        window.location.hash = id;
    });
    $("#player").on("click", ".player-left > a, .player-right > a", function(){
        if($(this).is(":visible") === false) return false;
        let id = $(this).data('id');
        if(id){
            let $target = $(".previewable[data-id="+id+"]:first");
            if($target.length) $target.click();
        }
    });
    $("#player").on("click", ".player-close", function(){
        $("#player-backdrop").hide();
        $("#player").hide();
        window.location.hash = "";
    });
    $(document).keyup(function(e){
        if(e.key === "Escape"){
            $(".player-close").click();
        }else if(e.key === "ArrowLeft"){
            $(".player-left > a").click();
        }else if(e.key === "ArrowRight"){
            $(".player-right > a").click();
        }
    });
    function captureUrl(){
        let hash = window.location.hash;
        let id = hash.replace("#","");
        if(id == "")return false;
        let $target = $(".previewable[data-id="+id+"]:first");
        if($target.length) $target.click();
    }
    captureUrl();
</script>
<?php JSRegister::end(); ?>
