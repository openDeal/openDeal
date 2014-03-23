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
class Log {

    /**
     * Holds the filename for the log
     * @var string 
     */
    private $filename;

    /**
     * 
     * @param string $filename
     */
    public function __construct($filename) {
        $this->filename = $filename;
        set_error_handler(array($this, 'error_handler'));
    }

    /**
     * Write a message to the log file
     * @param string $message
     */
    public function write($message) {
        $file = DIR_LOGS . $this->filename;

        $handle = fopen($file, 'a+');

        fwrite($handle, date('Y-m-d G:i:s') . ' - ' . $message . "\n");

        fclose($handle);
    }

    /**
     *  Our Sites Error Handler
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return boolean
     */
    public function error_handler($errno, $errstr, $errfile = '', $errline = 0) {
        $config = Core\Registry::getInstance()->config;

        switch ($errno) {
            case E_NOTICE:
            case E_USER_NOTICE:
                $error = 'Notice';
                break;
            case E_WARNING:
            case E_USER_WARNING:
                $error = 'Warning';
                break;
            case E_ERROR:
            case E_USER_ERROR:
                $error = 'Fatal Error';
                break;
            default:
                $error = 'Unknown';
                break;
        }

        if ($config->get('config_error_display')) {
            echo '<b>#' . $error . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b><br />';
        }

        if ($config->get('config_error_log')) {
            $this->write('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
        }

        return true;
    }

}
