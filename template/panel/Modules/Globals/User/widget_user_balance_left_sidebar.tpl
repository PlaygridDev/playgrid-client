<div class="d-block mb-3" id="sidebarBalance">
    <div class="py-2 px-10 bg-gd-aqua rounded">
        <div class="d-flex justify-content-between">
            <div class="d-flex flex-wrap" style="gap: 0.6rem">
                <div class="d-flex flex-row align-items-center">
                    <div class="mr-1">
                        <img src="{$.site.dir_panel}/assets/media/icon/main_balance.png" style="width: 20px; height: 20px;">
                    </div>
                    <div class="font-size-sm text-white d-flex align-items-center">
                        <span class="text-light font-w700">
                            {ignore}
                            {{ balance.main }}
                            {/ignore}
                        </span>
                    </div>
                </div>
                {if $.site.config.bonus_balance.status && $bonusBalanceSettings.display_mode == 'separate'}
                <div class="d-flex flex-row align-items-center" title="{$bonusBalanceSettings.balance_name}">
                    <div class="mr-1">
                        <img src="{$.site.dir_panel}/assets/media/icon/bonus_balance.png" style="width: 20px; height: 20px;">
                    </div>
                    <div class="font-size-sm text-white d-flex align-items-center">
                        <span class="text-light font-w700">
                            {ignore}
                            {{ balance.bonus }}
                            {/ignore}
                        </span>
                    </div>
                </div>
                {/if}
            </div>
            <a href="{$.php.set_url('/panel/donations')}" class="btn btn-sm btn-alt-success" id="topupBalanceBtn">
                <i class="si si-wallet"></i>
            </a>
        </div>
    </div>
</div>

<script>

    window.sidebarBalance = new Vue({

        el: '#sidebarBalance',

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
                sidebarBalance.setBalance(event.detail?.main_balance, event.detail?.bonus_balance);
            });
        },

    });

</script>