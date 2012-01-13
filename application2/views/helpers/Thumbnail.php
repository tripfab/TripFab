<?php

class Zend_View_Helper_Thumbnail {
    
    public function thumbnail($size, $image, $class='', $cropratio='1:1'){
        
        $result = "<img src='/images/image.php?%s' class='%s' width='%s' height='%s' />";
        $width  = (is_array($size)) ? $size['width'] : $size;
        $height = (is_array($size)) ? $size['height'] : $size;
        $params = array(
            'width' => $width,
            'height' => $height,
            'cropratio'=>$cropratio,
            'image'=>$image
        );
        
        $params = http_build_query($params, null, '&amp;');
        
        return sprintf($result, $params, $class, $width, $height); die;
    }
}
