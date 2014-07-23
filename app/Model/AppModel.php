<?php
App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Contains application-wide models methods
 *
 * @package       app.Model
 */
class AppModel extends Model {

	private $_thumbnailTypes = array(
		"_big.jpg" => 600,
		"_thumb.jpg" => 250
	);


    /**
     * Creates a unique slug based on the $string passed.
     * Unicity is based on a slug field in the table.
     *
     * @param string $string Desired slug label
     * @return string A unique, tested, slug for the current model
     */
    public function createSlug ($string, $ensureUnique = true)
    {
        $slug = strtolower(Inflector::slug ($string,'-'));
        return ($ensureUnique) ? $this->_makeSlugUnique($slug) : $slug;
    }

    /**
     * Checks if a slug value needs to be created. The function uses field names
     * specified by the $fields parameter to obtain a value to convert into a valid slug
     *
     * @param array $fields a list of possible slug sources
     * @return boolean True on success, false on failure
     */
    public function checkSlug($fields)
    {
        $slugFieldValue = "";

        // Only process if there is not a slug already set.
        if(!isset($this->data[$this->alias]['slug']))
        {
            foreach($fields as $field)
            {
                if(!empty($this->data[$this->alias][$field]))
                {
                    $slugFieldValue = $this->data[$this->alias][$field];
                    break;
                }
            }

            // Fallback to model alias when we have not found a value
            if(strlen($slugFieldValue) < 1)
            {
                $slugFieldValue = $this->alias;
            }

            $this->data[$this->alias]['slug'] = $this->createSlug($slugFieldValue);
            return true;
        }

        return false;
    }

    // Creates unique slugs, but takes into account
    // the values created by the current array instead of
    // only db values
    public function batchSlugs($names)
    {
    	$batchSlugs = array();

    	foreach ($names as $name)
    	{
            $name = utf8_encode($name);

    		// Create a unique slug based on db values
			$slug = $this->createSlug($name);
			$newSlug = $slug;

			// Double check the current set for duplicates
			// and loop until we have a available key
	 		if(in_array($slug, $batchSlugs))
	        {
	            $count = 0;
	            while(in_array($newSlug, $batchSlugs))
	            {
		            if (!preg_match ('/-{1}[0-9]+$/', $slug ))
		            {
		                $newSlug .= '-' . ++$count;
		            }
		            else
		            {
		                $newSlug = preg_replace ('/[0-9]+$/', ++$count, $slug );
		            }
	            }
	        }
            $batchSlugs[] = $newSlug;
    	}

        return $batchSlugs;
    }



    /**
     * Dispatches a preformated event.
     *
     * @param string $name The name of event triggered
     * @return void
     */
    function dispatchEvent($name)
    {
        $eventName = array("Model", $this->name, $name);
        CakeEventManager::instance()->dispatch(new CakeEvent(implode(".", $eventName), $this, $this->data));
    }

    public function getImageFromUrl($remoteUrl, $previousUrl = null)
    {
    	ini_set('memory_limit', '64M');

        $ds             = DIRECTORY_SEPARATOR;
        $newname        = md5($remoteUrl);
        $newnameExt     = $newname . ".jpg";
        $previousname   = md5($previousUrl);
        $objTypeFolder  = substr(md5($this->name), 0, 5);
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

        // Open the bigass image and save thumbnails from it
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

    private function _makeSlugUnique($slug)
    {
        $params = array(
            "fields"        => array($this->name.".slug"),
            "conditions"    => array()
        );
        $params['conditions'][$this->name.'.slug'] = $slug;

        // Speed up the query by removing all related model searches
        $oldRecursive = $this->recursive;
        $this->recursive = -1;

        // I bet this could be improved. For now, loop until we have a unique slug
        // in the model's table.
        $i = 0;
        while (count($this->find ('all',$params)))
        {
            if (!preg_match ('/-{1}[0-9]+$/', $slug ))
            {
                $slug .= '-' . ++$i;
            }
            else
            {
                $slug = preg_replace ('/[0-9]+$/', ++$i, $slug );
            }

            $params['conditions'][$this->name . '.slug'] = $slug;
        }

        // Set back default recursive value. Read somewhere that this might
        // be removed in future versions of Cake.
        $this->recursive = $oldRecursive;

        return $slug;
    }

    public function getData($key)
    {
        if (Hash::check($this->data, $key))
        {
            return Hash::get($this->data, $key);
        }

        throw new CakeException(sprintf("%s has no data value matching key '%s'", $this->alias, $key));
    }

}
