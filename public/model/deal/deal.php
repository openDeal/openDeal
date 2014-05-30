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
class ModelDealDeal extends \Core\Model {

    const AWAITING_APPROVAL = 0; // Awaiting approval - should not display on the frontend
    const DEAL_AVAILABLE = 1; // Deal Available for purchace
    const DEAL_FUTURE = 2; //Deal not yet available for purchace
    const SOLD_OUT = 3; //Deal is sold out, can display but not buy!
    const CLOSED = 4; //Deal Closed - can now display but not buy

    /*
     * Deal: min x to tip == tipping_point
     *       currently ordered == current_orders
     *       max_orders == maximum orders (total)
     *       max_per_user = maximim one user can order
     */

    public function getDeals($data = array()) {

        $sql = "Select d.deal_id,  ROUND( 100 - ( 100 * ( d.deal_price / d.market_price ) ) , 0 ) AS discount from #__deal d inner join #__deal_to_store d2s on d.deal_id = d2s.deal_id";
        $where = " Where d2s.store_id = '" . (int) $this->config->get('store_id') . "' and d.status = '1' ";

        if (!empty($data['filter_begin_time'])) {
            $where .= " and d.begin_time <= '" . (int) $data['filter_begin_time'] . "' ";
        }
        if (!empty($data['filter_end_time'])) {
            $where .= " and d.end_time > '" . (int) $data['filter_end_time'] . "' ";
        }

        if (!empty($data['filter_current'])) {
            $where .= " and d.begin_time <= '" . time() . "' "
                    . "and d.end_time > '" . time() . "' ";
        }

        if (!empty($data['filter_expired'])) {
            $where .= "and d.end_time < '" . time() . "' ";
        }

        if (!empty($data['filter_future'])) {
            $where .= "and d.begin_time > '" . time() . "' ";
        }

        if (!empty($data['filter_category_id'])) {
            $sql .= " inner join #__deal_to_category d2c on d.deal_id = d2c.deal_id ";
            $where .= " and d2c.category_id = '" . (int) $data['filter_category_id'] . "' ";
        }
        if (!empty($data['filter_city_id'])) {
            $sql .= " inner join #__deal_to_city dtc on d.deal_id = dtc.deal_id ";
            $where .= " and dtc.city_id = '" . (int) $data['filter_city_id'] . "' ";
        }

        $sql .= " inner join #__deal_description dd on d.deal_id = dd.deal_id ";
        $where .= " and dd.language_id = '" . (int) $this->config->get('config_language_id') . "' ";

        if (!empty($data['filter_title'])) {
            $where .= " and dd.title like '%" . $this->db->escape($data['filter_title']) . "%' ";
        }



        $sql .= $where;

        $sort_data = array(
            "d.feature_weight",
            "d.end_time",
            "d.begin_time",
            "dd.title",
            "d.deal_price",
            "d.market_price",
            "discount"
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            if ($data['sort'] == 'dd.title') {
                $sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
            } else {
                $sql .= " ORDER BY " . $data['sort'];
            }
        } else {
            $sql .= " ORDER BY d.feature_weight";
        }

        if (isset($data['order']) && (strtoupper($data['order']) == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if (!isset($data['start']) || $data['start'] < 0) {
                $data['start'] = 0;
            }

            if (!isset($data['limit']) || $data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $deal_data = array();

        $query = $this->db->query($sql);


        foreach ($query->rows as $result) {
            $deal_data[$result['deal_id']] = $this->getDeal($result['deal_id']);
        }

        return $deal_data;
    }

    public function getTotalDeals($data) {
        $sql = "Select count(d.deal_id) as total from #__deal d inner join #__deal_to_store d2s on d.deal_id = d2s.deal_id";
        $where = " Where d2s.store_id = '" . (int) $this->config->get('store_id') . "' and d.status = '1' ";

        if (!empty($data['filter_begin_time'])) {
            $where .= " and d.begin_time <= '" . (int) $data['filter_begin_time'] . "' ";
        }
        if (!empty($data['filter_end_time'])) {
            $where .= " and d.end_time > '" . (int) $data['filter_end_time'] . "' ";
        }

        if (!empty($data['filter_current'])) {
            $where .= " and d.begin_time <= '" . time() . "' "
                    . "and d.end_time > '" . time() . "' ";
        }

        if (!empty($data['filter_expired'])) {
            $where .= "and d.end_time < '" . time() . "' ";
        }

        if (!empty($data['filter_future'])) {
            $where .= "and d.begin_time > '" . time() . "' ";
        }

        if (!empty($data['filter_category_id'])) {
            $sql .= " inner join #__deal_to_category d2c on d.deal_id = d2c.deal_id ";
            $where .= " and d2c.category_id = '" . (int) $data['filter_category_id'] . "' ";
        }
        if (!empty($data['filter_city_id'])) {
            $sql .= " inner join #__deal_to_city dtc on d.deal_id = dtc.deal_id ";
            $where .= " and dtc.city_id = '" . (int) $data['filter_city_id'] . "' ";
        }

        $sql .= " inner join #__deal_description dd on d.deal_id = dd.deal_id ";
        $where .= " and dd.language_id = '" . (int) $this->config->get('config_language_id') . "' ";

        if (!empty($data['filter_title'])) {
            $where .= " and dd.title like '%" . $this->db->escape($data['filter_title']) . "%' ";
        }

        $sql .= $where;
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function getDeal($deal_id) {
        //Ok we have deal object!!!
        //Additionally deal images!!!
        $deal = array();
        $query = $this->db->query("SELECT DISTINCT *, (market_price - deal_price) as savings,  ROUND( 100 - ( 100 * ( deal_price / market_price ) ) , 0 ) AS discount "
                . " FROM #__deal d LEFT JOIN #__deal_description dd ON (d.deal_id = dd.deal_id) "
                . " LEFT JOIN #__deal_to_store d2s ON (d.deal_id = d2s.deal_id) "
                . " WHERE d.deal_id = '" . (int) $deal_id . "' "
                . " AND dd.language_id = '" . (int) $this->config->get('config_language_id') . "' "
                . " AND d.status = '1' "
                . " AND d2s.store_id = '" . (int) $this->config->get('config_store_id') . "'");

        if ($query->num_rows) {
            $deal = $query->row;


            $deal['images'] = $this->getDealImages($deal_id);

            //MAke sure the end_time is later than now
            if ($deal['end_time'] < $deal['begin_time']) {
                $deal['end_time'] = $deal['begin_time'];
            }
            $deal['closes_today'] = DATE("Y-m-d", $deal['begin_time']) == DATE("Y-m-d") ? 1 : 0;

            $deal['tipped'] = false;
            if ($deal['current_orders'] >= $deal['tip_point']) {
                $deal['tipped'] = true;
            }

            $now = time();

            $time_diff = $deal['end_time'] - $now;
            $deal['time_diff'] = $time_diff;
            
            

            $deal['state'] = $this->getDealState($deal);


            if ($deal['tipped'] && $deal['stock'] > 0 && $deal['current_orders'] < $deal['stock']) {
                $deal['tippingPercent'] = 100 * ($deal['current_orders'] / $deal['stock']);
                $deal['deals_left'] = $deal['stock'] - $deal['current_orders'];
            } else {
                $deal['tippingPercent'] = 100 * ($deal['current_orders'] / $deal['tip_point']);
                $deal['deals_left'] = $deal['tip_point'] - $deal['current_orders'];
            }
        }

        return $deal;
    }

    public function updateView($deal_id) {
        $this->db->query("UPDATE #__deal SET viewed = (viewed + 1) WHERE deal_id = '" . (int) $deal_id . "'");
    }

    public function getDealState(&$deal) {

        if ($deal['status'] == '0') {
            return $deal['state'] = self::AWAITING_APPROVAL;
        }
        $now = time();
        if ($deal['begin_time'] > $now) {
            return $deal['state'] = self::DEAL_FUTURE;
        }

        if ($deal['end_time'] <= $now) {
            return $deal['state'] = self::CLOSED;
        }

        if ($deal['current_orders'] >= $deal['tip_point']) {
            if ($deal['stock'] > 0) {
                if ($deal['current_orders'] >= $deal['stock']) {
                    return $deal['state'] = self::SOLD_OUT;
                }
            }
        }

        return $deal['state'] = self::DEAL_AVAILABLE;
    }

    public function getDealOptions($deal_id) {
        $deal_option_data = array();
        $query = $this->db->query("Select *, ROUND( 100 - ( 100 * ( price / market_price ) ) , 0 ) AS discount from #__deal_option where deal_id = " . (int) $deal_id . " order by sort_order asc");
        if ($query->num_rows) {
            foreach ($query->rows as $option) {
                $deal_option_data[$option['deal_option_id']] = array(
                    'deal_option_id' => $option['deal_option_id'],
                    'title' => $option['title'],
                    'market_price' => $option['market_price'],
                    'price' => $option['price'],
                    'discount' => $option['discount'],
                    'saving' => $option['market_price'] - $option['price']
                );
            }
        }
        return $deal_option_data;
    }

    public function getDealShippings($deal_id) {
        $deal_shipping_data = array();
        $query = $this->db->query("Select * from #__deal_shipping where deal_id = " . (int) $deal_id . " order by sort_order asc");
        if ($query->num_rows) {
            foreach ($query->rows as $option) {
                $deal_shipping_data[$option['deal_shipping_id']] = array(
                    'deal_shipping_id' => $option['deal_shipping_id'],
                    'title' => $option['title'],
                    'price' => $option['price']
                );
            }
        }
        return $deal_shipping_data;
    }

    public function getDealImages($deal_id) {
        $deal_image_data = array();
        $query = $this->db->query("select * from #__deal_image where deal_id = " . (int) $deal_id . " and image != '' order by sort_order asc");

        if ($query->num_rows) {
            foreach ($query->rows as $row) {
                $deal_image_data[] = $row['image'];
            }
        }
        return $deal_image_data;
    }

    public function getDealLayoutId($deal_id) {
        $query = $this->db->query("SELECT * FROM #__deal_to_layout WHERE deal_id = '" . (int) $deal_id . "' AND store_id = '" . (int) $this->config->get('config_store_id') . "'");

        if ($query->num_rows) {
            return $query->row['layout_id'];
        } else {
            return false;
        }
    }

}
