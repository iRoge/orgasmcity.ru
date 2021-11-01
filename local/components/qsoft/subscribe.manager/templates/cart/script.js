$(document).ready(function () {
    $(function() {
        $('.js-check-input').on('click' , function(){
            let that = $(this);
            let $curentInputState = that
                .find("input")
                .prop("checked");

            let $curentInput = that
                .find("input");

            if ($curentInputState == false) {
                $curentInput.prop("checked", true);
            } else {
                $curentInput.prop("checked", false);
            }

        });
    });
});