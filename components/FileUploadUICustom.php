<?php
/**
 * Created by PhpStorm.
 * User: Bravo
 * Date: 11/12/2017
 * Time: 1:21 PM
 */

namespace app\components;


class FileUploadUICustom extends \dosamigos\fileupload\FileUploadUI
{
    public $formView = '@app/views/blueimp/form';
    public $uploadTemplateView = '@app/views/blueimp/upload';
    public $downloadTemplateView = '@app/views/blueimp/download';
    public $galleryTemplateView = '@app/views/blueimp/gallery';
}