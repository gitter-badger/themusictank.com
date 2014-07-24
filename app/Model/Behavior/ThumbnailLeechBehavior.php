<?php

class ThumbnailLeechBehavior extends ModelBehavior {

    private $_thumbnailTypes = array(
        "_big.jpg" => 600,
        "_thumb.jpg" => 250
    );

    public function getImageFromUrl($model, $remoteUrl, $previousUrl = null)
    {
        ini_set('memory_limit', '64M');

        $ds             = DIRECTORY_SEPARATOR;
        $newname        = md5($remoteUrl);
        $newnameExt     = $newname . ".jpg";
        $previousname   = md5($previousUrl);
        $objTypeFolder  = substr(md5($model->name), 0, 5);
        $subfolder      = substr($newname, 0, 1);
        $imagesRoot     = WWW_ROOT . "img" . $ds . "cache";
        $path           = $imagesRoot . $ds . $objTypeFolder . $ds . $subfolder;

        // Create the full folder path if it does not already exist.
        if(!file_exists($path))
        {
            mkdir($path, 0776, true);
        }

        // If we have a previous thumbnail, try and erase it.
        if(!is_null($previousUrl))
        {
            if (file_exists($path . $ds . $previousname . ".jpg"))
            {
                unlink($path . $ds . $previousname . ".jpg");
            }

            foreach($this->_thumbnailTypes as $key => $size)
            {
                if (file_exists($path . $ds . $previousname . $key))
                {
                    unlink($path . $ds . $previousname . $key);
                }
            }
        }

        // Save new pic
        $ch = curl_init($remoteUrl);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $rawdata = curl_exec($ch);
        curl_close($ch);

        $fp = fopen($path . $ds . $newname . ".jpg", 'w');
        fwrite($fp, $rawdata);
        fclose($fp);

        // Open the bigass image and save thumbnails from it (if we have enough buffering to do so)
        if(filesize($path . $ds . $newname . ".jpg") < (54 * 1024))
        {
            $originalImg = $this->_imageCreateFromAny($path . $ds . $newname . ".jpg");
            $originalSize = GetimageSize($path . $ds . $newname . ".jpg");
            $originalX = ImagesX($originalImg);
            $originalY = ImagesY($originalImg);

            // Small thumb and bigger header pic
            foreach($this->_thumbnailTypes as $key => $size)
            {
                $ratio = 1;
                if($originalSize[0] > $size)
                {
                    $ratio = $size / $originalSize[0];
                }
                elseif ($originalSize[1] > $size)
                {
                    $ratio = $size / $originalSize[1];
                }
                $resizeWidth = (int)($originalSize[0] * $ratio);
                $resizeHeight = (int)($originalSize[1] * $ratio);

                $smallThumb = ImageCreateTrueColor($resizeWidth, $resizeHeight);
                ImageCopyResampled($smallThumb, $originalImg, 0, 0, 0, 0, $resizeWidth+1, $resizeHeight+1, $originalX, $originalY);
                ImageJPEG($smallThumb, $imagesRoot . $ds . $objTypeFolder . $ds . $subfolder . $ds . $newname . $key);
                ImageDestroy($smallThumb);
            }

            ImageDestroy($originalImg);

            return $objTypeFolder . $ds . $subfolder . $ds . $newname;
        } else {
            $this->log("[BIG IMAGE] " . $path . $ds . $newname . ".jpg is too big to be parsed by the cron.");
        }
    }


    private function _imageCreateFromAny($filepath) {
        $type = exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize()
        $allowedTypes = array(
            1,  // [] gif
            2,  // [] jpg
            3,  // [] png
            6   // [] bmp
        );
        if (!in_array($type, $allowedTypes)) {
            return false;
        }
        switch ($type) {
            case 1 :
                $im = imageCreateFromGif($filepath);
            break;
            case 2 :
                $im = imageCreateFromJpeg($filepath);
            break;
            case 3 :
                $im = imageCreateFromPng($filepath);
            break;
            case 6 :
                $im = imageCreateFromBmp($filepath);
            break;
        }
        return $im;
    }

}
