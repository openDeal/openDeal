<?php

class ControllerCommonSeoUrl extends \Core\Controller {

    public $core_routes = array(
        'deal/deal/current' => 'current-deals',
        'deal/deal/expired' => 'recent-deals',
        'deal/deal/future' => 'upcomming-deals',
        'information/contact' => 'contact-us',
    );
    
    public function index() {
        // Add rewrite to url class
        if ($this->config->get('config_seo_url')) {
            $this->url->addRewrite($this);
        }

        // Decode URL
        if (isset($this->request->get['_route_'])) {
            $parts = explode('/', $this->request->get['_route_']);

            foreach ($parts as $part) {
                $query = $this->db->query("SELECT * FROM #__url_alias WHERE keyword = '" . $this->db->escape($part) . "'");

                if ($query->num_rows) {
                    $url = explode('=', $query->row['query']);

                    if ($url[0] == 'deal_id') {
                        $this->request->get['deal_id'] = $url[1];
                    }

                    if ($url[0] == 'category_id') {
                        if (!isset($this->request->get['path'])) {
                            $this->request->get['path'] = $url[1];
                        } else {
                            $this->request->get['path'] .= '_' . $url[1];
                        }
                    }

                    if ($url[0] == 'information_id') {
                        $this->request->get['information_id'] = $url[1];
                    }
                }elseif(in_array($part, $this->core_routes)){
                    $this->request->get['route'] = array_search($part, $this->core_routes);
                } else {
                    $this->request->get['route'] = 'error/not_found';
                }
            }

            if (isset($this->request->get['deal_id'])) {
                $this->request->get['route'] = 'deal/deal';
            } elseif (isset($this->request->get['path'])) {
                $this->request->get['route'] = 'product/category';
            } elseif (isset($this->request->get['information_id'])) {
                $this->request->get['route'] = 'information/information';
            }

            if (isset($this->request->get['route'])) {
                return $this->forward($this->request->get['route']);
            }
        }
    }

    public function rewrite($link) {
        $url_info = parse_url(str_replace('&amp;', '&', $link));

        $url = '';

        $data = array();

        parse_str($url_info['query'], $data);

        foreach ($data as $key => $value) {
            if (isset($data['route'])) {
                if ($data['route'] == 'deal/deal' && $key == 'deal_id' || $data['route'] == 'information/information' && $key = 'information_id') {
                    $query = $this->db->query("SELECT * FROM #__url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int) $value) . "'");
                    if ($query->num_rows) {
                        $url .= '/' . $query->row['keyword'];

                        unset($data[$key]);
                    }
                } elseif ($key == 'path') {
                    $categories = explode('_', $value);

                    foreach ($categories as $category) {
                        $query = $this->db->query("SELECT * FROM #__url_alias WHERE `query` = 'category_id=" . (int) $category . "'");

                        if ($query->num_rows) {
                            $url .= '/' . $query->row['keyword'];
                        }
                    }

                    unset($data[$key]);
                } elseif(isset($this->core_routes[$data['route']])){
                    $url .= '/' . $this->core_routes[$data['route']];
                }
            }
        }

        if ($url || $data['route'] == 'common/home') {
            unset($data['route']);

            $query = '';

            if ($data) {
                foreach ($data as $key => $value) {
                    $query .= '&' . $key . '=' . $value;
                }

                if ($query) {
                    $query = '?' . trim($query, '&');
                }
            }

            return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
        } else {
            return $link;
        }
    }

}

?>