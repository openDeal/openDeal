<?php

class ModelSaleCustomerBanIp extends \Core\Model {

    public function addCustomerBanIp($data) {
        $this->db->query("INSERT INTO `#__customer_ban_ip` SET `ip` = '" . $this->db->escape($data['ip']) . "'");
    }

    public function editCustomerBanIp($customer_ban_ip_id, $data) {
        $this->db->query("UPDATE `#__customer_ban_ip` SET `ip` = '" . $this->db->escape($data['ip']) . "' WHERE customer_ban_ip_id = '" . (int) $customer_ban_ip_id . "'");
    }

    public function deleteCustomerBanIp($customer_ban_ip_id) {
        $this->db->query("DELETE FROM `#__customer_ban_ip` WHERE customer_ban_ip_id = '" . (int) $customer_ban_ip_id . "'");
    }

    public function getCustomerBanIp($customer_ban_ip_id) {
        $query = $this->db->query("SELECT * FROM `#__customer_ban_ip` WHERE customer_ban_ip_id = '" . (int) $customer_ban_ip_id . "'");

        return $query->row;
    }

    public function getCustomerBanIps($data = array()) {
        $sql = "SELECT *, (SELECT COUNT(DISTINCT customer_id) FROM `#__customer_ip` ci WHERE ci.ip = cbi.ip) AS total FROM `#__customer_ban_ip` cbi";

        $sql .= " ORDER BY `ip`";

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

    public function getTotalCustomerBanIps($data = array()) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `#__customer_ban_ip`");

        return $query->row['total'];
    }

}

?>