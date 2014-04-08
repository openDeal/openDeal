<?php
class ModelSettingExtension extends \Core\Model {
	function getExtensions($type) {
		$query = $this->db->query("SELECT * FROM #__extension WHERE `type` = '" . $this->db->escape($type) . "'");

		return $query->rows;
	}
}
?>