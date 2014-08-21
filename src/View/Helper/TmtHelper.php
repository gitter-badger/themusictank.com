<?php

namespace App\View\Helper;

use App\View\Helper\AppHelper;
use Cake\Core\Configure;

class TmtHelper extends AppHelper {

    public function contextToClassNames()
    {
        $params = $this->request->params;
        return strtolower(sprintf("%s %s %s", $params['controller'], $params['action'], implode(" ", $params['pass'])));
    }

}
