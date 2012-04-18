<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Xhr
 *
 * @author magentodeveloper
 */
class WS_Uploader_Service_Xhr {
    
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    public function save($path) {    
        $input = fopen("php://input", "r");
        $temp  = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);
        
        if ($realSize != $this->getSize()){            
            return false;
        }
        //var_dump($path); die; 
        if(file_exists($path)) 
            unlink($path);
        
        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);
        
        return true;
    }
    
    public function getName() {
        return $_GET['qqfile'];
    }
    
    public function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            throw new Exception('Getting content length is not supported.');
        }      
    } 
    
}
