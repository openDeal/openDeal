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

    public function getDeals($data = array()) {
        if ($data) {
            $sql = "Select * from #__deal ";
            $sort_data = array(
                'title',
                'begin_time',
                'end_time',
                'status',
                'current_orders',
                'deal_price'
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY end_time";
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
        } else {
            $deal_data = $this->cache->get('deals');
            if (!$deal_data) {
                $query = $this->db->query($sql = "Select * from #__deal order by end_time desc");
                $deal_data = $query->rows;
                $this->cache->set('deals', $deal_data);
            }
            return $deal_data;
        }
    }

}
