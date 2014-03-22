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

final class Action {

    /**
     * Filepath of the routed class
     * @var string 
     */
    protected $file;

    /**
     * Name of the routed class
     * @var string
     */
    protected $class;

    /**
     * name of the routed method
     * @var string
     */
    protected $method;

    /**
     * Arguments for the routed class
     * @var array
     */
    protected $args = array();

    /**
     * Action consttructor - Breaks the route into controller::action($args)
     * @param string $route
     * @param array $args
     */
    public function __construct($route, $args = array()) {
        $path = '';

        $parts = explode('/', str_replace('../', '', (string) $route));

        foreach ($parts as $part) {
            $path .= $part;

            if (is_dir(DIR_APPLICATION . 'controller/' . $path)) {
                $path .= '/';

                array_shift($parts);

                continue;
            }

            if (is_file(DIR_APPLICATION . 'controller/' . str_replace(array('../', '..\\', '..'), '', $path) . '.php')) {
                $this->file = DIR_APPLICATION . 'controller/' . str_replace(array('../', '..\\', '..'), '', $path) . '.php';

                $this->class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $path);

                array_shift($parts);

                break;
            }
        }


        if ($args) {
            $this->args = $args;
        }

        $method = array_shift($parts);

        if ($method) {
            $this->method = $method;
        } else {
            $this->method = 'index';
        }
    }

    /**
     * 
     * @return string
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * 
     * @return string
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * 
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * 
     * @return array
     */
    public function getArgs() {
        return $this->args;
    }

}
