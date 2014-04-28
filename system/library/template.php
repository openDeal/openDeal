<?php

class Template {

    public $data = array();

    public function __get($key) {
        return \Core\Registry::getInstance()->get($key);
    }

    /**
     * Sets a registry key
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        \Core\Registry::getInstance()->set($key, $value);
    }

    public function fetch($filename) {
        //  $file = DIR_TEMPLATE . $filename;

        if (APP_NAMESPACE == 'public' && file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/' . $filename)) {
            $file = DIR_TEMPLATE . $this->config->get('config_template') . '/template/' . $filename;
        } elseif (APP_NAMESPACE == 'admin' && file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/template/' . $filename)) {
            $file = DIR_TEMPLATE . $this->config->get('config_admin_template') . '/template/' . $filename;
        } elseif (APP_NAMESPACE == 'installer') {
            $file = DIR_TEMPLATE . $filename;
        } else {
            $file = DIR_TEMPLATE . 'default/template/' . $filename;
        }


        if (file_exists($file)) {
            extract($this->data);
            ob_start();
            include($file);
            $content = ob_get_clean();
            return $content;
        } else {
            trigger_error('Error: Could not load template ' . $file . '!');
            exit();
        }
    }

}
