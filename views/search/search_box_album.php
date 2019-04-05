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
    <form id="search" name="search" class="form" type="POST" action="<?=Url::toRoute("search/album")?>">
        <div class=" input-group">
            
            <input id="query" name="q" type="text" class="form-control">
            <input id="pager" name="p" type="hidden" name="p"/>
            <input id="oq" name="oq" type="hidden" name="oq"/>
            <span class="input-group-btn">
                <button id="btn-search" class="btn btn-default" type="submit" ><span style="color:#000;color:rgba(0,0,0,0)">A</span><i class="glyphicon glyphicon-search"></i><span style="color:#000;color:rgba(0,0,0,0)">A</span></button>
            </span>
            
        </div>
    </form>
    </center>
</div>

