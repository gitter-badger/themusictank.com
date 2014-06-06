<?php

App::uses('AppController', 'Controller');

class PagesController extends AppController {

    public $uses = array();

	public function display() {

		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			$this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}

		// temporarily forward the homepage
		if(Configure::read('debug') < 1 && $page == "home") {
			$this->layout = "blank";
			$this->render("soon");
			return;
		}

		// Send users to their dashboard page if they are logged in.
		if($page == "home" && $this->userIsLoggedIn())
		{
            $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
		}

		if($page == "about") {
			$this->set("francois", $this->User->findBySlug("francois"));
			$this->set("julien", $this->User->findBySlug("julien"));
		}


		$this->set(compact('page', 'subpage', 'title_for_layout'));
		$this->render(implode('/', $path));
	}
}
