<?php

class Cart {

    private $config;
    private $db;
    private $data = array();

    public function __construct($registry) {
        $this->config = $registry->get('config');
        $this->customer = $registry->get('customer');
        $this->session = $registry->get('session');
        $this->db = $registry->get('db');
        $this->tax = $registry->get('tax');
        $this->weight = $registry->get('weight');

        if (!isset($this->session->data['cart']) || !is_array($this->session->data['cart'])) {
            $this->session->data['cart'] = array();
        }
    }

    public function getProducts() {
        if (!$this->data) {
            foreach ($this->session->data['cart'] as $key => $quantity) {

                $product = explode(':', $key);
                $deal_id = $product[0];
                $stock = true;
                $option_id = (int) $product[1];
                $shipping_id = (int) $product[2];
                $key = $deal_id . ':' . $option_id . ':' . $shipping_id;

                $deal_query = $this->db->query("SELECT * FROM #__deal d LEFT JOIN #__deal_description dd ON (d.deal_id = dd.deal_id) WHERE d.deal_id = '" . (int) $deal_id . "' AND dd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND  d.begin_time <= '" . time() . "'  and d.end_time > '" . time() . "'  AND d.status = '1'");



                if ($deal_query->num_rows) {
                    $deal_price = $deal_query->row['deal_price'];
                    $option_title = $deal_query->row['product_name'];
                    if ($option_id > 0) {
                        $option_query = $this->db->query("SELECT * from #__deal_option where deal_option_id = " . (int) $option_id);
                        if (!$option_query->num_rows) {
                            $this->remove($key);
                            continue;
                        } else {
                            $option_title = $option_query->row['title'];
                            $deal_price = $option_query->row['price'];
                        }
                    }


                    // Stock
                    if ($deal_query->row['stock'] > 0 && (($deal_query->row['stock'] - $deal_query->row['current_orders']) < $quantity)) {
                        $stock = false;
                    }

                    $shipping_price = 0;
                    $shipping_title = 'Collect';
                    
                    if ($shipping_id > 0) {
                        $shipping_query = $this->db->query("Select * from #__deal_shipping where deal_shipping_id = " . (int) $shipping_id);
                        if ($shipping_query->num_rows) {
                            $shipping_price = $shipping_query->row['price'];
                            $shipping_title = $shipping_query->row['title'];
                        } else {
                            $this->remove($key);
                        }
                    }



                    $this->data[$key] = array(
                        'key' => $key,
                        'deal_id' => $deal_query->row['deal_id'],
                        'title' => $deal_query->row['title'],
                        'shipping' => array(
                            'deal_shipping_id' => $shipping_id,
                            'price' => (float)$shipping_price,
                            'title' => $shipping_title
                        ),
                        'option' => array(
                            'deal_option_id' => $option_id,
                            'title' => $option_title
                        ),
                        'stock' => $stock,
                        'is_coupon' => $deal_query->row['is_coupon'],
                        'coupon_expiry' => $deal_query->row['coupon_expiry'],
                        'price' => ($deal_price),
                        'total' => ($deal_price) * $quantity,
                        'quantity' => $quantity
                    );
                } else {
                    $this->remove($key);
                }
            }
        }
        
        return $this->data;
    }

    public function add($deal_id, $qty = 1, $option = 0, $shipping_id = '0') {
        $key = (int) $deal_id . ':' . (int) $option . ":" . (int) $shipping_id;


        if ((int) $qty && ((int) $qty > 0)) {
            if (!isset($this->session->data['cart'][$key])) {
                $this->session->data['cart'][$key] = (int) $qty;
            } else {
                $this->session->data['cart'][$key] += (int) $qty;
            }
        }

        $this->data = array();
    }

    public function update($key, $qty) {
        if ((int) $qty && ((int) $qty > 0)) {
            $this->session->data['cart'][$key] = (int) $qty;
        } else {
            $this->remove($key);
        }

        $this->data = array();
    }

    public function remove($key) {
        if (isset($this->session->data['cart'][$key])) {
            unset($this->session->data['cart'][$key]);
        }

        $this->data = array();
    }

    public function clear() {
        $this->session->data['cart'] = array();
        $this->data = array();
    }

    public function getSubTotal() {
        $total = 0;

        foreach ($this->getProducts() as $product) {
            $total += $product['total'];
        }

        return $total;
    }
    
    public function getShippingTotal() {
        $total = 0;

        foreach ($this->getProducts() as $product) {
            $total += ($product['shipping']['price'] * $product['quantity']);
        }

        return $total;
    }

    public function getTotal() {

        $total = 0;

        foreach ($this->getProducts() as $product) {
            $total += $product['total'];
            $total += ($product['shipping']['price'] * $product['quantity']);
        }

        return $total;
    }

    public function countProducts() {
        $product_total = 0;

        $products = $this->getProducts();

        foreach ($products as $product) {
            $product_total += $product['quantity'];
        }

        return $product_total;
    }

    public function hasProducts() {
        return count($this->session->data['cart']);
    }

    public function hasStock() {
        $stock = true;

        foreach ($this->getProducts() as $product) {
            if (!$product['stock']) {
                $stock = false;
            }
        }

        return $stock;
    }

    public function hasShipping() {
        $shipping = false;

        foreach ($this->getProducts() as $product) {
            if ($product['shipping']['deal_shipping_id'] > 0) {
                $shipping = true;

                break;
            }
        }

        return $shipping;
    }

}

?>