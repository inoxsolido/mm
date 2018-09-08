<?php
/**
 * Created by PhpStorm.
 * User: Bravo
 * Date: 11/13/2017
 * Time: 9:47 PM
 */
namespace app\components;

class FTPClient extends \yii2mod\ftp\FtpClient
{
    public function make_directory($dir)
    {
        $ftp_stream = $this->conn;
        // if directory already exists or can be immediately created return true
        if ($this->ftp_is_dir($ftp_stream, $dir))return true;
        if(@ftp_mkdir($ftp_stream, $dir)){
            ftp_chmod($ftp_stream, 0777, $dir);
            return true;
        }

        // otherwise recursively try to make the directory
        if ( ! $this->make_directory(dirname($dir)))
            return false;
        // final step to create the directory
        $status = ftp_mkdir($ftp_stream, $dir);
        ftp_chmod($ftp_stream, 0777, $dir);
        return $status;
    }

    private function ftp_is_dir($ftp_stream, $dir)
    {
        // get current directory
        $original_directory = ftp_pwd($ftp_stream);
        // test if you can change directory to $dir
        // suppress errors in case $dir is not a file or not a directory
        if (@ftp_chdir($ftp_stream, $dir))
        {
            // If it is a directory, then change the directory back to the original directory
            ftp_chdir($ftp_stream, $original_directory);
            return true;
        }
        else
        {
            return false;
        }
    }
}