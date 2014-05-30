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
class ModelDealCompany extends \Core\Model {

    public function getCompany($company_id) {
        $query = $this->db->query("Select * from #__company where company_id = " . (int) $company_id);
        return $query->row;
    }

    public function getLocations($company_id) {
        $query = $this->db->query("select * from #__company_location where company_id = " . (int) $company_id);
        return $query->rows;
    }

    public function getLocationsFromDealId($deal_id) {
        $query = $this->db->query("select c.* from #__company_location c inner join #__deal d on d.company_id = c.company_id where d.deal_id = " . (int) $deal_id);
        return $query->rows;
    }

}
