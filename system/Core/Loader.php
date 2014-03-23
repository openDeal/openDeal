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

namespace Core;

final class Loader {

    /**
     * Registry Object
     * @var \Core\Registry
     */
    protected $registry;

    /**
     * 
     * @param \Core\Registry $registry
     */
    public function __construct($registry) {
        $this->registry = $registry;
    }

    /**
     * Returns object from registry
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        return $this->registry->get($key);
    }

    /**
     * Sets object to the registry
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        $this->registry->set($key, $value);
    }

    /**
     * Loads Library class
     * @todo - add to allow additional libraries
     * @todo is this really required anymore ?AUTOLOADER?
     * @param string $library
     */
    public function library($library) {
        $file = DIR_SYSTEM . 'library/' . $library . '.php';

        if (file_exists($file)) {
            include_once($file);
        } else {
            trigger_error('Error: Could not load library ' . $library . '!');
            exit();
        }
    }

    /**
     * Loads helper class
     * @param string $helper
     */
    public function helper($helper) {
        $file = DIR_SYSTEM . 'helper/' . $helper . '.php';

        if (file_exists($file)) {
            include_once($file);
        } else {
            trigger_error('Error: Could not load helper ' . $helper . '!');
            exit();
        }
    }

    /**
     * Loads model for the current application namespace
     * @param string $model
     */
    public function model($model) {
        $file = DIR_APPLICATION . 'model/' . $model . '.php';
        $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);

        if (file_exists($file)) {
            include_once($file);

            $this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
        } else {
            trigger_error('Error: Could not load model ' . $model . '!');
            exit();
        }
    }

    /**
     * initialize the database
     * @param string $driver
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     */
    public function database($driver, $hostname, $username, $password, $database) {
        $file = DIR_SYSTEM . 'database/' . $driver . '.php';
        $class = 'Database' . preg_replace('/[^a-zA-Z0-9]/', '', $driver);

        if (file_exists($file)) {
            include_once($file);

            $this->registry->set(str_replace('/', '_', $driver), new $class($hostname, $username, $password, $database));
        } else {
            trigger_error('Error: Could not load database ' . $driver . '!');
            exit();
        }
    }

    /**
     * Load Configuration Helper
     * @param string $config path to configuration file
     */
    public function config($config) {
        $this->config->load($config);
    }

    /**
     * Loads a file into the langauge
     * @param string $language
     * @return array Language translation array
     */
    public function language($language) {
        return $this->language->load($language);
    }

}