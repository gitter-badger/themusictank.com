<?php

class ThumbnailLeechBehavior extends ModelBehavior {

    private $_thumbnailTypes = array(
        "_big.jpg" => 900,
        "_thumb.jpg" => 300
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

        // When the converter is not available, assume we are in dev and pull
        // the image right from tmt.com
        if(is_null(Configure::read('ConvertCMD')))
        {
            return $objTypeFolder ."/". $subfolder . "/" . $newname;
        }

        // Create the full folder path if it does not already exist.
        $this->_proofPath($path);

        // If we have a previous thumbnail, try and erase it.
        if(!is_null($previousUrl))
        {
            $this->_deletePreviousVersions($path . $ds . $previousname);
        }

        // Save new pic
        $this->_downloadRemoteImage($remoteUrl, $path . $ds . $newname . ".jpg");


        if(file_exists($path . $ds . $newname . ".jpg"))
        {
            // Small thumb and bigger header pic
            foreach($this->_thumbnailTypes as $key => $size)
            {
                // Run imagemagik in the command line as to stay more efficient resources wize.
                exec(sprintf("convert %s -resize %d %s", $path . $ds . $newname . ".jpg", $size, $path . $ds . $newname . $key ) );
            }

            exec(sprintf("convert %s -channel RGBA -blur 0x8 %s ",  $path . $ds . $newname . "_big.jpg",  $path . $ds . $newname . "_blur.jpg"));

            // Delete the file downloaded as to not take too much space
            unlink($path . $ds . $newname . ".jpg");
        }

        return $objTypeFolder ."/". $subfolder . "/" . $newname;
    }

    private function _proofPath($path)
    {
        if(!file_exists($path))
        {
            return mkdir($path, 0776, true);
        }
        return false;
    }

    private function _deletePreviousVersions($path)
    {
        if (file_exists($path . ".jpg"))
        {
            unlink($path . ".jpg");
        }

        foreach($this->_thumbnailTypes as $key => $size)
        {
            if (file_exists($path . $key))
            {
                unlink($path . $key);
            }
        }
        unlink($path . "_blur.jpg");
    }

    private function _imageCreateFromAny($filepath)
    {
        $type = exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize()
        $allowedTypes = array(
            1,  // [] gif
            2,  // [] jpg
            3,  // [] png
            6   // [] bmp
        );

        switch ($type) {
            case 1 : return imageCreateFromGif($filepath);
            case 2 : return imageCreateFromJpeg($filepath);
            case 3 : return imageCreateFromPng($filepath);
            case 6 : return imageCreateFromBmp($filepath);
        }
    }

    private function _downloadRemoteImage($remoteUrl, $path)
    {
        $ch = curl_init($remoteUrl);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $rawdata = curl_exec($ch);
        curl_close($ch);

        $fp = fopen($path, 'w');
        fwrite($fp, $rawdata);
        fclose($fp);
    }

}
