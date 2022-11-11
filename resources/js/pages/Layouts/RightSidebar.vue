<template>
<div class="header__right closedd">
    <div class="header__right-nav">
        <a href="#" class="header__right-icon" v-b-popover.hover.left.html="'Вопросы и ответы - Этот функционал в разработке'">
            <img src="/images/dist/header-right-1.svg" alt="nav icon" class="header__icon-img">
        </a>
        <a href="#" class="header__right-icon bell red" @click="$emit('pop', 'notifications')">
            <img src="/images/dist/header-right-2.svg" alt="nav icon" class="header__icon-img">
        </a>
        <a href="#" class="header__right-icon loop" v-b-popover.hover.left.html="'Поиск по чату - Этот функционал в разработке'">
            <img src="/images/dist/header-right-3.svg" alt="nav icon" class="header__icon-img">
        </a>
        <a href="#" class="header__right-icon" v-b-popover.hover.left.html="'Тикеты - Этот функционал в разработке'">
            <img src="/images/dist/header-right-5.svg" alt="nav icon" class="header__icon-img">
        </a>
        <a href="#" class="header__right-icon check" @click="$emit('pop', 'checklist')">
            <img src="/images/dist/header-right-6.svg" alt="nav icon" class="header__icon-img">
        </a>
    </div>

    <div class="header__right-messages">
        <!-- <chat-app></chat-app> -->
        <!-- <a href="#" class="header__message-item new">
            <img src="/images/dist/header-right-avatar-1.png" alt="header avatar">
        </a>
        <a href="#" class="header__message-item new">
            <img src="/images/dist/header-right-avatar-2.png" alt="header avatar">
        </a>
        <a href="#" class="header__message-item new">
            <img src="/images/dist/header-right-avatar-3.png" alt="header avatar">
        </a>
        <a href="#" class="header__message-item read">
            <img src="/images/dist/header-right-avatar-4.png" alt="header avatar">
        </a>
        <a href="#" class="header__message-item read">
            <img src="/images/dist/header-right-avatar-5.png" alt="header avatar">
        </a>
        <a href="#" class="header__message-item read">
            <img src="/images/dist/header-right-avatar-6.png" alt="header avatar">
        </a> -->
    </div>

    <div class="header__right-arrow">
        <a href="#"><img src="/images/dist/header-arrow.svg" alt="arrow icon"></a>
    </div>


   

</div>
</template>

<script>
export default {
    name: "RightSidebar", 
    props: {},
    data: function () {
        return {

        };
    },
    methods: {

    }
};
</script>