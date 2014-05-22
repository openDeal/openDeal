<?php

class ModelDealFreepon extends \Core\Model {

    public function getTotalFreepons() {
        $query = $this->db->query("Select count(*) as total from #__freepon");
        return $query->row['total'];
    }

    public function getFreepons($data = array()) {

        $sql = "Select *,fd.name as name, c.name as company_name from #__freepon f inner join #__freepon_description fd on f.freepon_id = fd.freepon_id "
                . " inner join #__company c on c.company_id = f.company_id "
                . " where fd.language_id = '" . (int) $this->config->get('config_language_id') . "' ";
        $sort_data = array(
            'fd.title',
            'f.begin_time',
            'f.end_time',
            'f.status',
            'f.viewed',
            'f.downloaded',
            'c.name'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY fd.name";
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
    }

    public function addFreepon($data) {

        $times = explode(" - ", $data['freepon_times']);

        $data['begin_time'] = strtotime($times[0]);
        $data['end_time'] = strtotime($times[1]);

        $this->db->query("Insert into #__freepon set "
                . " company_id = " . (int) $data['company_id'] . ", "
                . " download = " . $this->db->quote($data['freepon_download']) . ", "
                . " `code` = " . $this->db->quote($data['freepon_code']) . ", "
                . " begin_time = " . $this->db->quote($data['begin_time']) . ", "
                . " end_time = " . $this->db->quote($data['end_time']) . ", "
                . " status = " . (int) $data['status'] . ", "
                . " feature_weight = " . (int) $data['feature_weight'] . ", "
                . " create_date = " . time());

        $freepon_id = $this->db->getLastId();

        if (isset($data['images'])) {
            foreach ($data['images'] as $image) {
                $this->db->query("Insert into #__freepon_image set freepon_id = " . (int) $freepon_id . ", image = " . $this->db->quote($image));
            }
        }

        if (isset($data['freepon_description'])) {
            foreach ($data['freepon_description'] as $language_id => $description) {
                $this->db->query("Insert into #__freepon_description set freepon_id = " . (int) $freepon_id . ", "
                        . " language_id = " . (int) $language_id . ", "
                        . " name = " . $this->db->quote($description['name']) . ", "
                        . " description = " . $this->db->quote($description['description']) . ", "
                        . " meta_keyword = " . $this->db->quote($description['meta_keyword']) . ", "
                        . " meta_description = " . $this->db->quote($description['meta_description']));
            }
        }



        if (isset($data['freepon_category'])) {
            foreach ($data['freepon_category'] as $category) {
                $this->db->query("Insert into #__freepon_to_category set freepon_id = " . (int) $freepon_id . ", category_id = " . (int) $category);
            }
        }

        if (isset($data['freepon_city'])) {
            foreach ($data['freepon_city'] as $city) {
                $this->db->query("Insert into #__freepon_to_city set freepon_id = " . (int) $freepon_id . ", city_id = " . (int) $city);
            }
        }

        if (isset($data['freepon_layout'])) {
            foreach ($data['freepon_layout'] as $store_id => $layout) {
                if ($layout['layout_id']) {
                    $this->db->query("INSERT INTO #__freepon_to_layout SET freepon_id = '" . (int) $freepon_id . "', store_id = '" . (int) $store_id . "', layout_id = '" . (int) $layout['layout_id'] . "'");
                }
            }
        }

        if (isset($data['freepon_store'])) {
            foreach ($data['freepon_store'] as $store_id) {
                $this->db->query("INSERT INTO #__freepon_to_store SET freepon_id = '" . (int) $freepon_id . "', store_id = '" . (int) $store_id . "'");
            }
        }

        if ($data['keyword']) {
            $this->db->query("INSERT INTO #__url_alias SET query = 'freepon_id=" . (int) $freepon_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
        }

        $this->cache->delete('freepon');
    }

    public function editFreepon($freepon_id, $data) {

        $times = explode(" - ", $data['freepon_times']);

        $data['begin_time'] = strtotime($times[0]);
        $data['end_time'] = strtotime($times[1]);

        $this->db->query("update #__freepon set "
                . " company_id = " . (int) $data['company_id'] . ", "
                . " download = " . $this->db->quote($data['freepon_download']) . ", "
                . " `code` = " . $this->db->quote($data['freepon_code']) . ", "
                . " begin_time = " . $this->db->quote($data['begin_time']) . ", "
                . " end_time = " . $this->db->quote($data['end_time']) . ", "
                . " status = " . (int) $data['status'] . ", "
                . " feature_weight = " . (int) $data['feature_weight'] . ", "
                . " modify_date = " . time() . " where freepon_id = " . (int) $freepon_id);


        $this->db->query("Delete from #__freepon_image where freepon_id = " . (int) $freepon_id);
        if (isset($data['images'])) {
            foreach ($data['images'] as $image) {
                $this->db->query("Insert into #__freepon_image set freepon_id = " . (int) $freepon_id . ", image = " . $this->db->quote($image));
            }
        }

        $this->db->query("Delete from #__freepon_description where freepon_id = " . (int) $freepon_id);
        if (isset($data['freepon_description'])) {
            foreach ($data['freepon_description'] as $language_id => $description) {
                $this->db->query("Insert into #__freepon_description set freepon_id = " . (int) $freepon_id . ", "
                        . " language_id = " . (int) $language_id . ", "
                        . " name = " . $this->db->quote($description['name']) . ", "
                        . " description = " . $this->db->quote($description['description']) . ", "
                        . " meta_keyword = " . $this->db->quote($description['meta_keyword']) . ", "
                        . " meta_description = " . $this->db->quote($description['meta_description']));
            }
        }


        $this->db->query("delete from  #__freepon_to_category where freepon_id = " . (int) $freepon_id);
        if (isset($data['freepon_category'])) {
            foreach ($data['freepon_category'] as $category) {
                $this->db->query("Insert into #__freepon_to_category set freepon_id = " . (int) $freepon_id . ", category_id = " . (int) $category);
            }
        }

        $this->db->query("delete from  #__freepon_to_city where freepon_id = " . (int) $freepon_id);
        if (isset($data['freepon_city'])) {
            foreach ($data['freepon_city'] as $city) {
                $this->db->query("Insert into #__freepon_to_city set freepon_id = " . (int) $freepon_id . ", city_id = " . (int) $city);
            }
        }

        $this->db->query("delete from  #__freepon_to_layout where freepon_id = " . (int) $freepon_id);
        if (isset($data['freepon_layout'])) {
            foreach ($data['freepon_layout'] as $store_id => $layout) {
                if ($layout['layout_id']) {
                    $this->db->query("INSERT INTO #__freepon_to_layout SET freepon_id = '" . (int) $freepon_id . "', store_id = '" . (int) $store_id . "', layout_id = '" . (int) $layout['layout_id'] . "'");
                }
            }
        }

        $this->db->query("delete from  #__freepon_to_store where freepon_id = " . (int) $freepon_id);
        if (isset($data['freepon_store'])) {
            foreach ($data['freepon_store'] as $store_id) {
                $this->db->query("INSERT INTO #__freepon_to_store SET freepon_id = '" . (int) $freepon_id . "', store_id = '" . (int) $store_id . "'");
            }
        }

        $this->db->query("Delete from #__url_alias where query = 'freepon_id=" . (int) $freepon_id . "'");
        if ($data['keyword']) {
            $this->db->query("INSERT INTO #__url_alias SET query = 'freepon_id=" . (int) $freepon_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
        }

        $this->cache->delete('freepon');
    }

    public function deleteFreepon($freepon_id) {
        $this->db->query("Delete from #__freepon where freepon_id = " . (int) $freepon_id);
        $this->db->query("Delete from #__freepon_image where freepon_id = " . (int) $freepon_id);
        $this->db->query("Delete from #__freepon_description where freepon_id = " . (int) $freepon_id);
        $this->db->query("delete from  #__freepon_to_category where freepon_id = " . (int) $freepon_id);
        $this->db->query("delete from  #__freepon_to_city where freepon_id = " . (int) $freepon_id);
        $this->db->query("delete from  #__freepon_to_layout where freepon_id = " . (int) $freepon_id);
        $this->db->query("delete from  #__freepon_to_store where freepon_id = " . (int) $freepon_id);
        $this->db->query("Delete from #__url_alias where query = 'freepon_id=" . (int) $freepon_id . "'");
        $this->cache->delete('freepon');
    }

    public function getFreepon($freepon_id) {
        $query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM #__url_alias WHERE "
                . " query = 'freepon_id=" . (int) $freepon_id . "') AS keyword FROM #__freepon f "
                . " LEFT JOIN #__freepon_description fd ON (f.freepon_id = fd.freepon_id) WHERE "
                . " f.freepon_id = '" . (int) $freepon_id . "' "
                . " AND fd.language_id = '" . (int) $this->config->get('config_language_id') . "'");

        return $query->row;
    }

    public function getFreeponDescriptions($freepon_id) {
        $freepon_description_data = array();

        $query = $this->db->query("SELECT * FROM #__freepon_description WHERE freepon_id = '" . (int) $freepon_id . "'");

        foreach ($query->rows as $result) {
            $freepon_description_data[$result['language_id']] = array(
                'name' => $result['name'],
                'meta_keyword' => $result['meta_keyword'],
                'meta_description' => $result['meta_description'],
                'description' => $result['description'],
            );
        }

        return $freepon_description_data;
    }

    public function getFreeponCategories($freepon_id) {
        $freepon_category_data = array();

        $query = $this->db->query("SELECT * FROM #__freepon_to_category WHERE freepon_id = '" . (int) $freepon_id . "'");

        foreach ($query->rows as $result) {
            $freepon_category_data[] = $result['category_id'];
        }

        return $freepon_category_data;
    }

    public function getFreeponCities($freepon_id) {
        $deal_city_data = array();

        $query = $this->db->query("SELECT city_id FROM #__freepon_to_city WHERE freepon_id = '" . (int) $freepon_id . "'");

        foreach ($query->rows as $result) {
            $freepon_city_data[] = $result['city_id'];
        }

        return $freepon_city_data;
    }

    public function getFreeponImages($freepon_id) {
        $freepon_image_data = array();

        $query = $this->db->query("SELECT * FROM #__freepon_image WHERE freepon_id = '" . (int) $freepon_id . "' order by sort_order asc");

        foreach ($query->rows as $result) {
            $freepon_image_data[] = $result['image'];
        }

        return $freepon_image_data;
    }

    public function getFreeponStores($freepon_id) {
        $freepon_store_data = array();

        $query = $this->db->query("SELECT store_id FROM #__freepon_to_store WHERE freepon_id = '" . (int) $freepon_id . "'");

        foreach ($query->rows as $result) {
            $freepon_store_data[] = $result['store_id'];
        }

        return $freepon_store_data;
    }

    public function getFreeponLayouts($freepon_id) {
        $freepon_layout_data = array();

        $query = $this->db->query("SELECT * FROM #__freepon_to_layout WHERE freepon_id = '" . (int) $freepon_id . "'");

        foreach ($query->rows as $result) {
            $freepon_layout_data[$result['store_id']] = $result['layout_id'];
        }

        return $freepon_layout_data;
    }

}
