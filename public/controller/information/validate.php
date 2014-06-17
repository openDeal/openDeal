<?php

class ControllerInformationValidate extends \Core\Controller {

    public function index() {

        $this->language->load('information/validate');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('information/validate'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['legend_validate'] = $this->language->get('legend_validate');
        $this->data['entry_code'] = $this->language->get('entry_code');
        $this->data['entry_secret'] = $this->language->get('entry_secret');
        $this->data['button_continue'] = $this->language->get('button_continue');
        $this->data['action'] = $this->url->link('information/validate');

        $this->data['coupon_info'] = false;

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $code = $this->request->post['code'];
            $secret = $this->request->post['secret'];
            $this->load->model('account/order');
            $order_info = $this->model_account_order->getOrderFromCouponAndSecret($code, $secret);
            if ($order_info) {
                if ($order_info['coupon_redeemed'] > 0) {
                    $this->data['coupon_info'] = array(
                        'status' => 'danger',
                        'message' => $this->language->get('error_coupon_redeemed')
                    );
                } elseif (time() > strtotime($order_info['coupon_expire'])) {
                     $link = $this->url->link('information/validate/validate', '&validate_id=' . base64_encode($code . ':' . $secret));
                  
                    $this->data['coupon_info'] = array(
                        'status' => 'danger',
                        'message' => $this->language->get('error_coupon_expired') . '<br /><a class="btn btn-primary" href="' . $link . '">' . $this->language->get('buttom_mark_claimed') . '</a>'
                    );
                } else {
                    $expires = date($this->language->get('date_format_long') . ' ' . $this->language->get('time_format'), strtotime($order_info['coupon_expire']));
                    $link = $this->url->link('information/validate/validate', '&validate_id=' . base64_encode($code . ':' . $secret));
                    $message = $this->language->get('text_coupon_valid') . '<br />';
                    $message .= $this->language->get('text_expires') . ': ' . $expires . '<br />';
                    $message .= '<a class="btn btn-primary" href="' . $link . '">' . $this->language->get('buttom_mark_claimed') . '</a>';


                    $this->data['coupon_info'] = array(
                        'status' => 'success',
                        'message' => $message,
                    );
                }
            } else {
                $this->data['coupon_info'] = array(
                    'status' => 'danger',
                    'message' => $this->language->get('error_coupon_not_found')
                );
            }
            // debugPre($order_info);
        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    public function validate() {
        $id = explode(":", base64_decode($this->request->get['validate_id']));
        $code = isset($id[0])?$id[0]:'0';
        $secret = isset($id[1])?$id[1]:'0';
        $this->load->model('account/order');

        $this->language->load('information/validate');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('information/validate'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['legend_validate'] = $this->language->get('legend_validate');
        $this->data['entry_code'] = $this->language->get('entry_code');
        $this->data['entry_secret'] = $this->language->get('entry_secret');
        $this->data['button_continue'] = $this->language->get('button_continue');
        $this->data['action'] = $this->url->link('information/validate');

        $this->data['coupon_info'] = false;

        $order_info = $this->model_account_order->getOrderFromCouponAndSecret($code, $secret);
        if ($order_info) {
            $this->model_account_order->redeemCoupon($code, $secret);

            $this->data['coupon_info'] = array(
                'status' => 'success',
                'message' => $this->language->get("text_coupon_redeemed"),
            );
        } else {
            $this->data['coupon_info'] = array(
                'status' => 'danger',
                'message' => $this->language->get('error_coupon_not_found')
            );
        }


        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

}
