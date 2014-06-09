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

        $data['start'] = ($page - 1 )* $limit;
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

    public function view() {

        $freepon_id = (int) $this->request->get['freepon_id'];
        $this->load->model('deal/freepon');
        $freepon = $this->model_deal_freepon->getFreepon($freepon_id);

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_freepon'),
            'href' => $this->url->link('deal/freepon'),
            'separator' => $this->language->get('text_separator')
        );

        if ($freepon) {

            $this->data['breadcrumbs'][] = array(
                'text' => $freepon['name'],
                'href' => $this->url->link('deal/freepon', '&freepon_id=' . $freepon_id),
                'separator' => $this->language->get('text_separator')
            );

            $this->load->model('tool/image');
            $this->load->model('deal/company');
            foreach ($freepon['images'] as $i => $image) {
                if ($image) {
                    $freepon['images'][$i] = $this->model_tool_image->resize($image, 715, 480);
                } else {
                    unset($freepon['images'][$i]);
                }
            }

            $company = $this->model_deal_company->getCompany($freepon['company_id']);
            $freepon['company'] = array(
                'name' => $company['name'],
                'website' => $company['website'],
                'locations' => $this->model_deal_company->getLocations($company['company_id'])
            );
            $freepon['description'] = html_entity_decode($freepon['description'], ENT_QUOTES, 'UTF-8');
            $this->data['freepon'] = $freepon;
            $this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 715, 480);


            $this->document->addMeta('og:title', '<meta property="og:title" content="' . htmlspecialchars($freepon['name'], ENT_QUOTES) . '" />');
            $this->document->addMeta('og:description', '<meta property="og:description" content="' . htmlspecialchars($freepon['meta_description'], ENT_QUOTES) . '" />');

            $this->document->setTitle($freepon['name']);
            $this->document->setDescription($freepon['meta_description']);
            $this->document->setKeywords($freepon['meta_keyword']);
            $this->document->addLink($this->url->link('deal/deal/view', 'freepon_id=' . $this->request->get['freepon_id']), 'canonical');

            if (isset($deal['images'][0])) {
                $this->document->addMeta("og:image", '<meta property="og:image" content="' . $freepon['images'][0] . '" />');
            }

            $this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 715, 480);

            $this->data['lang'] = $this->language->get('code');
            $this->document->addScript('/public/view/javascript/countdown/jquery.plugin.min.js');
            $this->document->addScript('/public/view/javascript/countdown/jquery.countdown.min.js');
            $this->document->addScript('/public/view/javascript/countdown/jquery.countdown-' . $this->data['lang'] . '.js');
            $this->document->addStyle('/public/view/javascript/countdown/jquery.countdown.css');

            $this->model_deal_freepon->updateView($this->request->get['freepon_id']);

            $this->data['text_get_coupon'] = $this->language->get('text_get_coupon');
            $this->data['claim_coupon'] = $this->url->link('deal/freepon/claim', 'freepon_id=' . $freepon_id);
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

    public function claim() {
        $freepon_id = (int) $this->request->get['freepon_id'];
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('deal/freepon/claim', 'freepon_id=' . $freepon_id);
            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->load->model('deal/freepon');
        $freepon = $this->model_deal_freepon->getFreepon($freepon_id);
    
        if ($freepon) {



            $this->model_deal_freepon->updateClaim($freepon_id, $this->customer->getId());
            //is is a download or a code ?
            if ($freepon['download'] && is_file(DIR_IMAGE . $freepon['download'])) {
                $file = DIR_IMAGE . $freepon['download'];

                $mask = preg_replace("/[\s-]+/", "_", preg_replace('@[^a-z0-9_\-]@i', ' ', $freepon['name'])) . getFileExtension($freepon['download']);

                if (!headers_sent()) {
                    if (file_exists($file)) {
                        header('Content-Type: application/octet-stream');
                        header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                        header('Pragma: public');
                        header('Content-Length: ' . filesize($file));

                        if (ob_get_level())
                            ob_end_clean();

                        readfile($file, 'rb');

                        exit;
                    } else {
                        exit('Error: Could not find file ' . $file . '!');
                    }
                } else {
                    exit('Error: Headers already sent out!');
                }
            } else {
                $this->template = 'deal/freepon_coupon.phtml';
                if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
                    $server = $this->config->get('config_ssl');
                } else {
                    $server = $this->config->get('config_url');
                }
                $this->data['base'] = $server;
                $this->data['text_print'] = $this->language->get('text_print');

                if ($this->config->get('config_logo') && (substr($this->config->get('config_logo'), 0, 4) == "http" || file_exists(DIR_IMAGE . $this->config->get('config_logo')))) {

                    if (substr($this->config->get('config_logo'), 0, 4) == "http") {
                        $this->data['logo'] = $this->config->get('config_logo');
                    } else {
                        $this->data['logo'] = $server . 'image/' . $this->config->get('config_logo');
                    }
                } else {
                    $this->data['logo'] = '';
                }

                $this->data['code'] = $freepon['code'];
               // $this->data['code'] = 'asd4f';
                $this->data['name'] = $freepon['name'];
                
                

                $this->data['recipient'] = $this->customer->getFirstname() . ' ' . $this->customer->getLastname();
                $this->data['expires'] = date($this->language->get('date_format_long') . ' ' . $this->language->get('time_format'), $freepon['end_time']);


                $this->data['text_recipient'] = $this->language->get('text_recipient');
                $this->data['text_expires'] = $this->language->get('text_expires');
                $this->data['text_usage'] = $this->language->get('text_usage');
                $this->data['text_usage_text'] = ($freepon['usage'])?nl2br($freepon['usage']):$this->language->get('text_usage_text');
                $this->data['text_scan'] = $this->language->get('text_scan');


                $this->data['heading_title'] = $freepon['name'];
                $this->document->setTitle($freepon['name']);

                $PNG_TEMP_DIR = DIR_DOWNLOAD;
                $PNG_WEB_DIR = 'download/';
                $filename = $PNG_TEMP_DIR . 'coupon.png';
                $errorCorrectionLevel = 'L';
                $matrixPointSize = 6;
                $qrdat = str_replace("&amp;", "&", $this->url->link('deal/freepon/claim', 'freepon_id=' . $freepon_id));
                $filename = $PNG_TEMP_DIR . 'coupon' . md5($qrdat . '|' . $errorCorrectionLevel . '|' . $matrixPointSize) . '.png';
                QRcode::png($qrdat, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
                $this->data['qrcode'] = $PNG_WEB_DIR . basename($filename);


                $this->load->model('deal/company');
                $locations = $this->model_deal_company->getLocations($freepon['company_id']);
                $company = $this->model_deal_company->getCompany($freepon['company_id']);
            /*    $this->data['text_location'] = $this->language->get((count($locations) > 1) ? 'text_locations' : 'text_location');*/
                $this->data['text_location'] = $company['name'];
                $this->data['locations'] = $locations;

                $this->response->setOutput($this->render());
            }
        } else {
            $this->redirect($this->url->link('deal/freepon', '', 'SSL'));
        }
    }

}
