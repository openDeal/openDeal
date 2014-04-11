<?php

class ControllerCheckoutCart extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->language->load('checkout/cart');

        if (!isset($this->session->data['vouchers'])) {
            $this->session->data['vouchers'] = array();
        }

        // Update
        if (!empty($this->request->post['quantity'])) {
            foreach ($this->request->post['quantity'] as $key => $value) {
                $this->cart->update($key, $value);
            }

            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);

            $this->redirect($this->url->link('checkout/cart'));
        }

        // Remove
        if (isset($this->request->get['remove'])) {
            $this->cart->remove($this->request->get['remove']);

            unset($this->session->data['vouchers'][$this->request->get['remove']]);

            $this->session->data['success'] = $this->language->get('text_remove');

            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);

            $this->redirect($this->url->link('checkout/cart'));
        }

        // Coupon    
        if (isset($this->request->post['coupon']) && $this->validateCoupon()) {
            $this->session->data['coupon'] = $this->request->post['coupon'];

            $this->session->data['success'] = $this->language->get('text_coupon');

            $this->redirect($this->url->link('checkout/cart'));
        }

        // Voucher
        if (isset($this->request->post['voucher']) && $this->validateVoucher()) {
            $this->session->data['voucher'] = $this->request->post['voucher'];

            $this->session->data['success'] = $this->language->get('text_voucher');

            $this->redirect($this->url->link('checkout/cart'));
        }



        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->addScript('public/view/javascript/colorbox/jquery.colorbox-min.js');
        $this->document->addStyle('public/view/javascript/colorbox/colorbox.css');

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/cart'),
            'text' => $this->language->get('heading_title'),
            'separator' => $this->language->get('text_separator')
        );

        if ($this->cart->hasProducts() || !empty($this->session->data['vouchers'])) {


            $this->data['heading_title'] = $this->language->get('heading_title');

            $this->data['text_next'] = $this->language->get('text_next');
            $this->data['text_next_choice'] = $this->language->get('text_next_choice');
            $this->data['text_use_coupon'] = $this->language->get('text_use_coupon');
            $this->data['text_use_voucher'] = $this->language->get('text_use_voucher');
            $this->data['text_select'] = $this->language->get('text_select');
            $this->data['text_none'] = $this->language->get('text_none');
            $this->data['text_until_cancelled'] = $this->language->get('text_until_cancelled');
            $this->data['text_freq_day'] = $this->language->get('text_freq_day');
            $this->data['text_freq_week'] = $this->language->get('text_freq_week');
            $this->data['text_freq_month'] = $this->language->get('text_freq_month');
            $this->data['text_freq_bi_month'] = $this->language->get('text_freq_bi_month');
            $this->data['text_freq_year'] = $this->language->get('text_freq_year');

            $this->data['column_image'] = $this->language->get('column_image');
            $this->data['column_name'] = $this->language->get('column_name');
            $this->data['column_shipping'] = $this->language->get('column_shipping');
            $this->data['column_quantity'] = $this->language->get('column_quantity');
            $this->data['column_price'] = $this->language->get('column_price');
            $this->data['column_total'] = $this->language->get('column_total');

            $this->data['entry_coupon'] = $this->language->get('entry_coupon');
            $this->data['entry_voucher'] = $this->language->get('entry_voucher');
            $this->data['entry_country'] = $this->language->get('entry_country');
            $this->data['entry_zone'] = $this->language->get('entry_zone');
            $this->data['entry_postcode'] = $this->language->get('entry_postcode');

            $this->data['button_update'] = $this->language->get('button_update');
            $this->data['button_remove'] = $this->language->get('button_remove');
            $this->data['button_coupon'] = $this->language->get('button_coupon');
            $this->data['button_voucher'] = $this->language->get('button_voucher');
            $this->data['button_reward'] = $this->language->get('button_reward');
            $this->data['button_quote'] = $this->language->get('button_quote');
            $this->data['button_shipping'] = $this->language->get('button_shipping');
            $this->data['button_shopping'] = $this->language->get('button_shopping');
            $this->data['button_checkout'] = $this->language->get('button_checkout');


            if (isset($this->error['warning'])) {
                $this->data['error_warning'] = $this->error['warning'];
            } elseif (!$this->cart->hasStock()) {
                $this->data['error_warning'] = $this->language->get('error_stock');
            } else {
                $this->data['error_warning'] = '';
            }

            if (isset($this->session->data['success'])) {
                $this->data['success'] = $this->session->data['success'];

                unset($this->session->data['success']);
            } else {
                $this->data['success'] = '';
            }

            $this->data['action'] = $this->url->link('checkout/cart');



            $this->load->model('tool/image');

            $this->data['deals'] = array();

            $products = $this->cart->getProducts();

            $this->load->model('deal/deal');

            foreach ($products as $product) {
                $product_total = 0;

                $images = $this->model_deal_deal->getDealImages($product['deal_id']);


                if (isset($images[0])) {
                    $image = $this->model_tool_image->resize($images[0], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
                } else {
                    $image = '';
                }


                $price = $this->currency->format($product['price']);
                $total = $this->currency->format(($product['price'] + $product['shipping']['price'] )* $product['quantity']);




                $this->data['products'][] = array(
                    'key' => $product['key'],
                    'thumb' => $image,
                    'name' => $product['title'],
                    'option' => $product['option'],
                    'shipping' => $product['shipping'],
                    'quantity' => $product['quantity'],
                    'is_coupon' => $product['is_coupon'],
                    'stock' => $product['stock'],
                    'price' => $price,
                    'total' => $total,
                    'href' => $this->url->link('deal/deal', 'deal_id=' . $product['deal_id']),
                    'remove' => $this->url->link('checkout/cart', 'remove=' . $product['key'])
                );
            }

            // Gift Voucher
            $this->data['vouchers'] = array();

            if (!empty($this->session->data['vouchers'])) {
                foreach ($this->session->data['vouchers'] as $key => $voucher) {
                    $this->data['vouchers'][] = array(
                        'key' => $key,
                        'description' => $voucher['description'],
                        'amount' => $this->currency->format($voucher['amount']),
                        'remove' => $this->url->link('checkout/cart', 'remove=' . $key)
                    );
                }
            }

            if (isset($this->request->post['next'])) {
                $this->data['next'] = $this->request->post['next'];
            } else {
                $this->data['next'] = '';
            }

            $this->data['coupon_status'] = $this->config->get('coupon_status');

            if (isset($this->request->post['coupon'])) {
                $this->data['coupon'] = $this->request->post['coupon'];
            } elseif (isset($this->session->data['coupon'])) {
                $this->data['coupon'] = $this->session->data['coupon'];
            } else {
                $this->data['coupon'] = '';
            }

            $this->data['voucher_status'] = $this->config->get('voucher_status');

            if (isset($this->request->post['voucher'])) {
                $this->data['voucher'] = $this->request->post['voucher'];
            } elseif (isset($this->session->data['voucher'])) {
                $this->data['voucher'] = $this->session->data['voucher'];
            } else {
                $this->data['voucher'] = '';
            }




            $this->load->model('localisation/country');

            $this->data['countries'] = $this->model_localisation_country->getCountries();

            if (isset($this->request->post['zone_id'])) {
                $this->data['zone_id'] = $this->request->post['zone_id'];
            } elseif (isset($this->session->data['shipping_zone_id'])) {
                $this->data['zone_id'] = $this->session->data['shipping_zone_id'];
            } else {
                $this->data['zone_id'] = '';
            }

            if (isset($this->request->post['postcode'])) {
                $this->data['postcode'] = $this->request->post['postcode'];
            } elseif (isset($this->session->data['shipping_postcode'])) {
                $this->data['postcode'] = $this->session->data['shipping_postcode'];
            } else {
                $this->data['postcode'] = '';
            }

            if (isset($this->request->post['shipping_method'])) {
                $this->data['shipping_method'] = $this->request->post['shipping_method'];
            } elseif (isset($this->session->data['shipping_method'])) {
                $this->data['shipping_method'] = $this->session->data['shipping_method']['code'];
            } else {
                $this->data['shipping_method'] = '';
            }

            
            // Totals
            $this->load->model('setting/extension');

            $total_data = array();
            $total = 0;
            $taxes = array();

            // Display prices
            $sort_order = array();

            $results = $this->model_setting_extension->getExtensions('total');
            

            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('total/' . $result['code']);

                    $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, array());
                }

                $sort_order = array();

                foreach ($total_data as $key => $value) {
                    $sort_order[$key] = $value['sort_order'];
                }

                array_multisort($sort_order, SORT_ASC, $total_data);
            }


            $this->data['totals'] = $total_data;

            $this->data['continue'] = $this->url->link('common/home');

            $this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');

            $this->load->model('setting/extension');

            $this->data['checkout_buttons'] = array();

            $this->template = 'checkout/cart.phtml';

            $this->children = array(
                'common/column_left',
                'common/column_right',
                'common/content_bottom',
                'common/content_top',
                'common/footer',
                'common/header'
            );

            $this->response->setOutput($this->render());
        } else {
            $this->data['heading_title'] = $this->language->get('heading_title');

            $this->data['text_error'] = $this->language->get('text_empty');

            $this->data['button_continue'] = $this->language->get('button_continue');

            $this->data['continue'] = $this->url->link('common/home');

            unset($this->session->data['success']);


            $this->template = 'error/not_found.phtml';


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

    protected function validateCoupon() {
        $this->load->model('checkout/coupon');

        $coupon_info = $this->model_checkout_coupon->getCoupon($this->request->post['coupon']);

        if (!$coupon_info) {
            $this->error['warning'] = $this->language->get('error_coupon');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    protected function validateVoucher() {
        $this->load->model('checkout/voucher');

        $voucher_info = $this->model_checkout_voucher->getVoucher($this->request->post['voucher']);

        if (!$voucher_info) {
            $this->error['warning'] = $this->language->get('error_voucher');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    protected function validateReward() {
        $points = $this->customer->getRewardPoints();

        $points_total = 0;

        foreach ($this->cart->getProducts() as $product) {
            if ($product['points']) {
                $points_total += $product['points'];
            }
        }

        if (empty($this->request->post['reward'])) {
            $this->error['warning'] = $this->language->get('error_reward');
        }

        if ($this->request->post['reward'] > $points) {
            $this->error['warning'] = sprintf($this->language->get('error_points'), $this->request->post['reward']);
        }

        if ($this->request->post['reward'] > $points_total) {
            $this->error['warning'] = sprintf($this->language->get('error_maximum'), $points_total);
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function add() {
        $this->language->load('checkout/cart');


        $data = array();


        if (isset($this->request->post['deal_id'])) {
            $deal_id = $this->request->post['deal_id'];
        } else {
            $deal_id = 0;
        }

        $this->load->model('deal/deal');

        $deal_info = $this->model_deal_deal->getDeal($deal_id);

        if ($deal_info) {
            if (isset($this->request->post['quantity'])) {
                $quantity = $this->request->post['quantity'];
            } else {
                $quantity = 1;
            }

            if (isset($this->request->post['option'])) {
                $option = (int) $this->request->post['option'];
            } else {
                $option = '0';
            }

            if (isset($this->request->post['shipping'])) {
                $shipping = (int) $this->request->post['shipping'];
            } else {
                $shipping = 0;
            }

            $product_options = $this->model_deal_deal->getDealOptions($this->request->post['deal_id']);
            $shippings = $this->model_deal_deal->getDealShippings($deal_info['deal_id']);

            $this->cart->add($this->request->post['deal_id'], $quantity, $option, $shipping);

            $data['success'] = sprintf($this->language->get('text_success'), $this->url->link('deal/deal', 'deal_id=' . $this->request->post['deal_id']), $deal_info['title'], $this->url->link('checkout/cart'));
            $data['redirect'] = $this->url->link('checkout/cart');
        } else {
            $data['error'] = $this->language->get("text_error_deal_not_found");
            $data['redirect'] = str_replace('&amp;', '&', $this->url->link('deal/deal', 'deal_id=' . $this->request->post['deal_id']));
        }

        if (BASE_REQUEST_TYPE == 'ajax') {
            $this->response->setOutput(json_encode($json));
        } else {
            if (isset($data['success'])) {
                $this->session->data['success'] = $data['success'];
            }
            if (isset($data['error'])) {
                $this->session->data['error'] = $data['error'];
            }
            $this->redirect($data['redirect']);
        }
    }


}

?>
