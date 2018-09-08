<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;

use Yii;
use yii\web\UploadedFile;

/**
 * Description of MyFileValidator
 *
 * @author Bravo
 */
class MyFileValidator extends \yii\validators\FileValidator{
    
    
    protected function validateValue($value)
    {
        if (!$value instanceof UploadedFile || $value->error == UPLOAD_ERR_NO_FILE) {
            return [$this->uploadRequired, []];
        }
        switch ($value->error) {
            case UPLOAD_ERR_OK:
                if ($this->maxSize !== null && $value->size > $this->getSizeLimit()) {
                    return [
                        $this->tooBig,
                        [
                            'file' => $value->name,
                            'limit' => $this->getSizeLimit(),
                            'formattedLimit' => Yii::$app->formatter->asShortSize($this->getSizeLimit()),
                        ],
                    ];
                } elseif ($this->minSize !== null && $value->size < $this->minSize) {
                    return [
                        $this->tooSmall,
                        [
                            'file' => $value->name,
                            'limit' => $this->minSize,
                            'formattedLimit' => Yii::$app->formatter->asShortSize($this->minSize),
                        ],
                    ];
                } elseif (!empty($this->extensions) && !$this->validateExtension($value)) {
                    return [$this->wrongExtension, ['file' => $value->name, 'extensions' => implode(', ', $this->extensions)]];
                } elseif (!empty($this->mimeTypes) && !$this->validateMimeType($value)) {
                    return [$this->wrongMimeType, ['file' => $value->name, 'mimeTypes' => implode(', ', $this->mimeTypes)]];
                }
                return null;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return [$this->tooBig, [
                    'file' => $value->name,
                    'limit' => $this->getSizeLimit(),
                    'formattedLimit' => Yii::$app->formatter->asShortSize($this->getSizeLimit()),
                ]];
            case UPLOAD_ERR_PARTIAL:
                Yii::warning('File was only partially uploaded: ' . $value->name, __METHOD__);
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                Yii::warning('Missing the temporary folder to store the uploaded file: ' . $value->name, __METHOD__);
                break;
            case UPLOAD_ERR_CANT_WRITE:
                Yii::warning('Failed to write the uploaded file to disk: ' . $value->name, __METHOD__);
                break;
            case UPLOAD_ERR_EXTENSION:
                Yii::warning('File upload was stopped by some PHP extension: ' . $value->name, __METHOD__);
                break;
            default:
                break;
        }
        return [$this->message, []];
    }
    protected function validateExtension($file)
    {
        $extension = mb_strtolower($file->extension, 'UTF-8');
        if ($this->checkExtensionByMimeType) {
            $mimeType = FileHelper::getMimeType($file->tempName, null, false);
            if ($mimeType === null) {
                return false;
            }
            $extensionsByMimeType = FileHelper::getExtensionsByMimeType($mimeType);
            if (in_array($extension, $extensionsByMimeType, true)) {
                return false;
            }
        }
        if (in_array($extension, $this->extensions, true)) {
            return false;
        }
        return true;
    }
    protected function validateMimeType($file)
    {
        $fileMimeType = FileHelper::getMimeType($file->tempName);
        foreach ($this->mimeTypes as $mimeType) {
            if ($mimeType === $fileMimeType) {
                return true;
            }
            if (strpos($mimeType, '*') !== false && preg_match($this->buildMimeTypeRegexp($mimeType), $fileMimeType)) {
                return true;
            }
        }
        return false;
    }
    
    public function clientValidateAttribute($model, $attribute, $view)
    {
        $addpoint = function($e){
            return '.'.$e;
        };
        $extension_list = $this->extensions;
        if(is_array($extension_list)){
            $extension_list = array_map($addpoint, $extension_list);
        }else{
            $extension_list = explode(' ', $this->extensions);
            $extension_list = array_map($addpoint, $extension_list);
        }
        $jsonExtension_list = json_encode($extension_list);
        return <<<JS
        console.log(value);
        
            var extension = /\.\w+$/.exec(value);
            if(extension != null) extension = String(extension).toLowerCase();
            if ( $.inArray(extension, $jsonExtension_list) != -1 ) {
                messages.push('This file not allowed on this uploader.');
                console.log($jsonExtension_list);
            }
                console.log($jsonExtension_list);
JS;
    }
}
