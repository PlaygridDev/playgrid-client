<!DOCTYPE html>
<html lang="{$_LANG}">
  <head>
    {$_SEO_HEAD}
  </head>
  <body class="body">
  {$_SEO_BODY}
    <div class="page">
      {if $.site.config.pid == false}
      <div style="background-color: rgb(90, 53, 28); padding: 20px; text-align: center;">
        <a href="https://mmoweb.biz/panel/project/project" target="_blank">
          Go to ACP and upload project config
        </a>
      </div>
      {/if}
      <div class="page__section navigation page__navigation">
        <div class="page__container navigation__container">
          <div class="page__inner page__inner_rw navigation__inner">
            <div class="navigation__box navigation__box_left">
              <div class="navigation__logotype-box logotype-box">
                <a href="/" class="navigation__logotype">
                  <img src="{$.site.dir_site}/images/mmoweb-logo.svg" alt="mmoweb4" class="logotype">
                </a>
              </div>
              <div class="nav navigation__nav">
                <div class="gw-burger navigation__burger">
                  <div class="gw-burger__line gw-burger__line_pos_top"></div>
                  <div class="gw-burger__line gw-burger__line_pos_middle"></div>
                  <div class="gw-burger__line gw-burger__line_pos_bottom"></div>
                </div>
                <div class="navigation__list">
                  <a href="{$.php.set_url($L_MENU_BTN_HOME_URL)}" class="nav__link">
                    <div class="nav__name">{$L_MENU_BTN_HOME}</div>
                  </a>
                  <a href="{$.php.set_url($L_MENU_BTN_REG_URL)}" class="nav__link">
                    <div class="nav__name">{$L_MENU_BTN_REG}</div>
                  </a>
                  <a href="{$.php.set_url($L_MENU_BTN_FILE_URL)}" data-open-window="files" class="nav__link">
                    <div class="nav__name">{$L_MENU_BTN_FILE}</div>
                  </a>
                  <a href="{$.php.set_url($L_MENU_BTN_ABOUT_URL)}" class="nav__link">
                    <div class="nav__name">{$L_MENU_BTN_ABOUT}</div>
                  </a>
                  <a href="{$.php.set_url($L_MENU_BTN_DONATE_URL)}" class="nav__link">
                    <div class="nav__name">{$L_MENU_BTN_DONATE}</div>
                  </a>
                  <a href="{$.php.set_url($L_MENU_BTN_FORUM_URL)}" target="_blank" class="nav__link">
                    <div class="nav__name">{$L_MENU_BTN_FORUM}</div>
                  </a>
                </div>
              </div>
            </div>
            <div class="navigation__lang-box">
              {language}
            </div>
            <div class="navigation__box">
              <div class="navigation__cp-box cp-box">
                {include 'site:is_login.tpl'}
              </div>
            </div>
          </div>
        </div>
      </div>

      <header class="page__section header page__header">
        <div class="page__container header__container">
          <div class="page__inner header__inner">
            <div class="header__logotype">
              <a href="/" class="header__logo logo"></a>
            </div>
            <section class="header__content">
              <div class="content__invite invite">
                <div class="invite__desc">{$L_HEADER_TEXT}</div>
              </div>
              <div class="header__btns">
                <a href="javascript:void(0)" data-open-window="files" class="btn btn_type_2">
                  <div class="btn__content">{$L_HEADER_BTN_START}</div>
                </a>
              </div>
            </section>
          </div>
        </div>
      </header>

      <div class="page__section events page__events">
        <div class="page__container events__container">
          <div class="page__inner events__inner">
            {iblock ikey='event' count=3}
          </div>
        </div>
      </div>

      <div class="page__section mid page__mid">
        <div class="page__container mid__container">
          <div class="page__inner page__inner_rw mid__inner">

            <div class="mid__item mid__sidebar sidebar">

              <div class="sidebar__container sidebar__servers servers">
                {server count=3}
              </div>

              <div class="sidebar__container sidebar__forum forum">
                {forum count=5}
              </div>

              <div class="content__st st">
                {rating count=10}
              </div>
            </div>

            <div class="mid__item mid__content content">
              {streams count=3}

              <div class="content__box">
                {if $_CONTENT?}
                  {$_CONTENT}
                {else}
                  {news count=3 page=1}
                {/if}
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="page__section footer page__footer">
        <div class="page__container footer__container">
          <div class="page__inner page__inner_rw footer__inner">
            <div class="footer__content">
              <div class="footer__logotype">
                <a href="/">
                  <img src="{$.site.dir_site}/images/mmoweb-logo.svg" alt="" class="f-logo">
                </a>
              </div>
              <div class="footer__desc">
                <a href="https://mmoweb.biz" target="_blank">MmoWeb Game Panel</a>
                <p>
                  PLAY ON THE BEST GAME SERVERS. TRY THE TASTE OF HARDCORE.
                </p>
              </div>
            </div>

            <div class="footer__nav">
              <div class="f-nav footer__f-nav">
                <div class="f-nav__list">
                  <a href="{$.php.set_url($L_MENU_BTN_HOME_URL)}" class="f-nav__item">{$L_MENU_BTN_HOME}</a>
                  <a href="{$.php.set_url($L_MENU_BTN_REG_URL)}" class="f-nav__item">{$L_MENU_BTN_REG}</a>
                  <a href="{$.php.set_url($L_MENU_BTN_FILE_URL)}" data-open-window="files" class="f-nav__item">{$L_MENU_BTN_FILE}</a>
                  <a href="{$.php.set_url($L_MENU_BTN_ABOUT_URL)}" class="f-nav__item">{$L_MENU_BTN_ABOUT}</a>
                </div>
              </div>
              <div class="f-nav footer__f-nav">
                <div class="f-nav__list">
                  <a href="{$.php.set_url($L_MENU_BTN_SUPPORT_URL)}" class="f-nav__item">{$L_MENU_BTN_SUPPORT}</a>
                  <a href="{$.php.set_url($L_MENU_BTN_RULES_URL)}" class="f-nav__item">{$L_MENU_BTN_RULES}</a>
                  <a href="{$.php.set_url($L_MENU_BTN_DONATE_URL)}" class="f-nav__item">{$L_MENU_BTN_DONATE}</a>
                  <a href="{$.php.set_url($L_MENU_BTN_FORUM_URL)}" class="f-nav__item">{$L_MENU_BTN_FORUM}</a>
                </div>
              </div>
            </div>
            <!-- END  footer__nav -->
            <div class="footer__info">
              <div class="f-scl">
                <a href="#" class="f-scl__item">
                  <span class="gwi gwi_skype"></span>
                </a>
                <a href="#" class="f-scl__item">
                  <span class="gwi gwi_facebook-squared"></span>
                </a>
                <a href="#" class="f-scl__item">
                  <span class="gwi gwi_twitter"></span>
                </a>
                <a href="#" class="f-scl__item">
                  <span class="gwi gwi_vkontakte"></span>
                </a>
                <a href="#" class="f-scl__item">
                  <span class="gwi gwi_youtube-play"></span>
                </a>
              </div>
              <div class="footer__author author">
                <div class="author__logo">
                  <img src="{$.site.dir_site}/images/copyright__mex-vision.png" alt="">
                </div>
                <div class="author__logo">
                  <img src="{$.site.dir_site}/images/get-web-copyrights.png" alt="Get-Web">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div style="display: none;">
      <div class="ww ww_animated" id="files">
        <div class="ww__close" data-fancybox-close></div>
        <div class="ww__inner">
          <div class="ww__head">
            <div class="ww__title">{$L_MMOWEB_GET_STARTED.WINDOW_TITLE}</div>
            <div class="ww__desc">
              {$L_MMOWEB_GET_STARTED.WINDOW_SUBTITLE}
            </div>
          </div>
          <div class="ww__content">
            <div class="fl-list">
              {foreach $L_MMOWEB_GET_STARTED.STEPS as $STEP}
              <div class="fl fl_mb">
                <div class="fl__desc">{$STEP.SUMMARY}</div>
                <a href="{$STEP.BTN_LINK}" target="_blank" class="fl__link btn btn_type_3">
                  <div class="btn__content">
                    {$STEP.BTN_TEXT}
                  </div>
                </a>
              </div>
              {/foreach}
            </div>
          </div>
        </div>
      </div>
    </div>
    {$_SEO_FOOTER}
  </body>
</html>