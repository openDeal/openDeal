<?php

class ControllerDealFreepon extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('deal/freepon');
        $this->getList();
    }

    public function insert() {


        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('deal/freepon');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            ;

            $this->model_deal_freepon->addFreepon($this->request->post);

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

            $this->redirect($this->url->link('deal/freepon', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function update() {
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('deal/freepon');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_deal_freepon->editFreepon($this->request->get['freepon_id'], $this->request->post);

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

            $this->redirect($this->url->link('deal/freepon', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function delete() {
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('deal/freepon');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $freepon_id) {
                $this->model_deal_freepon->deleteFreepon($freepon_id);
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

            $this->redirect($this->url->link('deal/freepon', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'fd.name';
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
            'href' => $this->url->link('deal/freepon', 'token=' . $this->session->data['token'] . $url, 'SSL'),
            'separator' => ' :: '
        );

        $this->data['insert'] = $this->url->link('deal/freepon/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['delete'] = $this->url->link('deal/freepon/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $this->data['freepons'] = array();

        $data = array(
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_admin_limit'),
            'limit' => $this->config->get('config_admin_limit')
        );

        $freepon_total = $this->model_deal_freepon->getTotalFreepons();

        $results = $this->model_deal_freepon->getFreepons($data);

        foreach ($results as $result) {
            $action = array();

            $action[] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('deal/freepon/update', 'token=' . $this->session->data['token'] . '&freepon_id=' . $result['freepon_id'] . $url, 'SSL')
            );


            $this->data['freepons'][] = array(
                'freepon_id' => $result['freepon_id'],
                'name' => $result['name'],
                'company_name' => $result['company_name'],
                'status' => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disbled'),
                'views' => $result['viewed'],
                'downloaded' => $result['downloaded'],
                'date_added' => date($this->language->get('date_format_short'), $result['create_date']),
                'begin_time' => date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), $result['begin_time']),
                'end_time' => date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), $result['end_time']),
                'selected' => isset($this->request->post['selected']) && in_array($result['user_id'], $this->request->post['selected']),
                'action' => $action
            );
        }
        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['text_no_results'] = $this->language->get('text_no_results');

        $this->data['column_name'] = $this->language->get('column_name');
        $this->data['column_viewed'] = $this->language->get('column_viewed');
        $this->data['column_downloaded'] = $this->language->get('column_downloaded');
        $this->data['column_begin_time'] = $this->language->get('column_begin_time');
        $this->data['column_end_time'] = $this->language->get('column_end_time');
        $this->data['column_status'] = $this->language->get('column_status');
        $this->data['column_ordered'] = $this->language->get('column_ordered');
        $this->data['column_date_added'] = $this->language->get('column_date_added');
        $this->data['column_company'] = $this->language->get('column_company');

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

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['sort_name'] = $this->url->link('deal/freepon', 'token=' . $this->session->data['token'] . '&sort=fd.name' . $url, 'SSL');
        $this->data['sort_date_added'] = $this->url->link('deal/freepon', 'token=' . $this->session->data['token'] . '&sort=f.date_added' . $url, 'SSL');
        $this->data['sort_viewed'] = $this->url->link('deal/freepon', 'token=' . $this->session->data['token'] . '&sort=d.viewed' . $url, 'SSL');
        $this->data['sort_downloaded'] = $this->url->link('deal/freepon', 'token=' . $this->session->data['token'] . '&sort=d.downloaded' . $url, 'SSL');
        $this->data['sort_begin'] = $this->url->link('deal/freepon', 'token=' . $this->session->data['token'] . '&sort=d.begin_time' . $url, 'SSL');
        $this->data['sort_end'] = $this->url->link('deal/freepon', 'token=' . $this->session->data['token'] . '&sort=d.end_time' . $url, 'SSL');
        $this->data['sort_company'] = $this->url->link('deal/freepon', 'token=' . $this->session->data['token'] . '&sort=c.name' . $url, 'SSL');

        $this->data['sort_status'] = $this->url->link('deal/freepon', 'token=' . $this->session->data['token'] . '&sort=d.status' . $url, 'SSL');

        $url = '';
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $freepon_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_admin_limit');
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('deal/freepon', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $this->data['pagination'] = $pagination->render();

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->template = 'deal/freepon_list.phtml';


        $this->response->setOutput($this->render());
    }

    protected function getForm() {


        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');


        $this->data['tab_general'] = $this->language->get('tab_general');
        $this->data['tab_data'] = $this->language->get('tab_data');
        $this->data['tab_image'] = $this->language->get('tab_image');
        $this->data['tab_design'] = $this->language->get('tab_design');
        $this->data['tab_link'] = $this->language->get('tab_link');

        $this->data['entry_name'] = $this->language->get('entry_name');
        $this->data['entry_description'] = $this->language->get('entry_description');
        $this->data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
        $this->data['entry_meta_description'] = $this->language->get('entry_meta_description');
        $this->data['entry_company'] = $this->language->get('entry_company');
        $this->data['entry_begin_time'] = $this->language->get('entry_begin_time');
        $this->data['entry_end_time'] = $this->language->get('entry_end_time');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_priority'] = $this->language->get('entry_priority');
        $this->data['entry_priority_about'] = $this->language->get('entry_priority_about');
        $this->data['entry_keyword'] = $this->language->get('entry_keyword');
        $this->data['entry_category'] = $this->language->get('entry_category');
        $this->data['entry_city'] = $this->language->get('entry_city');
        $this->data['entry_store'] = $this->language->get('entry_store');
        $this->data['entry_download'] = $this->language->get('entry_download');
        $this->data['entry_code'] = $this->language->get('entry_code');

        $this->data['entry_deal_times'] = $this->language->get('entry_deal_times');


        $this->data['entry_layout'] = $this->language->get('entry_layout');

        $this->data['text_default'] = $this->language->get('text_default');

        $this->data['text_add_image'] = $this->language->get('text_add_image');

        $this->data['text_image_manager'] = $this->language->get('text_image_manager');
        $this->data['text_browse'] = $this->language->get('text_browse');
        $this->data['text_clear'] = $this->language->get('text_clear');

        $this->data['entry_image'] = $this->language->get('entry_image');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['title'])) {
            $this->data['error_title'] = $this->error['title'];
        } else {
            $this->data['error_title'] = '';
        }

        if (isset($this->error['duplicate_seo'])) {
            $this->data['error_duplicate_seo'] = $this->error['duplicate_seo'];
        } else {
            $this->data['error_duplicate_seo'] = array();
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
            'href' => $this->url->link('deal/freepon', 'token=' . $this->session->data['token'] . $url, 'SSL'),
            'separator' => ' :: '
        );

        if (!isset($this->request->get['freepon_id'])) {
            $this->data['action'] = $this->url->link('deal/freepon/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $this->data['action'] = $this->url->link('deal/freepon/update', 'token=' . $this->session->data['token'] . '&freepon_id=' . $this->request->get['freepon_id'] . $url, 'SSL');
        }

        $this->data['cancel'] = $this->url->link('deal/freepon', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['freepon_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $freepon_info = $this->model_deal_freepon->getFreepon($this->request->get['freepon_id']);
        }



        $this->data['token'] = $this->session->data['token'];

        $this->load->model('localisation/language');

        $this->data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['freepon_description'])) {
            $this->data['freepon_description'] = $this->request->post['freepon_description'];
        } elseif (isset($this->request->get['freepon_id'])) {
            $this->data['freepon_description'] = $this->model_deal_freepon->getFreeponDescriptions($this->request->get['freepon_id']);
        } else {
            $this->data['freepon_description'] = array();
        }



        if (isset($this->request->post['company_id'])) {
            $this->data['company_id'] = $this->request->post['company_id'];
        } elseif (!empty($freepon_info)) {
            $this->data['company_id'] = $freepon_info['company_id'];
        } else {
            $this->data['company_id'] = '';
        }

        if ($this->data['company_id']) {
            $this->load->model('deal/company');
            $company = $this->model_deal_company->getCompany($this->data['company_id']);
            $this->data['company'] = $company['name'];
        } else {
            $this->data['company'] = '';
        }


        if (isset($this->request->post['freepon_times'])) {
            $this->data['freepon_times'] = $this->request->post['freepon_times'];
        } elseif (!empty($freepon_info)) {
            $this->data['freepon_times'] = DATE("Y/m/d h:i A", $freepon_info['begin_time']) . ' - ' . DATE("Y/m/d h:i A", $freepon_info['end_time']);
        } else {
            $this->data['freepon_times'] = '';
        }

        if (isset($this->request->post['freepon_download'])) {
            $this->data['freepon_download'] = $this->request->post['freepon_download'];
        } elseif (!empty($freepon_info)) {
            $this->data['freepon_download'] = $freepon_info['download'];
        } else {
            $this->data['freepon_download'] = '';
        }

        if (isset($this->request->post['freepon_code'])) {
            $this->data['freepon_code'] = $this->request->post['freepon_code'];
        } elseif (!empty($freepon_info)) {
            $this->data['freepon_code'] = $freepon_info['code'];
        } else {
            $this->data['freepon_code'] = '';
        }


        if (isset($this->request->post['feature_weight'])) {
            $this->data['feature_weight'] = $this->request->post['feature_weight'];
        } elseif (!empty($deal_info)) {
            $this->data['feature_weight'] = $freepon_info['feature_weight'];
        } else {
            $this->data['feature_weight'] = '0';
        }

        if (isset($this->request->post['status'])) {
            $this->data['status'] = $this->request->post['status'];
        } elseif (!empty($freepon_info)) {
            $this->data['status'] = $freepon_info['status'];
        } else {
            $this->data['status'] = '0';
        }

        if (isset($this->request->post['keyword'])) {
            $this->data['keyword'] = $this->request->post['keyword'];
        } elseif (!empty($freepon_info)) {
            $this->data['keyword'] = $freepon_info['keyword'];
        } else {
            $this->data['keyword'] = '';
        }

        $this->load->model('deal/category');
        if (isset($this->request->post['freepon_category'])) {
            $categories = $this->request->post['freepon_category'];
        } elseif (!empty($freepon_info)) {
            $categories = $this->model_deal_freepon->getFreeponCategories($this->request->get['freepon_id']);
        } else {
            $categories = array();
        }

        $this->data['freepon_categories'] = array();
        foreach ($categories as $category_id) {
            $category_info = $this->model_deal_category->getCategory($category_id);

            if ($category_info) {
                $this->data['freepon_categories'][] = array(
                    'category_id' => $category_info['category_id'],
                    'name' => ($category_info['path'] ? $category_info['path'] . ' &gt; ' : '') . $category_info['name']
                );
            }
        }

        $this->load->model('localisation/city');
        if (isset($this->request->post['freepon_city'])) {
            $cities = $this->request->post['freepon_city'];
        } elseif (!empty($freepon_info)) {
            $cities = $this->model_deal_freepon->getFreeponCities($this->request->get['freepon_id']);
        } else {
            $cities = array();
        }

        $this->data['freepon_cities'] = array();
        foreach ($cities as $city_id) {
            $city_info = $this->model_localisation_city->getCity($city_id);
            if ($city_info) {
                $this->data['freepon_cities'][] = array(
                    'city_id' => $city_info['city_id'],
                    'name' => $city_info['city_name']
                );
            }
        }



        $this->load->model('tool/image');


        $this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

        if (isset($this->request->post['freepon_image'])) {
            $images = $this->request->post['freepon_image'];
        } elseif (!empty($freepon_info)) {
            $images = $this->model_deal_freepon->getFreeponImages($this->request->get['freepon_id']);
        } else {
            $images = array();
        }

        $this->data['images'] = array();
        foreach ($images as $image) {
            if (file_exists(DIR_IMAGE . $image)) {
                $this->data['images'][] = array(
                    'image' => $image,
                    'thumb' => $this->model_tool_image->resize($image, 100, 100)
                );
            }
        }


        $this->document->addScript('view/theme/default/js/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('view/theme/default/css/bootstrap-datetimepicker/bootstrap-datetimepicker.css');




        $this->load->model('setting/store');

        $this->data['stores'] = $this->model_setting_store->getStores();

        if (isset($this->request->post['freepon_store'])) {
            $this->data['freepon_store'] = $this->request->post['freepon_store'];
        } elseif (isset($this->request->get['freepon_id'])) {
            $this->data['freepon_store'] = $this->model_deal_freepon->getFreeponStores($this->request->get['freepon_id']);
        } else {
            $this->data['freepon_store'] = array(0);
        }

        if (isset($this->request->post['freepon_layout'])) {
            $this->data['freepon_layout'] = $this->request->post['freepon_layout'];
        } elseif (isset($this->request->get['deal_id'])) {
            $this->data['freepon_layout'] = $this->model_deal_freepon->getFreeponLayouts($this->request->get['freeon_id']);
        } else {
            $this->data['freepon_layout'] = array();
        }

        $this->load->model('design/layout');

        $this->data['layouts'] = $this->model_design_layout->getLayouts();



        $this->data['token'] = $this->session->data['token'];

        $this->template = 'deal/freepon_form.phtml';

        $this->children = array(
            'common/header',
            'common/footer'
        );


        //debugPre($this->data);
        //exit;

        $this->document->addScript('view/theme/default/js/plugins/daterangepicker/daterangepicker.js');
        $this->document->addStyle('view/theme/default/css/daterangepicker/daterangepicker-bs3.css');

        $this->response->setOutput($this->render());
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'deal/freepon')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        foreach ($this->request->post['freepon_description'] as $language_id => $value) {
            if ((utf8_strlen($value['name']) < 2)) {
                $this->error['name'][$language_id] = $this->language->get('error_name');
            }
        }

        if (!isset($_POST['company_id']) || (int) $_POST['company_id'] < 1) {
            $this->error['company_id'] = $this->language->get('error_company');
        }

        if (empty($this->request->post['freepon_code']) && empty($this->request->post['freepon_download'])) {
            $this->error['warning'] = $this->language->get('error_coupon_code_download');
        }

        $this->load->model('tool/seo');

        if ($this->request->post['keyword']) {
            if (isset($this->request->get['freepon_id'])) {
                $_info = $this->model_deal_freepon->getFreepon($this->request->get['freepon_id']);
            } else {
                $_info['keyword'] = '';
            }
            if ($this->request->post['keyword'] != $_info['keyword'] && $this->model_tool_seo->keywordExists($this->request->post['keyword'])) {
                $this->error['duplicate_seo'] = $this->language->get('error_same_seo_keyword');
            }
        }


        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }






        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'deal/freepon')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
