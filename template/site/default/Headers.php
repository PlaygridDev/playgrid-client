<?php


return array(
    'ru' => array(
        'init' => array(
            'head' => array(
                ['idx' => 'title',      'typex' => 'title',                                         'content' => '%site_name% Сайт сервера %server_name%.'],
                ['idx' => 'og_title',   'typex' => 'meta', 'property' => 'og:title',                'content' => '%site_name% Сайт сервера %server_name%.'],
                ['idx' => 'og_s_name',  'typex' => 'meta', 'property' => 'og:site_name',            'content' => '%site_name%'],
                ['idx' => 'og_type',    'typex' => 'meta', 'property' => 'og:type',                 'content' => 'website'],
                ['idx' => 'og_url',     'typex' => 'meta', 'property' => 'og:url',                  'content' => '%url%'],

                ['idx' => 'desc',       'typex' => 'meta', 'name' => 'description',                 'content' => '%site_name% Сайт сервера %server_name%!'],
                ['idx' => 'og_desc',    'typex' => 'meta', 'property' => 'og:description',          'content' => '%site_name% Сайт сервера %server_name%!'],
                ['idx' => 'tw_desc',    'typex' => 'meta', 'property' => 'twitter:description',     'content' => '%site_name% Сайт сервера %server_name%!'],

                ['idx' => 'keywords',   'typex' => 'meta', 'name' => 'keywords',                    'content' => 'mmoweb, mmoweb4'],

            ),
        ),
    ),
    'en' => array(

    ),
);