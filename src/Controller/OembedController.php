<?php
namespace App\Controller;

use Cake\Utility\Hash;
use Cake\Network\Exception\NotFoundException;

use App\Controller\AppController;
use App\Model\Factory\OembedFactory;

class OembedController extends AppController {

    public function index()
    {
        $this->layout = "ajax";
        $this->response->type('application/json');

        if (!Hash::check($this->request->query, "url")) {
            throw new NotFoundException();
        }

        $url = Hash::get($this->request->query, "url");
        $table = OembedFactory::getObjectFromUrl($url);

        if(is_null($url)) {
            throw new NotFoundException();
        }

        $slug = OembedFactory::getSlugFromUrl($url);
        $instance = $table->getOEmbedDataBySlug($slug)->first();

        $this->set("instance", $instance);
    }

}
