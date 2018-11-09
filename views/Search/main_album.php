<?php
/* @var $this yii\web\View */
/* @var $setting \app\models\Settings */
use yii\helpers\Html;
use richardfan\widget\JSRegister;
use yii\helpers\Url;
app\assets\JqueryAutoCompleteAsset::register($this);
\app\assets\PlyrAsset::register($this);
?>
<div class="search-main">
    <center>
    <div class="section-search-box" style="display:inline-block; min-width:500px; max-width:50%;">
        <?=Yii::$app->controller->renderPartial('search_box_album')?>
    </div><!--include search option box-->
    </center>
    
    <div class="section-content">
        <?=Yii::$app->controller->renderPartial('search_content_album',[
            'dataProvider' => $dataProvider,
            'setting' => $setting,
        ])?>
    </div>
    
</div>
<?php JSRegister::begin(['position'=> yii\web\View::POS_READY]); ?>
<script>
    //Thumbnail-Slider
    $(function(){
        $("body").on( 'mouseover',".thumbnail-slide", function(){
            $(this).animate({
                opacity:0
            }, 1000, function(){
                $(this).css({display:'none'});
                if($(this).siblings('img:visible').length === 0){
                    $(this).parent().children('img').css({opacity:1, display: 'block'});                    
                }
            });
        });
        $("body").on('mouseleave', '.thumbnail-slide-container', function(){
            $(this).find('img').stop().clearQueue().css({opacity:1, display: 'block'});  
        });
    });
/* 
 * This javascript file contain javascript of 
 * search-main 
 * search-box 
 * search content
 */
$(window).load(function () {

    /************* MAIN **************/
    function setCookie(cname, cvalue, exdays) {
        let d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        let cookies = document.cookie;
        let pattern = /(?<name>\w+)=(?<value>\w+)/g;
        let m = '';
        while ((m = pattern.exec(cookies)) !== null) {
            // This is necessary to avoid infinite loops with zero-width matches
            if (m.index === pattern.lastIndex) {
                pattern.lastIndex++;
            }

            // The result can be accessed through the `m`-variable.
            if (m[1] === cname) {
                return m[2];
            }
        }
        return '';
    }
    function getQuery(variable)
    {
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        for (var i = 0; i < vars.length; i++) {
            var pair = vars[i].split("=");
            if (pair[0] == variable) {
                return decodeURI(pair[1]);
            }
        }
        return '';
    }
    var main;
    /*********** END MAIN ************/
    /************* SEARCH CONTENTS **********/
    function changeView(event, view_name) {
        //clear state from all btn & add active state
        $(event).parent().children().removeClass("active");
        $(event).addClass("active");
        //switch current view to target view
        if (view_name === "t") {
            $(".content-list").hide();
            $(".content-table").show();
            setCookie('searchView', 't', 7);
        } else {
            $(".content-table").hide();
            $(".content-list").show();
            setCookie('searchView', 'l', 7);
        }
    }

    /*********** END SEARCH CONTENTS ********/
    /************* SEARCH BOX ***************/
    var queryValue = $("#query").val();
    var cache = {};
    $("#query").autocomplete({
        source: function (request, response) {
            let term = request.term;

            if (term in cache) {
                response(cache[ term ]);
                return;
            }
            $.ajax({
                url: "<?= yii\helpers\Url::to(['search/suggest-word'])?>",
                type: "post",
                dataType: "json",
                data: {
                    query: $("#query").val()
                },
                success: function (data) {
                    queryValue = $("#query").val();
                    response(data);
                }
            });

        },
        minLength: 1,
        select: function (event, ui) {

        }
    });
    $("#btn-search-option").click(function () {
        $("#search-option").slideToggle();
    });
    
    $(".check-type").change(function(){
        if($(".check-type:checked").length === 0) $(".check-type").prop({checked:true});
    });
    
    function createQueryParameter(page) {
        /* OBJECTS IN ARRAY
         * query => '',
         * mediaType[] => 0-5,
         * date => '2018-05-24 12:00 AM - 2018-05-24 11:59 PM',
         * onlyAlbum => 'only', */
        let form_var = $("#search").serializeArray();
        if (!page && page !== 0)
            page = 1;
        form_var.push({name: 'p', value: page});

        return form_var;
    }

    
    var queryCache = {};
    $("#query").bind("keydown", function() {
        var regex = /^[a-zA-Z0-9ก-๙ ]+$/;
        if (regex.test($(this).val()) || $(this).val() == '') {
            queryCache[$(this).attr("id")] = $(this).val();
        }
        
    });
    $("#query").bind("keyup", function() {
        var regex = /^[a-zA-Z0-9ก-๙ ]+$/;
        if (!(regex.test($(this).val()) || $(this).val() == '')) {
            $(this).val(queryCache[$(this).attr("id")]);
        }
        
    });
    /*********** END SEARCH BOX *************/



    /************** PAGINATION *************/
    function pagination(e) {
        e.preventDefault();

        let page = $(this).data('page') + 1;

        $("#search").trigger('submit', [page]);
        return false;
    }

    /************ END PAGINATION ***********/

    /***************** RUN *****************/
    main = function (takeUrl = false) {
        //copy url params to search field
        if (takeUrl) {
            $("#query").val(getQuery('q'));
        }
        //clear old event & binding new
        $(".section-content").off();
        $(".section-content").on("click", ".btn-page", pagination);
        $(".section-content").on("click", "#btntable", function () {
            changeView(this, "t");
        });
        $(".section-content").on("click", "#btnlist", function () {
            changeView(this, "l");
        });
        let cSearchView = getCookie('searchView');
        if (cSearchView === 't') {
            $("#btntable").click();
        } else if (cSearchView === 'l') {
            $("#btnlist").click();
        } else {
            $("#btntable").click();
    }
    };
    main(true);
    /*************** END RUN **************/
});    
</script>
<?php JSRegister::end(); ?>


