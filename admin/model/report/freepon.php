<?php
class ModelReportFreepon extends \Core\Model {
	public function getDealsViewed($data = array()) {
		$sql = "SELECT dd.name as title, d.viewed FROM #__freepon d LEFT JOIN #__freepon_description dd ON (d.freepon_id = dd.freepon_id) WHERE dd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND d.viewed > 0 ORDER BY d.viewed DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}			

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}	

		$query = $this->db->query($sql);

		return $query->rows;
	}	

	public function getTotalDealsViewed() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM #__freepon WHERE viewed > 0");

		return $query->row['total'];
	}

	public function getTotalDealViews() {
		$query = $this->db->query("SELECT SUM(viewed) AS total FROM #__freepon");

		return $query->row['total'];
	}

	public function reset() {
		$this->db->query("UPDATE #__freepon SET viewed = '0'");
	}


}