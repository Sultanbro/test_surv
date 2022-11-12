<template>
<div class="container header__container">
    <profile-sidebar></profile-sidebar>

    <div class="header">
        <left-sidebar></left-sidebar>
        <right-sidebar @pop="pop"></right-sidebar>

        <div class="header__arrow">
            <a href="#"><img src="/images/dist/header-arrow.svg" alt="arrow icon"></a>
        </div>
    </div>

    <!-- popup -->
    <popup v-if="popNotifications"
        title="Уведомления"
        desc="Дополнительное поле с описанием функционала данного окна"
        :open="popNotifications" 
        @close="popNotifications=false"
        width="75%">
        <popup-notifications></popup-notifications>
    </popup>

    <!-- popup -->
    <popup v-if="popChecklist"
        title="Чек лист"
        desc="Дополнительное поле с описанием функционала данного окна"
        :open="popChecklist" 
        @close="popChecklist=false"
        width="75%">
        <popup-checklist></popup-checklist>
    </popup>

    <!-- popup -->
    <popup v-if="popFAQ"
        title="Вопросы и ответы"
        desc="Дополнительное поле с описанием функционала данного окна"
        :open="popFAQ" 
        @close="popFAQ=false"
        width="75%">
        <popup-faq></popup-faq>
    </popup>

    <!-- popup -->
    <popup v-if="popSearch"
        title="Поиск"
        desc="Дополнительное поле с описанием функционала данного окна"
        :open="popSearch" 
        @close="popSearch=false"
        width="75%">
        <popup-search></popup-search>
    </popup>

    <!-- popup -->
    <popup v-if="popMail"
        title="Почта или что это?"
        desc="Дополнительное поле с описанием функционала данного окна"
        :open="popMail" 
        @close="popMail=false"
        width="75%">
        <popup-mail></popup-mail>
    </popup>

</div>
</template>

<script>
export default {

  name: 'Sidebars',
  props: {},

  data() {
    return {
      popNotifications: false,
      popChecklist: false,
      popMessenger: false,
      popFAQ: false,
      popSearch: false,
      popMail: false,
    }
  },
  methods: {
    /**
     * pop window
     */
    pop(window) {
      if(window == 'faq') this.popFAQ = true;
      if(window == 'notifications') this.popNotifications = true;
      if(window == 'search') this.popSearch = true;
      if(window == 'messenger') this.popMessenger = true;
      if(window == 'mail') this.popMail = true;
      if(window == 'checklist') this.popChecklist = true;
    }
  }

}
</script>

<style lang="scss" scoped>

</style>
