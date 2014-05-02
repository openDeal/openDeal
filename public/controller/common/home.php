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
class ControllerCommonHome extends \Core\Controller {

    /**
     * common/home page 
     */
    public function index() {


        $this->document->setTitle($this->config->get('config_title'));
        $this->document->setDescription($this->config->get('config_meta_description'));
        $this->document->addMeta('og:site_name', '<meta property="og:site_name" content="' . $this->config->get('config_title') . '" />');
        $this->document->addMeta('og:url', '<meta property="og:url" content="' . $this->url->link("common/home") . '" />');
        $this->document->addMeta('og:title', '<meta property="og:title" content="' . htmlspecialchars($this->config->get('config_title'), ENT_QUOTES) . '" />');
        $this->document->addMeta('og:type', '<meta property="og:type" content="product" />');
        $this->document->addMeta('og:description', '<meta property="og:description" content="' . htmlspecialchars($this->config->get('config_meta_description'), ENT_QUOTES) . '" />');

        $this->data['heading_title'] = $this->config->get('config_title');

        $filter = array(
            'limit' => $this->config->get('config_deals_home_count'),
            'filter_begin_time' => time(),
            'filter_end_time' => time(),
            'filter_city_id' => $this->city->city
        );
        $this->load->model('deal/deal');

        $this->data['deals'] = array();

        $deals = $this->model_deal_deal->getDeals($filter);

        $this->load->model('tool/image');
        $this->load->model('deal/company');

        foreach ($deals as $deal) {
            if (isset($deal['images'][0]) && !empty($deal['images'][0])) {
                $deal['image'] = $this->model_tool_image->resize($deal['images'][0], 360, 270);
            } else {
                $deal['image'] = false;
            }


            $company = $this->model_deal_company->getCompany($deal['company_id']);
            $deal['company'] = array(
                'name' => $company['name'],
                'website' => $company['website'],
                'locations' => $this->model_deal_company->getLocations($company['company_id'])
            );
            $deal['highlights'] = html_entity_decode($deal['highlights'], ENT_QUOTES, 'UTF-8');
            $deal['conditions'] = html_entity_decode($deal['conditions'], ENT_QUOTES, 'UTF-8');
            $deal['details'] = html_entity_decode($deal['details'], ENT_QUOTES, 'UTF-8');

            $deal['url'] = $this->url->link('deal/deal', 'deal_id=' . $deal['deal_id']);

            $this->data['deals'][$deal['deal_id']] = $deal;
        }


        $this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 715, 480);

        $this->data['deal'] = array();
        if ($this->data['deals']) {

            $this->data['deal'] = array_shift($this->data['deals']);

            foreach ($this->data['deal']['images'] as $i => $image) {
                if ($image) {
                    $this->data['deal']['images'][$i] = $this->model_tool_image->resize($image, 715, 480);
                } else {
                    unset($this->data['deal']['images'][$i]);
                }
            }

            $this->model_deal_deal->updateView($this->data['deal']['deal_id']);
            $this->document->addMeta('og:title', '<meta property="og:title" content="' . htmlspecialchars($this->data['deal']['title'], ENT_QUOTES) . '" />');
            $this->document->addMeta('og:description', '<meta property="og:description" content="' . htmlspecialchars($this->data['deal']['introduction'], ENT_QUOTES) . '" />');



            if (isset($this->data['deal']['images'][0]) && !empty($this->data['deal']['images'][0])) {
                $this->document->addMeta("og:image", '<meta property="og:image" content="' . $this->data['deal']['images'][0] . '" />');
            }



            $this->template = 'common/home_deal.phtml';
        }
        //Deals order by featured desc!

        $this->data['lang'] = $this->language->get('code');
        $this->document->addScript('/public/view/javascript/countdown/jquery.plugin.min.js');
        $this->document->addScript('/public/view/javascript/countdown/jquery.countdown.min.js');
        $this->document->addScript('/public/view/javascript/countdown/jquery.countdown-' . $this->data['lang'] . '.js');
        $this->document->addStyle('/public/view/javascript/countdown/jquery.countdown.css');


        $this->data['text_no_results'] = sprintf($this->language->get('text_no_results'), $this->city->city_name);


        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );




        $this->response->setOutput($this->render());
    }

}
