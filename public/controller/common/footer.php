<?php

class ControllerCommonFooter extends \Core\Controller {

    protected function index() {

        $this->data['text_information'] = $this->language->get('text_information');
        $this->data['text_service'] = $this->language->get('text_service');
        $this->data['text_extra'] = $this->language->get('text_extra');
        $this->data['text_contact'] = $this->language->get('text_contact');
        $this->data['text_return'] = $this->language->get('text_return');
        $this->data['text_sitemap'] = $this->language->get('text_sitemap');
        $this->data['text_manufacturer'] = $this->language->get('text_manufacturer');
        $this->data['text_voucher'] = $this->language->get('text_voucher');
        $this->data['text_affiliate'] = $this->language->get('text_affiliate');
        $this->data['text_special'] = $this->language->get('text_special');
        $this->data['text_account'] = $this->language->get('text_account');
        $this->data['text_order'] = $this->language->get('text_order');
        $this->data['text_wishlist'] = $this->language->get('text_wishlist');
        $this->data['text_newsletter'] = $this->language->get('text_newsletter');

        $this->load->model('public/information');

        $this->data['informations'] = array();

        foreach ($this->model_public_information->getInformations() as $result) {
            if ($result['bottom']) {
                $this->data['informations'][] = array(
                    'title' => $result['title'],
                    'href' => $this->url->link('information/information', 'information_id=' . $result['information_id'])
                );
            }
        }

        $this->data['contact'] = $this->url->link('information/contact');
        $this->data['return'] = $this->url->link('account/return/insert', '', 'SSL');
        $this->data['sitemap'] = $this->url->link('information/sitemap');
        $this->data['manufacturer'] = $this->url->link('product/manufacturer');
        $this->data['voucher'] = $this->url->link('account/voucher', '', 'SSL');
        $this->data['affiliate'] = $this->url->link('affiliate/account', '', 'SSL');
        $this->data['special'] = $this->url->link('product/special');
        $this->data['account'] = $this->url->link('account/account', '', 'SSL');
        $this->data['order'] = $this->url->link('account/order', '', 'SSL');
        $this->data['wishlist'] = $this->url->link('account/wishlist', '', 'SSL');
        $this->data['newsletter'] = $this->url->link('account/newsletter', '', 'SSL');

        $this->data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

        // Whos Online
        if ($this->config->get('config_customer_online')) {
            $this->load->model('tool/online');

            if (isset($this->request->server['REMOTE_ADDR'])) {
                $ip = $this->request->server['REMOTE_ADDR'];
            } else {
                $ip = '';
            }

            if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
                $url = 'http://' . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
            } else {
                $url = '';
            }

            if (isset($this->request->server['HTTP_REFERER'])) {
                $referer = $this->request->server['HTTP_REFERER'];
            } else {
                $referer = '';
            }

            $this->model_tool_online->whosonline($ip, $this->customer->getId(), $url, $referer);
        }


        //Should move to the footer (Will do shortly))
        $this->data['scripts'] = $this->document->getScripts();
        $this->data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');

        // A dirty hack to try to set a cookie for the multi-store feature
        $this->load->model('setting/store');

        $this->data['stores'] = array();

        if ($this->config->get('config_shared') && $status) {
            $this->data['stores'][] = $server . 'public/view/javascript/crossdomain.php?session_id=' . $this->session->getId();

            $stores = $this->model_setting_store->getStores();

            foreach ($stores as $store) {
                $this->data['stores'][] = $store['url'] . 'public/view/javascript/crossdomain.php?session_id=' . $this->session->getId();
            }
        }

        $query = $this->db->query("Select c.* from #__city c inner join #__city_to_store cs on c.city_id = cs.city_id where c.status = 1 and cs.store_id = '" . (int) $this->config->get('config_store_id') . "'");
        foreach ($query->rows as $row) {
            $this->data['cities'][$row['city_id']] = $row;
        }

        $this->data['showWelcome'] = false;

       /* if ($this->config->get('config_show_welcome')) {
            if (isset($this->request->cookie['returnvisitor'])) {
                setcookie('returnvisitor',  time(), time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
            } else {
                $this->data['showWelcome'] = true;
                $query = $this->db->query("Select c.* from #__city c inner join #__city_to_store cs on c.city_id = cs.city_id where c.status = 1 and cs.store_id = '" . (int) $this->config->get('config_store_id') . "'");
                foreach ($query->rows as $row) {
                    $this->data['cities'][$row['city_id']] = $row;
                }
                setcookie('returnvisitor',  time(), time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
               
            }
        }*/

        $this->render();
    }

}
