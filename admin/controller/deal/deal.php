<?php

class ControllerDealDeal extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('deal/deal');
        $this->getList();
    }

    public function insert() {


        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('deal/deal');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_deal_deal->addDeal($this->request->post);

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

            $this->redirect($this->url->link('deal/deal', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function update() {
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('deal/deal');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_deal_deal->editDeal($this->request->get['deal_id'], $this->request->post);

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

            $this->redirect($this->url->link('deal/deal', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function delete() {
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('deal/deal');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $deal_id) {
                $this->model_deal_deal->deleteDeal($deal_id);
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

            $this->redirect($this->url->link('deal/deal', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'd.end_time';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
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
            'href' => $this->url->link('deal/deal', 'token=' . $this->session->data['token'] . $url, 'SSL'),
            'separator' => ' :: '
        );

        $this->data['insert'] = $this->url->link('deal/deal/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['delete'] = $this->url->link('deal/deal/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $this->data['deals'] = array();

        $data = array(
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_admin_limit'),
            'limit' => $this->config->get('config_admin_limit')
        );

        $company_total = $this->model_deal_deal->getTotalDeals();

        $results = $this->model_deal_deal->getDeals($data);

        foreach ($results as $result) {
            $action = array();

            $action[] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('deal/deal/update', 'token=' . $this->session->data['token'] . '&deal_id=' . $result['deal_id'] . $url, 'SSL')
            );

            if ($result['tip_time'] > 0) {
                $tip_time = Date($this->language->get('date_format_short'), $result['tip_time']);
            } else {
                $tip_time = $this->language->get("text_untipped");
            }

            $this->data['deals'][] = array(
                'deal_id' => $result['deal_id'],
                'title' => $result['title'],
                'deal_price' => $result['deal_price'],
                'market_price' => $result['market_price'],
                'tip_time' => $tip_time,
                'status' => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disbled'),
                'current_orders' => $result['current_orders'],
                'date_added' => date($this->language->get('date_format_short'), $result['create_date']),
                'begin_time' => date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), $result['begin_time']),
                'end_time' => date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), $result['end_time']),
                'selected' => isset($this->request->post['selected']) && in_array($result['user_id'], $this->request->post['selected']),
                'action' => $action
            );
        }
        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['text_no_results'] = $this->language->get('text_no_results');

        $this->data['column_title'] = $this->language->get('column_title');
        $this->data['column_price'] = $this->language->get('column_price');
        $this->data['column_tip_time'] = $this->language->get('column_tip_time');
        $this->data['column_begin_time'] = $this->language->get('column_begin_time');
        $this->data['column_end_time'] = $this->language->get('column_end_time');
        $this->data['column_status'] = $this->language->get('column_status');
        $this->data['column_ordered'] = $this->language->get('column_ordered');
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

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['sort_title'] = $this->url->link('deal/deal', 'token=' . $this->session->data['token'] . '&sort=dd.title' . $url, 'SSL');
        $this->data['sort_date_added'] = $this->url->link('deal/deal', 'token=' . $this->session->data['token'] . '&sort=d.date_added' . $url, 'SSL');
        $this->data['sort_price'] = $this->url->link('deal/deal', 'token=' . $this->session->data['token'] . '&sort=d.deal_price' . $url, 'SSL');
        $this->data['sort_begin'] = $this->url->link('deal/deal', 'token=' . $this->session->data['token'] . '&sort=d.begin_time' . $url, 'SSL');
        $this->data['sort_end'] = $this->url->link('deal/deal', 'token=' . $this->session->data['token'] . '&sort=d.end_time' . $url, 'SSL');

        $this->data['sort_status'] = $this->url->link('deal/deal', 'token=' . $this->session->data['token'] . '&sort=d.status' . $url, 'SSL');
        $this->data['sort_ordered'] = $this->url->link('deal/deal', 'token=' . $this->session->data['token'] . '&sort=d.current_orders' . $url, 'SSL');

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
        $pagination->url = $this->url->link('deal/deal', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $this->data['pagination'] = $pagination->render();

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->template = 'deal/deal_list.phtml';


        $this->response->setOutput($this->render());
    }

    protected function getForm() {


        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');


        $this->data['tab_general'] = $this->language->get('tab_general');
        $this->data['tab_data'] = $this->language->get('tab_data');
        $this->data['tab_option'] = $this->language->get('tab_option');
        $this->data['tab_link'] = $this->language->get('tab_link');
        $this->data['tab_image'] = $this->language->get('tab_image');
        $this->data['tab_delivery'] = $this->language->get('tab_delivery');
        $this->data['tab_design'] = $this->language->get('tab_design');

        $this->data['entry_title'] = $this->language->get('entry_title');
        $this->data['entry_introduction'] = $this->language->get('entry_introduction');
        $this->data['entry_highlights'] = $this->language->get('entry_highlights');
        $this->data['entry_conditions'] = $this->language->get('entry_conditions');
        $this->data['entry_details'] = $this->language->get('entry_details');
        $this->data['entry_collect_instructions'] = $this->language->get('entry_collect_instructions');
        $this->data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
        $this->data['entry_meta_description'] = $this->language->get('entry_meta_description');
        $this->data['entry_company'] = $this->language->get('entry_company');
        $this->data['entry_market_price'] = $this->language->get('entry_market_price');
        $this->data['entry_product_name'] = $this->language->get('entry_product_name');
        $this->data['entry_deal_price'] = $this->language->get('entry_deal_price');
        $this->data['entry_begin_time'] = $this->language->get('entry_begin_time');
        $this->data['entry_end_time'] = $this->language->get('entry_end_time');
        $this->data['entry_tip_point'] = $this->language->get('entry_tip_point');
        $this->data['entry_stock'] = $this->language->get('entry_stock');
        $this->data['entry_user_max'] = $this->language->get('entry_user_max');
        $this->data['entry_deal_times'] = $this->language->get('entry_deal_times');
        $this->data['entry_commission'] = $this->language->get('entry_commission');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_priority'] = $this->language->get('entry_priority');
        $this->data['entry_priority_about'] = $this->language->get('entry_priority_about');
        $this->data['entry_keyword'] = $this->language->get('entry_keyword');
        $this->data['entry_category'] = $this->language->get('entry_category');
        $this->data['entry_city'] = $this->language->get('entry_city');
        $this->data['entry_store'] = $this->language->get('entry_store');
        $this->data['entry_is_coupon'] = $this->language->get('entry_is_coupon');
        $this->data['entry_coupon_expiry'] = $this->language->get('entry_coupon_expiry');
        $this->data['entry_can_collect'] = $this->language->get('entry_can_collect');


        $this->data['entry_shipping'] = $this->language->get('entry_shipping');
        $this->data['button_add_shipping'] = $this->language->get('button_add_shipping');
        $this->data['button_add_option'] = $this->language->get("button_add_option");
        $this->data['text_shipping_title'] = $this->language->get('text_shipping_title');
        $this->data['text_shipping_price'] = $this->language->get('text_shipping_price');
        $this->data['text_shipping_order'] = $this->language->get('text_shipping_order');


        $this->data['entry_option'] = $this->language->get("entry_option");
        $this->data['text_option_title'] = $this->language->get("text_option_title");

        $this->data['entry_layout'] = $this->language->get('entry_layout');

        $this->data['text_default'] = $this->language->get('text_default');
        $this->data['text_0_disables'] = $this->language->get('text_0_disables');
        $this->data['text_0_ignores'] = $this->language->get('text_0_ignores');

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
            'href' => $this->url->link('deal/deal', 'token=' . $this->session->data['token'] . $url, 'SSL'),
            'separator' => ' :: '
        );

        if (!isset($this->request->get['deal_id'])) {
            $this->data['action'] = $this->url->link('deal/deal/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $this->data['action'] = $this->url->link('deal/deal/update', 'token=' . $this->session->data['token'] . '&deal_id=' . $this->request->get['deal_id'] . $url, 'SSL');
        }

        $this->data['cancel'] = $this->url->link('deal/deal', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['deal_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $deal_info = $this->model_deal_deal->getDeal($this->request->get['deal_id']);
        }

        $this->data['token'] = $this->session->data['token'];

        $this->load->model('localisation/language');

        $this->data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['deal_description'])) {
            $this->data['deal_description'] = $this->request->post['deal_description'];
        } elseif (isset($this->request->get['deal_id'])) {
            $this->data['deal_description'] = $this->model_deal_deal->getDealDescriptions($this->request->get['deal_id']);
        } else {
            $this->data['deal_description'] = array();
        }

        if (isset($this->request->post['deal_option'])) {
            $this->data['deal_option'] = $this->request->post['deal_option'];
        } elseif (isset($this->request->get['deal_id'])) {
            $this->data['deal_option'] = $this->model_deal_deal->getDealOptions($this->request->get['deal_id']);
        } else {
            $this->data['deal_option'] = array();
        }


        if (isset($this->request->post['company_id'])) {
            $this->data['company_id'] = $this->request->post['company_id'];
        } elseif (!empty($deal_info)) {
            $this->data['company_id'] = $deal_info['company_id'];
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

        if (isset($this->request->post['market_price'])) {
            $this->data['market_price'] = $this->request->post['market_price'];
        } elseif (!empty($deal_info)) {
            $this->data['market_price'] = $deal_info['market_price'];
        } else {
            $this->data['market_price'] = '';
        }

        if (isset($this->request->post['product_name'])) {
            $this->data['product_name'] = $this->request->post['product_name'];
        } elseif (!empty($deal_info)) {
            $this->data['product_name'] = $deal_info['product_name'];
        } else {
            $this->data['product_name'] = '';
        }

        if (isset($this->request->post['deal_price'])) {
            $this->data['deal_price'] = $this->request->post['deal_price'];
        } elseif (!empty($deal_info)) {
            $this->data['deal_price'] = $deal_info['deal_price'];
        } else {
            $this->data['deal_price'] = '';
        }

        if (isset($this->request->post['deal_times'])) {
            $this->data['deal_times'] = $this->request->post['deal_times'];
        } elseif (!empty($deal_info)) {
            $this->data['deal_times'] = DATE("Y/m/d h:i A", $deal_info['begin_time']) . ' - ' . DATE("Y/m/d h:i A", $deal_info['end_time']);
        } else {
            $this->data['deal_times'] = '';
        }

        if (isset($this->request->post['tip_point'])) {
            $this->data['tip_point'] = $this->request->post['tip_point'];
        } elseif (!empty($deal_info)) {
            $this->data['tip_point'] = $deal_info['tip_point'];
        } else {
            $this->data['tip_point'] = '1';
        }

        if (isset($this->request->post['stock'])) {
            $this->data['stock'] = $this->request->post['entry_stock'];
        } elseif (!empty($deal_info)) {
            $this->data['stock'] = $deal_info['stock'];
        } else {
            $this->data['stock'] = '0';
        }

        if (isset($this->request->post['user_max'])) {
            $this->data['user_max'] = $this->request->post['user_max'];
        } elseif (!empty($deal_info)) {
            $this->data['user_max'] = $deal_info['user_max'];
        } else {
            $this->data['user_max'] = '0';
        }

        if (isset($this->request->post['commission'])) {
            $this->data['commission'] = $this->request->post['commission'];
        } elseif (!empty($deal_info)) {
            $this->data['commission'] = $deal_info['commission'];
        } else {
            $this->data['commission'] = '0';
        }

        if (isset($this->request->post['feature_weight'])) {
            $this->data['feature_weight'] = $this->request->post['feature_weight'];
        } elseif (!empty($deal_info)) {
            $this->data['feature_weight'] = $deal_info['feature_weight'];
        } else {
            $this->data['feature_weight'] = '0';
        }

        if (isset($this->request->post['status'])) {
            $this->data['status'] = $this->request->post['status'];
        } elseif (!empty($deal_info)) {
            $this->data['status'] = $deal_info['status'];
        } else {
            $this->data['status'] = '0';
        }

        if (isset($this->request->post['keyword'])) {
            $this->data['keyword'] = $this->request->post['keyword'];
        } elseif (!empty($deal_info)) {
            $this->data['keyword'] = $deal_info['keyword'];
        } else {
            $this->data['keyword'] = '';
        }

        $this->load->model('deal/category');
        if (isset($this->request->post['deal_category'])) {
            $categories = $this->request->post['deal_category'];
        } elseif (!empty($deal_info)) {
            $categories = $this->model_deal_deal->getDealCategories($this->request->get['deal_id']);
        } else {
            $categories = array();
        }

        $this->data['deal_categories'] = array();
        foreach ($categories as $category_id) {
            $category_info = $this->model_deal_category->getCategory($category_id);

            if ($category_info) {
                $this->data['deal_categories'][] = array(
                    'category_id' => $category_info['category_id'],
                    'name' => ($category_info['path'] ? $category_info['path'] . ' &gt; ' : '') . $category_info['name']
                );
            }
        }

        $this->load->model('localisation/city');
        if (isset($this->request->post['deal_city'])) {
            $cities = $this->request->post['deal_city'];
        } elseif (!empty($deal_info)) {
            $cities = $this->model_deal_deal->getDealCities($this->request->get['deal_id']);
        } else {
            $cities = array();
        }

        $this->data['deal_cities'] = array();
        foreach ($cities as $city_id) {
            $city_info = $this->model_localisation_city->getCity($city_id);
            if ($city_info) {
                $this->data['deal_cities'][] = array(
                    'city_id' => $city_info['city_id'],
                    'name' => $city_info['city_name']
                );
            }
        }



        $this->load->model('tool/image');


        $this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

        if (isset($this->request->post['deal_image'])) {
            $images = $this->request->post['deal_image'];
        } elseif (!empty($deal_info)) {
            $images = $this->model_deal_deal->getDealImages($this->request->get['deal_id']);
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

        if (isset($this->request->post['is_coupon'])) {
            $this->data['is_coupon'] = $this->request->post['is_coupon'];
        } elseif (!empty($deal_info)) {
            $this->data['is_coupon'] = $deal_info['is_coupon'];
        } else {
            $this->data['is_coupon'] = '1';
        }

        if (isset($this->request->post['coupon_expiry'])) {
            $this->data['coupon_expiry'] = $this->request->post['coupon_expiry'];
        } elseif (!empty($deal_info)) {
            $this->data['coupon_expiry'] = Date("Y/m/d H:i A", $deal_info['is_coupon']);
        } else {
            $this->data['coupon_expiry'] = Date("Y/m/d H:i A", strtotime("+ 60 days"));
        }

        $this->document->addScript('view/theme/default/js/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('view/theme/default/css/bootstrap-datetimepicker/bootstrap-datetimepicker.css');


        //$entry_can_collect
        if (isset($this->request->post['can_collect'])) {
            $this->data['can_collect'] = $this->request->post['can_collect'];
        } elseif (!empty($deal_info)) {
            $this->data['can_collect'] = $deal_info['can_collect'];
        } else {
            $this->data['is_coupon'] = '0';
        }

        if (isset($this->request->post['deal_shipping'])) {
            $this->data['deal_shipping'] = $this->request->post['deal_shipping'];
        } elseif (!empty($deal_info)) {
            $this->data['deal_shipping'] = $this->model_deal_deal->getDealShippings($this->request->get['deal_id']);
        } else {
            $this->data['deal_shipping'] = array();
        }



        $this->load->model('setting/store');

        $this->data['stores'] = $this->model_setting_store->getStores();

        if (isset($this->request->post['deal_store'])) {
            $this->data['deal_store'] = $this->request->post['deal_store'];
        } elseif (isset($this->request->get['deal_id'])) {
            $this->data['deal_store'] = $this->model_deal_deal->getDealStores($this->request->get['deal_id']);
        } else {
            $this->data['deal_store'] = array(0);
        }

        if (isset($this->request->post['deal_layout'])) {
            $this->data['deal_layout'] = $this->request->post['deal_layout'];
        } elseif (isset($this->request->get['deal_id'])) {
            $this->data['deal_layout'] = $this->model_deal_deal->getDealLayouts($this->request->get['deal_id']);
        } else {
            $this->data['deal_layout'] = array();
        }

        $this->load->model('design/layout');

        $this->data['layouts'] = $this->model_design_layout->getLayouts();



        $this->data['token'] = $this->session->data['token'];

        $this->template = 'deal/deal_form.phtml';

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
        if (!$this->user->hasPermission('modify', 'deal/deal')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        foreach ($this->request->post['deal_description'] as $language_id => $value) {
            if ((utf8_strlen($value['title']) < 2)) {
                $this->error['title'][$language_id] = $this->language->get('error_title');
            }
        }

        if (!isset($_POST['company_id']) || (int) $_POST['company_id'] < 1) {
            $this->error['company_id'] = $this->language->get('error_company');
        }


        $this->load->model('tool/seo');

        if ($this->request->post['keyword']) {
            if (isset($this->request->get['deal_id'])) {
                $_info = $this->model_deal_deal->getDeal($this->request->get['deal_id']);
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

}
