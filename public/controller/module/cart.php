<?php

class ControllerModuleCart extends \Core\Controller {

    protected function index() {
        $this->language->load('module/cart');

        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['text_subtotal'] = $this->language->get('text_subtotal');
        $this->data['text_empty'] = $this->language->get('text_empty');
        $this->data['text_remove'] = $this->language->get('text_remove');
        $this->data['text_confirm'] = $this->language->get('text_confirm');
        $this->data['text_cart'] = $this->language->get('text_cart');
        $this->data['text_checkout'] = $this->language->get('text_checkout');
        $this->data['button_checkout'] = $this->language->get('button_checkout');
        $this->data['button_remove'] = $this->language->get('button_remove');

        $this->data['cart'] = $this->url->link('checkout/cart');
        $this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');

        // Get Cart Products
        $this->data['products'] = array();

        $this->load->model('deal/deal');
        $this->load->model('tool/image');

        foreach ($this->cart->getProducts() as $product) {

            $product_total = 0;

            $images = $this->model_deal_deal->getDealImages($product['deal_id']);


            if (isset($images[0])) {
                $image = $this->model_tool_image->resize($images[0], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
            } else {
                $image = '';
            }


            $price = $this->currency->format($product['price']);
            $total = $this->currency->format(($product['price'] + $product['shipping']['price'] ) * $product['quantity']);




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

        if (isset($this->session->data['vouchers']) && $this->session->data['vouchers']) {
            foreach ($this->session->data['vouchers'] as $key => $voucher) {
                $this->data['vouchers'][] = array(
                    'key' => $key,
                    'description' => $voucher['description'],
                    'amount' => $this->currency->format($voucher['amount']),
                    'remove' => $this->url->link('checkout/cart', 'remove=' . $key)
                );
            }
        }

        $this->data['display_price'] = TRUE;

        /* if (!$this->config->get('config_customer_price')) {
          $this->data['display_price'] = TRUE;
          } elseif ($this->customer->isLogged()) {
          $this->data['display_price'] = TRUE;
          } else {
          $this->data['display_price'] = FALSE;
          } */

        // Calculate Totals
        $total_data = array();
        $total = 0;
        //$taxes = $this->cart->getTaxes();

        if ($this->data['display_price']) {
            $this->load->model('setting/extension');

            $sort_order = array();

            $results = $this->model_setting_extension->getExtensions('total');

            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('total/' . $result['code']);

                    $this->{'model_total_' . $result['code']}->getTotal($total_data, $total);
                }
            }
        }

        $sort_order = array();

        foreach ($total_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $total_data);

        $this->data['totals'] = $total_data;

        $this->data['ajax'] = $this->config->get('cart_ajax');

        $this->id = 'cart';

        $this->template = 'module/cart.phtml';


        $this->render();
    }

    public function update() {

        $this->language->load('checkout/cart');
        if (!empty($this->request->post['quantity'])) {
            foreach ($this->request->post['quantity'] as $key => $value) {
                $this->cart->update($key, $value);
            }

            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);

            $data = array();

            $cart = $this->cart->getProducts();
            $stock = $this->cart->hasStock();
            $data['product_count'] = $this->cart->hasProducts();

            $data['cart_stock'] = ($stock === false) ? $this->language->get('error_stock') : '';
            if (!$this->cart->hasProducts()) {
                $data['cart_stock'] = $this->language->get('text_empty');
            }
            if (isset($cart[$key])) {
                $cart[$key]['total'] = $this->currency->format(($cart[$key]['price'] + $cart[$key]['shipping']['price'] ) * $cart[$key]['quantity']);
                $data['item'] = $cart[$key];
            } else {
                $data['item'] = array('key' => $key, 'quantity' => 0);
            }
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


            $data['totals'] = $total_data;
            echo json_encode($data);
            exit;
            /*  if (isset($cart[$key])) {
              echo json_encode(array('quantity' => 0));
              } else {
              echo json_encode($cart[$key]);
              } */

            //       $this->redirect($this->url->link('checkout/cart'));
        }
    }

    public function callback() {
        $this->language->load('module/cart');

        $this->load->model('tool/seo_url');

        unset($this->session->data['payment_methods']);
        unset($this->session->data['payment_method']);

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {

            if (isset($this->request->post['remove'])) {
                $result = explode('_', $this->request->post['remove']);
                $this->cart->remove(trim($result[1]));
            } else {
                if (isset($this->request->post['option'])) {
                    $option = $this->request->post['option'];
                } else {
                    $option = array();
                }

                $this->cart->add($this->request->post['product_id'], $this->request->post['quantity'], $option);
            }
        }

        $output = '<table cellpadding="0" cellspacing="0">';

        if ($this->cart->getProducts()) {

            foreach ($this->cart->getProducts() as $product) {
                $output .= '<tr>';
                $output .= '<td width="1" valign="top" align="left"><span class="cart_remove" id="remove_ ' . $product['key'] . '" />&nbsp;</span></td><td width="1" valign="top" align="right">' . $product['quantity'] . '&nbsp;x&nbsp;</td>';
                $output .= '<td align="left" valign="top"><a href="' . $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product&product_id=' . $product['product_id']) . '">' . $product['name'] . '</a>';
                $output .= '<div>';

                foreach ($product['option'] as $option) {
                    $output .= ' - <small style="color: #999;">' . $option['name'] . ' ' . $option['value'] . '</small><br />';
                }

                $output .= '</div></td>';
                $output .= '</tr>';
            }

            $output .= '</table>';
            $output .= '<br />';

            $total = 0;
            $taxes = $this->cart->getTaxes();

            $this->load->model('checkout/extension');

            $sort_order = array();

            $view = HTTP_SERVER . 'index.php?route=checkout/cart';
            $checkout = HTTPS_SERVER . 'index.php?route=checkout/shipping';

            $results = $this->model_checkout_extension->getExtensions('total');

            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value['key'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            foreach ($results as $result) {
                $this->load->model('total/' . $result['key']);

                $this->{'model_total_' . $result['key']}->getTotal($total_data, $total, $taxes);
            }

            $sort_order = array();

            foreach ($total_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $total_data);

            $output .= '<div class="linebreak"></div>';
            $output .= '<table cellpadding="0" cellspacing="0" class="price_tab">';
            foreach ($total_data as $total) {
                $output .= '<tr>';
                $output .= '<td align="right"><span class="cart_module_total"><b>' . $total['title'] . '</b></span></td>';
                $output .= '<td align="right"><span class="cart_module_total">' . $total['text'] . '</span></td>';
                $output .= '</tr>';
            }
            $output .= '</table>';
        } else {
            $output .= '<div style="text-align: center;">' . $this->language->get('text_empty') . '</div>';
        }

        $this->response->setOutput($output, $this->config->get('config_compression'));
    }

}

?>