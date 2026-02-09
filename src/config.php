<?php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'mlaas_quotation');
define('DB_USER', 'root');
define('DB_PASS', 'P@ssw0rd@r00t');
define('DB_CHARSET', 'utf8mb4');

define('CF_SITE_KEY', '0x4AAAAAACYTiBuLgkyGB7q8');
define('CF_SECRET_KEY', '0x4AAAAAACYTiD3TFmSUAotsvQ3OOVQbj7Q');

define('APP_NAME', 'Mlaas Price Quotation System');
define('BASE_URL', 'http://localhost/pqms/');

date_default_timezone_set('Asia/Manila'); 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);