<?php

class ControllerLocalisationCity extends \Core\Controller {

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('localisation/city');

            $data = array(
                'filter_name' => $this->request->get['filter_name'],
                'start' => 0,
                'limit' => 20
            );

            $results = $this->model_localisation_city->getCities($data);

            foreach ($results as $result) {
                $json[] = array(
                    'city_id' => $result['city_id'],
                    'name' => strip_tags(html_entity_decode($result['city_name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->setOutput(json_encode($json));
    }

}
