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
class DB {

    /**
     * holds the current database driver instance
     * @var database class 
     */
    private $driver;

    /**
     * holds collection of database connections
     * @var array 
     */
    private $drivers = array();

    /**
     * Constructs an instance of the db class
     * @param string $driver
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     */
    public function __construct($driver, $hostname, $username, $password, $database) {
        $file = DIR_DATABASE . $driver . '.php';

        if (file_exists($file)) {
            require_once($file);

            $class = 'DB' . $driver;

            $this->drivers['default'] = new $class($hostname, $username, $password, $database);
            $this->driver = $this->drivers['default'];
        } else {
            exit('Error: Could not load database driver type ' . $driver . '!');
        }
    }

    /**
     * Creates a connection to a secondary database and if activate = true sets it as the current connection
     * @param string $driver
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     * @param string $identifier
     * @param boolean $activate
     */
    public function addConnection($driver, $hostname, $username, $password, $database, $identifier, $activate = false) {
        $file = DIR_DATABASE . $driver . '.php';

        if (file_exists($file)) {
            require_once($file);

            $class = 'DB' . $driver;

            $this->drivers[$identifier] = new $class($hostname, $username, $password, $database);
            if ($activate) {
                $this->driver = $this->drivers[$identifier];
            }
        } else {
            exit('Error: Could not load database driver type ' . $driver . '!');
        }
    }

    /**
     * switch between different database connections
     * @param string $identifier
     */
    public function selectConnection($identifier = 'default') {
        if (!isset($this->drivers[$identifier])) {
            exit('Error: ' . $identifier . ' Connection not found');
        }
        $this->driver = $this->drivers[$identifier];
    }
    
        /**
     * check if there is a database connection
     * @param string $identifier
     */
    public function hasConnection($identifier = 'default') {
        return isset($this->drivers[$identifier]);
    }

    /**
     * wrapper to connections query method
     * @param string $sql
     * @return \stdClass|boolean
     */
    public function query($sql) {
        return $this->driver->query($this->prepair($sql));
    }

    /**
     * wrapper to connection escaped string method
     * @param string $value
     * @return string
     */
    public function escape($value) {
        return $this->driver->escape($value);
    }

    /**
     * wrapper to return number of affected rows from the connection
     * @return int
     */
    public function countAffected() {
        return $this->driver->countAffected();
    }

    /**
     * Wrapper to return the last insert id from the connection
     * @return int
     */
    public function getLastId() {
        return $this->driver->getLastId();
    }

    /**
     * quotes and optionally escapes the variable for the database query
     * @param string $text
     * @param boolean $escape
     * @return string
     */
    public function quote($text, $escape = true) {
        if (is_array($text)) {
            foreach ($text as $k => $v) {
                $text[$k] = $this->quote($v, $escape);
            }

            return $text;
        } else {
            return '\'' . ($escape ? $this->escape($text) : $text) . '\'';
        }
    }

    /**
     * updates the query to replace the prefix placeholder with the prefix on database table names
     * @param string $sql
     * @return string
     */
    public function prepair($sql) {
        if(!defined('DB_PREFIX')){
            return $sql;
        }
        return str_replace("#__", DB_PREFIX, $sql);
    }

}
