<?php


namespace app\components;

namespace app\components;

use yii\validators\Validator;

class RequiredWhenOneEmptyValidator extends Validator
{
    public $emptyAttribute;
    public function init()
    {
        parent::init();
        $this->message = 'cannot be blank.555';
    }

//    public function validateAttribute($model, $attribute)
//    {
//        /* @var $model \app\models\Media */
//        
//        $value = $model->attributes[$emptyAttribute];
//        if (empty($value) &&  !empty($model->$attribute) ) {
//            $model->addError($attribute, $this->message);
//        }
//    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        
        return <<<JS
            
            if ( value == '' && $("#thumbnail_from_video").val() == "" ) {
                messages.push('$attribute '+"$this->message");
            }
JS;
    }
}