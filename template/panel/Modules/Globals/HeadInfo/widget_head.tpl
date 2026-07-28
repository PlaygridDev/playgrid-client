<div class="row invisible" data-toggle="appear">
    {if $.site.session->session.master_account.status == 1
        && $.site.session->session.master_account.email_valid == 0
        && ($.site.config.cabinet.registration_confirmation || $.site.config.security.email_alias_mode !== 'off')}
    <div class="col-12">
        <div class="alert alert-primary" role="alert">
            <h5 class="alert-heading">
                {$verification_title}
            </h5>
            <p class="mb-3">
                {$verification_description}
            </p>
            <a href="javascript:void(0);" class="btn btn-alt-primary submit-btn" {$.php.btn_ajax("Modules\Globals\User\User", "email_verification_popup", [])}>
                {$verification_button}
            </a>
        </div>
    </div>
    {/if}
    <div class="col-12 col-xl-{$row_col}">
        <div class="block block-rounded block-bordered block-link-shadow options-container" >
            <div class="block-content block-content-full clearfix">
                <div class="float-right mt-15 d-none d-sm-block">
                    <i class="fa fa-user-o fa-2x text-primary-light"></i>
                </div>
                <div class="font-size-h4 font-w600 ">{$.site.session->getName()}</div>
                {if $.site.session->session.master_account.status == 2}
                    <div class="font-size-sm font-w600 text-uppercase text-danger">{$lang_account_ban}</div>
                {else}
                    <div class="font-size-sm font-w600 text-uppercase text-muted">{$lang_btn_last_enter} <span class="badge badge-pill badge-secondary">{$.site.session->session.master_account.last_login|date_format:"%y/%m/%d %H:%M"}</span></div>
                {/if}
            </div>
            <div class="options-overlay bg-white">
                <div class="options-overlay-content">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="h5 mb-5">{$lang_title_master_account}</h3>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <ul class="nav nav-pills flex-column push mb-0 ml-15">
                                        <li class="nav-item">
                                            <a class="nav-link d-flex align-items-center justify-content-between p-1" href="{$.php.set_url('panel/settings')}">
                                                <span><i class="si fa-fw si-settings mr-5"></i>{$lang_btn_go_settings}</span>
                                            </a>
                                        </li>
                                        {if $.site.config.cabinet.pin_shield}
                                        <li class="nav-item">
                                            <a class="nav-link d-flex align-items-center justify-content-between p-1" href="{$.php.set_url('panel/settings')}">
                                                <span><i class="fa fa-fw fa-shield mr-5"></i> {$lang_btn_reset_pin}</span>
                                            </a>
                                        </li>
                                        {/if}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {if $status_balance}
    <div class="col-12 col-md-4 col-xl-3" id="widgetHeadBalance">
        <div class="block block-rounded block-bordered block-link-shadow options-container" >
            <div class="block-content block-content-full clearfix">
                <div class="float-right mt-15 d-none d-sm-block">
                    <i class="fa fa-diamond fa-2x text-primary-light"></i>
                </div>
                <div class="d-flex">
                    <div class="d-flex flex-column">
                        <div class="font-size-h4 font-w600">
                            {ignore}
                            {{ balance.main }}
                            {/ignore}
                        </div>
                        <div class="font-size-sm font-w600 text-uppercase text-muted">
                            {if $.site.config.payment_system.long_name_valute?}
                                {if $.php.strlen($.site.config.payment_system.long_name_valute) > 10}
                                    {$.site.config.payment_system.short_name_valute}
                                {else}
                                    {$.site.config.payment_system.long_name_valute}
                                {/if}
                            {else}
                                {$lang_title_balance}
                            {/if}
                        </div>
                    </div>
                    {if $.site.config.bonus_balance.status && $bonusBalanceSettings.display_mode == 'separate'}
                    <div class="d-flex flex-column ml-3 border-left pl-3">
                        <div class="font-size-h4 font-w600">
                            {ignore}
                            {{ balance.bonus }}
                            {/ignore}
                        </div>
                        <div class="font-size-sm font-w600 text-uppercase text-muted">
                            {$bonusBalanceSettings.balance_name}
                        </div>
                    </div>
                    {/if}
                </div>
            </div>
            <div class="options-overlay bg-white">
                <div class="options-overlay-content">
                    <ul class="nav nav-pills flex-column push mb-0 ml-10">
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center justify-content-between p-1 submit-btn" href="javascript:void(0)" {$.php.btn_ajax("Modules\Globals\InGameCurrency\InGameCurrency", "open_form", [1])}>
                                <span><i class="fa fa-fw fa-gamepad mr-5"></i>{$lang_btn_go_transfer_game}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center justify-content-between p-1" href="{$.php.set_url('panel/donations')}">

                                <span><i class="si fa-fw si-plus  mr-5" style="color: #fab81b;"></i> {$lang_btn_go_enroll}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center justify-content-between p-1 submit-btn" href="javascript:void(0)" {$.php.btn_ajax("Modules\Globals\Donations\Donations", "ajax_refresh_balance", [0])}>
                                <span><i class="fa fa-fw fa-refresh mr-5"></i>{$lang_btn_go_refresh}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    {/if}

    {if $status_discount}
    <div class="col-6 col-md-4 col-xl-2">
        <div class="block block-rounded block-bordered block-link-shadow options-container" >
            <div class="block-content block-content-full clearfix">
                <div class="float-right mt-15 d-none d-sm-block">
                    <i class="si si-wallet fa-2x text-primary-light"></i>
                </div>
                <div class="font-size-h4 font-w600 " >{$.site.session->session.user_data.discount}%</div>
                <div class="font-size-sm font-w600 text-uppercase text-muted">{$lang_title_sale}</div>
            </div>
            <div class="options-overlay bg-white">
                <div class="options-overlay-content">
                    <h3 class="h4 mb-5">{$lang_title_sale} {$.site.session->session.user_data.discount}%</h3>
                    <a href="#" class="link-effect h6 mb-5"><i class="fa fa-info-circle"></i> {$lang_btn_go_info_sale}</a>
                </div>
            </div>
        </div>
    </div>
    {/if}

    {if $status_bonus_cod}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="block block-rounded block-bordered block-link-shadow options-container" >
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="fa fa-gift fa-2x text-primary-light"></i>
                    </div>
                    <div class="font-size-h4 font-w600">{$lang_btn_bc_title}</div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">{$lang_btn_bc_desc}</div>
                </div>
                <div class="options-overlay bg-white">
                    <div class="options-overlay-content">
                        <h3 class="h4 mb-5">{$lang_btn_bc_desc}</h3>
                        <button type="button" class="btn btn-sm btn-rounded btn-dual-secondary h6 mb-5 submit-btn" {$.php.btn_ajax("Modules\Plugins\BonusCod\BonusCod", "ajax_open_form")}><i class="fa fa-dropbox"></i> {$lang_btn_bc_open}</button>
                    </div>
                </div>
            </div>
        </div>
    {/if}
</div>

<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        function swich_status_widget_head(btn) {
            $(".options-overlay-show").removeClass("options-overlay-show");
            if (($('.options-overlay').length - 1) >= btn) {
                $(".options-overlay").eq(btn).addClass("options-overlay-show");
                btn++;
                setTimeout(swich_status_widget_head, 1000, btn);
            }else{
                setTimeout(swich_status_widget_head, 15000, 0);
            }
        }
        setTimeout(swich_status_widget_head, 5000, 0);
    });

</script>

<script>

    window.widgetHeadBalance = new Vue({

        el: '#widgetHeadBalance',

        data: function() {
            return {
                balance: {
                    main: {$.site.session->getBalance('main')},
                    bonus: {$.site.session->getBalance('bonus')}
                }
            }
        },

        methods: {
            setBalance(mainBalance, bonusBalance) {
                if(mainBalance !== null) {
                    this.balance.main = mainBalance;
                }
                if(bonusBalance !== null) {
                    this.balance.bonus = bonusBalance;
                }
            },
        },

        mounted: function() {
            window.addEventListener("localStorageChange", (event) => {
                widgetHeadBalance.setBalance(event.detail?.main_balance, event.detail?.bonus_balance);
            });
        },

    });

</script>