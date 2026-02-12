$(function () {
    var burgerBtn = $('.gw-burger');
    var menuWrp = $('.gw-nav');
    var menuLink = $('.gw-nav__link');

    burgerBtn.click(function () {
        burgerBtn.toggleClass('gw-burger_active');
        menuWrp.toggleClass('gw-nav_active');
    });
});


$(function () {

    $('.rating-btn').on("click", function () {
        if (!$(this).is('.rating-btn_active')) {
            const __this = $(this);
            const ratingBtns = __this.closest('[data-rating-btns]');
            ratingBtns.find('.rating-btn_active').removeClass('rating-btn_active');
            ratingBtns.siblings('[data-rating]').find('[data-rating-tab]').removeClass('rating__tab_active');
            ratingBtns.siblings('[data-rating]').find('[data-rating-tab]').eq(__this.index()).addClass('rating__tab_active');
            __this.addClass('rating-btn_active');

        }
    });

    $('.rating-sub-btn').on("click", function () {
        if (!$(this).is('.rating-btn-sub_active')) {
            const __this = $(this);
            const ratingBtns = __this.closest('[data-sub-rating-btns]');
            ratingBtns.find('.rating-sub-btn_active ').removeClass('rating-sub-btn_active ');
            ratingBtns.siblings('[data-sub-rating]').find('[data-sub-rating-tab]').removeClass('rating__tab_active');
            ratingBtns.siblings('[data-sub-rating]').find('[data-sub-rating-tab]:eq(' + __this.index() + ')').addClass('rating__tab_active');
            __this.addClass('rating-sub-btn_active ');

        }
    });


    $('.rating-btn:nth-child(1)').trigger('click');
    $('.rating-sub-btn:nth-child(1)').trigger('click');

    $('body').on('click', '[data-open-window]', function () {
        $.fancybox.getInstance('close');
        let target = $(this).attr('data-open-window');
        $.fancybox.defaults.animationDuration = 300;
        $.fancybox.defaults.autoFocus = false;
        $.fancybox.defaults.touch = false;
        $.fancybox.open({
            src: '#' + target,
            type: 'inline',
            selectable: true,
            opts: {
                smallBtn: false,
                toolbar: false,
                touch: false,
                afterShow: function (instance, current) {

                }
            }
        });
    })
});




$(document).on('submit', 'form', function () {
    if (!verifyСheckboxInForm($(this))) return !1
});

function verifyСheckboxInForm(a) {
    let b = a.find('[data-checkbox-required]:not(:checked)');
    return !(0 < b.length) || (colorBoxOpen(b.eq(0).attr('data-checkbox-required')), !1)
}


function colorBoxOpen(txt, ttl) {
    let title = ttl || 'Warning',
        text = txt || 'Что-то пошло не так';
    $.fancybox.open('<div class="warning warning_animated"><div><div class="warning__title">' + title + '</div>' + text + '</div></div>');
}

function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min)) + min;
}

function roundingNum(x, n) {
    return parseFloat(Number.parseFloat(x).toFixed(n || 2));
}