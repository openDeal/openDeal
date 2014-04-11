<?php

class ModelTotalShipping extends \Core\Model {

    public function getTotal(&$total_data, &$total) {
        $this->language->load('total/shipping');

        $sub_total = $this->cart->getShippingTotal();

        if ($sub_total > 0) {
            $total_data[] = array(
                'code' => 'shipping',
                'title' => $this->language->get('text_shipping'),
                'text' => $this->currency->format($sub_total),
                'value' => $sub_total,
                'sort_order' => $this->config->get('shipping_sort_order')
            );
        }
        $total += $sub_total;
    }

}

?>