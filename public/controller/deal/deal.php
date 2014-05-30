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
class ControllerDealDeal extends \Core\Controller {

    /**
     * common/home page 
     */
    public function index() {
        $deal_id = (int) $this->request->get['deal_id'];
        $this->load->model('deal/deal');
        $deal = $this->model_deal_deal->getDeal($deal_id);

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );


        if ($deal) {

            $this->data['breadcrumbs'][] = array(
                'text' => $deal['title'],
                'href' => $this->url->link('deal/deal', '&deal_id=' . $deal_id),
                'separator' => $this->language->get('text_separator')
            );

            $this->load->model('tool/image');
            $this->load->model('deal/company');
            foreach ($deal['images'] as $i => $image) {
                if ($image) {
                    $deal['images'][$i] = $this->model_tool_image->resize($image, 715, 480);
                } else {
                    unset($deal['images'][$i]);
                }
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
            $this->data['deal'] = $deal;
            $this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 715, 480);


            $this->document->addMeta('og:title', '<meta property="og:title" content="' . htmlspecialchars($deal['title'], ENT_QUOTES) . '" />');
            $this->document->addMeta('og:description', '<meta property="og:description" content="' . htmlspecialchars($deal['introduction'], ENT_QUOTES) . '" />');

            $this->document->setTitle($deal['title']);
            $this->document->setDescription($deal['meta_description']);
            $this->document->setKeywords($deal['meta_keyword']);
            $this->document->addLink($this->url->link('deal/deal', 'deal_id=' . $this->request->get['deal_id']), 'canonical');




            if (isset($deal['images'][0])) {
                $this->document->addMeta("og:image", '<meta property="og:image" content="' . $deal['images'][0] . '" />');
            }

            $this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 715, 480);

            $this->data['lang'] = $this->language->get('code');
            $this->document->addScript('/public/view/javascript/countdown/jquery.plugin.min.js');
            $this->document->addScript('/public/view/javascript/countdown/jquery.countdown.min.js');
            $this->document->addScript('/public/view/javascript/countdown/jquery.countdown-' . $this->data['lang'] . '.js');
            $this->document->addStyle('/public/view/javascript/countdown/jquery.countdown.css');

            $this->model_deal_deal->updateView($this->request->get['deal_id']);
        } else {
            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_error'),
                'href' => $this->url->link('deal/deal', '&deal_id=' . $deal_id),
                'separator' => $this->language->get('text_separator')
            );

            $this->document->setTitle($this->language->get('text_error'));

            $this->data['heading_title'] = $this->language->get('text_error');

            $this->data['text_error'] = $this->language->get('text_error');

            $this->data['button_continue'] = $this->language->get('button_continue');

            $this->data['continue'] = $this->url->link('common/home');

            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . '/1.1 404 Not Found');

            $this->template = 'error/not_found.phtml';
        }
        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());

//Gets a current deal and displays it!   
    }

    public function current() {
        $data = array(
            'filter_current' => true
        );
        $this->data['heading_title'] = $this->language->get('heading_title_current');
//displays paged pages of current deals
        $this->getList($data, 'current');
    }

    public function expired() {

//displays expired deals
        $data = array(
            'filter_expired' => true
        );
        $this->data['heading_title'] = $this->language->get('heading_title_expired');
        $this->getList($data, 'expired');
    }

    public function future() {
//displays upcomming deals
        $data = array(
            'filter_future' => true
        );
        $this->language->load('deal/future');
        $this->data['heading_title'] = $this->language->get('heading_title_future');
        $this->getList($data, 'future');
    }

    protected function getList($data, $current) {

        $this->load->model('deal/deal');
        $this->load->model('tool/image');

        $data['filter_city_id'] = $this->city->city;

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'd.feature_weight';
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

        $url = $this->url->link('deal/deal/' . $current);
        if (isset($this->request->get['filter_category_id']) && $this->request->get['filter_category_id']) {
            $data['filter_category_id'] = $this->request->get['filter_category_id'];
            $url .= '&filter_category_id=' . (int) $data['filter_category_id'];
        }

        if (!empty($this->request->get['filter_title'])) {
            $data['filter_title'] = $this->request->get['filter_title'];
            $url.= '&filter_title=' . $data['filter_title'];
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

        $this->data['sorts']['d.feature_weight-DESC'] = array(
            'text' => $this->language->get('text_default'),
            'value' => 'd.feature_weight-DESC',
            'href' => $this->url->link('deal/deal/' . $current, '&sort=d.feature_weight&order=DESC')
        );

        if (!isset($data['filter_expired'])) {

            $this->data['sorts']['d.begin_time-ASC'] = array(
                'text' => $this->language->get('text_start_time_asc'),
                'value' => 'd.begin_time-ASC',
                'href' => $this->url->link('deal/deal/' . $current, 'sort=d.begin_time&order=ASC')
            );
            if (!isset($data['filter_future'])) {
                $this->data['sorts']['d.begin_time-DESC'] = array(
                    'text' => $this->language->get('text_start_time_desc'),
                    'value' => 'd.begin_time-DESC',
                    'href' => $this->url->link('deal/deal/' . $current, 'sort=d.begin_time&order=DESC')
                );

                $this->data['sorts']['d.end_time-ASC'] = array(
                    'text' => $this->language->get('text_end_time_asc'),
                    'value' => 'd.end_time-ASC',
                    'href' => $this->url->link('deal/deal/' . $current, 'sort=d.end_time&order=ASC')
                );
                $this->data['sorts']['d.end_time-DESC'] = array(
                    'text' => $this->language->get('text_end_time_desc'),
                    'value' => 'd.end_time-DESC',
                    'href' => $this->url->link('deal/deal/' . $current, 'sort=d.end_time&order=DESC')
                );
            }
        }
        $this->data['sorts']['dd.title-ASC'] = array(
            'text' => $this->language->get('text_title_asc'),
            'value' => 'dd.title-ASC',
            'href' => $this->url->link('deal/deal/' . $current, 'sort=dd.title&order=ASC')
        );
        $this->data['sorts']['dd.title-DESC'] = array(
            'text' => $this->language->get('text_title_desc'),
            'value' => 'dd.title-DESC',
            'href' => $this->url->link('deal/deal/' . $current, 'sort=dd.title&order=DESC')
        );


        $url = '';

        if (isset($this->request->get['filter_title'])) {
            $url .= '&filter_title=' . $this->request->get['filter_title'];
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
                'href' => $this->url->link('deal/deal/' . $current, $url . '&limit=' . $value)
            );
        }

        $url = '';
        if (isset($this->request->get['filter_title'])) {
            $url .= '&filter_title=' . $this->request->get['filter_title'];
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


        $deal_total = $this->model_deal_deal->getTotalDeals($data);

        $deals = $this->model_deal_deal->getDeals($data);
        $this->data['deals'] = array();
        foreach ($deals as $deal) {
            if (isset($deal['images'][0])) {
                $deal['image'] = $this->model_tool_image->resize($deal['images'][0], 360, 270);
            } else {
                $deal['image'] = false;
            }
            $deal['url'] = $this->url->link('deal/deal', 'deal_id=' . $deal['deal_id']);
            $this->data['deals'][$deal['deal_id']] = $deal;
        }

        $pagination = new Pagination();
        $pagination->total = $deal_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('deal/deal/' . $current, $url . '&page={page}');

        $this->data['pagination'] = $pagination->render();
        $this->data['sort'] = $sort;
        $this->data['order'] = $order;
        $this->data['limit'] = $limit;

        $this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 360, 270);
        // $this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 715, 480);

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );


        $this->template = 'deal/deal_list.phtml';

        $this->data['lang'] = $this->language->get('code');
        $this->document->addScript('/public/view/javascript/countdown/jquery.plugin.min.js');
        $this->document->addScript('/public/view/javascript/countdown/jquery.countdown.min.js');
        $this->document->addScript('/public/view/javascript/countdown/jquery.countdown-' . $this->data['lang'] . '.js');
        $this->document->addStyle('/public/view/javascript/countdown/jquery.countdown.css');


        $this->data['text_limit'] = $this->language->get('text_limit');
        $this->data['text_sort'] = $this->language->get('text_sort');
        $this->data['text_no_results'] = $this->language->get('text_no_results');

        $this->response->setOutput($this->render());
    }

    public function buy() {
        //Ok some specific options
        //ist is the deal valid
        $deal_id = (int) $this->request->get['deal_id'];
        $this->load->model('deal/deal');
        $deal = $this->model_deal_deal->getDeal($deal_id);

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );


        if ($deal && $deal['state'] == ModelDealDeal::DEAL_AVAILABLE) {

            $deal_options = $this->model_deal_deal->getDealOptions($deal_id);

            //No Options and is a coupon - so lets add to cart straight off:::
            if (!count($deal_options) && $deal['is_coupon']) {
                //Add to cart and redirect to the cart
                //Would be deal_id, 0, -1 (-1 for coupon
                $this->language->load('checkout/cart');
                $this->cart->add($deal_id, 1, '0', '0');
                $data['success'] = sprintf($this->language->get('text_success'), $this->url->link('deal/deal', 'deal_id=' . $deal_id), $deal['title'], $this->url->link('checkout/cart'));
                $data['redirect'] = $this->url->link('checkout/cart');
                $this->session->data['success'] = $data['success'];
                $this->redirect($data['redirect']);
            }

            //Ok here means that we may offer more than one shipping option
            $deal_shipping = $this->model_deal_deal->getDealShippings($deal_id);


            //Can collect, not shipping and no no ptions - add directly to cart
            if (!count($deal_shipping) && !count($deal_options) && $deal['can_collect'] == 1) {
                $this->language->load('checkout/cart');
                $this->cart->add($deal_id, 1, '0', '0');
                $data['success'] = sprintf($this->language->get('text_success'), $this->url->link('deal/deal', 'deal_id=' . $deal_id), $deal['title'], $this->url->link('checkout/cart'));
                $data['redirect'] = $this->url->link('checkout/cart');
                $this->session->data['success'] = $data['success'];
                $this->redirect($data['redirect']);
            }

            //only one shipping option,no deal options and not collect -- add direct to cart
            if (count($deal_shipping) == 1 && !count($deal_options) && $deal['can_collect'] == 0) {
                //Add to cart and redirect to the cart
                $ship = array_shift($deal_shipping);
                //Would be deal_id, 0, $ship['deal_shipping_id'] (to set the shipping id :-) 
                $this->language->load('checkout/cart');
                $this->cart->add($deal_id, 1, '0', $ship['deal_shipping_id']);
                $data['success'] = sprintf($this->language->get('text_success'), $this->url->link('deal/deal', 'deal_id=' . $deal_id), $deal['title'], $this->url->link('checkout/cart'));
                $data['redirect'] = $this->url->link('checkout/cart');
                $this->session->data['success'] = $data['success'];
                $this->redirect($data['redirect']);
            }


            if (isset($_POST['shipping'])) {
                $this->data['shipping_id'] = $_POST['shipping'];
            } elseif ($deal['can_collect'] == 1) {
                $this->data['shipping_id'] = '0';
            } elseif (count($deal_shipping)) {
                $this->data['shipping_id'] = current(array_keys($deal_shipping));
            }



            if (isset($_POST['option'])) {
                $this->data['option'] = $_POST['option'];
            } elseif ($deal_options) {
                $this->data['option'] = '0';
            }


            $this->data['deal_shipping'] = $deal_shipping;
            $this->data['deal_can_collect'] = $deal['can_collect'];
            $this->data['deal_options'] = $deal_options;

            $this->data['text_collect'] = $this->language->get("text_collect");


            //Ok this no means we have some choices for the user to make! - so lets display a mini- version of the deal!
            //Ok deal is valid::
            //Are there any options
            //@todo build options subsystem ?


            $this->data['breadcrumbs'][] = array(
                'text' => $deal['title'],
                'href' => $this->url->link('deal/deal', '&deal_id=' . $deal_id),
                'separator' => $this->language->get('text_separator')
            );

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get("button_buy"),
                'href' => $this->url->link('deal/deal/buy', '&deal_id=' . $deal_id),
                'separator' => $this->language->get('text_separator')
            );

            $this->document->setTitle($deal['title']);
            $this->document->setDescription($deal['meta_description']);
            $this->document->setKeywords($deal['meta_keyword']);
            $this->document->addLink($this->url->link('deal/deal/buy', 'deal_id=' . $this->request->get['deal_id']), 'canonical');



            $this->data['heading_title'] = $this->language->get("heading_purchace_deal");
            $this->data['text_choose_option'] = $this->language->get("text_choose_option");
            $this->data['text_choose_shipping'] = $this->language->get("text_choose_shipping");


            $this->load->model('tool/image');
            $this->load->model('deal/company');
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
            $this->data['deal'] = $deal;
            $this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 360, 270);

            $this->data['lang'] = $this->language->get('code');
            $this->document->addScript('/public/view/javascript/countdown/jquery.plugin.min.js');
            $this->document->addScript('/public/view/javascript/countdown/jquery.countdown.min.js');
            $this->document->addScript('/public/view/javascript/countdown/jquery.countdown-' . $this->data['lang'] . '.js');
            $this->document->addStyle('/public/view/javascript/countdown/jquery.countdown.css');

            $this->template = 'deal/buy.phtml';
        } else {
            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_error'),
                'href' => $this->url->link('deal/deal', '&deal_id=' . $deal_id),
                'separator' => $this->language->get('text_separator')
            );

            $this->document->setTitle($this->language->get('text_error'));

            $this->data['heading_title'] = $this->language->get('text_error');

            $this->data['text_error'] = $this->language->get('text_error');

            $this->data['button_continue'] = $this->language->get('button_continue');

            $this->data['continue'] = $this->url->link('common/home');

            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . '/1.1 404 Not Found');

            $this->template = 'error/not_found.phtml';
        }
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
