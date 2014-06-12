<?php

class ControllerModuleFacebook extends \Core\Controller {

    protected function index($setting) {
        static $module = 0;

      //  $this->document->addScript('public/view/javascript/facebook/facebook_comment.js');

        $this->data['adminuid'] = $this->config->get('adminuid');

        $this->data['appid'] = $this->config->get('appid');

        if ($this->config->get('adminuid')) {
            $this->document->addMeta('fb:admins', '<meta property="fb:admins" content="' . $this->config->get('adminuid') . '"/>');
        }
        if ($this->config->get('appid')) {
            $this->document->addMeta('fb:app_id', '<meta property="fb:app_id" content="' . $this->config->get('appid') . '"/>');
        }
        /*
         * $this->data['adminuid'] = $this->config->get('adminuid');

          $this->data['appid'] = $this->config->get('appid');
         */


        $this->data['width'] = $setting['width'];

        $this->data['numpost'] = $setting['numpost'];


        $siteurl = "http://" . $this->request->server["SERVER_NAME"];

      /*  if ($setting['store'] != "default" && $setting['store']) {
            $siteurl = $setting['store'];
        } else {
            $siteurl = $this->config->get('config_url');
        }*/
$this->document->addMeta('og:type', '<meta property="og:type" content="product"/>');

        if ((isset($this->request->get['deal_id']) || isset($this->request->get['freepon_id'])) && $setting['urltype'] == "2") {
            $this->data['siteurl'] = $siteurl . $_SERVER["REQUEST_URI"];
             
            
        } else {
            $this->data['siteurl'] = $siteurl;
            
        }

        $this->document->addMeta('og:url', '<meta property="og:url" content="'.$this->data['siteurl'].'"/>');

        $this->data['colorscheme'] = $setting['colorscheme'];

        $this->data['module'] = $module++;

        $this->template = 'module/facebook.phtml';


        $this->render();
    }

}

?>