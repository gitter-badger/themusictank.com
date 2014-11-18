<?php

namespace App\Model\Behavior;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\Table;
use Cake\ORM\Entity;
use Cake\ORM\Behavior;
use Aws\S3\S3Client;

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

    public function beforeSave(Event $event, Entity $entity, $options)
    {
        $config = $this->config();
        // Fetch new thumbnails each time the entity is updated
        if($entity->dirty($config['image_src'])) {
            $this->_deleteThumbnails($entity);
            $this->_createThumbnails($entity);
        }
    }

    protected function _deleteThumbnails(Entity $entity)
    {
        if (!is_null($entity->get('image'))) {
            $config = $this->config();
            foreach (array_keys($config['types']) as $type) {
                $this->_deleteFromBucket($entity->get('image') . "_" . $type . ".jpg");
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

            $entity->set('image', $typeSubfolder . DS . $firstLetterSubfolder . DS . $secondLetterSubfolder . DS . $newFileName);

            // When the converter is not available, just pretend to save the paths.
            if(is_null(Configure::read('Tools.imagemagik'))) {
                return;
            }

            if (!file_exists($entity->get('image'))) {
                $original = $this->_downloadRemoteImage($entity->get('image_src'));
                if (file_exists($original)) {
                    foreach ($config['types'] as $type => $size) {
                        $filename = $entity->get('image') . "_" . $type . ".jpg";
                        $thumbnail = $this->_resize($original, $size);

                        // Run images requiring special effects
                        if (strstr($type, "blur")) {
                            $thumbnail = $this->_blur($thumbnail);
                        }

                        // Once the file is saved remotly, remove it from this server.
                        if ($this->_sendToBucket($thumbnail, $filename)) {
                            unlink($thumbnail);
                        }
                    }

                    // Remove the bigass image as well.
                    unlink($original);
                }
            }
        }
    }

    private function _resize($original, $size)
    {
        // Run imagemagik in the command line as to stay more efficient resources wise.
        $saveLocation = WWW_ROOT . "img" . DS . "resized.jpg";
        exec(sprintf("convert %s -resize %d %s", $original, $size, $saveLocation));
        return $saveLocation;
    }

    /**
     * Implicitely, effects are applied on an existing image.
     */
    private function _blur($filename)
    {
        exec(sprintf("convert %s -channel RGBA -blur 0x8 %s ", $filename, $filename));
        return $filename;
    }

    private function _sendToBucket($thumbnail, $filename)
    {
        $s3 = S3Client::factory([
            'key'    =>  Configure::read("Apis.Amazon.s3.key"),
            'secret' =>  Configure::read("Apis.Amazon.s3.secret")
        ]);
        $s3->upload(Configure::read("Apis.Amazon.s3.bucket"), $filename, fopen($thumbnail, 'rb'), 'public-read');
    }

    private function _deleteFromBucket($filename)
    {
        $s3 = S3Client::factory([
            'key'    =>  Configure::read("Apis.Amazon.s3.key"),
            'secret' =>  Configure::read("Apis.Amazon.s3.secret")
        ]);

        return $s3->deleteObject([
            'Bucket' => Configure::read("Apis.Amazon.s3.bucket"),
            'Key' => $filename
        ]);
    }

    private function _downloadRemoteImage($remoteUrl)
    {
        ini_set('memory_limit', '64M'); // big files break the code
        $ch = curl_init($remoteUrl);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $rawdata = curl_exec($ch);
        curl_close($ch);

        $saveLocation = WWW_ROOT . "img" . DS . "original.jpg";
        $fp = fopen($saveLocation, 'w');
        fwrite($fp, $rawdata);
        fclose($fp);

        return $saveLocation;
    }

    private function _getFormattedClassName($obj)
    {
        $namespace = explode('\\', strtolower(get_class($obj)));
        return array_pop($namespace);
    }

}


