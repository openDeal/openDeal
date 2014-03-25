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
define('APP_NAMESPACE', 'admin');

if (file_exists('../system/config/config.php')) {
    require_once('../system/config/config.php');
}

if (!defined('DIR_APPLICATION')) {
    header('Location: ../install/index.php');
    exit;
}

require_once(DIR_SYSTEM . 'startup.php');

// Weight
//$registry->set('weight', new Weight($registry));
// Length
//$registry->set('length', new Length($registry));
// User
$registry->set('user', new User($registry));



// Front Controller
$controller = new \Core\Front($registry);

// Login
$controller->addPreAction(new \Core\Action('common/home/login'));

// Permission
$controller->addPreAction(new \Core\Action('common/home/permission'));

// Router
if (isset($request->get['route'])) {
    $action = new \Core\Action($request->get['route']);
} else {
    $action = new \Core\Action('common/home');
}

// Dispatch
$controller->dispatch($action, new \Core\Action('error/not_found'));

// Output
$response->output();
