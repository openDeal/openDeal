<?php       
class ControllerUserLogout extends \Core\Controller {   
	public function index() { 
		$this->user->logout();

		unset($this->session->data['token']);

		$this->redirect($this->url->link('user/login', '', 'SSL'));
	}
}  
?>