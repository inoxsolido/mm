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
            ftp_chmod($ftp_stream, 0775, $dir);
            return true;
        }

        // otherwise recursively try to make the directory
        if ( ! $this->make_directory(dirname($dir)))
            return false;
        // final step to create the directory
        $status = ftp_mkdir($ftp_stream, $dir);
        ftp_chmod($ftp_stream, 0775, $dir);
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
    
    public function put($remote_file, $local_file, $mode, $startpos = 0) {
        if(parent::put($remote_file, $local_file, $mode, $startpos)){
            ftp_chmod($this->conn, 0755, $remote_file);
            return true;
        }
        return false;
    }
    
    public function rename($oldname, $newname) {
        if(!$this->ftp_is_dir($this->conn, $oldname) && parent::size($oldname) === -1){
            return false;
        }
        try{
            return parent::rename($oldname, $newname);
        }catch(\Exception $ex){
            return false;
        }
    }
    /**
     * Check if a directory exist.
     *
     * @param $directory
     *
     * @throws FtpException
     *
     * @return bool
     */
    public function isDir($directory)
    {
        $pwd = parent::pwd();
        if ($pwd === false) {
            throw new FtpException('Unable to resolve the current directory');
        }
        if (@parent::chdir($directory) && @parent::size($directory) === -1) {
            parent::chdir($pwd);

            return true;
        }
        parent::chdir($pwd);

        return false;
    }
}