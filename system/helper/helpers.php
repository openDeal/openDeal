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
/**
 * @todo - repackage to better places
 */

/**
 * Autoloads class
 * @param string $className
 */
function autoloader($className) {

    $className = ltrim($className, '\\');
    $fileName = '';
    $namespace = '';
    if ($lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    if (is_file(DIR_SYSTEM . $fileName)) {

        require(DIR_SYSTEM . $fileName);
    } else {
        $fileName = strtolower($fileName);
        if (is_file(DIR_SYSTEM . $fileName)) {
            require(DIR_SYSTEM . $fileName);
        } elseif (is_file(DIR_SYSTEM . 'library/' . $fileName)) {
            require(DIR_SYSTEM . 'library/' . $fileName);
        } else {

            trigger_error("Could not load class $className ");
        }
    }
}

/**
 * Splits a string by capitals (usefull for camelcase seperation)
 * @param string $string
 * @param boolean $ucfirst - return first letters upper (true) or all lower (false)
 * @param string|boolean $glue use this character to group the items together
 * @return string
 */
function splitByCaps($string, $ucfirst = true, $glue = false) {

    $pattern = "/(.)([A-Z])/";
    $replacement = "\\1 \\2";
    $return = ($ucfirst) ?
            ucfirst(preg_replace($pattern, $replacement, $string)) :
            strtolower(preg_replace($pattern, $replacement, $string));

    return ($glue) ? str_replace(' ', $glue, $return) : $return;
}

/**
 * print_r wrapper with pre tags pre added
 * @param mixed $val
 */
function debugPre($val) {
    echo '<pre>';
    print_r($val);
    echo '</pre>';
}

/**
 * var_dump wrapper with pre tags pre added
 * @param mixed $val
 */
function debugDump($val) {
    echo '<pre>';
    var_dump($val);
    echo '</pre>';
}

/**
 * Get the current users ipaddress
 * @return string ipaddress
 */
function get_client_ip() {
    $r = Core\Registry::getInstance()->request;

    $ipaddress = '';
    if (isset($r->server['HTTP_CLIENT_IP']))
        $ipaddress = $r->server['HTTP_CLIENT_IP'];
    else if (isset($r->server['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $r->server['HTTP_X_FORWARDED_FOR'];
    else if (isset($r->server['HTTP_X_FORWARDED']))
        $ipaddress = $r->server['HTTP_X_FORWARDED'];
    else if (isset($r->server['HTTP_FORWARDED_FOR']))
        $ipaddress = $r->server['HTTP_FORWARDED_FOR'];
    else if (isset($r->server['HTTP_FORWARDED']))
        $ipaddress = $r->server['HTTP_FORWARDED'];
    else if (isset($r->server['REMOTE_ADDR']))
        $ipaddress = $r->server['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';

    return $ipaddress;
}
