<div class="block rounded">
    <div class="block-header block-header-default">
        <h3 class="block-title">{$th_title}</h3>
        <div class="block-options">
            <button type="button" class="btn-block-option submit-btn" {$.php.btn_ajax("Modules\Plugins\GiftCode\GiftCode", "ajax_refresh_history")}>
                <i class="fa fa-refresh mr-5"></i>{$btn_refresh}
            </button>
        </div>
    </div>
    <div class="block-content">
        <table class="table table-striped table-vcenter">
            <thead>
            <tr>
                <th class="text-center" style="width: 100px;">#</th>
                <th>{$th_code}</th>
                <th style="width: 15%;">{$th_sum}</th>
                <th class="d-none d-md-table-cell" style="width: 20%;">{$th_date_create}</th>
                <th style="width: 15%;">{$th_status}</th>
            </tr>
            </thead>
            <tbody>
            {if count($.site.session->session.user_data.gift_code.history) > 0}
                {foreach $.site.session->session.user_data.gift_code.history as $code}
                    <tr>
                        <td class="text-center">
                            {$code.id}
                        </td>
                        <td class="font-w600">{$code.cod}</td>
                        <td >{$code.sum}</td>
                        <td class="d-none d-sm-table-cell">{$code.date_create}</td>
                        <td>
                            {if $code.status == 1}
                                <span class="badge badge-secondary">{$status_not_activated}</span>
                            {else}
                                <span class="badge badge-success">{$status_activated}</span>
                            {/if}
                        </td>
                    </tr>

                {/foreach}
            {else}
            <tr>
                <td class="text-center" colspan="5">
                    {$th_gift_empty}
                </td>
            </tr>
            {/if}
            </tbody>
        </table>
    </div>
</div>
