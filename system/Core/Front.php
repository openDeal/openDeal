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

final class Front {

    /**
     * Registry Object
     * @var \Core\Registry
     */
    protected $registry;

    /**
     * Any actions that should run prior to the requested route
     * @var array 
     */
    protected $pre_action = array();

    /**
     * Holds the error handling data
     * @var null|\Core\Action 
     */
    protected $error;

    /**
     * Initialize with Registry object
     * @param \Core\Registry $registry
     */
    public function __construct($registry) {
        $this->registry = $registry;
    }

    /**
     * Add any pre-actions to the current request
     * @param \Core\Action $pre_action
     */
    public function addPreAction($pre_action) {
        $this->pre_action[] = $pre_action;
    }

    /**
     * Dispatches the current action
     * @param \Core\Action $action
     * @param \Core\Action $error - what should happen if the route does not exist
     */
    public function dispatch($action, $error) {
        $this->error = $error;

        foreach ($this->pre_action as $pre_action) {
            $result = $this->execute($pre_action);

            if ($result) {
                $action = $result;

                break;
            }
        }

        while ($action) {
            $action = $this->execute($action);
        }
    }

    /**
     * Executes the current action
     * @param \Core\Action $action
     * @return string
     */
    private function execute($action) {
        if (file_exists($action->getFile())) {
            require_once($action->getFile());

            $class = $action->getClass();

            $controller = new $class($this->registry);

            if (is_callable(array($controller, $action->getMethod()))) {
                $action = call_user_func_array(array($controller, $action->getMethod()), $action->getArgs());
            } else {
                $action = $this->error;

                $this->error = '';
            }
        } else {
            $action = $this->error;

            $this->error = '';
        }

        return $action;
    }

}
