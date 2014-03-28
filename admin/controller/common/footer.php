<?php

class ControllerCommonFooter extends \Core\Controller {

    protected function index() {
        $this->language->load('common/footer');
        $this->data['scripts'] = $this->document->getScripts();
        $this->data['text_footer'] = sprintf($this->language->get('text_footer'), VERSION);
        $this->data['lang'] = $this->language->get('code');
        $this->data['text_confirm'] = $this->language->get('text_confirm');
        $this->data['form_missed_one'] = $this->language->get('form_missed_one');
        $this->data['form_missed_x'] = $this->language->get('form_missed_x');
        
        $this->data['text_image_manager'] = $this->language->get('text_image_manager');
        $this->data['token'] = isset($this->session->data['token'])? $this->session->data['token']:'';


        if (!$this->user->isLogged() || !isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token'])) {
            $this->template = "common/alt-footer.phtml";
        }

        $this->render();
    }

}

?>