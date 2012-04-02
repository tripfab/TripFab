<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Service
 *
 * @author magentodeveloper
 */


class WS_Uploader_Service {
    
    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;

    public function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
        
        $this->checkServerSettings();       

        if (isset($_GET['qqfile'])) {
            require "WS/Uploader/Service/Xhr.php";
            $this->file = new WS_Uploader_Service_Xhr();
        } elseif (isset($_FILES['qqfile'])) {
            require "WS/Uploader/Service/Files.php";
            $this->file = new WS_Uploader_Service_Files();
        } else {
            $this->file = false; 
        }
    }
    
    private function checkServerSettings(){        
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        
        
        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';             
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");    
        }        
    }
    
    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    }
    
    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    public function handleUpload($paths, $uploadDirectory, $replaceOldFile = FALSE){        
        if(!is_dir(str_replace('//','/',$paths['public']))){
            mkdir(str_replace('//','/',$paths['public']), 0777, true);
        }
        if(!is_dir(str_replace('//','/',$paths['public2']))){
            mkdir(str_replace('//','/',$paths['public2']), 0777, true);
        }
        
        if (!is_writable($paths['public'])){
            return array('error' => "Server error. Upload directory isn't writable.");
        }
        if (!is_writable($paths['public2'])){
            return array('error' => "Server error. Upload directory isn't writable.");
        }
        
        if (!$this->file){
            return array('error' => 'No files were uploaded.');
        }
        
        $size = $this->file->getSize();
        
        if ($size == 0) {
            return array('error' => 'File is empty');
        }
        
        if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }
        
        $pathinfo = pathinfo($this->file->getName());
        $ext = $pathinfo['extension'];

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }
        
        if ($this->file->save($paths['tagetfile'])){
            exec("cp {$paths['tagetfile']} {$paths['public2']}");
            return array('success'=>true);
        } else {
            return array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
        
    }    
    
}