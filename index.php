<?php

/**
 * openDeal - Opensource Deals Platform
 *
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2014 Craig Smith
 * @link        https://github.com/openDeal/openDeal
 * @license     https://raw.githubusercontent.com/openDeal/openDeal/master/LICENSE
 * @version     1.0.0
 *
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
// Version

define('APP_NAMESPACE', 'public');

// Configuration
if (file_exists('system/config/config.php')) {

    require_once('system/config/config.php');
}
// Install 
if (!defined('DIR_APPLICATION')) {
    header('Location: install/index.php');
    exit;
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Tax
$registry->set('tax', new Tax($registry));

// Weight
$registry->set('weight', new Weight($registry));

// Length
$registry->set('length', new Length($registry));

// Cart
$registry->set('cart', new Cart($registry));

// Customer
$registry->set('customer', new Customer($registry));
// City
$registry->set('city', new City($registry));
// Affiliate
$registry->set('affiliate', new Affiliate($registry));

if (isset($request->get['tracking'])) {
    setcookie('tracking', $request->get['tracking'], time() + 3600 * 24 * 1000, '/');
}

/*
//QR TEST!
$PNG_TEMP_DIR = DIR_DOWNLOAD;
$PNG_WEB_DIR = 'download/';
$filename = $PNG_TEMP_DIR . 'coupon.png';
$errorCorrectionLevel = 'L';
$matrixPointSize = 6;
$qrdat = 'http://demo.rankedbyreview.com.info/?route=coupon/print&coupon_id=25';
 $filename = $PNG_TEMP_DIR.'coupon'.md5($qrdat.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
        QRcode::png($qrdat, $filename, $errorCorrectionLevel, $matrixPointSize, 2); 

echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" /><hr/>';  
*/
// Front Controller 
$controller = new \Core\Front($registry);

// Maintenance Mode
//$controller->addPreAction(new \Core\Action('common/maintenance'));
// SEO URL's
$controller->addPreAction(new \Core\Action('common/seo_url'));
// Router


if (isset($request->get['route'])) {
    $action = new \Core\Action($request->get['route']);
} else {
    $request->get['route'] = 'common/home';
    $action = new \Core\Action('common/home');
}

// Dispatch
$controller->dispatch($action, new \Core\Action('error/not_found'));

// Output
$response->output();
?>