<?php

class ControllerCommonMaintenance extends \Core\Controller {

    public function index() {
        if ($this->config->get('config_maintenance')) {
            $route = '';

            if (isset($this->request->get['route'])) {
                $part = explode('/', $this->request->get['route']);

                if (isset($part[0])) {
                    $route .= $part[0];
                }
            }

            // Show site if logged in as admin
            $this->load->library('user');

            $this->user = new User($this->registry);

            if (($route != 'payment') && !$this->user->isLogged()) {
                return $this->forward('common/maintenance/info');
            }
        }
    }

    public function info() {
        $this->language->load('common/maintenance');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->document->breadcrumbs = array();

        $this->document->breadcrumbs[] = array(
            'text' => $this->language->get('text_maintenance'),
            'href' => $this->url->link('common/maintenance'),
            'separator' => false
        );

        $this->data['message'] = $this->language->get('text_message');
        $this->data['countdown_format'] = $this->language->get('countdown_format');
        $this->data['countdown_till'] = $this->language->get('countdown_till');

        $this->data['title'] = $this->document->getTitle();

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }

        $this->data['base'] = $server;
        $this->data['description'] = $this->document->getDescription();
        $this->data['keywords'] = $this->document->getKeywords();
        $this->data['links'] = $this->document->getLinks();
        $this->data['styles'] = $this->document->getStyles();
        $this->data['meta'] = $this->document->getMeta();
        $this->data['lang'] = $this->language->get('code');
        $this->data['direction'] = $this->language->get('direction');
        $this->data['name'] = $this->config->get('config_name');

        if ($this->config->get('config_icon') && file_exists(DIR_IMAGE . $this->config->get('config_icon'))) {
            $this->data['icon'] = $server . 'image/' . $this->config->get('config_icon');
        } else {
            $this->data['icon'] = '';
        }
        
        if ($this->config->get('config_logo') && (substr($this->config->get('config_logo'), 0, 4) == "http" || file_exists(DIR_IMAGE . $this->config->get('config_logo')))) {

            if (substr($this->config->get('config_logo'), 0, 4) == "http") {
                $this->data['logo'] = $this->config->get('config_logo');
            } else {
                $this->data['logo'] = $server . 'image/' . $this->config->get('config_logo');
            }
        } else {
            $this->data['logo'] = '';
        }


        $this->template = 'common/maintenance.phtml';


        $this->response->setOutput($this->render());
    }

}

?>