<?php

abstract class ControllerCommonContentPos extends \Core\Controller {

    protected function fetch($pos) {
        $this->load->model('design/layout');
        $this->load->model('deal/category');
        $this->load->model('deal/deal');
        $this->load->model('deal/freepon');
        $this->load->model('public/information');

        if (isset($this->request->get['route'])) {
            $route = (string) $this->request->get['route'];
        } else {
            $route = 'common/home';
        }

        $layout_id = 0;

        if ($route == 'deal/category' && isset($this->request->get['path'])) {
            $path = explode('_', (string) $this->request->get['path']);

            $layout_id = $this->model_deal_category->getCategoryLayoutId(end($path));
        }

        if (($route == 'deal/deal' || $route == 'deal/deal/buy') && isset($this->request->get['deal_id'])) {
            $layout_id = $this->model_deal_deal->getDealLayoutId($this->request->get['deal_id']);
        }

        if ($route == 'deal/freepon' && isset($this->request->get['freepon_id'])) {
            $layout_id = $this->model_deal_freepon->getFreeponLayoutId($this->request->get['freepon_id']);
        }

        if ($route == 'information/information' && isset($this->request->get['information_id'])) {
            $layout_id = $this->model_public_information->getInformationLayoutId($this->request->get['information_id']);
        }

        if (!$layout_id) {
            $layout_id = $this->model_design_layout->getLayout($route);
        }

        if (!$layout_id) {
            $layout_id = $this->config->get('config_layout_id');
        }


        $module_data = array();

        $this->load->model('setting/extension');

        $extensions = $this->model_setting_extension->getExtensions('module');

        foreach ($extensions as $extension) {
            $modules = $this->config->get($extension['code'] . '_module');

            if ($modules) {
                foreach ($modules as $module) {
                    if ($module['layout_id'] == $layout_id && $module['position'] == $pos && $module['status']) {
                        $module_data[] = array(
                            'code' => $extension['code'],
                            'setting' => $module,
                            'sort_order' => $module['sort_order']
                        );
                    }
                }
            }
        }

        $sort_order = array();

        foreach ($module_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $module_data);

        $this->data['modules'] = array();

        foreach ($module_data as $module) {
            $module = $this->getChild('module/' . $module['code'], $module['setting']);

            if ($module) {
                $this->data['modules'][] = $module;
            }
        }


        $this->template = 'common/' . $pos . '.phtml';


        $this->render();
    }

}

?>