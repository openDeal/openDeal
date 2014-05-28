<?php

class ControllerModuleCategory extends \Core\Controller {

    protected function index($setting) {
        $this->language->load('module/category');

        $this->data['heading_title'] = $this->language->get('heading_title');

        if (isset($this->request->get['path'])) {
            $parts = explode('_', (string) $this->request->get['path']);
        } else {
            $parts = array();
        }

        if (isset($parts[0])) {
            $this->data['category_id'] = $parts[0];
        } else {
            $this->data['category_id'] = 0;
        }

        if (isset($parts[1])) {
            $this->data['child_id'] = $parts[1];
        } else {
            $this->data['child_id'] = 0;
        }

        $this->load->model('deal/category');

        $this->load->model('deal/deal');

        $this->data['categories'] = array();

        $categories = $this->model_deal_category->getCategories(0);

        foreach ($categories as $category) {
            $total = $this->model_deal_deal->getTotalDeals(array('filter_category_id' => $category['category_id']));

            $children_data = array();

            $children = $this->model_deal_category->getCategories($category['category_id']);

            foreach ($children as $child) {
                $data = array(
                    'filter_category_id' => $child['category_id'],
                    'filter_sub_category' => true
                );

                $product_total = $this->model_deal_deal->getTotalProducts($data);

                $total += $product_total;

                $children_data[] = array(
                    'category_id' => $child['category_id'],
                    'name' => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $product_total . ')' : ''),
                    'href' => $this->url->link('deal/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
                );
            }

            $this->data['categories'][] = array(
                'category_id' => $category['category_id'],
                'name' => $category['name'],
                'total' => $total,
                'children' => $children_data,
                'href' => $this->url->link('deal/category', 'path=' . $category['category_id'])
            );
        }


        $this->template = 'module/category.phtml';


        $this->render();
    }

}

?>