<?php

class ControllerDealCompany extends \Core\Controller {

    private $error = array();

    public function index() {


        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('deal/company');
        $this->getList();
    }

    public function insert() {


        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('deal/company');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_deal_company->addCompany($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('deal/company', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function update() {
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('deal/company');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_deal_company->editCompany($this->request->get['company_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('deal/company', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function delete() {
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('deal/company');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $company_id) {
                $this->model_deal_company->deleteCompany($company_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('deal/company', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }
        
       if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = null;
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        
         if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }


        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('deal/company', 'token=' . $this->session->data['token'] . $url, 'SSL'),
            'separator' => ' :: '
        );

        $this->data['insert'] = $this->url->link('deal/company/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['delete'] = $this->url->link('deal/company/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $this->data['companies'] = array();

        $data = array(
            'filter_name' => $filter_name,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_admin_limit'),
            'limit' => $this->config->get('config_admin_limit')
        );

        $company_total = $this->model_deal_company->getTotalCompanies();

        $results = $this->model_deal_company->getCompanies($data);

        foreach ($results as $result) {
            $action = array();

            $action[] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('deal/company/update', 'token=' . $this->session->data['token'] . '&company_id=' . $result['company_id'] . $url, 'SSL')
            );

            $this->data['companies'][] = array(
                'company_id' => $result['company_id'],
                'name' => $result['name'],
                'commission' => $result['commission'],
                'date_added' => date($this->language->get('date_format_short'), $result['date_added']),
                'selected' => isset($this->request->post['selected']) && in_array($result['user_id'], $this->request->post['selected']),
                'action' => $action
            );
        }

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_no_results'] = $this->language->get('text_no_results');

        $this->data['column_name'] = $this->language->get('column_name');
        $this->data['column_commission'] = $this->language->get('column_commission');
        $this->data['column_date_added'] = $this->language->get('column_date_added');
        $this->data['column_action'] = $this->language->get('column_action');

        $this->data['button_insert'] = $this->language->get('button_insert');
        $this->data['button_delete'] = $this->language->get('button_delete');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        $url = '';
        
         if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['sort_name'] = $this->url->link('deal/company', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
        $this->data['sort_date_added'] = $this->url->link('deal/company', 'token=' . $this->session->data['token'] . '&sort=date_added' . $url, 'SSL');

        $url = '';
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $company_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_admin_limit');
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('deal/company', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $this->data['pagination'] = $pagination->render();

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->template = 'deal/company_list.phtml';

        $this->response->setOutput($this->render());
    }

    protected function getForm() {


        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');

        $this->data['tab_profile'] = $this->language->get('tab_profile');
        $this->data['tab_location'] = $this->language->get('tab_location');
        $this->data['tab_staff'] = $this->language->get('tab_staff');


        $this->data['entry_company'] = $this->language->get('entry_company');
        $this->data['entry_website'] = $this->language->get('entry_website');
        $this->data['entry_image'] = $this->language->get('entry_image');
        $this->data['entry_commission'] = $this->language->get('entry_commission');
        $this->data['entry_about'] = $this->language->get('entry_about');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_store'] = $this->language->get('entry_store');

        $this->data['text_browse'] = $this->language->get('text_browse');
        $this->data['text_clear'] = $this->language->get('text_clear');
        $this->data['text_default'] = $this->language->get('text_default');
        $this->data['text_location'] = $this->language->get('text_location');
        $this->data['entry_address'] = $this->language->get('entry_address');
        $this->data['entry_phone'] = $this->language->get('entry_phone');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');
        $this->data['button_add_location'] = $this->language->get('button_add_location');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $this->data['error_name'] = $this->error['name'];
        } else {
            $this->data['error_name'] = '';
        }

        if (isset($this->error['commission'])) {
            $this->data['error_commission'] = $this->error['commission'];
        } else {
            $this->data['error_commission'] = '';
        }

        if (isset($this->error['location'])) {
            $this->data['error_location'] = $this->error['location'];
            $this->data['error_warning'] .= $this->language->get('error_on_location');
        } else {
            $this->data['error_location'] = array();
        }




        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('deal/category', 'token=' . $this->session->data['token'] . $url, 'SSL'),
            'separator' => ' :: '
        );

        if (!isset($this->request->get['company_id'])) {
            $this->data['action'] = $this->url->link('deal/company/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $this->data['action'] = $this->url->link('deal/company/update', 'token=' . $this->session->data['token'] . '&company_id=' . $this->request->get['company_id'] . $url, 'SSL');
        }

        $this->data['cancel'] = $this->url->link('deal/company', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['company_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $company_info = $this->model_deal_company->getCompany($this->request->get['company_id']);
        }

        if (isset($this->request->post['name'])) {
            $this->data['name'] = $this->request->post['name'];
        } elseif (!empty($company_info)) {
            $this->data['name'] = $company_info['name'];
        } else {
            $this->data['name'] = '';
        }

        if (isset($this->request->post['website'])) {
            $this->data['website'] = $this->request->post['website'];
        } elseif (!empty($company_info)) {
            $this->data['website'] = $company_info['website'];
        } else {
            $this->data['website'] = '';
        }

        if (isset($this->request->post['image'])) {
            $this->data['image'] = $this->request->post['image'];
        } elseif (!empty($company_info)) {
            $this->data['image'] = $company_info['image'];
        } else {
            $this->data['image'] = '';
        }

        $this->load->model('tool/image');

        if (isset($this->request->post['image']) && file_exists(DIR_IMAGE . $this->request->post['image'])) {
            $this->data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
        } elseif (!empty($company_info) && $company_info['image'] && file_exists(DIR_IMAGE . $company_info['image'])) {
            $this->data['thumb'] = $this->model_tool_image->resize($company_info['image'], 100, 100);
        } else {
            $this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
        }

        $this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

        if (isset($this->request->post['commission'])) {
            $this->data['commission'] = $this->request->post['commission'];
        } elseif (!empty($company_info)) {
            $this->data['commission'] = $company_info['commission'];
        } else {
            $this->data['commission'] = $this->config->get('config_default_commission');
        }

        if (isset($this->request->post['about'])) {
            $this->data['about'] = $this->request->post['about'];
        } elseif (!empty($company_info)) {
            $this->data['about'] = $company_info['about'];
        } else {
            $this->data['about'] = '';
        }

        if (isset($this->request->post['status'])) {
            $this->data['status'] = $this->request->post['status'];
        } elseif (!empty($company_info)) {
            $this->data['status'] = $company_info['status'];
        } else {
            $this->data['status'] = '0';
        }

        /*  if (isset($this->request->post['staff'])) {
          $this->data['staff'] = $this->request->post['staff'];
          } elseif (!empty($company_info)) {
          $this->data['staff'] = $company_info['staff'];
          } else {
          $this->data['staff'] = '';
          } */


        $this->load->model('setting/store');

        $this->data['stores'] = $this->model_setting_store->getStores();

        if (isset($this->request->post['company_store'])) {
            $this->data['company_store'] = $this->request->post['company_store'];
        } elseif (isset($this->request->get['company_id'])) {
            $this->data['company_store'] = $this->model_deal_company->getCompanyStores($this->request->get['company_id']);
        } else {
            $this->data['company_store'] = array(0);
        }

        if (isset($this->request->post['company_location'])) {
            $this->data['company_location'] = $this->request->post['company_location'];
        } elseif (isset($this->request->get['company_id'])) {
            $this->data['company_location'] = $this->model_deal_company->getCompanyLocations($this->request->get['company_id']);
        } else {
            $this->data['company_location'] = array(
                array('address' => '',
                    'latitude' => '',
                    'longitude' => '',
                    'phone' => '')
            );
        }


        $this->data['token'] = $this->session->data['token'];

        $this->template = 'deal/company_form.phtml';

        $this->children = array(
            'common/header',
            'common/footer'
        );


        $this->response->setOutput($this->render());
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'deal/company')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 2)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        $_POST['commission'] = str_replace("%", "", $_POST['commission']);

        if ((float) $_POST['commission'] < 0 || (float) $_POST['commission'] > 100) {
            $this->error['commission'] = $this->language->get('error_valid_commission');
        }

        foreach ($_POST['company_location'] as $i => $location) {
            if (trim($location['address']) == '') {
                unset($_POST['company_location'][$i]);
                continue;
            } else {
                if (empty($location['latitude']) || empty($location['longitude'])) {
                    $this->error['location'][$i] = $this->language->get('error_select_address');
                }
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'deal/company')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('deal/deal');

        foreach ($this->request->post['selected'] as $company_id) {
            $deal_total = $this->model_deal_deal->getTotalDealsByCompany($company_id);

            if ($deal_total) {
                $this->error['warning'] = sprintf($this->language->get('error_deal'), $deal_total);
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('deal/company');

            $data = array(
                'filter_name' => $this->request->get['filter_name'],
                'start' => 0,
                'limit' => 20
            );

            $results = $this->model_deal_company->getCompanies($data);

            foreach ($results as $result) {
                $json[] = array(
                    'company_id' => $result['company_id'],
                    'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                    'location' => $this->model_deal_company->getCompanyLocations($result['company_id'])
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->setOutput(json_encode($json));
    }

}
