<?php

class ControllerModuleFacebook extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->load->language('module/facebook');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('facebook', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_content_top'] = $this->language->get('text_content_top');
        $this->data['text_content_bottom'] = $this->language->get('text_content_bottom');
        $this->data['text_column_left'] = $this->language->get('text_column_left');
        $this->data['text_column_right'] = $this->language->get('text_column_right');
        $this->data['text_chooseurl'] = $this->language->get('text_chooseurl');
        $this->data['text_choosecolor'] = $this->language->get('text_choosecolor');
        $this->data['text_staticurl'] = $this->language->get('text_staticurl');
        $this->data['text_dynamicurl'] = $this->language->get('text_dynamicurl');
        $this->data['text_lightcolor'] = $this->language->get('text_lightcolor');
        $this->data['text_darkcolor'] = $this->language->get('text_darkcolor');

        $this->data['entry_banner'] = $this->language->get('entry_banner');
        $this->data['entry_siteurl'] = $this->language->get('entry_siteurl');
        $this->data['entry_numpost'] = $this->language->get('entry_numpost');
        $this->data['entry_width'] = $this->language->get('entry_width');
        $this->data['entry_layout'] = $this->language->get('entry_layout');
        $this->data['entry_position'] = $this->language->get('entry_position');
        $this->data['entry_store'] = $this->language->get('entry_store');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $this->data['entry_colorscheme'] = $this->language->get('entry_colorscheme');
        $this->data['entry_adminuid'] = $this->language->get('entry_adminuid');
        $this->data['entry_appid'] = $this->language->get('entry_appid');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');
        $this->data['button_add_module'] = $this->language->get('button_add_module');
        $this->data['button_remove'] = $this->language->get('button_remove');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['dimension'])) {
            $this->data['error_dimension'] = $this->error['dimension'];
        } else {
            $this->data['error_dimension'] = array();
        }

        if (isset($this->error['urltype'])) {
            $this->data['error_urltype'] = $this->error['urltype'];
        } else {
            $this->data['error_urltype'] = '';
        }

        if (isset($this->error['numpost'])) {
            $this->data['error_numpost'] = $this->error['numpost'];
        } else {
            $this->data['error_numpost'] = '';
        }

        $this->load->model('setting/store');

        $this->data['stores'] = $this->model_setting_store->getStores();

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/facebook', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['action'] = $this->url->link('module/facebook', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['modules'] = array();

        if (isset($this->request->post['facebook_module'])) {
            $this->data['modules'] = $this->request->post['facebook_module'];
        } elseif ($this->config->get('facebook_module')) {
            $this->data['modules'] = $this->config->get('facebook_module');
        }

        $this->load->model('design/layout');

        if (isset($this->request->post['adminuid'])) {
            $this->data['adminuid'] = $this->request->post['adminuid'];
        } else {
            $this->data['adminuid'] = $this->config->get('adminuid');
        }

        if (isset($this->request->post['appid'])) {
            $this->data['appid'] = $this->request->post['appid'];
        } else {
            $this->data['appid'] = $this->config->get('appid');
        }

        $this->data['layouts'] = $this->model_design_layout->getLayouts();

        $this->template = 'module/facebook.phtml';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'module/facebook')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (isset($this->request->post['facebook_module'])) {
            foreach ($this->request->post['facebook_module'] as $key => $value) {
                if (!$value['width']) {
                    $this->error['dimension'][$key] = $this->language->get('error_dimension');
                }
                if (!$value['numpost'] || $value['numpost'] <= "0") {
                    $this->error['numpost'][$key] = $this->language->get('error_numpost');
                }
                if (!$value['urltype'] || $value['urltype'] == "0") {
                    $this->error['urltype'][$key] = $this->language->get('error_urltype');
                }
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}

?>