<?php

class ControllerInformationInformation extends \Core\Controller {

    public function index() {
        $this->language->load('information/information');

        $this->load->model('public/information');

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        if (isset($this->request->get['information_id'])) {
            $information_id = (int) $this->request->get['information_id'];
        } else {
            $information_id = 0;
        }

        $information_info = $this->model_public_information->getInformation($information_id);

        if ($information_info) {
            $this->document->setTitle($information_info['title']);

            $this->data['breadcrumbs'][] = array(
                'text' => $information_info['title'],
                'href' => $this->url->link('information/information', 'information_id=' . $information_id),
                'separator' => $this->language->get('text_separator')
            );

            $this->data['heading_title'] = $information_info['title'];

            $this->data['button_continue'] = $this->language->get('button_continue');

            $this->data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');

            $this->data['continue'] = $this->url->link('common/home');



            $this->children = array(
                'common/column_left',
                'common/column_right',
                'common/content_top',
                'common/content_bottom',
                'common/footer',
                'common/header'
            );

            $this->response->setOutput($this->render());
        } else {
            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_error'),
                'href' => $this->url->link('information/information', 'information_id=' . $information_id),
                'separator' => $this->language->get('text_separator')
            );

            $this->document->setTitle($this->language->get('text_error'));

            $this->data['heading_title'] = $this->language->get('text_error');

            $this->data['text_error'] = $this->language->get('text_error');

            $this->data['button_continue'] = $this->language->get('button_continue');

            $this->data['continue'] = $this->url->link('common/home');

            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . '/1.1 404 Not Found');


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

    public function info() {
        $this->load->model('public/information');

        if (isset($this->request->get['information_id'])) {
            $information_id = (int) $this->request->get['information_id'];
        } else {
            $information_id = 0;
        }

        $information_info = $this->model_public_information->getInformation($information_id);

        if ($information_info) {
            $output = '<html dir="ltr" lang="en">' . "\n";
            $output .= '<head>' . "\n";
            $output .= '  <title>' . $information_info['title'] . '</title>' . "\n";
            $output .= '  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
            $output .= '  <meta name="robots" content="noindex">' . "\n";
            $output .= '</head>' . "\n";
            $output .= '<body>' . "\n";
            $output .= '  <h1>' . $information_info['title'] . '</h1>' . "\n";
            $output .= html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8') . "\n";
            $output .= '  </body>' . "\n";
            $output .= '</html>' . "\n";

            $this->response->setOutput($output);
        }
    }

}

?>