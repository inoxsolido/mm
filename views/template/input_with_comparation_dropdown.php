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
        function getQuery(variable)
        {
            variable = encodeURIComponent(variable);
            var query = window.location.search.substring(1);
            var vars = query.split("&");
            for (var i = 0; i < vars.length; i++) {
                var pair = vars[i].split("=");
                if (pair[0] == variable) {
                    return decodeURIComponent(pair[1]);
                }
            }
            return '';
        }
        function captureUrl(){
            let query = getQuery('<?=$attributeName?>');
            console.log('<?=$attributeName?>');
            if(query == '')return false;
            let res = query.match(/^(?<prefix>=|>|>=|<=|<)(?<value>\d+)$/);
            if(!res) return false;
            let $hidden = $(document.getElementsByName('<?=$attributeName?>'));
            let mark = $hidden.siblings('.prefix-input').val(res[1]);
            let value = $hidden.siblings('.prefix-textbox').val(res[2]);
        }
        console.log(captureUrl());
    });
</script>
<?php JSRegister::end(); ?>