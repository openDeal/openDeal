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
class ControllerDealFreepon extends \Core\Controller {

    public function index() {


        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->load->model('deal/freepon');
        $this->load->model('tool/image');

        $data['filter_city_id'] = $this->city->city;

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'f.feature_weight';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        } else {
            $limit = $this->config->get('config_catalog_limit');
        }

        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['sort'] = $sort;
        $data['order'] = $order;


        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        $url = $this->url->link('deal/freepon');
        if (isset($this->request->get['filter_category_id']) && $this->request->get['filter_category_id']) {
            $data['filter_category_id'] = $this->request->get['filter_category_id'];
            $url .= '&filter_category_id=' . (int) $data['filter_category_id'];
        }

        if (!empty($this->request->get['filter_name'])) {
            $data['filter_name'] = $this->request->get['filter_name'];
            $url.= '&filter_name=' . $data['filter_name'];
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $this->data['breadcrumbs'][] = array(
            'text' => $this->data['heading_title'],
            'href' => $url,
            'separator' => $this->language->get('text_separator')
        );



        $this->data['sorts']['f.feature_weight-DESC'] = array(
            'text' => $this->language->get('text_default'),
            'value' => 'f.feature_weight-DESC',
            'href' => $this->url->link('deal/freepon', '&sort=f.feature_weight&order=DESC')
        );

        $this->data['sorts']['f.begin_time-ASC'] = array(
            'text' => $this->language->get('text_start_time_asc'),
            'value' => 'f.begin_time-ASC',
            'href' => $this->url->link('deal/freepon', 'sort=f.begin_time&order=ASC')
        );
        $this->data['sorts']['f.begin_time-DESC'] = array(
            'text' => $this->language->get('text_start_time_desc'),
            'value' => 'f.begin_time-DESC',
            'href' => $this->url->link('deal/freepon', 'sort=f.begin_time&order=DESC')
        );
        $this->data['sorts']['f.end_time-ASC'] = array(
            'text' => $this->language->get('text_end_time_asc'),
            'value' => 'f.end_time-ASC',
            'href' => $this->url->link('deal/freepon', 'sort=f.end_time&order=ASC')
        );
        $this->data['sorts']['f.end_time-DESC'] = array(
            'text' => $this->language->get('text_end_time_desc'),
            'value' => 'f.end_time-DESC',
            'href' => $this->url->link('deal/freepon', 'sort=f.end_time&order=DESC')
        );
        $this->data['sorts']['fd.name-ASC'] = array(
            'text' => $this->language->get('text_name_asc'),
            'value' => 'fd.name-ASC',
            'href' => $this->url->link('deal/freepon', 'sort=fd.name&order=ASC')
        );
        $this->data['sorts']['fd.name-DESC'] = array(
            'text' => $this->language->get('text_name_desc'),
            'value' => 'fd.name-DESC',
            'href' => $this->url->link('deal/freepon', 'sort=fd.name&order=DESC')
        );

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }

        if (isset($this->request->get['filter_category_id'])) {
            $url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $this->data['limits'] = array();

        $limits = array_unique(array($this->config->get('config_catalog_limit'), 6, 12, 24, 48, 96));

        sort($limits);

        foreach ($limits as $value) {
            $this->data['limits'][] = array(
                'text' => $value,
                'value' => $value,
                'href' => $this->url->link('deal/freepon', $url . '&limit=' . $value)
            );
        }

        $url = '';
        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }

        if (isset($this->request->get['filter_category_id'])) {
            $url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }


        $freepon_total = $this->model_deal_freepon->getTotalFreepons($data);
    

        $freepons = $this->model_deal_freepon->getFreepons($data);
        $this->data['freepons'] = array();
        foreach ($freepons as $freepon) {
            if (isset($freepon['images'][0])) {
                $freepon['image'] = $this->model_tool_image->resize($freepon['images'][0], 360, 270);
            } else {
                $freepon['image'] = false;
            }
            $freepon['url'] = $this->url->link('deal/freepon/view', 'freepon_id=' . $freepon['freepon_id']);
            $this->data['freepons'][$freepon['freepon_id']] = $freepon;
        }

        $pagination = new Pagination();
        $pagination->total = $freepon_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('deal/freepon', $url . '&page={page}');

        $this->data['pagination'] = $pagination->render();
        
        
        $this->data['sort'] = $sort;
        $this->data['order'] = $order;
        $this->data['limit'] = $limit;

        $this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 360, 270);


        $this->template = 'deal/freepon_list.phtml';


        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );
        
        
        $this->data['text_no_results'] = $this->language->get('text_no_results');
        $this->data['text_limit'] = $this->language->get('text_limit');
        $this->data['text_sort'] = $this->language->get('text_sort');
        $this->data['button_view_coupon'] = $this->language->get('button_view_coupon');
        
        
         $this->data['lang'] = $this->language->get('code');
        $this->document->addScript('/public/view/javascript/countdown/jquery.plugin.min.js');
        $this->document->addScript('/public/view/javascript/countdown/jquery.countdown.min.js');
        $this->document->addScript('/public/view/javascript/countdown/jquery.countdown-' . $this->data['lang'] . '.js');
        $this->document->addStyle('/public/view/javascript/countdown/jquery.countdown.css');

        $this->response->setOutput($this->render());
    }

}
