{foreach $servers as $sid => $info}
    <div class="sidebar__server server">
        <div class="server__progress" style="background: url({$.site.dir_site}/images/server/server-{if $info.online.server? AND $info.online.server == 1}on{else}off{/if}.png) no-repeat;">
            <div class="server__rate">x{$info.rate}</div>
            <div class="server__chorn">{$info.chronicle}</div>
        </div>
        <div class="server__content">
            <div class="server__title">
                {$info.name} <span class="color-orange">x{$info.rate}</span>
            </div>
            <div class="server__online">
                {if $info.online.server? AND $info.online.server == 1}
                {$L_SERVER_ONLINE}
                <span class="server__online-marker">
                    {$info.online.online_multiple}
                </span>
                {else}
                <span>
                     {$L_SERVER_OFFLINE}
                </span>
                {/if}
            </div>
            <a href="{$info.link}" class="server__link-info">{$L_SERVER_ABOUT_READ}</a>
        </div>
    </div>
{/foreach}

