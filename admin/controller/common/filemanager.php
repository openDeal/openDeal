<?php

class ControllerCommonFilemanager extends \Core\Controller {

    public function index() {
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $this->data['base'] = HTTPS_SERVER;
        } else {
            $this->data['base'] = HTTP_SERVER;
        }

        if (isset($this->request->get['field'])) {
            $this->data['field'] = $this->request->get['field'];
        } else {
            $this->data['field'] = '';
        }

        if (isset($this->request->get['CKEditorFuncNum'])) {
            $this->data['fckeditor'] = $this->request->get['CKEditorFuncNum'];
        } else {
            $this->data['fckeditor'] = false;
        }
        
        $this->data['image_url'] =  HTTP_CATALOG . 'image/';

        $this->load->model('tool/image');

        $this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

        $this->data['lang'] = $this->language->get('code');

        $this->data['filemanger_langfile'] = is_file(DIR_APPLICATION . 'view/elfinder/js/i18n/elfinder.' . $this->data['lang'] . ' .js') ? 'view/elfinder/js/i18n/elfinder.' . $this->data['lang'] . ' .js' : false;

        $this->response->setOutput($this->render());
    }

    public function image() {
        $this->load->model('tool/image');

        if (isset($this->request->get['image'])) {
            $this->response->setOutput($this->model_tool_image->resize(html_entity_decode($this->request->get['image'], ENT_QUOTES, 'UTF-8'), 100, 100));
        }
    }

    public function connector() {
        $this->config->set('config_error_display',0);
        error_reporting(0);
        include_once DIR_SYSTEM . 'vendor/elfinder' . DIRECTORY_SEPARATOR . 'elFinderConnector.class.php';
        include_once DIR_SYSTEM . 'vendor/elfinder' . DIRECTORY_SEPARATOR . 'elFinder.class.php';
        include_once DIR_SYSTEM . 'vendor/elfinder' . DIRECTORY_SEPARATOR . 'elFinderVolumeDriver.class.php';
        include_once DIR_SYSTEM . 'vendor/elfinder' . DIRECTORY_SEPARATOR . 'elFinderVolumeLocalFileSystem.class.php';

        $opts = array(
            // 'debug' => true,
            'roots' => array(
                array(
                    'driver' => 'LocalFileSystem', // driver for accessing file system (REQUIRED)
                    'path' => DIR_IMAGE . 'data/', // path to files (REQUIRED)
                    'URL' => HTTP_CATALOG . 'image/data/', // URL to files (REQUIRED)
                    'accessControl' => 'filemanager_access', // disable and hide dot starting files (OPTIONAL)
                   'tmbPath' => DIR_IMAGE . 'tmb',
                    'tmbURL' => HTTP_CATALOG . 'image/tmb/',
                    'attributes' => array(
                        array(// hide readmes
                            'pattern' => '/\.(txt|html|php|py|pl|sh|xml|htm|phtml)$/i',
                            'read' => false,
                            'write' => false,
                            'locked' => true,
                            'hidden' => true
                        )
                    )
                )
            )
        );

        $connector = new elFinderConnector(new elFinder($opts));
        $connector->run();
    }
    
    

}

function filemanager_access($attr, $path, $data, $volume) {    
    return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
            ? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
            : null;                                    // else elFinder decide it itself
}
