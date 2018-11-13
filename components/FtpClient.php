<?php
/**
 * Created by PhpStorm.
 * User: Bravo
 * Date: 11/13/2017
 * Time: 9:47 PM
 */
namespace app\components;

class FtpClient extends \yii2mod\ftp\FtpClient
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
    
    public function isEmpty($directory){
        $items = $this->nlist($directory);
        return count($items)?false:true;
    }
    
    /**
     * Returns a list of files in the given directory
     *
     * @param string $directory The directory, by default is "." the current directory
     * @param bool $recursive
     * @param callable|string $filter A callable to filter the result, by default is asort() PHP function.
     * The result is passed in array argument, must take the argument by reference!
     * The callable should proceed with the reference array because is the behavior of several PHP sorting functions (by reference ensure directly the compatibility with all PHP sorting functions)
     *
     * @throws FtpException
     *
     * @return array
     */
    public function nlist($directory = '.', $recursive = false, $filter = 'sort')
    {
        if (!$this->isDir($directory)) {
            return false;
        }
        $files = $this->getWrapper()->nlist($directory);
        if ($files === false) {
            return false;
        }
        $result = [];
        $dir_len = strlen($directory);
        // if it's the current
        if (false !== ($kdot = array_search('.', $files))) {
            unset($files[$kdot]);
        }
        // if it's the parent
        if (false !== ($kdot = array_search('..', $files))) {
            unset($files[$kdot]);
        }
        if (!$recursive) {
            foreach ($files as $file) {
                $result[] = $file;
            }
            // working with the reference (behavior of several PHP sorting functions)
            $filter($result);

            return $result;
        }
        // utils for recursion
        $flatten = function (array $arr) use (&$flatten) {
            $flat = [];
            foreach ($arr as $k => $v) {
                if (is_array($v)) {
                    $flat = array_merge($flat, $flatten($v));
                } else {
                    $flat[] = $v;
                }
            }

            return $flat;
        };
        foreach ($files as $file) {
            $file = $directory . '/' . $file;
            // if contains the root path (behavior of the recursivity)
            if (0 === strpos($file, $directory, $dir_len)) {
                $file = substr($file, $dir_len);
            }
            if ($this->isDir($file)) {
                $result[] = $file;
                $items = $flatten($this->nlist($file, true, $filter));
                foreach ($items as $item) {
                    $result[] = $item;
                }
            } else {
                $result[] = $file;
            }
        }
        $result = array_unique($result);
        $filter($result);

        return $result;
    }
}