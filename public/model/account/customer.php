<?php

class ModelAccountCustomer extends \Core\Model {

    public function addCustomer($data) {
        $approved = !$this->config->get('config_customer_approval');

        $city_id = $this->city->city;

        $this->db->query("INSERT INTO #__customer SET city_id = '" . (int) $city_id . "', "
                . "store_id = '" . (int) $this->config->get('config_store_id') . "', "
                . "firstname = '" . $this->db->escape($data['firstname']) . "', "
                . "lastname = '" . $this->db->escape($data['lastname']) . "', "
                . "email = '" . $this->db->escape($data['email']) . "', "
                . "telephone = '" . $this->db->escape($data['telephone']) . "', "
                . "salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', "
                . "password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', "
                . "newsletter = '" . (isset($data['newsletter']) ? (int) $data['newsletter'] : 0) . "', "
                . "ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', "
                . "status = '1', approved = '" . (int) $approved . "', date_added = NOW()");

        $customer_id = $this->db->getLastId();

        $this->db->query("INSERT INTO #__address SET customer_id = '" . (int) $customer_id . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "',  address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int) $data['country_id'] . "', zone_id = '" . (int) $data['zone_id'] . "'");

        $address_id = $this->db->getLastId();

        $this->db->query("UPDATE #__customer SET address_id = '" . (int) $address_id . "' WHERE customer_id = '" . (int) $customer_id . "'");

        $this->language->load('mail/customer');

        $subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'));

        $message = sprintf($this->language->get('text_welcome'), $this->config->get('config_name')) . "\n\n";

        if ($approved) {
            $message .= $this->language->get('text_login') . "\n";
        } else {
            $message .= $this->language->get('text_approval') . "\n";
        }

        $message .= $this->url->link('account/login', '', 'SSL') . "\n\n";
        $message .= $this->language->get('text_services') . "\n\n";
        $message .= $this->language->get('text_thanks') . "\n";
        $message .= $this->config->get('config_name');

        $mail = new Mail();
        $mail->protocol = $this->config->get('config_mail_protocol');
        $mail->parameter = $this->config->get('config_mail_parameter');
        $mail->hostname = $this->config->get('config_smtp_host');
        $mail->username = $this->config->get('config_smtp_username');
        $mail->password = $this->config->get('config_smtp_password');
        $mail->port = $this->config->get('config_smtp_port');
        $mail->timeout = $this->config->get('config_smtp_timeout');
        $mail->setTo($data['email']);
        $mail->setFrom($this->config->get('config_email'));
        $mail->setSender($this->config->get('config_name'));
        $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
        $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
        $mail->send();

        // Send to main admin email if new account email is enabled
        if ($this->config->get('config_account_mail')) {
            $message = $this->language->get('text_signup') . "\n\n";
            $message .= $this->language->get('text_website') . ' ' . $this->config->get('config_name') . "\n";
            $message .= $this->language->get('text_firstname') . ' ' . $data['firstname'] . "\n";
            $message .= $this->language->get('text_lastname') . ' ' . $data['lastname'] . "\n";

            if ($data['company']) {
                $message .= $this->language->get('text_company') . ' ' . $data['company'] . "\n";
            }

            $message .= $this->language->get('text_email') . ' ' . $data['email'] . "\n";
            $message .= $this->language->get('text_telephone') . ' ' . $data['telephone'] . "\n";

            $mail->setTo($this->config->get('config_email'));
            $mail->setSubject(html_entity_decode($this->language->get('text_new_customer'), ENT_QUOTES, 'UTF-8'));
            $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
            $mail->send();

            // Send to additional alert emails if new account email is enabled
            $emails = explode(',', $this->config->get('config_alert_emails'));

            foreach ($emails as $email) {
                if (strlen($email) > 0 && preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $email)) {
                    $mail->setTo($email);
                    $mail->send();
                }
            }
        }
    }

    public function editCity($city_id) {
        $this->db->query("UPDATE #__customer SET city_id = '" . (int) $city_id . " WHERE customer_id = '" . (int) $this->customer->getId() . "'");
    }

    public function editCustomer($data) {
        $this->db->query("UPDATE #__customer SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "' WHERE customer_id = '" . (int) $this->customer->getId() . "'");
    }

    public function editPassword($email, $password) {
        $this->db->query("UPDATE #__customer SET salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', "
                . "password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "' "
                . "WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
    }

    public function editNewsletter($newsletter) {
        $this->db->query("UPDATE #__customer SET newsletter = '" . (int) $newsletter . "' WHERE customer_id = '" . (int) $this->customer->getId() . "'");
    }

    public function getCustomer($customer_id) {
        $query = $this->db->query("SELECT * FROM #__customer WHERE customer_id = '" . (int) $customer_id . "'");

        return $query->row;
    }

    public function getCustomerByEmail($email) {
        $query = $this->db->query("SELECT * FROM #__customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

        return $query->row;
    }

    public function getCustomerByToken($token) {
        $query = $this->db->query("SELECT * FROM #__customer WHERE token = '" . $this->db->escape($token) . "' AND token != ''");

        $this->db->query("UPDATE #__customer SET token = ''");

        return $query->row;
    }

    public function getCustomers($data = array()) {
        $sql = "SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM #__customer  ";

        $implode = array();

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $implode[] = "LCASE(CONCAT(firstname, ' ', lastname)) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
        }

        if (isset($data['filter_email']) && !is_null($data['filter_email'])) {
            $implode[] = "LCASE(email) = '" . $this->db->escape(utf8_strtolower($data['filter_email'])) . "'";
        }



        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $implode[] = "status = '" . (int) $data['filter_status'] . "'";
        }

        if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
            $implode[] = "approved = '" . (int) $data['filter_approved'] . "'";
        }

        if (isset($data['filter_ip']) && !is_null($data['filter_ip'])) {
            $implode[] = "customer_id IN (SELECT customer_id FROM #__customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
        }

        if (isset($data['filter_date_added']) && !is_null($data['filter_date_added'])) {
            $implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        $sort_data = array(
            'name',
            'email',
            'status',
            'ip',
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
    }

    public function getTotalCustomersByEmail($email) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

        return $query->row['total'];
    }

    public function getIps($customer_id) {
        $query = $this->db->query("SELECT * FROM `#__customer_ip` WHERE customer_id = '" . (int) $customer_id . "'");

        return $query->rows;
    }

    public function isBanIp($ip) {
        $query = $this->db->query("SELECT * FROM `#__customer_ban_ip` WHERE ip = '" . $this->db->escape($ip) . "'");

        return $query->num_rows;
    }

}

?>
