<?php

define('MEMORY_TO_ALLOCATE', '100M');
define('DEFAULT_QUALITY',    100);
define('CURRENT_DIR',        dirname(__FILE__));
define('CACHE_DIR_NAME',     '/imagecache/');
define('CACHE_DIR',          CURRENT_DIR . CACHE_DIR_NAME);
define('DOCUMENT_ROOT',      $_SERVER['DOCUMENT_ROOT']);

class Zend_View_Helper_Blur {
    
    public function blur($image){
        
        $imagePath = $image;
        
        $opts = array(
            'w' => 1400,
            'h' => 250,
            'crop' => true,
            //'scale' => true,
        );

        if (!file_exists($imagePath)) {
            $imagePath = APPLICATION_PATH . '/../html/d3E3v8E3l5O6p7E7r3' . $imagePath;
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

        $cacheFolder = APPLICATION_PATH . '/../html/d3E3v8E3l5O6p7E7r3/cache/'; # path to your cache folder, must be writeable by web server
        $remoteFolder = $cacheFolder . 'remote/'; # path to the folder you wish to download remote images into
        $quality = 90; # image quality to use for ImageMagick (0 - 100)

        $cache_http_minutes = 20;  # cache downloaded http images 20 minutes

        $path_to_convert = Zend_Registry::get('convert'); # this could be something like /usr/bin/convert or /opt/local/share/bin/convert
        ## you shouldn't need to configure anything else beyond this point

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
                return '<img src="' . $imagePath . '" width="' . $opts['w'] . '" height="' . $opts['h'] . '" />';
            endif;
        endif;

        if (isset($opts['w'])): 
            $w = $opts['w'];
        endif;
        if (isset($opts['h'])): 
            $h = $opts['h'];
        endif;
        
        $filename = 'blured_'.md5_file($imagePath);
        
        if (!empty($w) and !empty($h)):
            $newPath = $cacheFolder . $filename . '_w' . $w . '_h' . $h . (isset($opts['crop']) && $opts['crop'] == true ? "_cp" : "") . (isset($opts['scale']) && $opts['scale'] == true ? "_sc" : "") . '.' . $ext;
        elseif (!empty($w)):
            $newPath = $cacheFolder . $filename . '_w' . $w . '.' . $ext;
        elseif (!empty($h)):
            $newPath = $cacheFolder . $filename . '_h' . $h . '.' . $ext;
        else:
            return false;
        endif;
        
        $create = true;
        
        if (file_exists($newPath) == true):
            $create = false;
            $origFileTime = date("YmdHis", filemtime($imagePath));
            $newFileTime = date("YmdHis", filemtime($newPath));
            if ($newFileTime < $origFileTime):
                $create = true;
            endif;
        endif;
        
		
        if ($create == true):
            
            
            $size = GetImageSize($imagePath);
            $mime = $size['mime'];
            
            $width  = $size[0];
            $height = $size[1];

            $maxWidth  = (isset($opts['w'])) ? (int) $opts['w'] : 0;
            $maxHeight = (isset($opts['h'])) ? (int) $opts['h'] : 0;
            
            $cropRatio = array(
                $opts['w'],
                $opts['h']
            );
            
            if (count($cropRatio) == 2)
            {
                $ratioComputed		= $width / $height;
		$cropRatioComputed	= (float) $cropRatio[0] / (float) $cropRatio[1];
                
                if ($ratioComputed < $cropRatioComputed)
		{ // Image is too tall so we will crop the top and bottom
			$origHeight	= $height;
			$height		= $width / $cropRatioComputed;
			$offsetY	= ($origHeight - $height) / 2;
		}
		else if ($ratioComputed > $cropRatioComputed)
		{ // Image is too wide so we will crop off the left and right sides
			$origWidth	= $width;
			$width		= $height * $cropRatioComputed;
			$offsetX	= ($origWidth - $width) / 2;
		}
            }
            
            $xRatio		= $maxWidth / $width;
            $yRatio		= $maxHeight / $height;
            
            if ($xRatio * $height < $maxHeight)
            { // Resize the image based on width
                $tnHeight	= ceil($xRatio * $height);
                $tnWidth	= $maxWidth;
            }
            else // Resize the image based on height
            {
                $tnWidth	= ceil($yRatio * $width);
                $tnHeight	= $maxHeight;
            }
            
            $quality = 100;
            
            $dst	= imagecreatetruecolor($tnWidth, $tnHeight);
            
            switch ($size['mime'])
            {
                case 'image/gif':
                    // We will be converting GIFs to PNGs to avoid transparency issues when resizing GIFs
                    // This is maybe not the ideal solution, but IE6 can suck it
                    $creationFunction	= 'ImageCreateFromGif';
                    $outputFunction		= 'ImagePng';
                    $mime				= 'image/png'; // We need to convert GIFs to PNGs
                    $doSharpen			= FALSE;
                    $quality			= round(10 - ($quality / 10)); // We are converting the GIF to a PNG and PNG needs a compression level of 0 (no compression) through 9
                break;

                case 'image/x-png':
                case 'image/png':
                    $creationFunction	= 'ImageCreateFromPng';
                    $outputFunction		= 'ImagePng';
                    $doSharpen			= FALSE;
                    $quality			= round(10 - ($quality / 10)); // PNG needs a compression level of 0 (no compression) through 9
                break;

                default:
                    $creationFunction	= 'ImageCreateFromJpeg';
                    $outputFunction	 	= 'ImageJpeg';
                    $doSharpen			= TRUE;
                break;
            }
            
            // Read in the original image
            $src	= $creationFunction($imagePath);
            
            $color      = false;
            
            if (in_array($size['mime'], array('image/gif', 'image/png')))
            {
                if (!$color)
                {
                    // If this is a GIF or a PNG, we need to set up transparency
                    imagealphablending($dst, false);
                    imagesavealpha($dst, true);
                }
                else
                {
                    // Fill the background with the specified color for matting purposes
                    if ($color[0] == '#')
                            $color = substr($color, 1);

                    $background	= FALSE;

                    if (strlen($color) == 6)
                            $background	= imagecolorallocate($dst, hexdec($color[0].$color[1]), hexdec($color[2].$color[3]), hexdec($color[4].$color[5]));
                    else if (strlen($color) == 3)
                            $background	= imagecolorallocate($dst, hexdec($color[0].$color[0]), hexdec($color[1].$color[1]), hexdec($color[2].$color[2]));
                    if ($background)
                            imagefill($dst, 0, 0, $background);
                }
            }
            
            // Resample the original image into the resized canvas we set up earlier
            @ImageCopyResampled($dst, $src, 0, 0, $offsetX, $offsetY, $tnWidth, $tnHeight, $width, $height);
            
            if ($doSharpen)
            {
                // Sharpen the image based on two things:
                //	(1) the difference between the original size and the final size
                //	(2) the final size
                $sharpness	= $this->findSharp($width, $tnWidth);

                $sharpenMatrix	= array(
                    array(-1, -2, -1),
                    array(-2, $sharpness + 12, -2),
                    array(-1, -2, -1)
                );
                $divisor		= $sharpness;
                $offset			= 0;
                imageconvolution($dst, $sharpenMatrix, $divisor, $offset);
            }
            
            for($t=0;$t<50;$t++)
                imagefilter($dst, IMG_FILTER_GAUSSIAN_BLUR);
            
            // Write the resized image to the cache
            $outputFunction($dst, $newPath, $quality);
            
            ImageDestroy($src);            
            ImageDestroy($dst);
            
            
            
            
            
            
            /**
            
            
            
            
            
            if (!empty($w) and !empty($h)):

                list($width, $height) = getimagesize($imagePath);
                $resize = $w;

                if ($width > $height):
                    $resize = $w;
                    if (isset($opts['crop']) && $opts['crop'] == true):
                        $resize = "x" . $h;
                    endif;
                else:
                    $resize = "x" . $h;
                    if (isset($opts['crop']) && $opts['crop'] == true):
                        $resize = $w;
                    endif;
                endif;

                if (isset($opts['scale']) && $opts['scale'] == true):
                    $cmd = $path_to_convert . " '" . $imagePath . "' -resize " . $resize . " -quality " . $quality . " " . $newPath;
                else:
                    $cmd = $path_to_convert . " '" . $imagePath . "' -resize " . $resize . " -size " . $w . "x" . $h . " xc:" . (isset($opts['canvas-color']) ? $opts['canvas-color'] : "transparent") . " +swap -gravity center -composite -quality " . $quality . " " . $newPath;
                endif;

            else:
                $cmd = $path_to_convert . " '" . $imagePath . "' -thumbnail " . (!empty($h) ? 'x' : '') . $w . "" . (isset($opts['maxOnly']) && $opts['maxOnly'] == true ? "\>" : "") . " -quality " . $quality . " " . $newPath;
            endif;
            $c = exec($cmd);
             *
             *  
             */
        endif;

        # return cache file path
        return '<img id="blur" class="background" src="'. str_replace(APPLICATION_PATH . '/../html/d3E3v8E3l5O6p7E7r3', '', $newPath) . '" width="' . $opts['w'] . '" height="' . $opts['h'] . '" />';
        
    }
    
    protected function findSharp($orig, $final) // function from Ryan Rud (http://adryrun.com)
    {
            $final	= $final * (750.0 / $orig);
            $a		= 52;
            $b		= -0.27810650887573124;
            $c		= .00047337278106508946;

            $result = $a + $b * $final + $c * $final * $final;

            return max(round($result), 0);
    } // findSharp()
    
    
}
