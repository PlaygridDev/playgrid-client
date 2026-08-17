<div class="block block-rounded">
    <ul class="nav nav-tabs nav-tabs-block" data-toggle="tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="#settings">{$lang_tab_title_settings}</a>
        </li>
        {if $.site.config.cabinet.manager_ma? AND $.site.config.cabinet.manager_ma AND $check_plugin_man_acc}
        <li class="nav-item">
            <a class="nav-link" href="#manager">{$lang_tab_title_manager}</a>
        </li>
        {/if}
        {if $account_list_hide? AND $account_list_hide != false}
        <li class="nav-item">
            <a class="nav-link" href="#hide">{$lang_tab_title_account_hide}</a>
        </li>
        {/if}
        {if $.site.config.cabinet.tab_active_log}
        <li class="nav-item">
            <a class="nav-link" href="#log-list">{$lang_tab_title_logs}</a>
        </li>
        {/if}
        {if $.site.config.cabinet.tab_active_invoice}
        <li class="nav-item">
            <a class="nav-link" href="#invoice-list">{$lang_tab_title_invoice}</a>
        </li>
        {/if}
        <li class="nav-item">
            <a class="nav-link" href="#security">{$security_tab_title}</a>
        </li>


    </ul>
    <div class="block-content tab-content">
        <div class="tab-pane active" id="settings" role="tabpanel">
            <div class="row justify-content-center py-20">
                <div class="col-xl-12">
                    <h4 class="font-w400 text-center">{$lang_tab_title_settings_desc}</h4>
                    <hr>
                    {$content}
                </div>
            </div>
        </div>
        {if $.site.config.cabinet.manager_ma? AND $.site.config.cabinet.manager_ma AND $check_plugin_man_acc}
        <div class="tab-pane" id="manager" role="tabpanel">
            <div class="row justify-content-center py-20">
                <div class="col-xl-12">
                    <h4 class="font-w400 text-center">{$lang_tab_title_manager_desc}</h4>
                    <hr>
                    {$manager_content}
                </div>
            </div>
        </div>
        {/if}

        {if $account_list_hide? AND $account_list_hide != false}
            <div class="tab-pane" id="hide" role="tabpanel">
                <div class="row justify-content-center py-20">
                    <div class="col-xl-12">
                        <h4 class="font-w400 text-center">{$lang_tab_title_account_hide_desc}</h4>
                        <hr>
                        {$account_list_hide}
                    </div>
                </div>
            </div>
        {/if}

        {if $.site.config.cabinet.tab_active_log}
        <div class="tab-pane mb-20" id="log-list" role="tabpanel">
            <h4 class="font-w400 text-center">{$lang_tab_title_logs_desc}</h4>
            <table class="table table-bordered table-striped table-vcenter log-list-table ">
                <thead>
                <tr>
                    <th class="text-center"></th>
                    <th>{$lang_tab_logs_th_action}</th>
                    <th class="d-none d-sm-table-cell">IP</th>
                    <th class="d-none d-sm-table-cell">{$lang_tab_logs_th_date}</th>
                </tr>
                </thead>
                <tbody>
                {if $.site.session->session.user_data.logs? AND $.php.is_array($.site.session->session.user_data.logs)}
                    {foreach $.site.session->session.user_data.logs as $log}
                        <tr>
                            <td class="text-center">{$log.id}</td>
                            <td class="font-w600">{$log.description}</td>
                            <td class="d-none d-sm-table-cell">{$log.ip}</td>
                            <td class="d-none d-sm-table-cell">{$log.date}</td>
                        </tr>
                    {/foreach}
                {/if}
                </tbody>
            </table>
        </div>
        {/if}

        {if $.site.config.cabinet.tab_active_invoice}
        <div class="tab-pane mb-20" id="invoice-list" role="tabpanel">
            <h4 class="font-w400 text-center">{$lang_tab_title_invoice}</h4>

            <table class="table table-bordered table-striped table-vcenter log-list-table ">
                <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="d-none d-sm-table-cell">{$lang_tab_invoice_th_payment}</th>
                    <th class="">{$.site.config.payment_system.long_name_valute}</th>
                    <th class="">{$lang_tab_invoice_th_sum}</th>
                    <th class="d-none d-sm-table-cell">{$lang_tab_logs_th_date}</th>
                    <th class="d-none d-sm-table-cell">Status</th>
                    {if $.site.config.cabinet.tab_active_invoice_detail}<th class=""></th>{/if}
                </tr>
                </thead>




                <tbody>
                {if $.site.session->session.user_data.invoice? AND $.php.is_array($.site.session->session.user_data.invoice)}
                    {foreach $.site.session->session.user_data.invoice as $invoice}
                        <tr>
                            <td class="text-center">{$invoice.id}</td>
                            <td class="d-none d-sm-table-cell">{$invoice.ps}</td>
                            <td class="font-w600">{$invoice.c}</td>
                            <td class="font-w600">{$invoice.s} {$invoice.cur}</td>
                            <td class="d-none d-sm-table-cell">{$invoice.dc}</td>
                            <td class="d-none d-sm-table-cell">{if $invoice.st == 0}waiting{elseif $invoice.st == 1}complete{elseif $invoice.st == 2}refund{elseif $invoice.st == 3}waiting for confirmation{else}Status:{$invoice.st}{/if}</td>
                            {if $.site.config.cabinet.tab_active_invoice_detail}<td class="text-center"><a href="{$.php.set_url('/invoice/'~$invoice.payid)}" class="btn btn-sm btn-secondary" target="_blank"><i class="fa fa-external-link"></i></a></td>{/if}
                        </tr>
                    {/foreach}
                {/if}
                </tbody>
            </table>
        </div>
        {/if}
        <div class="tab-pane mb-20" id="security" role="tabpanel">
            <h4 class="font-w400 text-center">{$security_tab_title}</h4>
            <div class="form-group row">
                <label class="col-md-4 col-form-label text-right border-right">
                    {$two_factor_auth_title}<br />
                </label>
                <div class="col-md-6 pt-5">
                    <div class="d-flex flex-column align-items-start">
                        {if count($.site.session->get2FAMethods()) == 0}
                        <a href="javascript:void(0);" class="btn btn-alt-primary submit-btn" {$.php.btn_ajax("Modules\Globals\Settings\Settings", "enable_two_factor_auth_method_popup", [])}>
                            <i class="fa fa-lock mr-2"></i>
                            {$two_factor_enable_button}
                        </a>
                        {else}
                            <div class="d-flex justify-content-center align-items-center mb-3">
                                <span class="text-success">{$two_factor_auth_enabled}</span>
                                {foreach $.site.session->get2FAMethods() as $method}
                                <div class="badge badge-success text-capitalize ml-2">
                                    <i class="fa fa-lock mr-1"></i>
                                    {$two_factor_auth_method_labels[$method]}
                                </div>
                                {/foreach}
                            </div>
                            {if !$.site.session->get2FAStatusForMethod('email') || !$.site.session->get2FAStatusForMethod('totp') || (!$.site.session->get2FAStatusForMethod('phone') && $.site.config.cabinet.signin_type.phone is set)}
                                <a href="javascript:void(0);" class="btn btn-alt-primary submit-btn mb-3" {$.php.btn_ajax("Modules\Globals\Settings\Settings", "enable_two_factor_auth_method_popup", [])}>
                                    <i class="fa fa-plus mr-2"></i>
                                    {$two_factor_auth_add_method}
                                </a>
                            {/if}
                            <a href="javascript:void(0);" class="btn btn-alt-secondary submit-btn" {$.php.btn_ajax("Modules\Globals\Security\Security", "two_factor_verification_popup", ['action'=>'2fa_disable'])}>
                                <i class="fa fa-unlock mr-2"></i>
                                {$two_factor_auth_disable}
                            </a>
                        {/if}
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-4 col-form-label text-right border-right">
                    {$change_password_title}
                </label>
                <div class="col-md-6 pt-5">
                    <a href="javascript:void(0);" class="btn btn-alt-secondary submit-btn" {$.php.btn_ajax("Modules\Globals\Settings\Settings", "change_password_popup", [])}>
                        {$change_password_button}
                    </a>
                </div>
            </div>
            <!-- <div class="form-group row">
                <label class="col-md-4 col-form-label text-right border-right">
                    {$notifications_title}
                </label>
                <div class="col-md-6 pt-5">
                    <div class="d-block">
                        <div class="form-group row mb-2">
                            <label class="col-12">{$notofications_send_to}</label>
                            <div class="col-12">
                                <div class="custom-control custom-radio custom-control-inline mb-5">
                                    <input class="custom-control-input" type="radio" name="example-inline-radios" id="example-inline-radio1" value="option1" checked="">
                                    <label class="custom-control-label" for="example-inline-radio1">{$notifications_send_to_email}</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline mb-5">
                                    <input class="custom-control-input" type="radio" name="example-inline-radios" id="example-inline-radio2" value="option2" disabled>
                                    <label class="custom-control-label" for="example-inline-radio2">{$notifications_send_to_phone}</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline mb-5">
                                    <input class="custom-control-input" type="radio" name="example-inline-radios" id="example-inline-radio3" value="option3" disabled>
                                    <label class="custom-control-label" for="example-inline-radio3">{$notifications_send_to_telegram}</label>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column mb-2">
                            <label class="css-control css-control-sm css-control-primary css-switch m-0 my-1">
                                <input type="checkbox" class="css-control-input">
                                <span class="css-control-indicator"></span> {$notifications_on_signin_from_new_devices}
                            </label>
                        </div>
                        <button class="btn btn-sm btn-alt-primary">
                            <i class="fa fa-bell mr-1"></i>
                            {$notifications_save_button}
                        </button>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
</div>
<!-- Page JS Plugins -->
{if $.site.config.cabinet.tab_active_log}
    {$.site._SEO->addTegHTML('head', 'datatablesb4_css', 'link', ['rel'=>'stylesheet', 'href'=> $.const.VIEWPATH~'/panel/assets/js/plugins/datatables/dataTables.bootstrap4.css'])}
    {$.site._SEO->addTegHTML('footer', 'dataTables', 'script', ['src'=> $.const.VIEWPATH~'/panel/assets/js/plugins/datatables/jquery.dataTables.min.js'])}
    {$.site._SEO->addTegHTML('footer', 'datatablesb4', 'script', ['src'=> $.const.VIEWPATH~'/panel/assets/js/plugins/datatables/dataTables.bootstrap4.min.js'])}

<script>
    class BeTableDatatables {
        static exDataTable() {
            jQuery.extend( jQuery.fn.dataTable.ext.classes, {
                sWrapper: "dataTables_wrapper dt-bootstrap4"
            });
        }
        static initDataTableFull() {
            jQuery('.log-list-table').dataTable({
                columnDefs: [ { orderable: false, targets: [ 1 ] } ],
                pageLength: 8,
                lengthMenu: [[5, 8, 15, 20], [5, 8, 15, 20]],
                autoWidth: false
            });
        }
        static init() {
            this.exDataTable();
            this.initDataTableFull();

        }
    }
    document.addEventListener("DOMContentLoaded", function(event) {
        jQuery(() => { BeTableDatatables.init(); });
    });

</script>
{/if}