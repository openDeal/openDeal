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
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'begin_time' => date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), strtotime($result['date_added'])),
                'end_time' => date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), strtotime($result['date_added'])),
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

        $this->data['sort_title'] = $this->url->link('deal/deal', 'token=' . $this->session->data['token'] . '&sort=title' . $url, 'SSL');
        $this->data['sort_date_added'] = $this->url->link('deal/deal', 'token=' . $this->session->data['token'] . '&sort=date_added' . $url, 'SSL');
        $this->data['sort_price'] = $this->url->link('deal/deal', 'token=' . $this->session->data['token'] . '&sort=deal_price' . $url, 'SSL');
        $this->data['sort_begin'] = $this->url->link('deal/deal', 'token=' . $this->session->data['token'] . '&sort=begin_time' . $url, 'SSL');
        $this->data['sort_end'] = $this->url->link('deal/deal', 'token=' . $this->session->data['token'] . '&sort=end_time' . $url, 'SSL');

        $this->data['sort_status'] = $this->url->link('deal/deal', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
        $this->data['sort_ordered'] = $this->url->link('deal/deal', 'token=' . $this->session->data['token'] . '&sort=current_orders' . $url, 'SSL');

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

}
