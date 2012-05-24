<?php


class Zend_View_Helper_Image {
    
    public function image($image,$class,$id){
        
        $imagePath = $image;

        if (!file_exists($imagePath)) {
            $imagePath = APPLICATION_PATH . '/../html' . $imagePath;
            if (!file_exists($imagePath)) {
                $purl = parse_url($image);
                $finfo = pathinfo($image);
                @$ext = $finfo['extension'];
                if (isset($purl['scheme']) && ($purl['scheme'] == 'http' || $purl['scheme'] == 'https')):
                    $imagePath = $image;
                else:
                    $imagePath = 'http://partners.tripfab.com' . $image;
                endif;
            }
        }

        $cacheFolder = APPLICATION_PATH . '/../html/cache/'; # path to your cache folder, must be writeable by web server
        $remoteFolder = $cacheFolder . 'remote/'; # path to the folder you wish to download remote images into

        $purl = parse_url($imagePath);
        $finfo = pathinfo($imagePath);
        @$ext = $finfo['extension'];

        # check for remote image..
        if (isset($purl['scheme']) && ($purl['scheme'] == 'http' || $purl['scheme'] == 'https')):
            # grab the image, and cache it so we have something to work with..
            list($filename) = explode('?', $finfo['basename']);
            $local_filepath = $remoteFolder . $filename;
            $download_image = true;
            if (file_exists($local_filepath)):
                $download_image = false;
            endif;
            if ($download_image == true):
				
                $ch = curl_init($imagePath);
                $fp = fopen($local_filepath, "w");

                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);

                curl_exec($ch);
                curl_close($ch);
                fclose($fp);
            endif;
			
            $imagePath = $local_filepath;
        endif;

        if (file_exists($imagePath) == false):
            $imagePath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;
            if (file_exists($imagePath) == false):
                return $imagePath;
            endif;
        else:
            $size = getimagesize($imagePath);
            if($size[0] > 900) {
                $width = 'width="900"';
            }
            return str_replace(APPLICATION_PATH . '/../html', '', $imagePath);
        endif;
        
    }
    
}