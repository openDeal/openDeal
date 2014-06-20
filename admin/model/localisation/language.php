<?php

/**
 * @todo - update all translation tables !!!!
 * @todo - per example in add language
 */
class ModelLocalisationLanguage extends \Core\Model {

    public function addLanguage($data) {
        $this->db->query("INSERT INTO #__language SET name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', locale = '" . $this->db->escape($data['locale']) . "', directory = '" . $this->db->escape($data['directory']) . "', filename = '" . $this->db->escape($data['filename']) . "', image = '" . $this->db->escape($data['image']) . "', sort_order = '" . $this->db->escape($data['sort_order']) . "', status = '" . (int) $data['status'] . "'");

        $this->cache->delete('language');

        //od_category_description
        $query = $this->db->query("SELECT * FROM #__category_description WHERE language_id = '" . (int) $this->config->get('config_language_id') . "'");

        foreach ($query->rows as $category) {
            $this->db->query("INSERT INTO #__category_description SET category_id = '" . (int) $category['category_id'] . "', language_id = '" . (int) $language_id . "', name = '" . $this->db->escape($category['name']) . "'");
        }


        $language_id = $this->db->getLastId();
    }

    public function editLanguage($language_id, $data) {
        $this->db->query("UPDATE #__language SET name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', locale = '" . $this->db->escape($data['locale']) . "', directory = '" . $this->db->escape($data['directory']) . "', filename = '" . $this->db->escape($data['filename']) . "', image = '" . $this->db->escape($data['image']) . "', sort_order = '" . $this->db->escape($data['sort_order']) . "', status = '" . (int) $data['status'] . "' WHERE language_id = '" . (int) $language_id . "'");

        $this->cache->delete('language');
    }

    public function deleteLanguage($language_id) {
        $this->db->query("DELETE FROM #__language WHERE language_id = '" . (int) $language_id . "'");

        $this->cache->delete('language');
    }

    public function getLanguage($language_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM #__language WHERE language_id = '" . (int) $language_id . "'");

        return $query->row;
    }

    public function getLanguages($data = array()) {
        if ($data) {
            $sql = "SELECT * FROM #__language";

            $sort_data = array(
                'name',
                'code',
                'sort_order'
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY sort_order, name";
            }

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }

            if (isset($data ['start']) || isset($data['limit'])) {
                if ($data['start'] < 0) {

                    $data['start'] = 0;
                }

                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }

                $sql .= " LIMIT " . (int) $data ['start'] . "," . (int) $data['limit'];
            }

            $query = $this->db->query($sql);

            return $query->rows;
        } else {
            $language_data = $this->cache->get('language');

            if (!$language_data) {
                $language_data = array();

                $query = $this->db->query("SELECT * FROM #__language ORDER BY sort_order, name");

                foreach ($query->rows as $result) {
                    $language_data[$result['code']] = array('language_id' => $result['language_id'],
                        'name' => $result['name'],
                        'code' => $result['code'],
                        'locale' => $result['locale'],
                        'image' => $result['image'],
                        'directory' => $result ['directory'],
                        'filename' => $result ['filename'],
                        'sort_order' => $result['sort_order'],
                        'status' => $result['status']
                    );
                }

                $this->cache->set('language', $language_data);
            }

            return $language_data;
        }
    }

    public function getTotalLanguages() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__language");

        return $query->row ['total'];
    }

}
