$(document).ready(function () {
    const countdown = function(_config) {
        const tarDate = $(_config.target).data('date').split('-');
        const day = parseInt(tarDate[0]);
        const month = parseInt(tarDate[1]);
        const year = parseInt(tarDate[2]);
        let tarTime = $(_config.target).data('time');
        let tarhour, tarmin;

        if (tarTime != null) {
            tarTime = tarTime.split(':');
            tarhour = parseInt(tarTime[0]);
            tarmin = parseInt(tarTime[1]);
        }

        // Set the date we're counting down to
        const countDownDate = new Date(year, month-1, day, tarhour, tarmin, 0, 0).getTime();

        $(_config.target+' .day .word').html(_config.dayWord);
        $(_config.target+' .hour .word').html(_config.hourWord);
        $(_config.target+' .min .word').html(_config.minWord);
        $(_config.target+' .sec .word').html(_config.secWord);

        let updateTime = () => {
            // Get todays date and time
            const now = new Date().getTime();

            // Find the distance between now an the count down date
            const distance = countDownDate - now;

            // Time calculations for days, hours, minutes and seconds
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // requestAnimationFrame(updateTime);

            $(_config.target+' .day .num').innerHTML = addZero(days);
            $(_config.target+' .hour .num').innerHTML = addZero(hours);
            $(_config.target+' .min .num').innerHTML = addZero(minutes);
            $(_config.target+' .sec .num').innerHTML = addZero(seconds);

            $(_config.target+' .day .num').html(addZero(days));
            $(_config.target+' .hour .num').html(addZero(hours));
            $(_config.target+' .min .num').html(addZero(minutes));
            $(_config.target+' .sec .num').html(addZero(seconds));

            if (distance < 0) {
                $('.action-closed-wrapper').css('display', 'flex');
                $('.countdown').hide();
                clearInterval(timerId);
            }
        }
        let timerId = setInterval(function() {
            updateTime();
        }, 1000);
    }

    const addZero = (x) => (x < 10 && x >= 0) ? "0"+x : x;

    new countdown({
        target: '.countdown',
        dayWord: ' дней',
        hourWord: ' часов',
        minWord: ' минут',
        secWord: ' секунд'
    });
});