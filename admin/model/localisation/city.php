<?php

class ModelLocalisationCity extends \Core\Model {

    public function addCity($data) {
        $this->db->query("INSERT INTO #__city SET "
                . " city_name = " . $this->db->quote($data['city_name']) . ", "
                . " status = '" . (int) $data['status'] . "', "
                . " latitude = " . $this->db->quote($data['latitude']) . ", "
                . " longitude = " . $this->db->quote($data['longitude']) . ", "
                . " zone_id = " . $this->db->quote($data['zone_id']));

        $city_id = $this->db->getLastId();

        if (isset($data['city_store'])) {
            foreach ($data['city_store'] as $store_id) {
                $this->db->query("INSERT INTO #__city_to_store SET city_id = '" . (int) $city_id . "', store_id = '" . (int) $store_id . "'");
            }
        }

        if (isset($data['city_layout'])) {
            foreach ($data['city_layout'] as $store_id => $layout) {
                if ($layout) {
                    $this->db->query("INSERT INTO #__city_to_layout SET city_id = '" . (int) $city_id . "', store_id = '" . (int) $store_id . "', layout_id = '" . (int) $layout['layout_id'] . "'");
                }
            }
        }

        if ($data['keyword']) {
            $this->db->query("INSERT INTO #__url_alias SET query = 'city_id=" . (int) $city_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
        }

        $this->cache->delete('city');
    }

    public function editCity($city_id, $data) {
        $this->db->query("UPDATE #__city SET "
                . " city_name = " . $this->db->quote($data['city_name']) . ", "
                . " status = '" . (int) $data['status'] . "', "
                . " latitude = " . $this->db->quote($data['latitude']) . ", "
                . " longitude = " . $this->db->quote($data['longitude']) . ", "
                . " zone_id = " . $this->db->quote($data['zone_id'])
                . " WHERE city_id = '" . (int) $city_id . "'");


        $this->db->query("DELETE FROM #__city_to_store WHERE city_id = '" . (int) $city_id . "'");

        if (isset($data['city_store'])) {
            foreach ($data['city_store'] as $store_id) {
                $this->db->query("INSERT INTO #__city_to_store SET city_id = '" . (int) $city_id . "', store_id = '" . (int) $store_id . "'");
            }
        }

        $this->db->query("DELETE FROM #__city_to_layout WHERE city_id = '" . (int) $city_id . "'");

        if (isset($data['city_layout'])) {
            foreach ($data['city_layout'] as $store_id => $layout) {
                if ($layout['layout_id']) {
                    $this->db->query("INSERT INTO #__city_to_layout SET city_id = '" . (int) $city_id . "', store_id = '" . (int) $store_id . "', layout_id = '" . (int) $layout['layout_id'] . "'");
                }
            }
        }

        $this->db->query("DELETE FROM #__url_alias WHERE query = 'city_id=" . (int) $city_id . "'");

        if ($data['keyword']) {
            $this->db->query("INSERT INTO #__url_alias SET query = 'city_id=" . (int) $city_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
        }

        $this->cache->delete('city');
    }

    public function deleteCity($city_id) {
        $this->db->query("DELETE FROM #__city WHERE city_id = '" . (int) $city_id . "'");
        $this->db->query("DELETE FROM #__url_alias WHERE query = 'city_id=" . (int) $city_id . "'");
        $this->db->query("DELETE FROM #__city_to_layout WHERE city_id = '" . (int) $city_id . "'");
        $this->db->query("DELETE FROM #__city_to_store WHERE city_id = '" . (int) $city_id . "'");

        $this->cache->delete('city');
    }

    public function getCity($country_id) {
        $query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM #__url_alias WHERE query = 'city_id=" . (int) $city_id . "') AS keyword FROM #__city WHERE city_id = '" . (int) $city_id . "'");
        return $query->row;
    }

    public function getCities($data = array()) {
        if ($data) {
            $sql = "SELECT * FROM #__city";

            $sort_data = array(
                'city_name'
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY city_name";
            }

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }

            if (isset($data['start']) || isset($data['limit'])) {
                if ($data['start'] < 0) {
                    $data['start'] = 0;
                }

                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }

                $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
            }

            $query = $this->db->query($sql);

            return $query->rows;
        } else {
            $city_data = $this->cache->get('city');

            if (!$city_data) {
                $query = $this->db->query("SELECT * FROM #__city ORDER BY city_name ASC");

                $city_data = $query->rows;

                $this->cache->set('city', $city_data);
            }

            return $city_data;
        }
    }

    public function getTotalCountries() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__city");

        return $query->row['total'];
    }

    public function getCityStores($city_id) {
        $city_store_data = array();

        $query = $this->db->query("SELECT * FROM #__city_to_store WHERE city_id = '" . (int) $city_id . "'");

        foreach ($query->rows as $result) {
            $city_store_data[] = $result['store_id'];
        }

        return $city_store_data;
    }

    public function getCityLayouts($city_id) {
        $city_layout_data = array();

        $query = $this->db->query("SELECT * FROM #__city_to_layout WHERE city_id = '" . (int) $city_id . "'");

        foreach ($query->rows as $result) {
            $city_layout_data[$result['store_id']] = $result['layout_id'];
        }

        return $city_layout_data;
    }

    public function getTotalCitiesByLayoutId($layout_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__city_to_layout WHERE layout_id = '" . (int) $layout_id . "'");

        return $query->row['total'];
    }

}

?>