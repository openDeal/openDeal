<?php

class ModelDealDeal extends \Core\Model {

    public function getTotalDeals() {
        $query = $this->db->query("Select count(*) as total from #__deal");
        return $query->row['total'];
    }

    public function getTotalDealsByCompany($company_id) {
        $query = $this->db->query("Select count(*) as total from #__deal where company_id = " . (int) $company_id);
        return $query->row['total'];
    }

    public function getDealCities($city_id) {
        $deal_city_data = array();

        $query = $this->db->query("SELECT * FROM #__deal_to_city WHERE deal_id = '" . (int) $deal_id . "'");

        foreach ($query->rows as $result) {
            $deal_city_data[] = $result['city_id'];
        }

        return $deal_city_data;
    }

    public function getDealShippings($city_id) {
        $deal_shipping_data = array();

        $query = $this->db->query("SELECT * FROM #__deal_shipping WHERE deal_id = '" . (int) $deal_id . "' order by sort_order asc deal_shipping_id asc");

        foreach ($query->rows as $result) {
            $deal_shipping_data[] = $result;
        }

        return $deal_shipping_data;
    }

    public function getDealLayouts($deal_id) {
        $deal_layout_data = array();

        $query = $this->db->query("SELECT * FROM #__deal_to_layout WHERE deal_id = '" . (int) $deal_id . "'");

        foreach ($query->rows as $result) {
            $deal_layout_data[$result['store_id']] = $result['layout_id'];
        }

        return $deal_layout_data;
    }

    public function getDealCategories($deal_id) {
        $deal_category_data = array();

        $query = $this->db->query("SELECT * FROM #__deal_to_category WHERE deal_id = '" . (int) $deal_id . "'");

        foreach ($query->rows as $result) {
            $deal_category_data[] = $result['category_id'];
        }

        return $deal_category_data;
    }

    public function getDeal($deal_id) {
        //(SELECT keyword FROM #__url_alias WHERE query = 'deal_id=" . (int)$deal_id . "') AS keyword
    }

    public function getDeals($data = array()) {

        $sql = "Select * from #__deal d inner join #__deal_description dd on d.deal_id = dd.deal_id where dd.language_id = '" . (int) $this->config->get('config_language_id') . "' ";
        $sort_data = array(
            'dd.title',
            'd.begin_time',
            'd.end_time',
            'd.status',
            'd.current_orders',
            'd.deal_price'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY d.end_time";
        }

        if (isset($data['order']) && ($data['order'] == 'ASC')) {
            $sql .= " ASC";
        } else {
            $sql .= " DESC";
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

    public function getDealDescriptions($deal_id) {
        $deal_description_data = array();

        $query = $this->db->query("SELECT * FROM #__deal_description WHERE deal_id = '" . (int) $category_id . "'");

        foreach ($query->rows as $result) {
            $deal_description_data[$result['language_id']] = array(
                'title' => $result['title'],
                'meta_keyword' => $result['meta_keyword'],
                'meta_description' => $result['meta_description'],
                'details' => $result['details'],
                'introduction' => $result['details'],
                'hightlights' => $result['details'],
                'conditions' => $result['details'],
                'collect_instructions' => $result['details'],
            );
        }

        return $deal_description_data;
    }

    public function addDeal($data) {

        $times = explode("-", $data['deal_times']);
        $data['begin_time'] = strtotime(trim($times[0]));
        $data['end_time'] = strtotime(trim($times[1]));

        $this->db->query("Insert into #__deal set "
                . " market_price = '" . (float) $data['market_price'] . "', "
                . " deal_price = '" . (float) $data['deal_price'] . "', "
                . " begin_time = " . $this->db->quote($data['begin_time']) . ", "
                . " end_time = " . $this->db->quote($data['end_time']) . ", "
                . " tip_point = " . (int) $data['tip_point'] . ", "
                . " stock = " . (int) $data['stock'] . ", "
                . " user_max = " . (int) $data['user_max'] . ", "
                . " company_id = " . (int) $data['company_id'] . ", "
                . " status = " . (int) $data['status'] . ", "
                . " commission = " . (float) $data['commission'] . ", "
                . " feature_weight = " . (int) $data['feature_weight'] . ", "
                . " can_collect = " . (int) $data['can_collect'] . ", "
                . " is_coupon = " . (int) $data['is_coupon'] . ", "
                . " coupon_expiry = " . $this->db->quote(strtotime($data['coupon_expiry'])) . ", "
                . " create_date = " . time());

        $deal_id = $this->db->getLastId();

        if (isset($data['images'])) {
            foreach ($data['images'] as $image) {
                $this->db->query("Insert into #__deal_image set deal_id = " . (int) $deal_id . ", image = " . $this->db->quote($image));
            }
        }

        if (isset($data['deal_description'])) {
            foreach ($data['deal_description'] as $language_id => $description) {
                $this->db->query("Insert into #__deal_description set deal_id = " . (int) $deal_id . ", "
                        . " language_id = " . (int) $language_id . ", "
                        . " title = " . $this->db->quote($description['title']) . ", "
                        . " introduction = " . $this->db->quote($description['introduction']) . ", "
                        . " highlights = " . $this->db->quote($description['highlights']) . ", "
                        . " conditions = " . $this->db->quote($description['conditions']) . ", "
                        . " details = " . $this->db->quote($description['details']) . ", "
                        . " meta_keyword = " . $this->db->quote($description['meta_keyword']) . ", "
                        . " meta_description = " . $this->db->quote($description['meta_description']));
            }
        }

        /** DEAL OPTION TO DO * */
        if (isset($data['deal_option'])) {
            foreach ($deal['deal_option'] as $option) {
                $this->db->query("Insert into #__deal_option set deal_id = " . (int) $deal_id . ", "
                        . " title = " . $this->db->quote($option['title']) . ", "
                        . " price = " . (float) $option['price']);
            }
        }

        if (isset($data['deal_shipping'])) {
            foreach ($data['deal_shipping'] as $shipping) {
                $this->db->query("Insert into #__deal_shipping set deal_id = " . (int) $deal_id . ", "
                        . " title = " . $this->db->quote($shipping['title']) . ", "
                        . " price = " . (float) $shipping['price'] . ", "
                        . " sort_order = " . (int) $shipping['sort_order']);
            }
        }

        if (isset($data['deal_category'])) {
            foreach ($data['deal_category'] as $category) {
                $this->db->query("Insert into #__deal_to_category set deal_id = " . (int) $deal_id . ", category_id = " . (int) $category);
            }
        }

        if (isset($data['deal_city'])) {
            foreach ($data['deal_city'] as $city) {
                $this->db->query("Insert into #__deal_to_city set deal_id = " . (int) $deal_id . ", city_id = " . (int) $city);
            }
        }

        if (isset($data['deal_layout'])) {
            foreach ($data['deal_layout'] as $store_id => $layout) {
                if ($layout['layout_id']) {
                    $this->db->query("INSERT INTO #__deal_to_layout SET deal_id = '" . (int) $deal_id . "', store_id = '" . (int) $store_id . "', layout_id = '" . (int) $layout['layout_id'] . "'");
                }
            }
        }

        if (isset($data['deal_store'])) {
            foreach ($data['deal_store'] as $store_id) {
                $this->db->query("INSERT INTO #__deal_to_store SET deal_id = '" . (int) $deal_id . "', store_id = '" . (int) $store_id . "'");
            }
        }

        if ($data['keyword']) {
            $this->db->query("INSERT INTO #__url_alias SET query = 'deal_id=" . (int) $deal_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
        }

        $this->cache->delete('deal');
    }

}
