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
class Config {

    /**
     * Holds the configuration data
     * @var array 
     */
    private $data = array();

    /**
     * return the configuration variable if set
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        return (isset($this->data[$key]) ? $this->data[$key] : null);
    }

    /**
     * Sets configuration variable
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value) {
        $this->data[$key] = $value;
    }

    /**
     * checks if a configuration key exists
     * @param string $key
     * @return boolean
     */
    public function has($key) {
        return isset($this->data[$key]);
    }

    /**
     * load an additional configuration file from the config directory (3rd party intergrations)
     * @param string $filename
     */
    public function load($filename) {
        $file = DIR_CONFIG . $filename . '.php';

        if (file_exists($file)) {
            $_ = array();

            require($file);

            $this->data = array_merge($this->data, $_);
        } else {
            trigger_error('Error: Could not load config ' . $filename . '!');
            exit();
        }
    }

}

?>