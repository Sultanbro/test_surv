// // Import vendor jQuery plugin example
// import '~/app/libs/mmenu/dist/mmenu.js'
import 'jquery';
import 'slick-carousel';


document.addEventListener('DOMContentLoaded', () => {

	// Custom JS

        const animItems = document.querySelectorAll('._anim')

        if (animItems.length > 0) {
            window.addEventListener('scroll', animOnScroll)

            function animOnScroll() {
                for (let index = 0; index < animItems.length; index++) {
                    const animItem = animItems[index]
                    const animItemHeight = animItem.offsetHeight
                    const animItemOffset = offset(animItem)
                    const animStart = 5

                    let animItemPoint = window.innerHeight - animItemHeight / animStart

                    if (animItemHeight > window.innerHeight) {
                        animItemPoint = window.innerHeight - window.innerHeight / animStart
                    }

                    if ((pageYOffset > animItemOffset - animItemPoint) && pageYOffset < (animItemOffset + animItemHeight)) {
                        animItem.classList.add('_active');
                        if(animItem.classList.contains('schedule')){
                            animItem.dispatchEvent(new Event('change'));
                            $('.schedule__table tbody td:first-child').addClass('anim')
                        }
                    } else {
                        if (!animItem.classList.contains('_anim-no-hide'))
                            animItem.classList.remove('_active')
                    }
                }
            }

            function offset(el) {
                const rect = el.getBoundingClientRect(),
                    scrollTop = window.pageYOffset || document.documentElement.scrollTop
                return (rect.top + scrollTop)

            }


            if(window.innerWidth>900){
                animOnScroll()
            } else{
                $('._anim').addClass('_active');
            }

        }




    // Profit slider

    $('.profit__inner').slick({
        infinite: true,
        speed: 400,
        fade: true,
        adaptiveHeight: true,
    });
    $('.profit__prev').on('click', function(e) {
        e.preventDefault();
        $('.profit__inner').slick('slickPrev');
    });
    $('.profit__next').on('click', function(e) {
        e.preventDefault();
        $('.profit__inner').slick('slickNext');
    });




    // Profit info slider

    $('.courses__content__wrapper').slick({
        variableWidth: true,
    })

    $('.courses__item').each(function(){
        let coursePercent = $(this).find('.courses__percent span').text();
        $(this).find('.courses__line').css('width',coursePercent);
    })

    $('.courses__button').click(function(e){
        e.preventDefault();
        $('.profit__info').addClass('active')
        $('.courses__content').addClass('hidden')
    })
    $('.profit__info-back, .profit__info-back-mobile').click(function(e){
        e.preventDefault();
        $('.profit__info').removeClass('active')
        $('.courses__content').removeClass('hidden')
    })




    $('.profit__info__wrapper').slick({
        variableWidth: false,
        infinite:false,
        slidesToScroll:2,
        slidesToShow: 10,
        responsive: [
            {
                breakpoint: 2140,
                settings: {
                    variableWidth: false,
                    infinite:false,
                    swipeToSlide: false,
                    slidesToScroll: 2,
                    slidesToShow: 9,

                }
            },
            {
                breakpoint: 2000,
                settings: {
                    variableWidth: false,
                    infinite:false,
                    swipeToSlide: false,
                    slidesToScroll: 2,
                    slidesToShow: 6,

                }
            },
            {
                breakpoint: 1800,
                settings: {
                    variableWidth: false,
                    infinite:false,
                    swipeToSlide: false,
                    slidesToScroll: 2,
                    slidesToShow: 5,

                }
            },
            {
                breakpoint: 1600,
                settings: {
                    infinite: true,
                    variableWidth: true,
                    swipeToSlide: true,
                    slidesToShow: 1,

                }
            },

            {
                breakpoint: 780,
                settings: {
                    variableWidth: false,
                    infinite:false,
                    slidesToShow: 2,
                    slidesToScroll: 2,
                    swipeToSlide: false,
                }
            },
            {
                breakpoint: 500,
                settings: {
                    variableWidth: false,
                    infinite:false,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    swipeToSlide: false,
                }
            },

        ]

    });


    if(window.innerWidth>900){
        $('.header__left,.header__right').removeClass('closed');

    }




    // Table's background filling %

    $('.colored').each(function(e,i){
       $(this).css('background',`linear-gradient(to right, ${$(this).attr('data-color')} ${$(this).find('.value').text()}, #fff ${$(this).find('.value').text()})`)

    })

    // Star settings

        $('.trainee__content').each(function(){

            let starLength = $(this).find('.trainee__star-value span').text();
            console.log(starLength);
            if(starLength<=10 && starLength>=0){
                for(let i=0; i<starLength;i++){
                    $(this).find('.trainee__star-wrapper .star__item')[i].classList.add('done');
                }
            }
        })

    // Smooth Scroll

    $(".intro__button").on("click", function (event) {
        event.preventDefault();
        var id  = $(this).attr('href'),
            top = $(id).offset().top;
        $('body,html').animate({scrollTop: top-150}, 1000);
    });

    // Header burger

    $('.intro__top-burger, .burger-left').on('click', function(event){
        event.preventDefault();
        $('.burger-left').toggleClass('opened')
        $('.header__left').toggleClass('closed')

    })
    $('.header__right-arrow, .header__arrow, .burger-right').on('click', function(event){
        event.preventDefault();
        $('.header__right').toggleClass('closed')
        $('.burger-right').toggleClass('opened')
        $('.header__arrow').toggleClass('show')

    })




    const closePopup = $('.js-close-popup');
    const modalOverlay = $('.overlay');
    const popupAward = $('.popup.award');
    const popupKPI = $('.popup.kpi');
    const popupBalance = $('.popup.balance');
    const popupKASPI = $('.popup.kaspi');
    const popupNominations = $('.popup.nominations');
    const popupNotifications = $('.popup.notifications');
    const popupCheck = $('.popup.check');
    let profileFlag;


    //Hiding all Popups
    $('.popup').fadeOut(0);

    // Project popup
    $('.header__nav-link a[href$="award"], .stat__item[data-item$="award"]').on('click', function(e){
        e.preventDefault();
        openPopup(popupAward, modalOverlay);
    })
    $('.header__nav-link a[href$="kpi"], .stat__item[data-item$="kpi"]').on('click', function(e){
        e.preventDefault();
        openPopup(popupKPI, modalOverlay);
    })
    $('.header__nav-link a[href$="balance"], .stat__item[data-item$="balance"]').on('click', function(e){
        e.preventDefault();
        openPopup(popupBalance, modalOverlay);
    })
    $('.header__nav-link a[href$="kaspi"], .stat__item[data-item$="kaspi"]').on('click', function(e){
        e.preventDefault();
        openPopup(popupKASPI, modalOverlay);
    })
    $('.header__nav-link a[href$="nominations"], .stat__item[data-item$="nominations"]').on('click', function(e){
        e.preventDefault();
        openPopup(popupNominations, modalOverlay);
    })
    $('.header__right-icon.bell').on('click', function(e){
        e.preventDefault();
        openPopup(popupNotifications, modalOverlay);
    })
    $('.header__right-icon.check').on('click', function(e){
        e.preventDefault();
        openPopup(popupCheck, modalOverlay);
    })



    function openPopup(popup,overlay){
        overlay.addClass('active');
        $(document.body).addClass('modal-open')
        $('html').addClass('modal-open')
        popup.fadeIn(500);
        closeModalOverlay(popup,overlay)
        closeModal(closePopup,overlay,popup)
    }


    function closeModalOverlay(formPopup,object){
        $(document).mousedown(function (e) {
            if (e.target !== formPopup[0] && formPopup.has(e.target).length === 0) {
                object.removeClass('active')
                $(document.body).removeClass('modal-open')
                $('html').removeClass('modal-open')
                formPopup.fadeOut(500);
                $(document).off('mousedown')
            }

        })
    }

    function closeModal(close,object,popup) {
        close.click(function (e){
            e.preventDefault();
            object.removeClass('active')
            $('.popup').removeClass('extra');
            $(document.body).removeClass('modal-open')
            $('html').removeClass('modal-open')
            popup.fadeOut(500);
        })
    }
    const circle = document.querySelector('.progress-ring__circle');
        const radius = circle.r.baseVal.value;
        const circumference = 2 * Math.PI * radius;

        circle.style.strokeDasharray = `${circumference} ${circumference}`;
        circle.style.strokeDashoffset = circumference;

        const circleParent = circle.closest('.profile__progressbar');
        const circleNum = circleParent.querySelector('.profile__progressbar-number span');
        const circleNumValue = circleParent.querySelector('.profile__progressbar-number span').textContent;
        animateValue(circleNum, 0, 0, 1);






       $(document).on('mouseup',function(e){
           if($('.header__profile').hasClass('opened') && !(e.target.closest('.header__profile'))){
               $('.header__profile').removeClass('opened');
               $('.header__nav-link a[href$="profile"]').parent().removeClass('opened');
               $(document.body).removeClass('modal-open')
               $('html').removeClass('modal-open')
           }
           if(!($('.header__left').hasClass('closed')) && !(e.target.closest('.header__profile')) && !(e.target.closest('.header__right')) && !(e.target.closest('.header__left')) && window.innerWidth < 900){
               $('.header__left').addClass('closed');
               $('.burger-left').removeClass('opened');
           }
           if(!($('.header__right').hasClass('closed')) && !(e.target.closest('.header__left')) && !(e.target.closest('.header__right')) && window.innerWidth < 900){
               $('.header__right').addClass('closed');
               $('.header__arrow').addClass('show')
               $('.burger-right').removeClass('opened');
           }

       })


        function animateValue(obj, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const progressNum = Math.floor(progress * (end - start) + start);
                obj.innerText = progressNum;
                setProgress(progressNum);

                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }
        function setProgress(percent){
            const offset = circumference - percent / 100 * circumference;
            circle.style.strokeDashoffset = offset;
        }

        if(window.innerWidth>1370){
            if(!profileFlag){
                animateValue(circleNum, 0, circleNumValue, 1500);
                profileFlag = true;
            }
        }

        $('.header__nav-link a[href$="profile"]').on('click', function(e){
            e.preventDefault();
            $('.header__profile').addClass('opened')
            $(this).parent().addClass('opened')
            if(!profileFlag){
                animateValue(circleNum, 0, circleNumValue, 1500);
                profileFlag = true;
            }
            if(window.innerWidth<440){
                $(document.body).addClass('modal-open')
                $('html').addClass('modal-open')
            }
        })
    let switchTabs = (tab) => {
        // get all tab list items and remove the is-active class
        let tabs = tab.closest('.tabs').querySelectorAll('.tab__item');
        tabs.forEach(t => {t.classList.remove("is-active");});
        // set is-active on the passed tab element
        tab.classList.add("is-active");
        // get all content elements and remove is-active
        let contents = tab.closest('.tabs').querySelectorAll(".tab__content .tab__content-item");
        contents.forEach(t => {t.classList.remove("is-active");});
        // get the data-index data attribute from the selected tab (passed here)
        let activeTabIndex = tab.getAttribute("data-index");
        // get the corresonding tab content via the data-content attribute
        let activeContent = tab.closest('.tabs').querySelector(`[data-content='${activeTabIndex}']`);
        // set is-active on the corresponding tab content
        activeContent.classList.add("is-active");
    }

    $('.mobile-select').change(function(){

        let selectTab = ($(this).closest('.tabs').find(`.tab__item[data-index=${$(this).val()}]`));
        switchTabs(selectTab[0])
    })


    $(window).on('resize',function(e){

    })
    function OpacityStats(){
            let     MAXBALANCE = 40000,
                    MAXKPI = 40000,
                    MAXBONUSES = 30,
                    MAXKVARTAL = 40000,
                    MAXNOMINATIONS = 30,
                    maxArray = [MAXBALANCE, MAXKPI,MAXBONUSES, MAXKVARTAL, MAXNOMINATIONS];
                    let values = $('.stat__value span');
            for(let i=0;i<values.length;i++){

                let value = values[i].textContent.replace(/,/g,"")
                if(value !== '0'){
                    $(values[i]).closest('.stat__value').addClass('active')
                }




                $({numberValue: 0}).animate({numberValue: value/maxArray[i] * 100}, {
                    duration: 4000,
                    easing: "swing",
                    step: function(val) {
                        $(values[i]).closest('.stat__item').find('.front').css('height',val+'%')
                    },
                    complete: function(){
                        $(values[i]).closest('.stat__item').find('.front').css('height',value/maxArray[i] * 100 + '%')
                    }
                });
            }

    }
    OpacityStats();

        $('.stat__value').each(function(){
            let n = $(this).children('span').text().replace(/\D/g,'');
            let element = $(this);

            function separateNumber(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                //разделитель можно задать тут вторым аргументом для метода replace. Сейчас, как видно, пробел
            }

            $({numberValue: 0}).animate({numberValue: n}, {
                duration: 4000,
                easing: "swing",
                step: function(val) {
                    element.children('span').text(separateNumber(Math.round(val)));
                },
                complete: function(){
                    element.children('span').text(separateNumber(Math.round(n)));
                }
            });
        })






    $('.point-close').click(function(){
        $(this).closest('.profile-box').find('.profile-slick').slideToggle(700, 'swing')
        $(this).closest('.profile-box').find('.profile__title').toggleClass('_slicked')
    })
    let buttonText = $('.profile__button p').text();
    $('.profile__button').click(function(e){
        e.preventDefault();

        $(this).toggleClass('active');
        if($(this).hasClass('active')){
            $(this).find('p').text('Завершить рабочий день');
        } else{
            $(this).find('p').text(buttonText);
        }
    })
})
