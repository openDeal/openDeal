<?php

class ModelSettingLanguage extends \Core\Model {

    public function getLanguage($language_id) {

        $query = $this->db->query("SELECT DISTINCT * FROM #__language WHERE language_id = '" . (int) $language_id . "'");

        return $query->row['filename'];
    }

}

?>
