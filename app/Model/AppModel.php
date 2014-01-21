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
        $ds             = DIRECTORY_SEPARATOR;
        $newname        = md5($remoteUrl) . ".jpg";
        $previousname   = md5($previousUrl) . ".jpg";
        $subfolder      = substr(strtolower($this->name), 0, 5); 
        $imagesRoot     = "img";
        $cacheRoot      = "cache";
        $path           = $imagesRoot . $ds . $cacheRoot . $ds . $subfolder;
                
        if(!file_exists($path))
        {
            mkdir($path, 0776, true);
        }
             
        // Delete previous pic
        if(!is_null($previousUrl) && file_exists($path . $ds . $previousname))
        {
            unlink($path . $previousname);
        }
        
        // Save new pic        
        $ch = curl_init($remoteUrl);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $rawdata = curl_exec($ch);
        curl_close($ch);
        $fp = fopen($path . $ds . $newname, 'w');
        fwrite($fp, $rawdata);
        fclose($fp);
        
        return $cacheRoot . $ds . $subfolder . $ds . $newname;
    }
    
    
    public function _makeSlugUnique($slug)
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
    
}
