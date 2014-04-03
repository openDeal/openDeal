<?php

class ControllerCommonHeader extends \Core\Controller {

    protected function index() {
        $this->data['title'] = $this->document->getTitle();

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }

        if (isset($this->session->data['error']) && !empty($this->session->data['error'])) {
            $this->data['error'] = $this->session->data['error'];

            unset($this->session->data['error']);
        } else {
            $this->data['error'] = '';
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

        $city = $this->city->get();

        $city_link = $this->url->link('common/city', array('city_id' => $city['city_id']));

        $this->data['city_head'] = sprintf($this->language->get('text_current_city'), $city_link, $city['city_name']);
        $this->data['change_city'] = $this->language->get('text_change_city');
        $this->data['daily_arerts'] = $this->language->get('text_daily_arerts');

        $this->data['text_home'] = $this->language->get('text_home');
        $this->data['text_all_deals'] = $this->language->get('text_all_deals');
        $this->data['text_past_deals'] = $this->language->get('text_past_deals');
        $this->data['text_future_deals'] = $this->language->get('text_future_deals');

        $this->data['logged'] = $this->customer->isLogged();

        $this->data['text_logged'] = sprintf($this->language->get('text_logged'), $this->customer->getFirstName());


        $this->data['home'] = $this->url->link("common/home");
        $this->data['current_deals'] = $this->url->link("deal/deal/current");
        $this->data['future_deals'] = $this->url->link("deal/deal/future");
        $this->data['past_deals'] = $this->url->link("deal/deal/expired");

        $this->data['link_login'] = $this->url->link('account/login', '', 'SSL');
        $this->data['text_login'] = $this->language->get('text_login');
        $this->data['link_register'] = $this->url->link('account/register', '', 'SSL');
        $this->data['text_register'] = $this->language->get('text_register');
        $this->data['link_logout'] = $this->url->link('account/logout', '', 'SSL');
        $this->data['text_logout'] = $this->language->get('text_logout');

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


        // Daniel's robot detector
        $status = true;

        if (isset($this->request->server['HTTP_USER_AGENT'])) {
            $robots = explode("\n", trim($this->config->get('config_robots')));

            foreach ($robots as $robot) {
                if ($robot && strpos($this->request->server['HTTP_USER_AGENT'], trim($robot)) !== false) {
                    $status = false;

                    break;
                }
            }
        }




        $this->render();
    }

}
