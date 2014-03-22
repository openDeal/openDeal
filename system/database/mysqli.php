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
final class DBMySQLi {

    /**
     * mysqli connection
     * @var object 
     */
    private $link;

    /**
     * Initializes the database
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     * @throws ErrorException
     */
    public function __construct($hostname, $username, $password, $database) {
        $this->link = new mysqli($hostname, $username, $password, $database);

        if (mysqli_connect_error()) {
            throw new ErrorException('Error: Could not make a database link (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
        }

        $this->link->set_charset("utf8");
    }

    /**
     * Execute query on the database
     * @param string $sql
     * @return \stdClass|boolean
     * @throws ErrorException
     */
    public function query($sql) {
        $query = $this->link->query($sql);

        if (!$this->link->errno) {
            if (isset($query->num_rows)) {
                $data = array();

                while ($row = $query->fetch_assoc()) {
                    $data[] = $row;
                }

                $result = new stdClass();
                $result->num_rows = $query->num_rows;
                $result->row = isset($data[0]) ? $data[0] : array();
                $result->rows = $data;

                unset($data);

                $query->close();

                return $result;
            } else {
                return true;
            }
        } else {
            throw new ErrorException('Error: ' . $this->link->error . '<br />Error No: ' . $this->link->errno . '<br />' . $sql);
            exit();
        }
    }

    /**
     * Escapes the value
     * @param string $value
     * @return type
     */
    public function escape($value) {
        return $this->link->real_escape_string($value);
    }

    /**
     * returns the number of affected rows
     * @return int
     */
    public function countAffected() {
        return $this->link->affected_rows;
    }

    /**
     * returns the insert id from the last query
     * @return int
     */
    public function getLastId() {
        return $this->link->insert_id;
    }

    /**
     * Closes the database connection
     */
    public function __destruct() {
        $this->link->close();
    }

}

?>