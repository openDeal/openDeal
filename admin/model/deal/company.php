<?php

class ModelDealCompany extends \Core\Model {

    public function getCompanies($data = array()) {
        if ($data) {
            $sql = "Select * from #__company ";
            $sort_data = array(
                'name',
                'company_id',
                'date_added'
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY name";
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
            $company_data = $this->cache->get('companies');
            if (!$company_data) {
                $query = $this->db->query($sql = "Select * from #__company order by name asc");
                $company_data = $query->rows;
                $this->cache->set('categories', $company_data);
            }
            return $company_data;
        }
    }

    public function getTotalCompanies() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__company");

        return $query->row['total'];
    }

    public function getCompanyStores($company_id) {
        $company_store_data = array();

        $query = $this->db->query("SELECT * FROM #__company_to_store WHERE company_id = '" . (int) $company_id . "'");

        foreach ($query->rows as $result) {
            $company_store_data[] = $result['store_id'];
        }

        return $company_store_data;
    }

    public function getCompanyLocations($company_id) {

        $query = $this->db->query("Select * from #__company_location where company_id = '" . (int) $company_id . "'");

        return $query->rows;
    }

    public function addCompany($data) {
        $this->db->query("INSERT INTO #__company SET "
                . "name = " . $this->db->quote($data['name']) . ", "
                . "website = " . $this->db->quote($data['website']) . ", "
                . "image = " . $this->db->quote($data['image']) . ", "
                . "commission = " . $this->db->quote((float) $data['commission']) . ", "
                . "about = " . $this->db->quote($data['about']) . ", "
                . "date_added = now(), "
                . "status = " . (int) $data['status']);

        $company_id = $this->db->getLastId();

        if (isset($data['company_store'])) {
            foreach ($data['company_store'] as $store_id) {
                $this->db->query("INSERT INTO #__company_to_store SET company_id = '" . (int) $company_id . "', store_id = '" . (int) $store_id . "'");
            }
        }

        if (isset($data['company_location'])) {
            foreach ($data['company_location'] as $location) {
                $this->db->query("INSERT INTO #__company_location SET company_id = '" . (int) $company_id . "', "
                        . "address = " . $this->db->quote($location['address']) . ", "
                        . "phone = " . $this->db->quote($location['phone']) . ", "
                        . "latitude = " . $this->db->quote($location['latitude']) . ", "
                        . "longitude = " . $this->db->quote($location['longitude']));
            }
        }
    }

    public function getCompany($company_id) {
        $query = $this->db->query("SELECT  * FROM #__company WHERE company_id = '" . (int) $company_id . "'");

        return $query->row;
    }

    public function editCompany($company_id, $data) {
        $this->db->query("update #__company SET "
                . "name = " . $this->db->quote($data['name']) . ", "
                . "website = " . $this->db->quote($data['website']) . ", "
                . "image = " . $this->db->quote($data['image']) . ", "
                . "commission = " . (float) $data['commission'] . ", "
                . "about = " . $this->db->quote($data['about']) . ", "
                . "date_added = now(), "
                . "status = " . (int) $data['status'] . " "
                . "where company_id = " . (int) $company_id);

        $this->db->query("DELETE FROM #__company_to_store where company_id = " . (int) $company_id);

        if (isset($data['company_store'])) {
            foreach ($data['company_store'] as $store_id) {
                $this->db->query("INSERT INTO #__company_to_store SET company_id = '" . (int) $company_id . "', store_id = '" . (int) $store_id . "'");
            }
        }

        $this->db->query("DELETE FROM #__company_location where company_id = " . (int) $company_id);
        if (isset($data['company_location'])) {
            foreach ($data['company_location'] as $location) {
                $this->db->query("INSERT INTO #__company_location SET company_id = '" . (int) $company_id . "', "
                        . "address = " . $this->db->quote($location['address']) . ", "
                        . "phone = " . $this->db->quote($location['phone']) . ", "
                        . "latitude = " . $this->db->quote($location['latitude']) . ", "
                        . "longitude = " . $this->db->quote($location['longitude']));
            }
        }
    }

    public function deleteCompany($company_id) {
        $this->db->query("Delete from #__company where company_id = " . (int) $company_id);
        $this->db->query("DELETE FROM #__company_location where company_id = " . (int) $company_id);
        $this->db->query("DELETE FROM #__company_to_store where company_id = " . (int) $company_id);
    }

}
