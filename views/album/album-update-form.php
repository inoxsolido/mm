<?php

use yii\helpers\Html;
use yii\helpers\Url;
use richardfan\widget\JSRegister;

\app\assets\JqueryTagboxAsset::register($this);
/* @var $this yii\web\View */
/* @var $album \app\models\Album */
/* @var $media \app\models\Media */
/* @var $m \app\models\Media */

$title = $album ? $album->name : "ไม่มีชื่อ";
$this->title = "แก้ไขอัลบั้ม $title";
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => $album ? ['album', 'id' => $album->id] : '#'];
$this->params['breadcrumbs'][] = 'แก้ไขอัลบั้ม';
?>
<style>
    #tags {
        width: 100%;
        margin-top: -10px;
        float: none;
        clear: both;
    }

    .tag {
        float: left;
        margin: 5px;
        padding: 5px;
        background: #efefef;
        cursor: pointer;
        border: 1px solid #e0e0e0;
    }

    .tag::after {
        content: '\2612';
        float: right;
        display: block;
        margin: -2px 0 0 10px;
    }
</style>
<div class="album-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <!--Album-->
    <?php if ($album): ?>
        <div id="form-1">
            <input id="album-id" name="album[id]" value="<?=$album->id?>" type="hidden">
            <div class="form-group">
                <label>ชื่ออัลบั้ม</label>
                <input id="album-name" name="album[name]" value="<?= $album->name ?>" class="form-control" type="text"/>
                <span id="album-name-error" class="text-error" style="display:none; color:#FF0000">ชื่ออัลบั้มนี้มีอยู่แล้ว กรุณากำหนดชื่อใหม่</span>
            </div>

            <div class="form-group">
                <label>Tags</label>
                <div class="tagbox form-control">
                    <input id="album-tags" name="album[tags]" value="<?= $album->tags ?>" class="invisible-input add-tag" type="text"
                            placeholder="Tags (e.g., albert einstein, flying pig, mashup)"/>
                </div>
            </div>




        </div>
    <?php endif; ?>
    <!--Controller-->
    <div style="display:block; height:75px; margin-top:25px">
        <div style='float:left;'><?php if ($album): ?>
                <button id="btnapplytag" class="btn btn-success">Use Album's tag</button><?php endif; ?></div>
        <div style='float:right'>
            <button id="media-delete" class="btn btn-warning">Delete Selected Media</button>
            <a href='<?= Url::to(['album/delete', 'id'=>@$album->id])?>' id="album-delete" class="btn btn-danger" data-method='post' data-confirm="Are you sure you want to delete this item?"> Delete Album</a>
            <button id="album-save" class="btn btn-primary">Save</button>
        </div>
    </div>
    <!--Media-->
    <div id="form-2">
        <table class="table table-bordered ">
            <thead>
            <tr>
                <th style='min-width:10px'><input id="checkmainall" class="check-main-all" type="checkbox"/></th>
                <th style='min-width:75px'>Preview</th>
                <th style="width:35%">Name</th>
                <th style="width:50%">Tags</th>
                <th><input id="checkpuball" class="check-pub-all" type="checkbox"/> Public</th>
            </tr>
            </thead>
            <tbody id="media-form">
            <?php foreach ($media as $m): ?>
                <tr data-id="<?= $m->id; ?>">
                    <!--checkbox-->
                    <td><input class="check-main" type="checkbox" value="<?= $m->id; ?>"/></td>
                    <!--preview-->
                    <td><img src="<?= $m->getHttpPath() ?>" width="50"></td>
                    <!--name-->
                    <td><input value="<?= $m->name ?>" class="in-name form-control" required=""/></td>

                    <!--tag-->
                    <td>
                        <div class="tagbox form-control">
                            <input class="invisible-input in-tags" type="text" name="tagbox" value='<?= $m->tags?>'
                                   placeholder="Tags (e.g., albert einstein, flying pig, mashup)"/>
                        </div>
                    </td>
                    <!--pub -->
                    <td width="50"><input class="check-pub" type='checkbox' <?= $m->is_public ? 'checked=""' : '' ?> value="<?=$m->is_public?>"/>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>
<script>
    var url={
        album_save: "<?= Url::to(['album/update']) ?>",
        media_delete: "<?= Url::to(['media/delete-selected-media']) ?>",
        check_album_name: "<?=Url::to(['album/check-album-name']) ?>"
    }
</script>
<?php JSRegister::begin(['position'=> yii\web\View::POS_READY]); ?>
<script>
$(document).ready(function(){
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    
    function main(){
        tagbox.tagbox(".tagbox");
        //active checkbox
        $(".check-pub:first").change();
    }
    //use album's tag
    $("#btnapplytag").click(function(){
        var el_checkbox = $("#form-2").find(".check-main:checked");
        if(el_checkbox.length > 0){
            var album_tag = $("#album-tags").tagvalue();
            $(el_checkbox).each(function(){
                var checked_input = $(this).parents("tr").find(".in-tags");
                checked_input.tagvalue(album_tag);
        console.log(checked_input);
            });
        }else{
            alert("Select image before apply album's tag");
        }
    });
    
    //check duplicate album name
    $("#album-name").change(function(){
        $.ajax({
            url:url.check_album_name,
            type:'POST',
            async: false,
            data:{
                album_id: $("#album-id").val(),
                album_name: $("#album-name").val()
            }
        }).done(function(responseText){
            if(responseText === 'ok')
                $("#album-name-error").hide();
            else 
                $("#album-name-error").show();
        })
        .fail(function(jqXHR){
            errorPopUp(jqXHR.responseText)
        });
    });
    
    //checkbox main
    $("#form-2").on("change",".check-main", function(){
        var checked = $(".check-main:checked").length;
        var checkbox_all = $(".check-main").length;
        if(checked === checkbox_all){
            $(".check-main-all").prop({checked: true});
            $(".check-main-all").prop({indeterminate: false});
        }else if(checked === 0){
            $(".check-main-all").prop({checked: false});
            $(".check-main-all").prop({indeterminate: false});
        }else{
            $(".check-main-all").prop({indeterminate: true});
            $(".check-main-all").prop({checked: false});
        }
        
    });
    
    $("#form-2").on("change", ".check-main-all", function(){
        var state = $(".check-main-all").is(":checked");
        $(".check-main").prop({checked: state});
    });
    
    //checkbox pub
    $("#form-2").on("change",".check-pub", function(){
        console.log("work");
        var checked = $(".check-pub:checked").length;
        var checkbox_all = $(".check-pub").length;
        if(checked === checkbox_all){
            $(".check-pub-all").prop({checked: true});
            $(".check-pub-all").prop({indeterminate: false});
        }else if(checked === 0){
            $(".check-pub-all").prop({checked: false});
            $(".check-pub-all").prop({indeterminate: false});
        }else{
            $(".check-pub-all").prop({indeterminate: true});
            $(".check-pub-all").prop({checked: false});
        }
        
    });
    
    $("#form-2").on("change", ".check-pub-all", function(){
        var state = $(".check-pub-all").is(":checked");
        $(".check-pub").prop({checked: state});
    });

    $("#media-delete").click(function(){
        var media_id_set,
        media_checked = $(".check-main:checked");
        if(!media_checked.length){
            errorPopUp("กรุณาเลือกไฟล์ก่อน");
            return false;
        }else{
            media_id_set = $.map(media_checked, function(media){
                return $(media).val();
            });
            $.ajax({
                url: url.media_delete,
                type: "POST",
                async: false,
                data:{
                    _csrf: csrfToken,
                    media_id_set: media_id_set
                }
            }).done(function(data, textStatus, jqXHR){
//                successPopUp("ลบข้อมูลสำเร็จ");
                $(".check-main:checked").parents('tr').remove();
                window.location.reload();
            }).fail(function(jqXHR,textStatus,errorThrown){
                if(jqXHR.status === 500){
                    errorPopUp(jqXHR.responseText);
                }else{
                    errorPopUp(textStatus);
                }
            });
        }
        
    });
    $("#album-save").click(function(){
        var album={
            id: $("#album-id").val(),
            name: $("#album-name").val(),
            tags: $("#album-tags").tagvalue()
        };
        var media_set = [];
        var record = $("#media-form").find("tr");
        
        $(record).each(function(){
//            var media_id =  'x'+$(this).attr('data-id');
            var media_id =  $(this).attr('data-id');
            var name = $(this).find(".in-name").val();
            var tags = $(this).find(".in-tags").tagvalue();
            var is_public = $(this).find(".check-pub").is(":checked")?1:0;
//            media_set[media_id] = {
//                name: name,
//                tags: tags,
//                is_public: is_public
//            };
            media_set.push([media_id, name, tags, is_public]);

            
        });
        media_set.length = 1;
        console.log(media_set);
        var m = JSON.stringify(media_set);
        console.log(m);
        $.ajax({
            url: url.album_save,
            type: "POST",
            async: false,
            data:{
                _csrf: csrfToken,
                album_data: album,
                media_set: media_set
            }
        }).done(function(responseText, textStatus, jqXHR){
            successPopUp(jqXHR.responseText);
        }).fail(function(jqXHR,textStatus,errorThrown){
            if(jqXHR.status === 500){
                errorPopUp(jqXHR.responseText);
            }else{
                errorPopUp(textStatus);
        
            }
        });
        
    });
   
    main();
});
</script>
<?php JSRegister::end(); ?>


