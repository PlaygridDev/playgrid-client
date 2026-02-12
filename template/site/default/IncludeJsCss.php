<?php

return array(
    'ru' => [
        'init' => [
            'head' => array(
                ['idx' => 'charset', 'typex' => 'meta', 'charset' => 'utf-8'],
                ['idx' => 'viewport', 'typex' => 'meta', 'name' => 'viewport', 'content' => 'width=device-width'],
                ['idx' => 'favicons', 'typex' => 'link', 'rel' => 'shortcut icon', 'href' => TEMPLATE_DIR.'/images/favicon.ico'],
                ['idx' => 'jquery', 'typex' => 'link', 'rel' => 'stylesheet', 'href' => TEMPLATE_DIR.'/fonts/BeaufortforLOL/fonts.css'],
                ['idx' => 'jquery', 'typex' => 'link', 'rel' => 'stylesheet', 'href' => TEMPLATE_DIR.'/css/adaptation.min.css'],
                ['idx' => 'jquery', 'typex' => 'link', 'rel' => 'stylesheet', 'href' => TEMPLATE_DIR.'/libs/fancybox/css/jquery.fancybox.min.css'],
                ['idx' => 'jquery', 'typex' => 'link', 'rel' => 'stylesheet', 'href' => TEMPLATE_DIR.'/css/style.min.css'],
                ['idx' => 'jquery', 'typex' => 'script', 'src' => TEMPLATE_DIR.'/libs/jquery/jquery-3.7.1.min.js'],
                ['idx' => 'jquery', 'typex' => 'script', 'src' => TEMPLATE_DIR.'/js/app.js'],
            ),
            'body' => array(),
            'footer' => array(
                ['idx' => 'jquery', 'typex' => 'script', 'src' => TEMPLATE_DIR.'/libs/fancybox/js/jquery.fancybox.min.js'],
                ['idx' => 'webstat', 'typex' => 'script', 'src' => 'https://mmoweb.biz/watch.js'],
            ),
        ],
    ],

    'en' => [],
);
