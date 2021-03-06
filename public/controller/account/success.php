<?php

class ControllerAccountSuccess extends \Core\Controller {

    public function index() {
        $this->language->load('account/success');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_account'),
            'href' => $this->url->link('account/account', '', 'SSL'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_success'),
            'href' => $this->url->link('account/success'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');

        $approval = !$this->config->get('config_customer_approval');

        if ($approval) {
            $this->data['text_message'] = sprintf($this->language->get('text_message'), $this->url->link('information/contact'));
        } else {
            $this->data['text_message'] = sprintf($this->language->get('text_approval'), $this->config->get('config_name'), $this->url->link('information/contact'));
        }

        $this->data['button_continue'] = $this->language->get('button_continue');



        if ($this->cart->hasProducts()) {
            $this->data['continue'] = $this->url->link('checkout/cart', '', 'SSL');
        } else {
            if (isset($this->session->data['redirect'])) {
                $redirect = $this->session->data['redirect'];
                unset($this->session->data['redirect']);
                //    $this->redirect(str_replace('&amp;', '&', $redirect));
                $this->data['continue'] = str_replace('&amp;', '&', $redirect);
            } else {
                $this->data['continue'] = $this->url->link('account/account', '', 'SSL');
            }
        }


        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->template = "common/success.phtml";

        $this->response->setOutput($this->render());
    }

}

?>