<?php

namespace App\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Table;
use Cake\ORM\Entity;
use Cake\ORM\Behavior;

class ThumbnailBehavior extends Behavior {

    protected $_defaultConfig = [
        'field' => 'name',
        'image_src' => 'image_src',
        'image' => 'image',
        'path' => '',
        'types' => [
            "mobile_thumb" => 150,
            "thumb" => 300,
            "mobile_big" => 400,
            "big"   => 900,
            "mobile_blur" => 400,
            "blur"  => 900
        ]
    ];

    public function __construct(Table $table, array $config = []) {
        parent::__construct($table, $config);
        if (!array_key_exists("path", $config)) {
            $this->config('path', WWW_ROOT . "img" . DS . "cache" . DS, false);
        }
    }

    public function beforeSave(Event $event, Entity $entity)
    {
        $config = $this->config();
        // Fetch new thumbnails each time the entity is updated
        if($entity->get($config['image_src'])) {
            $this->_deleteThumbnails($entity);
            $this->_createThumbnails($entity);
        }
    }

    protected function _deleteThumbnails(Entity $entity)
    {
        if (!is_null($entity->get('image'))) {
            $config = $this->config();
            foreach (array_keys($config['types']) as $type) {
                $filename = $config['path'] . $entity->get('image') . "_" . $type . ".jpg";
                if (file_exists($filename)) {
                    unlink($filename);
                }
            }
            $entity->set('image', null);
        }
    }

    protected function _createThumbnails(Entity $entity)
    {
        if (!is_null($entity->get('image_src'))) {

            $config = $this->config();
            $newFileName = $entity->get('slug');
            $typeSubfolder = $this->_getFormattedClassName($entity);
            $firstLetterSubfolder = substr($newFileName, 0, 1);
            $secondLetterSubfolder = substr($newFileName, 1, 1);

           /* // When the converter is not available, assume we are in dev and pull
            // the image right from tmt.com
            if(is_null(Configure::read('ConvertCMD'))) {
                return $firstLetterSubfolder ."/". $secondLetterSubfolder . "/" . $newFileName;
            }*/

            $entity->set('image', $typeSubfolder . DS . $firstLetterSubfolder . DS . $secondLetterSubfolder . DS . $newFileName);
            $original = $config['path'] . $entity->get('image') . "_original.jpg";

            if(!file_exists($config['path'] . $typeSubfolder . DS . $firstLetterSubfolder . DS . $secondLetterSubfolder)) {
                mkdir($config['path'] . $typeSubfolder . DS . $firstLetterSubfolder . DS . $secondLetterSubfolder, 0776, true);
            }

            ini_set('memory_limit', '64M'); // big files break the code
            $this->_downloadRemoteImage($entity->get('image_src'), $original);

            if (file_exists($original)) {
                foreach ($config['types'] as $type => $size) {
                    // Run imagemagik in the command line as to stay more efficient resources wise.
                    exec(sprintf("convert %s -resize %d %s", $original, $size, $config['path'] . $entity->get('image') . "_" . $type . ".jpg" ) );
                }

                // Run images requiring special effects
                exec(sprintf("convert %s -channel RGBA -blur 0x8 %s ", $config['path'] . $entity->get('image') . "_blur.jpg", $config['path'] . $entity->get('image') . "_blur.jpg"));
                exec(sprintf("convert %s -channel RGBA -blur 0x8 %s ", $config['path'] . $entity->get('image') . "_mobile_blur.jpg", $config['path'] . $entity->get('image') . "_mobile_blur.jpg"));

                // remote the bigass image now that we have the sizes we want.
                unlink($original);
            }
        }
    }

    private function _downloadRemoteImage($remoteUrl, $saveLocation)
    {
        $ch = curl_init($remoteUrl);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $rawdata = curl_exec($ch);
        curl_close($ch);

        $fp = fopen($saveLocation, 'w');
        fwrite($fp, $rawdata);
        fclose($fp);
    }

    private function _getFormattedClassName($obj)
    {
        $namespace = explode('\\', strtolower(get_class($obj)));
        return array_pop($namespace);
    }

}


