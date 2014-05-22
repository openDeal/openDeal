<?php
class ModelSaleFraud extends \Core\Model {
	public function getFraud($order_id) {
		$query = $this->db->query("SELECT * FROM `#__order_fraud` WHERE order_id = '" . (int)$order_id . "'");
	
		return $query->row;
	}
}
?>