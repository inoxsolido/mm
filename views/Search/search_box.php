<?php
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;

$addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;
$currentDateTime = date("Y-m-d h:i A");
?>
<div>
    <center>
    <form id="search" name="search" class="form" type="POST" action="<?=Url::toRoute("search/search")?>">
        <div class=" input-group">
            
            <input id="query" name="q" type="text" class="form-control">
            <span class="input-group-btn">
                <button id="btn-search" class="btn btn-default" type="submit" ><span style="color:#000;color:rgba(0,0,0,0)">A</span><i class="glyphicon glyphicon-search"></i><span style="color:#000;color:rgba(0,0,0,0)">A</span></button>
                <button id="btn-search-option" class="btn btn-info" type="button">Advanced&nbsp;&nbsp;<i class="glyphicon glyphicon-plus"></i></button>
            </span>
            
        </div>
        
        <!--option-->
        <div id="search-option" class="search-option " style="text-align: left; background-color:#00c0ef;display:none;color:white;padding-top:5px">
            <div class="form-group row no-margin ">
                <div class="col-md-3 no-padding-right">
                    <label for="mediaType" class="" style="">ประเภทไฟล์</label>
                </div>
                <div class="col-md-9 no-padding-left">
                    <label class="">&nbsp;&nbsp;<input type="checkbox" name="v" value="1" class="check-type" >&nbsp;Video</label>
                    <label class="">&nbsp;&nbsp;<input type="checkbox" name="i" value="1" class="check-type" >&nbsp;Image</label>
                    <label class="">&nbsp;&nbsp;<input type="checkbox" name="a" value="1" class="check-type" >&nbsp;Audio</label>
                    <label class="">&nbsp;&nbsp;<input type="checkbox" name="d" value="1" class="check-type" >&nbsp;Document</label>
                    <label class="">&nbsp;&nbsp;<input type="checkbox" name="e" value="1" class="check-type" >&nbsp;Etc</label>
                </div>
            </div><br/>
            <div class="form-group row no-margin">
                <div class="col-md-3 no-padding-right">
                    <label for="" class="">ช่วงเวลาอัพโหลด</label>
                </div>
                <div class="col-md-9">
                    <?php 
                    echo '<div class="drp-container" style="min-width:370px">';
                    echo DateRangePicker::widget([
                        'name'=>'dr',
//                        'presetDropdown'=>true,
                        'hideInput'=>true,
//                        'value'=>'',
//                        'startAttribute' => 'df',
//                        'endAttribute' => 'dt',
//                        'startInputOptions' => ['value' => $currentDateTime],
//                        'endInputOptions' => ['value' => $currentDateTime],
//                        'useWithAddon'=>true,
                        'convertFormat'=>true,
                        'pluginOptions'=>[
     
                            'timePicker'=>true,
                            "timePicker24Hour"=> true,
                            'timePickerIncrement'=>1,
                            'locale'=>['format'=>'Y-m-d H:i:s'],
                        ],
                        'pluginEvents'=>[
                            'cancel.daterangepicker' => 'function() { $("[name=dr]").val(""); $("[name=dr]").parent().find(".range-value").text(""); }',
                        ]
                    ]);
                    echo '</div>';
                    ?>
                </div>
            </div><br/>
            <div class="form-group row no-margin">
                <div class="col-md-12">
                    <label for="oAlbum" class=""><input type="checkbox" id="oAlbum" name="onlyAlbum" value="only" />&nbsp;&nbsp;&nbsp;ค้นหาเฉพาะอัลบั้ม</label>
                </div>
            </div>
        </div>
        <!--end option-->
    </form>
    </center>
</div>

