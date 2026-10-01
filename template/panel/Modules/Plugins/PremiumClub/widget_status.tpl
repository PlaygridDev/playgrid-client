{$.site._SEO->addTegHTML('head', 'premium_club_css', 'link', ['rel'=>'stylesheet', 'href'=> $.const.VIEWPATH~'/panel/assets/css/premium_club.css?v=' ~ filemtime($.const.ROOT_DIR~$.const.VIEWPATH~'/panel/assets/css/premium_club.css')])}
<div class="block block-rounded invisible" data-toggle="appear">
    <div class="block-content block-content-full animated fadeIn" id="premiumClubStatus">
        <div class="d-flex justify-content-center align-items-center flex-wrap py-10">
            <a v-for="(status, index) in statuses" :key="status.group_id" href="javascript:void(0)" class="premium-club-icon mx-10 my-5" :class="{ 'premium-club-icon-active': index === selected }" :title="status.group_name" @click="selected = index">
                <img v-if="status.icon_url" :src="status.icon_url" :alt="status.group_name">
                <span v-else class="badge badge-secondary" v-text="status.group_name"></span>
            </a>
        </div>
        <transition name="premium-club-fade" mode="out-in" @enter="relayout">
            <div :key="selected">
                <div class="text-center">
                    <span class="font-size-h4 font-w600" v-text="current.group_name"></span>
                </div>
                <div class="mt-10">
                    <div class="d-flex justify-content-between font-size-sm">
                        <span class="font-w600" v-text="current.level_name"></span>
                        <span class="text-muted text-right">
                            <template v-if="current.goal === 'max'">{$lang_progress} 100%</template>
                            <template v-else-if="current.goal === 'transition'">{$lang_until_transition} [[ current.next_group_name ]]: [[ current.remaining ]] {$payment_system.short_name_valute}</template>
                            <template v-else>{$lang_remaining}: [[ current.remaining ]] {$payment_system.short_name_valute}</template>
                        </span>
                    </div>
                    <div class="progress mt-5" style="height: 8px;">
                        <div class="progress-bar" :class="current.goal === 'max' ? 'bg-success' : 'bg-primary'" role="progressbar" :style="{ width: current.percent + '%' }" :aria-valuenow="current.percent" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div v-if="hasDetails" class="text-center mt-10">
                    <a href="javascript:void(0)" class="font-size-sm link-effect" @click="showDetails = !showDetails">
                        <span v-if="showDetails">{$lang_details_hide}</span>
                        <span v-else>{$lang_details}</span>
                    </a>
                </div>
                <transition name="premium-club-fade" @enter="relayout" @after-leave="relayout">
                    <div v-if="hasDetails && showDetails" class="font-size-sm mt-10">
                        <div v-if="current.bonus.ranges.length">
                            <div class="font-w600 mb-5">{$lang_bonus}</div>
                            <table class="table table-sm table-borderless mb-5">
                                <tr v-for="(range, index) in current.bonus.ranges" :key="index">
                                    <td>[[ rangeLabel(range) ]]</td>
                                    <td class="text-right font-w600">+[[ range.percent ]]%</td>
                                </tr>
                            </table>
                            <div v-if="current.bonus.target === 'bonus'" class="text-muted mb-5">
                                {$lang_bonus_balance}<template v-if="current.bonus.lifetime > 0">, [[ current.bonus.lifetime ]] {$lang_hours}</template>
                            </div>
                        </div>
                        <div v-if="discounts.length">
                            <div class="font-w600 mb-5">{$lang_discount}</div>
                            <table class="table table-sm table-borderless mb-0">
                                <tr v-for="discount in discounts" :key="discount.type">
                                    <td>[[ discount.name ]]</td>
                                    <td class="text-right font-w600">-[[ discount.percent ]]%</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </transition>
            </div>
        </transition>
    </div>
</div>
<script>
    new Vue({
        el: '#premiumClubStatus',
        delimiters: ['[[', ']]'],
        data: function () {
            return {
                statuses: {$.php.json_encode($statuses)},
                types: {$.php.json_encode($types)},
                currency: {$.php.json_encode($payment_system.short_name_valute)},
                from: {$.php.json_encode($lang_from)},
                selected: 0,
                showDetails: false
            };
        },
        computed: {
            current: function () {
                return this.statuses[this.selected];
            },
            discounts: function () {
                var list = [];
                for (var type in this.types) {
                    if (this.current.discounts && this.current.discounts[type] > 0) {
                        list.push({ type: type, name: this.types[type], percent: this.current.discounts[type] });
                    }
                }
                return list;
            },
            hasDetails: function () {
                return this.current.bonus.ranges.length > 0 || this.discounts.length > 0;
            }
        },
        methods: {
            relayout: function () {
                window.masonry_div.masonry('layout');
            },
            rangeLabel: function (range) {
                if (range.open) {
                    return this.from + ' ' + range.start + ' ' + this.currency;
                }
                return range.start + ' – ' + range.end + ' ' + this.currency;
            }
        }
    });
</script>
