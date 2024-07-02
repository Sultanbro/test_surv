<div class="bonus_notification pyro" id="bonus_notification">

    <div class="before"></div>
    <div class="after"></div>

    <div class="inner">
        <h5 class="text-center ae">Сегодня бонусы получили</h5>

        <div class="mb-3">
            {!! $bonus_notification->message !!}
        </div>
        <button class="btn btn-primary rounded m-auto btn-sm" id="goToNoti2">
            <span class="text">Перейти к уведомлениям</span>
        </button>
    </div>      
</div>



<script>
$(document).ready(function() {

    $('#goToNoti2').click(function () {
        $('#bonus_notification').hide();
        $('#toggle_panel').trigger('click');
        window.scrollTo(0,0)
    });

    setTimeout(function () {
        $('.bonus_notification').removeClass('pyro');
    }, 3000);
});


</script>
<style>
.m-auto {
    margin: 0 auto;
    display: block;
    cursor: pointer;
}
.aet {
    color: #042a40c9;
    font-size: 18px;
    border-bottom: 1px dashed #e1e1e1;
    padding-bottom: 9px;
}
.bonus_notification {
    position: fixed;
    width: 100vw;
    top: 0;
    z-index: 9999;
    height: 100vh;
    padding: 15px 30px 15px;
    background: rgba(0,0,0,0.7);
}
.bonus_notification .inner {
    margin: 0 auto;
    border-radius: 5px;
    padding: 20px 20px 20px;
    background: #fff;
    width: 560px;
    max-width: 100%;
    overflow: auto;
    max-height: 100%;
}


.pyro > .before, .pyro > .after {
  position: absolute;
  width: 5px;
  height: 5px;
  border-radius: 50%;
  box-shadow: 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff, 0 0 #fff;
  -moz-animation: 1s bang ease-out infinite backwards, 1s gravity ease-in infinite backwards, 5s position linear infinite backwards;
  -webkit-animation: 1s bang ease-out infinite backwards, 1s gravity ease-in infinite backwards, 5s position linear infinite backwards;
  -o-animation: 1s bang ease-out infinite backwards, 1s gravity ease-in infinite backwards, 5s position linear infinite backwards;
  -ms-animation: 1s bang ease-out infinite backwards, 1s gravity ease-in infinite backwards, 5s position linear infinite backwards;
  animation: 1s bang ease-out infinite backwards, 1s gravity ease-in infinite backwards, 5s position linear infinite backwards;
}

.pyro > .after {
  -moz-animation-delay: 1.25s, 1.25s, 1.25s;
  -webkit-animation-delay: 1.25s, 1.25s, 1.25s;
  -o-animation-delay: 1.25s, 1.25s, 1.25s;
  -ms-animation-delay: 1.25s, 1.25s, 1.25s;
  animation-delay: 1.25s, 1.25s, 1.25s;
  -moz-animation-duration: 1.25s, 1.25s, 6.25s;
  -webkit-animation-duration: 1.25s, 1.25s, 6.25s;
  -o-animation-duration: 1.25s, 1.25s, 6.25s;
  -ms-animation-duration: 1.25s, 1.25s, 6.25s;
  animation-duration: 1.25s, 1.25s, 6.25s;
}

@-webkit-keyframes bang {
  to {
    box-shadow: -214px -363.6666666667px #0022ff, 86px -351.6666666667px #11ff00, -56px -396.6666666667px #ffea00, 173px -134.6666666667px #007bff, -249px -59.6666666667px #1eff00, 178px -107.6666666667px #00ff99, -175px -374.6666666667px #8800ff, -152px -357.6666666667px #48ff00, 97px -32.6666666667px #ff00cc, -170px -218.6666666667px #ff00ea, -63px -323.6666666667px #00ffb3, -195px -131.6666666667px #ff2200, -69px 83.3333333333px #0062ff, -216px -266.6666666667px #2600ff, -176px -214.6666666667px #00bbff, 166px -41.6666666667px #00ffe1, -183px -347.6666666667px #c800ff, 143px -330.6666666667px #00ff59, -50px 52.3333333333px #8400ff, 203px 10.3333333333px #ff00ee, 137px -404.6666666667px #ff0900, 107px -322.6666666667px #00d5ff, -70px -79.6666666667px #91ff00, 191px -79.6666666667px #ff1e00, -102px -25.6666666667px #4000ff, 243px -373.6666666667px #ff0073, 125px 0.3333333333px #e100ff, 120px -326.6666666667px #ffea00, -174px -392.6666666667px #ff00f2, 122px -265.6666666667px #ff9500, 203px -279.6666666667px #ff00e1, 54px 12.3333333333px #ff00e1, 138px -193.6666666667px #00ddff, 230px -44.6666666667px #aeff00, 231px 3.3333333333px #00ff1e, 135px -0.6666666667px #0059ff, 42px -360.6666666667px #2b00ff, 147px 24.3333333333px #ff00cc, -207px -61.6666666667px #37ff00, -196px -18.6666666667px #51ff00, -41px -48.6666666667px #ff001e, -17px -391.6666666667px #ffd900, -50px -257.6666666667px #00ff62, -58px -174.6666666667px #00ffbf, -26px -288.6666666667px #ff000d, -202px 3.3333333333px #9100ff, 178px -394.6666666667px #88ff00, -41px 5.3333333333px #ff1500, -229px -4.6666666667px #ff6f00, 176px -151.6666666667px #1e00ff, -171px -172.6666666667px #4d00ff;
  }
}
@-moz-keyframes bang {
  to {
    box-shadow: -214px -363.6666666667px #0022ff, 86px -351.6666666667px #11ff00, -56px -396.6666666667px #ffea00, 173px -134.6666666667px #007bff, -249px -59.6666666667px #1eff00, 178px -107.6666666667px #00ff99, -175px -374.6666666667px #8800ff, -152px -357.6666666667px #48ff00, 97px -32.6666666667px #ff00cc, -170px -218.6666666667px #ff00ea, -63px -323.6666666667px #00ffb3, -195px -131.6666666667px #ff2200, -69px 83.3333333333px #0062ff, -216px -266.6666666667px #2600ff, -176px -214.6666666667px #00bbff, 166px -41.6666666667px #00ffe1, -183px -347.6666666667px #c800ff, 143px -330.6666666667px #00ff59, -50px 52.3333333333px #8400ff, 203px 10.3333333333px #ff00ee, 137px -404.6666666667px #ff0900, 107px -322.6666666667px #00d5ff, -70px -79.6666666667px #91ff00, 191px -79.6666666667px #ff1e00, -102px -25.6666666667px #4000ff, 243px -373.6666666667px #ff0073, 125px 0.3333333333px #e100ff, 120px -326.6666666667px #ffea00, -174px -392.6666666667px #ff00f2, 122px -265.6666666667px #ff9500, 203px -279.6666666667px #ff00e1, 54px 12.3333333333px #ff00e1, 138px -193.6666666667px #00ddff, 230px -44.6666666667px #aeff00, 231px 3.3333333333px #00ff1e, 135px -0.6666666667px #0059ff, 42px -360.6666666667px #2b00ff, 147px 24.3333333333px #ff00cc, -207px -61.6666666667px #37ff00, -196px -18.6666666667px #51ff00, -41px -48.6666666667px #ff001e, -17px -391.6666666667px #ffd900, -50px -257.6666666667px #00ff62, -58px -174.6666666667px #00ffbf, -26px -288.6666666667px #ff000d, -202px 3.3333333333px #9100ff, 178px -394.6666666667px #88ff00, -41px 5.3333333333px #ff1500, -229px -4.6666666667px #ff6f00, 176px -151.6666666667px #1e00ff, -171px -172.6666666667px #4d00ff;
  }
}
@-o-keyframes bang {
  to {
    box-shadow: -214px -363.6666666667px #0022ff, 86px -351.6666666667px #11ff00, -56px -396.6666666667px #ffea00, 173px -134.6666666667px #007bff, -249px -59.6666666667px #1eff00, 178px -107.6666666667px #00ff99, -175px -374.6666666667px #8800ff, -152px -357.6666666667px #48ff00, 97px -32.6666666667px #ff00cc, -170px -218.6666666667px #ff00ea, -63px -323.6666666667px #00ffb3, -195px -131.6666666667px #ff2200, -69px 83.3333333333px #0062ff, -216px -266.6666666667px #2600ff, -176px -214.6666666667px #00bbff, 166px -41.6666666667px #00ffe1, -183px -347.6666666667px #c800ff, 143px -330.6666666667px #00ff59, -50px 52.3333333333px #8400ff, 203px 10.3333333333px #ff00ee, 137px -404.6666666667px #ff0900, 107px -322.6666666667px #00d5ff, -70px -79.6666666667px #91ff00, 191px -79.6666666667px #ff1e00, -102px -25.6666666667px #4000ff, 243px -373.6666666667px #ff0073, 125px 0.3333333333px #e100ff, 120px -326.6666666667px #ffea00, -174px -392.6666666667px #ff00f2, 122px -265.6666666667px #ff9500, 203px -279.6666666667px #ff00e1, 54px 12.3333333333px #ff00e1, 138px -193.6666666667px #00ddff, 230px -44.6666666667px #aeff00, 231px 3.3333333333px #00ff1e, 135px -0.6666666667px #0059ff, 42px -360.6666666667px #2b00ff, 147px 24.3333333333px #ff00cc, -207px -61.6666666667px #37ff00, -196px -18.6666666667px #51ff00, -41px -48.6666666667px #ff001e, -17px -391.6666666667px #ffd900, -50px -257.6666666667px #00ff62, -58px -174.6666666667px #00ffbf, -26px -288.6666666667px #ff000d, -202px 3.3333333333px #9100ff, 178px -394.6666666667px #88ff00, -41px 5.3333333333px #ff1500, -229px -4.6666666667px #ff6f00, 176px -151.6666666667px #1e00ff, -171px -172.6666666667px #4d00ff;
  }
}
@-ms-keyframes bang {
  to {
    box-shadow: -214px -363.6666666667px #0022ff, 86px -351.6666666667px #11ff00, -56px -396.6666666667px #ffea00, 173px -134.6666666667px #007bff, -249px -59.6666666667px #1eff00, 178px -107.6666666667px #00ff99, -175px -374.6666666667px #8800ff, -152px -357.6666666667px #48ff00, 97px -32.6666666667px #ff00cc, -170px -218.6666666667px #ff00ea, -63px -323.6666666667px #00ffb3, -195px -131.6666666667px #ff2200, -69px 83.3333333333px #0062ff, -216px -266.6666666667px #2600ff, -176px -214.6666666667px #00bbff, 166px -41.6666666667px #00ffe1, -183px -347.6666666667px #c800ff, 143px -330.6666666667px #00ff59, -50px 52.3333333333px #8400ff, 203px 10.3333333333px #ff00ee, 137px -404.6666666667px #ff0900, 107px -322.6666666667px #00d5ff, -70px -79.6666666667px #91ff00, 191px -79.6666666667px #ff1e00, -102px -25.6666666667px #4000ff, 243px -373.6666666667px #ff0073, 125px 0.3333333333px #e100ff, 120px -326.6666666667px #ffea00, -174px -392.6666666667px #ff00f2, 122px -265.6666666667px #ff9500, 203px -279.6666666667px #ff00e1, 54px 12.3333333333px #ff00e1, 138px -193.6666666667px #00ddff, 230px -44.6666666667px #aeff00, 231px 3.3333333333px #00ff1e, 135px -0.6666666667px #0059ff, 42px -360.6666666667px #2b00ff, 147px 24.3333333333px #ff00cc, -207px -61.6666666667px #37ff00, -196px -18.6666666667px #51ff00, -41px -48.6666666667px #ff001e, -17px -391.6666666667px #ffd900, -50px -257.6666666667px #00ff62, -58px -174.6666666667px #00ffbf, -26px -288.6666666667px #ff000d, -202px 3.3333333333px #9100ff, 178px -394.6666666667px #88ff00, -41px 5.3333333333px #ff1500, -229px -4.6666666667px #ff6f00, 176px -151.6666666667px #1e00ff, -171px -172.6666666667px #4d00ff;
  }
}
@keyframes bang {
  to {
    box-shadow: -214px -363.6666666667px #0022ff, 86px -351.6666666667px #11ff00, -56px -396.6666666667px #ffea00, 173px -134.6666666667px #007bff, -249px -59.6666666667px #1eff00, 178px -107.6666666667px #00ff99, -175px -374.6666666667px #8800ff, -152px -357.6666666667px #48ff00, 97px -32.6666666667px #ff00cc, -170px -218.6666666667px #ff00ea, -63px -323.6666666667px #00ffb3, -195px -131.6666666667px #ff2200, -69px 83.3333333333px #0062ff, -216px -266.6666666667px #2600ff, -176px -214.6666666667px #00bbff, 166px -41.6666666667px #00ffe1, -183px -347.6666666667px #c800ff, 143px -330.6666666667px #00ff59, -50px 52.3333333333px #8400ff, 203px 10.3333333333px #ff00ee, 137px -404.6666666667px #ff0900, 107px -322.6666666667px #00d5ff, -70px -79.6666666667px #91ff00, 191px -79.6666666667px #ff1e00, -102px -25.6666666667px #4000ff, 243px -373.6666666667px #ff0073, 125px 0.3333333333px #e100ff, 120px -326.6666666667px #ffea00, -174px -392.6666666667px #ff00f2, 122px -265.6666666667px #ff9500, 203px -279.6666666667px #ff00e1, 54px 12.3333333333px #ff00e1, 138px -193.6666666667px #00ddff, 230px -44.6666666667px #aeff00, 231px 3.3333333333px #00ff1e, 135px -0.6666666667px #0059ff, 42px -360.6666666667px #2b00ff, 147px 24.3333333333px #ff00cc, -207px -61.6666666667px #37ff00, -196px -18.6666666667px #51ff00, -41px -48.6666666667px #ff001e, -17px -391.6666666667px #ffd900, -50px -257.6666666667px #00ff62, -58px -174.6666666667px #00ffbf, -26px -288.6666666667px #ff000d, -202px 3.3333333333px #9100ff, 178px -394.6666666667px #88ff00, -41px 5.3333333333px #ff1500, -229px -4.6666666667px #ff6f00, 176px -151.6666666667px #1e00ff, -171px -172.6666666667px #4d00ff;
  }
}
@-webkit-keyframes gravity {
  to {
    transform: translateY(200px);
    -moz-transform: translateY(200px);
    -webkit-transform: translateY(200px);
    -o-transform: translateY(200px);
    -ms-transform: translateY(200px);
    opacity: 0;
  }
}
@-moz-keyframes gravity {
  to {
    transform: translateY(200px);
    -moz-transform: translateY(200px);
    -webkit-transform: translateY(200px);
    -o-transform: translateY(200px);
    -ms-transform: translateY(200px);
    opacity: 0;
  }
}
@-o-keyframes gravity {
  to {
    transform: translateY(200px);
    -moz-transform: translateY(200px);
    -webkit-transform: translateY(200px);
    -o-transform: translateY(200px);
    -ms-transform: translateY(200px);
    opacity: 0;
  }
}
@-ms-keyframes gravity {
  to {
    transform: translateY(200px);
    -moz-transform: translateY(200px);
    -webkit-transform: translateY(200px);
    -o-transform: translateY(200px);
    -ms-transform: translateY(200px);
    opacity: 0;
  }
}
@keyframes gravity {
  to {
    transform: translateY(200px);
    -moz-transform: translateY(200px);
    -webkit-transform: translateY(200px);
    -o-transform: translateY(200px);
    -ms-transform: translateY(200px);
    opacity: 0;
  }
}
@-webkit-keyframes position {
  0%, 19.9% {
    margin-top: 10%;
    margin-left: 40%;
  }
  20%, 39.9% {
    margin-top: 40%;
    margin-left: 30%;
  }
  40%, 59.9% {
    margin-top: 20%;
    margin-left: 70%;
  }
  60%, 79.9% {
    margin-top: 30%;
    margin-left: 20%;
  }
  80%, 99.9% {
    margin-top: 30%;
    margin-left: 80%;
  }
}
@-moz-keyframes position {
  0%, 19.9% {
    margin-top: 10%;
    margin-left: 40%;
  }
  20%, 39.9% {
    margin-top: 40%;
    margin-left: 30%;
  }
  40%, 59.9% {
    margin-top: 20%;
    margin-left: 70%;
  }
  60%, 79.9% {
    margin-top: 30%;
    margin-left: 20%;
  }
  80%, 99.9% {
    margin-top: 30%;
    margin-left: 80%;
  }
}
@-o-keyframes position {
  0%, 19.9% {
    margin-top: 10%;
    margin-left: 40%;
  }
  20%, 39.9% {
    margin-top: 40%;
    margin-left: 30%;
  }
  40%, 59.9% {
    margin-top: 20%;
    margin-left: 70%;
  }
  60%, 79.9% {
    margin-top: 30%;
    margin-left: 20%;
  }
  80%, 99.9% {
    margin-top: 30%;
    margin-left: 80%;
  }
}
@-ms-keyframes position {
  0%, 19.9% {
    margin-top: 10%;
    margin-left: 40%;
  }
  20%, 39.9% {
    margin-top: 40%;
    margin-left: 30%;
  }
  40%, 59.9% {
    margin-top: 20%;
    margin-left: 70%;
  }
  60%, 79.9% {
    margin-top: 30%;
    margin-left: 20%;
  }
  80%, 99.9% {
    margin-top: 30%;
    margin-left: 80%;
  }
}
@keyframes position {
  0%, 19.9% {
    margin-top: 10%;
    margin-left: 40%;
  }
  20%, 39.9% {
    margin-top: 40%;
    margin-left: 30%;
  }
  40%, 59.9% {
    margin-top: 20%;
    margin-left: 70%;
  }
  60%, 79.9% {
    margin-top: 30%;
    margin-left: 20%;
  }
  80%, 99.9% {
    margin-top: 30%;
    margin-left: 80%;
  }
}
</style>