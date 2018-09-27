<?php 
/* @var $this \yii\web\View */
use richardfan\widget\JSRegister;
use yii\helpers\Html;
?>

<style>
    .prefix-input{
        width: 40px;
        position: absolute;
        text-align: right;
        padding: 0px;
    }
    .prefix-textbox{
        padding-left: 45px;
    }
</style>
<div>
    <select class="prefix-input form-control">
        <option value=">"> > </option>
        <option value=">="> >= </option>
        <option value="<"> < </option>
        <option value="<="> <= </option>
        <option value="=" selected> = </option>
    </select>
<input type="text" class="form-control prefix-textbox"/>
<input type="hidden" class="prefix-text-dropdown-input" name="<?=$attributeName?>"/>
</div>
<?php JSRegister::begin(['position'=> yii\web\View::POS_READY]); ?>
<script>
    $(function(){
        $(".prefix-input, .prefix-textbox").change(function(e){
            e.preventDefault();
            e.stopPropagation();
            $input = $(this).parent().find('.prefix-text-dropdown-input');
            $prefix = $(this).parent().find('.prefix-input');
            $textbox = $(this).parent().find('.prefix-textbox');
            $input.val($prefix.val()+$textbox.val());
        });
        $(".prefix-textbox").keydown(function(e){
            if(e.which == 13) {
                $input = $(this).parent().find('.prefix-text-dropdown-input');
                $prefix = $(this).parent().find('.prefix-input');
                $input.val($prefix.val()+$(this).val());
            }
        });
    });
</script>
<?php JSRegister::end(); ?>