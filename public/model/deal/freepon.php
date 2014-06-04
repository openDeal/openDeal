<?php

/**
 * openDeal - Opensource Deals Platform
 *
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2014 Craig Smith
 * @link        https://github.com/openDeal/openDeal
 * @license     https://raw.githubusercontent.com/openDeal/openDeal/master/LICENSE
 * @since       1.0.0
 * @package     Core
 * GPLV3 Licence
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class ModelDealFreepon extends \Core\Model {

    public function getFreepons($data) {


        $sql = "Select f.freepon_id from #__freepon f inner join #__freepon_to_store f2s on f.freepon_id = f2s.freepon_id";
        $where = " Where f2s.store_id = '" . (int) $this->config->get('store_id') . "' and f.status = '1' and f.begin_time <= '" . time() . "' "
                . "and f.end_time > '" . time() . "' ";


        if (!empty($data['filter_category_id'])) {
            $sql .= " inner join #__freepon_to_category f2c on df.freepon_id = f2c.freepon_id ";
            $where .= " and f2c.category_id = '" . (int) $data['filter_category_id'] . "' ";
        }
        if (!empty($data['filter_city_id'])) {
            $sql .= " inner join #__freepon_to_city ftc on f.freepon_id = ftc.freepon_id ";
            $where .= " and ftc.city_id = '" . (int) $data['filter_city_id'] . "' ";
        }

        $sql .= " inner join #__freepon_description fd on f.freepon_id = fd.freepon_id ";
        $where .= " and fd.language_id = '" . (int) $this->config->get('config_language_id') . "' ";

        if (!empty($data['filter_name'])) {
            $where .= " and fd.name like '%" . $this->db->escape($data['filter_name']) . "%' ";
        }

        $sql .= $where;


        $sort_data = array(
            "f.feature_weight",
            "f.end_time",
            "f.begin_time",
            "fd.name"
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            if ($data['sort'] == 'fd.name') {
                $sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
            } else {
                $sql .= " ORDER BY " . $data['sort'];
            }
        } else {
            $sql .= " ORDER BY f.feature_weight";
        }

        if (isset($data['order']) && (strtoupper($data['order']) == 'ASC')) {
            $sql .= " ASC";
        } else {
            $sql .= " DESC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if (!isset($data['start']) || $data['start'] < 0) {
                $data['start'] = 0;
            }

            if (!isset($data['limit']) || $data['limit'] < 1) {
                $data['limit'] = $this->config->get('config_catalog_limit');
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $freepon_data = array();


        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $freepon_data[$result['freepon_id']] = $this->getFreepon($result['freepon_id']);
        }

        return $freepon_data;
    }

    public function getFreepon($freepon_id) {
        $freepon = array();
        $query = $this->db->query("SELECT DISTINCT * "
                . " FROM #__freepon f LEFT JOIN #__freepon_description fd ON (f.freepon_id = fd.freepon_id) "
                . " LEFT JOIN #__freepon_to_store f2s ON (f.freepon_id = f2s.freepon_id) "
                . " WHERE f.freepon_id = '" . (int) $freepon_id . "' "
                . " AND fd.language_id = '" . (int) $this->config->get('config_language_id') . "' "
                . " AND f.status = '1' "
                . " AND f2s.store_id = '" . (int) $this->config->get('config_store_id') . "'");

        if ($query->num_rows) {
            $freepon = $query->row;


            $freepon['images'] = $this->getFreeponImages($freepon_id);

            //MAke sure the end_time is later than now
            if ($freepon['end_time'] < $freepon['begin_time']) {
                $freepon['end_time'] = $freepon['begin_time'];
            }
            $freepon['closes_today'] = DATE("Y-m-d", $freepon['begin_time']) == DATE("Y-m-d") ? 1 : 0;


            $now = time();

            $time_diff = $freepon['end_time'] - $now;
            $freepon['time_diff'] = $time_diff;
        }

        return $freepon;
    }

    public function getFreeponImages($freepon_id) {
        $freepon_image_data = array();
        $query = $this->db->query("select * from #__freepon_image where freepon_id = " . (int) $freepon_id . " and image != '' order by sort_order asc");

        if ($query->num_rows) {
            foreach ($query->rows as $row) {
                $freepon_image_data[] = $row['image'];
            }
        }
        return $freepon_image_data;
    }

    public function getTotalFreepons($data) {
        //$sql = "Select count(f.freepon_id) as total from #__freepon f inner join #__freepon_to_store f2s on f.freepon_id = f2s.freepon_id";
       
         $sql = "Select count(f.freepon_id) as total from #__freepon f inner join #__freepon_to_store f2s on f.freepon_id = f2s.freepon_id";
        $where = " Where f2s.store_id = '" . (int) $this->config->get('store_id') . "' and f.status = '1' and f.begin_time <= '" . time() . "' "
                . "and f.end_time > '" . time() . "' ";


        if (!empty($data['filter_category_id'])) {
            $sql .= " inner join #__freepon_to_category f2c on df.freepon_id = f2c.freepon_id ";
            $where .= " and f2c.category_id = '" . (int) $data['filter_category_id'] . "' ";
        }
        if (!empty($data['filter_city_id'])) {
            $sql .= " inner join #__freepon_to_city ftc on f.freepon_id = ftc.freepon_id ";
            $where .= " and ftc.city_id = '" . (int) $data['filter_city_id'] . "' ";
        }

        $sql .= " inner join #__freepon_description fd on f.freepon_id = fd.freepon_id ";
        $where .= " and fd.language_id = '" . (int) $this->config->get('config_language_id') . "' ";

        if (!empty($data['filter_name'])) {
            $where .= " and fd.name like '%" . $this->db->escape($data['filter_name']) . "%' ";
        }

        $sql .= $where;
        
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function updateView($freepon_id) {
        $this->db->query("UPDATE #__freepon SET viewed = (viewed + 1) WHERE freepon_id = '" . (int) $freepon_id . "'");
    }

    public function getFreeponLayoutId($freepon_id) {
        $query = $this->db->query("SELECT * FROM #__freepon_to_layout WHERE freepon_id = '" . (int) $freepon_id . "' AND store_id = '" . (int) $this->config->get('config_store_id') . "'");

        if ($query->num_rows) {
            return $query->row['layout_id'];
        } else {
            return false;
        }
    }

    public function updateClaim($freepon_id, $customer_id) {
        $repeat = $this->db->query("Select count(*) as total from #__freepon_claim where freepon_id = '" . (int) $freepon_id . "' and customer_id = '" . (int) $customer_id . "'");
        if ($repeat->row['total'] == 0) {
            $this->db->query("insert into #__freepon_claim SET freepon_id = '" . (int) $freepon_id . "', customer_id = '" . $customer_id . "', timestamp = '" . time() . "'");
            //If !user->hasClaim
            $this->db->query("UPDATE #__freepon SET downloaded = (downloaded + 1) WHERE freepon_id = '" . (int) $freepon_id . "'");
        }
    }

}
