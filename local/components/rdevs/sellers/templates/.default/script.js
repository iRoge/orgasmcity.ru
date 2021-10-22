$('.sellerPlane ').click(function () {
    if ($(this).hasClass('opened')) {
        $('.sideSellerPanel').animate({
            marginLeft: -300
        }, 500, 'linear', $(this).removeClass('opened'));
    } else {
        $('.sideSellerPanel').animate({
            marginLeft: 0
        }, 500, 'linear', $(this).addClass('opened'));
    }
});

$('.storeSellerSelect').on('change', function () {

    $(".storeSellerSelect option:selected").each(function () {
        let currentSellerElem = $(this);
        let currentSeller = currentSellerElem.text();
        let array = currentSeller.split(' ');
        let result = array[0];

        if (array[1]) {
            result += ' ' + array[1][0] + '.';
        }

        if (array[2]) {
            result += array[2][0] + '.';
        }

        $('.currentStoreSeller').text(result);
        $('.currentSeller').text(currentSeller);

        setCookie('storeSeller_id', currentSellerElem.val());
    });
})

function checkStoreSellerCookie() {
    if (getCookie('seller_id')) {
        if (!getCookie('storeSeller_id')) {
            let sellerPlaneElem = $('.sideSellerPanel');

            if (!sellerPlaneElem.hasClass('opened')) {
                sellerPlaneElem.animate({
                    marginLeft: 0
                }, 500, 'linear', $(this).addClass('opened'));
            }

            $('.storeSellerSelect').css('border', 'solid 3px red');

            return false;
        }
        return true;
    }
}