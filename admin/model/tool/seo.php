<?php

class ModelToolSeo extends \Core\Model {

    public function keywordExists($keyword) {
        $query = $this->db->query("SELECT * FROM #__url_alias WHERE keyword = '" . $keyword . "' LIMIT 1");
        $found = false;
        if ($query->num_rows) {
            $found = true;
        } //same SEO already exist
        return $found;
    }

}
