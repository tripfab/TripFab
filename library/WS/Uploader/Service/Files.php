<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Files
 *
 * @author magentodeveloper
 */
class WS_Uploader_Service_Files {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    public function save($path) {
        if(file_exists($path)) 
            unlink($path);
        
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
            return false;
        }
        return true;
    }
    
    public function getName() {
        return $_FILES['qqfile']['name'];
    }
    
    public function getSize() {
        return $_FILES['qqfile']['size'];
    }
}
