<?php

defined('ROOT_DIR') OR exit('No direct script access allowed');
return array (

    'L_MENU_BTN_HOME' => 'Главная',
    'L_MENU_BTN_HOME_URL' => '/',

    'L_MENU_BTN_REG' => 'Регистрация',
    'L_MENU_BTN_REG_URL' => 'sign-up',

    'L_MENU_BTN_FILE' => 'Начать',
    'L_MENU_BTN_FILE_URL' => 'javascript:void(0);',

    'L_MENU_BTN_ABOUT' => 'О сервере',
    'L_MENU_BTN_ABOUT_URL' => 'about',

    'L_MENU_BTN_DONATE' => 'Пожертвования',
    'L_MENU_BTN_DONATE_URL' => 'donations',

    'L_MENU_BTN_FORUM' => 'Форум',
    'L_MENU_BTN_FORUM_URL' => 'https://mmoweb.biz/',

    'L_MENU_BTN_CP' => 'Личный кабинет',
    'L_MENU_BTN_CP_URL' => 'sign-in',

	'L_MENU_BTN_SUPPORT' => 'Тех.Поддержка',
	'L_MENU_BTN_SUPPORT_URL' => 'panel/support',

	'L_MENU_BTN_RULES' => 'Правила',
	'L_MENU_BTN_RULES_URL' => 'rules',


	'L_HEADER_TEXT' => 'Добро пожаловать в <span class="color-orange">MMOWEB</span>',
	'L_HEADER_BTN_START' => 'Что дальше?',

	'L_NEWS_TITLE' => '<span class="color-orange">НОВОСТИ </span> сервера',
	'L_NEWS_BTN_READ_MORE' => 'Подробнее',
	'L_NEWS_TITLE_EMPTY' => 'Новостей нет',


	'L_RATING_TITLE' => '<span class="color-orange">ТОП</span> Игроков',
	'L_RATING_EMPTY_RATING' => 'Статистики по этому серверу еще нет!',

	'L_FORUM_TITLE' => '<span class="color-orange">Темы</span> форума',
	'L_FORUM_GO' => 'На форум',
	'L_FORUM_AUTHOR' => 'Автор',

	'L_SERVER_ONLINE' => 'Игроки онлайн :',
	'L_SERVER_OFFLINE' => 'Сервер выключен',
	'L_SERVER_ABOUT_READ' => 'Описание сервера',

	'L_STREAM_TITLE' => '<span class="color-orange">Трансляции</span> онлайн',

	'L_MMOWEB_GET_STARTED' => [
		'WINDOW_TITLE' => 'Добро пожаловать в <span class="color-orange">MMOWEB</span>',
		'WINDOW_SUBTITLE' => 'Для управления проектом, нужно зарегистрироваться в <a href="https://mmoweb.biz/sign-up" target="_blank">админ-панели MmoWeb</a>.',
		'STEPS' => [
			[
				'SUMMARY' => '1. Активируйте тариф чтобы начать!',
				'BTN_LINK' => 'https://mmoweb.biz/panel/tariffs',
				'BTN_TEXT' => 'Тарифы',
			],
			[
				'SUMMARY' => '2. Настройте ваш проект и загрузите конфиг',
				'BTN_LINK' => 'https://mmoweb.biz/panel/project/project',
				'BTN_TEXT' => 'Настройки',
			],
			[
				'SUMMARY' => '3. Начните работу с вашим сайтом',
				'BTN_LINK' => 'https://docs.mmoweb.biz',
				'BTN_TEXT' => 'Документация',
			]
		],
	],


);
