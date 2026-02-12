<?php

defined('ROOT_DIR') OR exit('No direct script access allowed');

$route['default_controller'] = 'Pages';

$route['(\w{2})/(.*)'] = '$2';
$route['(\w{2})'] = $route['default_controller'];


$route['app'] = "app/index";
$route['app/(:any)'] = "app/$1";

$route['api/(:any)'] = "ApiController/$1";
$route['api/payment/(:any)/(:any)'] = "ApiController/payment/$1/$2";

if (WEB_ITEM_DB) {
    $route['api/item'] = "ApiController/item";
}

$route['panel'] = "Panel/main";
$route['panel/(:any)'] = "Panel/main";
$route['panel/(:any)/(:any)'] = "Panel/main/$1/$2";
$route['panel/(:any)/(:any)/(:any)'] = "Panel/main/$1/$2/$3";

$route['logout'] = "Panel/logout";

$route['rating'] = "Panel/rating";
$route['rating/(:any)'] = "Panel/rating/$1";
$route['rating/(:any)/(:any)'] = "Panel/rating/$1/$2";

$route['donations'] = "Panel/donations";
$route['donations/(:any)'] = "Panel/donations/$1";
$route['donations/(:any)/(:any)'] = "Panel/donations/$1/$2";


$route['shop'] = "Panel/shop";
$route['shop/(:any)'] = "Panel/shop/$1";
$route['shop/(:any)/(:any)'] = "Panel/shop/$1/$2";

$route['sign-in'] = "Panel/signin";
$route['sign-in/(:any)'] = "Panel/signin/$1";

$route['sign-up'] = "Panel/signup";
$route['sign-up/activation'] = "Panel/signup_activation";
$route['sign-up/completed'] = "Panel/signup_completed";
$route['sign-up/code/([A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4})$'] = "Panel/signup_code/$1";


$route['reminder'] = "Panel/reminder";
$route['reminder/email'] = "Panel/reminder_email";
$route['reminder/([A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4})$'] = "Panel/reminder_email_code/$1";
$route['reminder/phone'] = "Panel/reminder_phone";

$route['invoice/(:any)'] = "Panel/invoice/$1";

$route['input'] = "In";
$route['captcha/altcha'] = "In/altchaChallenge";
$route['prefix/refresh'] = "In/prefix_list";
$route['text'] = "In/txt";
$route['promo-game'] = "In/promo_game";

$route['site-reconstruction'] = "Pages/site_reconstruction";
$route['panel-reconstruction'] = "Panel/panel_reconstruction";

if (ADMIN_PANEL AND (ADMIN_IP == '*' OR ADMIN_IP == get_ip())) {
    $route[ADMIN_URL] = "Panel/admin";
    $route[ADMIN_URL.'/(:any)'] = "Panel/admin/$1";
    $route[ADMIN_URL.'/(:any)/(:any)'] = "Panel/admin/$1/$2";
}

$route['(:any)'] = 'Pages/page_static/$1';
$route['(:any)/(:any)'] = 'Pages/page_static/$1/$2';

$route['404_override'] = '';
$route['translate_uri_dashes'] = TRUE;
