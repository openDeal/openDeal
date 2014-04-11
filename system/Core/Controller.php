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

abstract class Controller {

    /**
     * Registry object
     * @var \Core\Registry
     */
    protected $registry;
    protected $id;
    protected $layout;

    /**
     * Template to be rendered
     * @var string
     */
    protected $template;

    /**
     * Child Controller/Actions to call
     * @var array
     */
    protected $children = array();

    /**
     * Data for injection into the Template
     * @var array 
     */
    protected $data = array();

    /**
     * The parsed output of the tempate and data
     * @var string
     */
    protected $output;

    /**
     * Constructs the Controller instance and sets default template & language files
     * @param \Core\Registry $registry
     */
    public function __construct($registry) {
        $this->registry = $registry;
        $called = explode(" ", splitByCaps(get_called_class(), false));
        array_shift($called);
        $this->template = implode("/", $called) . '.phtml';
        if($this->registry->has('language')){
        $this->language->load(implode("/", $called));
        }
    }

    /**
     * Magic method to get a key from the registry
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        return $this->registry->get($key);
    }

    /**
     * Sets a registry key
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        $this->registry->set($key, $value);
    }

    /**
     * Forward current call to a differend action without reload
     * @param string $route
     * @param array $args
     * @return \Core\Action
     */
    protected function forward($route, $args = array()) {
        return new \Core\Action($route, $args);
    }

    /**
     * Redirect the browser to a new URL
     * @param string $url
     * @param int $status
     */
    protected function redirect($url, $status = 302) {
        header('Status: ' . $status);
        header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $url));
        exit();
    }

    /**
     * Returns child output
     * @param string $child
     * @param array $args
     * @return string
     */
    protected function getChild($child, $args = array()) {
        $action = new \Core\Action($child, $args);

        if (file_exists($action->getFile())) {
            require_once($action->getFile());

            $class = $action->getClass();

            $controller = new $class($this->registry);

            $controller->{$action->getMethod()}($action->getArgs());

            return $controller->output;
        } else {
            trigger_error('Error: Could not load controller ' . $child . '!');
            exit();
        }
    }

    /**
     * Test whether or not the Action has a specific action
     * @param string $child
     * @param array $args
     * @return boolean
     */
    protected function hasAction($child, $args = array()) {
        $action = new \Core\Action($child, $args);

        if (file_exists($action->getFile())) {
            require_once($action->getFile());

            $class = $action->getClass();

            $controller = new $class($this->registry);

            if (method_exists($controller, $action->getMethod())) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Returns the output
     * @return string
     */
    protected function render() {
        foreach ($this->children as $child) {
            $this->data[basename($child)] = $this->getChild($child);
        }

        
        if (APP_NAMESPACE == 'public' && file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/' . $this->template)) {
            $template = DIR_TEMPLATE . $this->config->get('config_template') . '/template/' . $this->template;
        } elseif (APP_NAMESPACE == 'admin' && file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/template/' . $this->template)) {
            $template = DIR_TEMPLATE . $this->config->get('config_admin_template') . '/template/' . $this->template;
        }elseif(APP_NAMESPACE == 'installer') {
              $template = DIR_TEMPLATE . $this->template;
        } else {
            $template = DIR_TEMPLATE . 'default/template/' . $this->template;
        }
        

        if (file_exists($template)) {
            extract($this->data);

            ob_start();

            require($template);

            $this->output = ob_get_contents();

            ob_end_clean();

            return $this->output;
        } else {
            trigger_error('Error: Could not load template ' . $this->template . '!');
            exit();
        }
    }

}
