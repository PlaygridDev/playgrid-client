<div class="form-group row justify-content-center">
    <div class="col-lg-10">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-user"></i></span>
            </div>
            <select id="account_name_in_game" name="account_name" class="form-control" size="1">
                <option value="0">{$lang_select_account}</option>
                {foreach $.site.session->session.user_data.account as $login => $info}
                    {if $info['status'] == 2}
                        {continue}
                    {/if}
                    <option value="{$login}">{$login}</option>
                {/foreach}
            </select>
        </div>
    </div>
</div>

<div class="form-group row justify-content-center">
    <div class="col-lg-10">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-user-plus"></i></span>
            </div>
            {if $.site.config.cabinet.signin_type.phone? OR $.site.config.cabinet.registration_type.phone?}
                <input type="text" name="email" class="form-control" placeholder="{$l2_game_account_transfer_receiver_contact}">
            {else}
                <input type="text" name="email" class="form-control" placeholder="{$l2_game_account_transfer_receiver_email}">
            {/if}
        </div>
    </div>
</div>

{if !$.site.session->get2FAStatus()}
    <div class="form-group row justify-content-center">
        <div class="col-lg-10">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                </div>
                <input type="password" name="password" class="form-control" placeholder="{$l2_game_account_transfer_password}">
            </div>
        </div>
    </div>
{/if}

<div class="form-group row justify-content-center">
    <div class="col-lg-10">
        <p class="alert alert-warning font-w600 text-center mb-0" style="border-radius: 3px;">
            {$l2_game_account_transfer_notice}
        </p>
    </div>
</div>
