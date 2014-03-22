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
class City {

    /**
     * All the cities for the current store
     * @var array 
     */
    private $cities = array();

    /**
     * Currnt city id
     * @var int 
     */
    public $city = 0;

    /**
     * The \Core\Registry instance
     * @var \Core\Registry 
     */
    public $registry;

    /**
     * Constructor - sets current city
     * @param \Core\Registry $registry
     */
    public function __construct($registry) {

        $this->registry = $registry;
        $this->db = $registry->get('db');
        $this->config = $registry->get('config');
        $this->request = $registry->get('request');
        $this->session = $registry->get('session');

        $query = $this->db->query("Select c.* from #__city c inner join #__city_to_store cs on c.city_id = cs.city_id where c.status = 1 and cs.store_id = '" . (int) $this->config->get('config_store_id') . "'");
        foreach ($query->rows as $row) {
            $this->cities[$row['city_id']] = $row;
        }
        if (isset($this->request->get['city_id']) && (array_key_exists($this->request->get['city_id'], $this->cities))) {
            $this->set($this->request->get['city_id']);
        } elseif ((isset($this->session->data['city_id'])) && (array_key_exists($this->session->data['city_id'], $this->cities))) {
            $this->set($this->session->data['city_id']);
        } elseif ((isset($this->request->cookie['city_id'])) && (array_key_exists($this->request->cookie['city_id'], $this->cities))) {
            $this->set($this->request->cookie['city_id']);
        } else {

            if ($this->config->get('config_city')) {
                $this->set($this->config->get('config_city'));
            } else {
                $geo = new Geoplugin(get_client_ip());
                $geo->locate();
                $row = $this->returnClosest($geo->latitude, $geo->longitude);
                $city = $row['city_id'];
                $this->set($city);
            }
        }
    }

    /**
     * returns the city or false if city id not in range, defaults to current city if not passed
     * @param boolean|int $city_id
     * @return mixed
     */
    public function get($city_id = false) {
        if ($city_id && isset($this->cities[$city_id])) {
            return $this->cities[$city_id];
        } elseif ($this->city) {
            return $this->cities[$this->city];
        }
        return false;
    }

    /**
     *  Sets teh current session / cookie with the city id
     * @param int $city
     */
    public function set($city) {
        $this->city = $city;

        if (!isset($this->session->data['city_id']) || ($this->session->data['city_id'] != $city)) {
            $this->session->data['city_id'] = $city;
        }

        if (!isset($this->request->cookie['city_id']) || ($this->request->cookie['city_id'] != $city)) {
            setcookie('city_id', $city, time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
        }
    }

    /**
     * Returns the closest city based on longitude and latitude
     * @param double $latitude
     * @param double $longitude
     * @return array
     */
    public function returnClosest($latitude, $longitude) {
        $db = $this->registry->get('db');
        $query = sprintf("SELECT c.*, ( 3959 * acos( cos( radians('%s') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude ) ) ) ) AS distance FROM #__city c inner join #__city_to_store cs on c.city_id = cs.city_id where cs.store_id = %d ORDER BY distance LIMIT 1", $this->registry->get('config')->get('store_id'), $db->escape($latitude), $db->escape($longitude), $db->escape($latitude));
        $result = $db->query($query);
        return $result->row;
    }

}
