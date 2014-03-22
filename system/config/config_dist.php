<?php

define('DIR_ROOT', dirname(dirname(__DIR__)) . '/');

define('HTTP_CATALOG', '');
define('HTTPS_CATALOG', '');

// DB
define('DB_DRIVER', '');
define('DB_HOSTNAME', '');
define('DB_USERNAME', '');
define('DB_PASSWORD', '');
define('DB_DATABASE', '');
define('DB_PREFIX', '');


define('DIR_APPLICATION', DIR_ROOT . APP_NAMESPACE . '/');

define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/theme/');

define('DIR_SYSTEM', DIR_ROOT . 'system/');
define('DIR_IMAGE', DIR_ROOT . 'image/');
define('DIR_DOWNLOAD', DIR_ROOT . 'download/');

define('DIR_DATABASE', DIR_SYSTEM . 'database/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_SYSTEM . 'cache/');
define('DIR_LOGS', DIR_SYSTEM . 'logs/');

//really only an admi one, buy hey:
define('DIR_CATALOG', DIR_ROOT . 'public/');
define('DIR_MERCHANT', DIR_ROOT . 'merchant/');
define('DIR_ADMIN', DIR_ROOT . 'admin/');

if (APP_NAMESPACE == 'public') {
    define('HTTP_SERVER', HTTP_CATALOG);
    define('HTTPS_SERVER', HTTPS_CATALOG);
} else {
    define('HTTP_SERVER', HTTP_CATALOG . APP_NAMESPACE . '/');
    define('HTTPS_SERVER', HTTPS_CATALOG . APP_NAMESPACE . '/');
}   
