{if $.site.session->session.master_account.status == 1 && $.site.session->session.master_account.email_valid == 0}
<div class="form-group row">
    <label class="col-md-4 col-form-label text-right border-right">
        {$verification_title}
    </label>
    <div class="col-md-6 pt-5">
        <p class="mb-2">
            {$verification_description}
        </p>
        <a href="javascript:void(0);" class="btn btn-alt-primary submit-btn" {$.php.btn_ajax("Modules\Globals\User\User", "email_verification_popup", [])}>
            <i class="fa fa-envelope-o mr-2"></i>
            {$verification_button}
        </a>
    </div>
</div>
{/if}