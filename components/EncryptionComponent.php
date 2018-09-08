<?php

namespace app\components;

use yii\base\Component;

class EncryptionComponent extends Component
{
    /**
     * This method use to encryp password with MD5
     * @param string $password
     * @return string encrypted password
     */
    public function encryptUserPassword($password)
    {
        return hash('md5', 'tgde'.$password.'tdge');
    }
    
    public function encryptFtpPassword($password)
    {
        return base64_encode(base64_encode(base64_encode($password)));
    }
    
    public function decryptFtpPassword($entryped_password)
    {
        return base64_decode(base64_decode(base64_decode($entryped_password)));
    }
}
