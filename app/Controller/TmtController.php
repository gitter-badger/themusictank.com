<?php
class TmtController extends AppController {

	var $components = array("Paginator");
 	public $paginate = array(
        'limit' => 25,
        'order' => array(
            'Bug.created' => 'desc',
            'Bug.is_fixed' => 'asc'
        )
    );

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->deny("*");

        $user = $this->getAuthUser();
        if($user) {
        	$role = Hash::get($user, "User.role");
        	if($role !== "admin") {
            	throw new NotFoundException();
            }
        } else {
            throw new NotFoundException();
        }
    }

    public function index()
    {
    	$this->layout = "blank";
    }

    public function bugs()
    {
    	$this->layout = "blank";
    	$this->loadModel("Bug");

    	$this->set("bugs", $this->Paginator->paginate('Bug'));
    }

    public function bugstatus()
    {
    	$this->layout = "ajax";
    	$this->loadModel("Bug");

    	$this->Bug->save(array(
    		"id" 		=> (int)$this->request->data["id"],
    		"is_fixed" 	=> (int)$this->request->data["is_fixed"]
		));
    }

    public function sync()
    {
    	$this->layout = "ajax";
    	$this->loadModel("Config");
    	$this->loadModel("Artist");
    	$this->loadModel("Album");
    	$this->loadModel("Track");
    	$this->loadModel("Users");
    	$this->loadModel("Bug");

    	// Get general website data
    	$this->set("artistCount", $this->Artist->find("count"));
    	$this->set("albumCount", $this->Album->find("count"));
    	$this->set("trackCount", $this->Track->find("count"));
    	$this->set("userCount", $this->User->find("count"));
    	$this->set("bugCount", $this->Bug->find("count", array("conditions" => array("is_fixed" => false))));

    	// Prepare all config values
    	$this->set("configs", $this->Config->find("all", array("order" => "key ASC")));

    	foreach(array("cron", "debug", "error") as $logName)
    	{
    		$lines = array();
    		$file = ROOT . DS . APP_DIR . DS . "tmp" . DS . "logs" . DS .$logName . ".log";
    		if(file_exists($file))
    		{
				$fp = fopen($file, "r");
				if($fp)
				{
					while(!feof($fp))
					{
					   $line = fgets($fp, 4096);
					   array_push($lines, $line);
					   if (count($lines) > 500)
					   {
					       array_shift($lines);
					   }
					}
					fclose($fp);
				}
			}
			else {
				$lines[] = "File does not exist.";
			}
			$this->set("log" . ucfirst($logName), implode("", $lines));
    	}
    }

}
