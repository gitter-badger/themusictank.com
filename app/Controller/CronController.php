<?php
/**
 * CronController controller
 *
 * Contains Cron pages methods
 *
 * @package       app.Controller
 */
class CronController extends AppController {
    
    public function beforeFilter()
    {   
        $this->layout = "blank";  
        $this->loadModel("Config"); 
        parent::beforeFilter();
    }
    
    /** 
     * Actions that are expected to run daily.
     */
    public function daily()
    {        
        $this->Config->updateCachedDaily();
        $this->render('/Pages/cron/');
    }
    
    public function weekly()
    {
        $this->Config->updateCachedWeekly();
        $this->render('/Pages/cron/');
    }  
}
