// // Import vendor jQuery plugin example
// import '~/app/libs/mmenu/dist/mmenu.js'

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
                            VJQuery('.schedule__table tbody td:first-child').addClass('anim')
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
                VJQuery('._anim').addClass('_active');
            }

        }




    // Profit slider

    


    // Profit info slider
   


    // VJQuery('.courses__item').each(function(){
    //     let coursePercent = VJQuery(this).find('.courses__percent span').text();
    //     VJQuery(this).find('.courses__line').css('width',coursePercent);
    // })

    // VJQuery('.courses__button').click(function(e){
    //     e.preventDefault();
    //     VJQuery('.profit__info').addClass('active')
    //     VJQuery('.courses__content').addClass('hidden')
    // })
    // VJQuery('.profit__info-back, .profit__info-back-mobile').click(function(e){
    //     e.preventDefault();
    //     VJQuery('.profit__info').removeClass('active')
    //     VJQuery('.courses__content').removeClass('hidden')
    // })




    


    if(window.innerWidth>900){
        VJQuery('.header__left,.header__right').removeClass('closed');

    }




    // Table's background filling %

    VJQuery('.colored').each(function(e,i){
       VJQuery(this).css('background',`linear-gradient(to right, ${VJQuery(this).attr('data-color')} ${VJQuery(this).find('.value').text()}, #fff ${VJQuery(this).find('.value').text()})`)

    })

    // Star settings

        VJQuery('.trainee__content').each(function(){

            let starLength = VJQuery(this).find('.trainee__star-value span').text();
            console.log(starLength);
            if(starLength<=10 && starLength>=0){
                for(let i=0; i<starLength;i++){
                    VJQuery(this).find('.trainee__star-wrapper .star__item')[i].classList.add('done');
                }
            }
        })

    // Smooth Scroll

    VJQuery(".intro__button").on("click", function (event) {
        event.preventDefault();
        var id  = VJQuery(this).attr('href'),
            top = VJQuery(id).offset().top;
        VJQuery('body,html').animate({scrollTop: top-150}, 1000);
    });

    // Header burger

    VJQuery('.intro__top-burger, .burger-left').on('click', function(event){
        event.preventDefault();
        VJQuery('.burger-left').toggleClass('opened')
        VJQuery('.header__left').toggleClass('closed')

    })
    VJQuery('.header__right-arrow, .header__arrow, .burger-right').on('click', function(event){
        event.preventDefault();
        VJQuery('.header__right').toggleClass('closed')
        VJQuery('.burger-right').toggleClass('opened')
        VJQuery('.header__arrow').toggleClass('show')

    })




    const closePopup = VJQuery('.js-close-popup');
    const modalOverlay = VJQuery('.overlay');
    const popupAward = VJQuery('.popup.award');
    const popupKPI = VJQuery('.popup.kpi');
    const popupBalance = VJQuery('.popup.balance');
    const popupKASPI = VJQuery('.popup.kaspi');
    const popupNominations = VJQuery('.popup.nominations');
    const popupNotifications = VJQuery('.popup.notifications');
    const popupCheck = VJQuery('.popup.check');
    let profileFlag;


    //Hiding all Popups
    VJQuery('.popup').fadeOut(0);

    // Project popup
    VJQuery('.header__nav-link a[href$="award"], .stat__item[data-item$="award"]').on('click', function(e){
        e.preventDefault();
        openPopup(popupAward, modalOverlay);
    })
    VJQuery('.header__nav-link a[href$="kpi"], .stat__item[data-item$="kpi"]').on('click', function(e){
        e.preventDefault();
        openPopup(popupKPI, modalOverlay);
    })
    VJQuery('.header__nav-link a[href$="balance"], .stat__item[data-item$="balance"]').on('click', function(e){
        e.preventDefault();
        openPopup(popupBalance, modalOverlay);
    })
    VJQuery('.header__nav-link a[href$="kaspi"], .stat__item[data-item$="kaspi"]').on('click', function(e){
        e.preventDefault();
        openPopup(popupKASPI, modalOverlay);
    })
    VJQuery('.header__nav-link a[href$="nominations"], .stat__item[data-item$="nominations"]').on('click', function(e){
        e.preventDefault();
        openPopup(popupNominations, modalOverlay);
    })
    VJQuery('.header__right-icon.bell').on('click', function(e){
        e.preventDefault();
        openPopup(popupNotifications, modalOverlay);
    })
    VJQuery('.header__right-icon.check').on('click', function(e){
        e.preventDefault();
        openPopup(popupCheck, modalOverlay);
    })



    function openPopup(popup,overlay){
        overlay.addClass('active');
        VJQuery(document.body).addClass('modal-open')
        VJQuery('html').addClass('modal-open')
        popup.fadeIn(500);
        closeModalOverlay(popup,overlay)
        closeModal(closePopup,overlay,popup)
    }


    function closeModalOverlay(formPopup,object){
        VJQuery(document).mousedown(function (e) {
            if (e.target !== formPopup[0] && formPopup.has(e.target).length === 0) {
                object.removeClass('active')
                VJQuery(document.body).removeClass('modal-open')
                VJQuery('html').removeClass('modal-open')
                formPopup.fadeOut(500);
                VJQuery(document).off('mousedown')
            }

        })
    }

    function closeModal(close,object,popup) {
        close.click(function (e){
            e.preventDefault();
            object.removeClass('active')
            VJQuery('.popup').removeClass('extra');
            VJQuery(document.body).removeClass('modal-open')
            VJQuery('html').removeClass('modal-open')
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






       VJQuery(document).on('mouseup',function(e){
           if(VJQuery('.header__profile').hasClass('opened') && !(e.target.closest('.header__profile'))){
               VJQuery('.header__profile').removeClass('opened');
               VJQuery('.header__nav-link a[href$="profile"]').parent().removeClass('opened');
               VJQuery(document.body).removeClass('modal-open')
               VJQuery('html').removeClass('modal-open')
           }
           if(!(VJQuery('.header__left').hasClass('closed')) && !(e.target.closest('.header__profile')) && !(e.target.closest('.header__right')) && !(e.target.closest('.header__left')) && window.innerWidth < 900){
               VJQuery('.header__left').addClass('closed');
               VJQuery('.burger-left').removeClass('opened');
           }
           if(!(VJQuery('.header__right').hasClass('closed')) && !(e.target.closest('.header__left')) && !(e.target.closest('.header__right')) && window.innerWidth < 900){
               VJQuery('.header__right').addClass('closed');
               VJQuery('.header__arrow').addClass('show')
               VJQuery('.burger-right').removeClass('opened');
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

        VJQuery('.header__nav-link a[href$="profile"]').on('click', function(e){
            e.preventDefault();
            VJQuery('.header__profile').addClass('opened')
            VJQuery(this).parent().addClass('opened')
            if(!profileFlag){
                animateValue(circleNum, 0, circleNumValue, 1500);
                profileFlag = true;
            }
            if(window.innerWidth<440){
                VJQuery(document.body).addClass('modal-open')
                VJQuery('html').addClass('modal-open')
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

    VJQuery('.mobile-select').change(function(){

        let selectTab = (VJQuery(this).closest('.tabs').find(`.tab__item[data-index=${VJQuery(this).val()}]`));
        switchTabs(selectTab[0])
    })


    VJQuery(window).on('resize',function(e){

    })
 
    VJQuery('.point-close').click(function(){
        VJQuery(this).closest('.profile-box').find('.profile-slick').slideToggle(700, 'swing')
        VJQuery(this).closest('.profile-box').find('.profile__title').toggleClass('_slicked')
    })
    let buttonText = VJQuery('.profile__button p').text();
    VJQuery('.profile__button').click(function(e){
        e.preventDefault();

        VJQuery(this).toggleClass('active');
        if(VJQuery(this).hasClass('active')){
            VJQuery(this).find('p').text('Завершить рабочий день');
        } else{
            VJQuery(this).find('p').text(buttonText);
        }
    })
})
