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
class Geoplugin {

    /**
     * the geoPlugin server
     * @var string  
     */
    private $host = 'http://www.geoplugin.net/php.gp?ip={IP}&base_currency={CURRENCY}';

    /**
     * the default base currency
     * @var string 
     */
    private $currency = 'USD';
    public $ip = null;
    public $city = null;
    public $region = null;
    public $areaCode = null;
    public $dmaCode = null;
    public $countryCode = null;
    public $countryName = null;
    public $continentCode = null;
    public $latitude = null;
    public $longitude = null;
    public $currencyCode = null;
    public $currencySymbol = null;
    public $currencyConverter = null;

    /**
     * Constructor
     * @param string $ip
     */
    public function __construct($ip = false) {
        if (!$ip) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $this->ip = $ip;
    }

    /**
     * Locate the current geo data
     */
    public function locate() {

        $ip = $this->ip;

        $host = str_replace('{IP}', $ip, $this->host);
        $host = str_replace('{CURRENCY}', $this->currency, $host);

        $data = array();

        $response = $this->fetch($host);

        $data = unserialize($response);

        //set the geoPlugin vars
        $this->ip = $ip;
        $this->city = $data['geoplugin_city'];
        $this->region = $data['geoplugin_region'];
        $this->areaCode = $data['geoplugin_areaCode'];
        $this->dmaCode = $data['geoplugin_dmaCode'];
        $this->countryCode = $data['geoplugin_countryCode'];
        $this->countryName = $data['geoplugin_countryName'];
        $this->continentCode = $data['geoplugin_continentCode'];
        $this->latitude = $data['geoplugin_latitude'];
        $this->longitude = $data['geoplugin_longitude'];
        $this->currencyCode = $data['geoplugin_currencyCode'];
        $this->currencySymbol = $data['geoplugin_currencySymbol'];
        $this->currencyConverter = $data['geoplugin_currencyConverter'];
    }

    /**
     * Performs the curl request
     * @param string $host
     * @return array
     */
    protected function fetch($host) {

        if (function_exists('curl_init')) {

            //use cURL to fetch data
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $host);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'geoPlugin PHP Class v1.0');
            $response = curl_exec($ch);
            curl_close($ch);
        } else if (ini_get('allow_url_fopen')) {

            //fall back to fopen()
            $response = file_get_contents($host, 'r');
        } else {

            trigger_error('geoPlugin class Error: Cannot retrieve data. Either compile PHP with cURL support or enable allow_url_fopen in php.ini ', E_USER_ERROR);
            return;
        }

        return $response;
    }

    /**
     * easily convert amounts to geolocated currency.
     * @param float $amount
     * @param float $float
     * @param boolean $symbol
     * @return string
     */
    public function convert($amount, $float = 2, $symbol = true) {

        //easily convert amounts to geolocated currency.
        if (!is_numeric($this->currencyConverter) || $this->currencyConverter == 0) {
            trigger_error('geoPlugin class Notice: currencyConverter has no value.', E_USER_NOTICE);
            return $amount;
        }
        if (!is_numeric($amount)) {
            trigger_error('geoPlugin class Warning: The amount passed to geoPlugin::convert is not numeric.', E_USER_WARNING);
            return $amount;
        }
        if ($symbol === true) {
            return $this->currencySymbol . round(($amount * $this->currencyConverter), $float);
        } else {
            return round(($amount * $this->currencyConverter), $float);
        }
    }

    /**
     * returns items nearby that could be interesting
     * @param int $radius
     * @param int $limit
     * @return array
     */
    public function nearby($radius = 10, $limit = null) {

        if (!is_numeric($this->latitude) || !is_numeric($this->longitude)) {
            trigger_error('geoPlugin class Warning: Incorrect latitude or longitude values.', E_USER_NOTICE);
            return array(array());
        }

        $host = "http://www.geoplugin.net/extras/nearby.gp?lat=" . $this->latitude . "&long=" . $this->longitude . "&radius={$radius}";

        if (is_numeric($limit))
            $host .= "&limit={$limit}";

        return unserialize($this->fetch($host));
    }

}

?>
