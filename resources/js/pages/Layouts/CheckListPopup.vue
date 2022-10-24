<template>
<div class="popup__con">
    <div class="check__title popup__content-title">
        Каримов Адиль
    </div>
    <div class="check__item">
        <label class="check__checkbox">
            <input type="checkbox">
            <p>Сделать 1</p>
        </label>
        <input class="check__url" type="text" placeholder="URL">
    </div>
    <div class="check__item">
        <label class="check__checkbox">
            <input type="checkbox">
            <p>Сделать 2</p>
        </label>
        <input class="check__url" type="text" placeholder="URL">
    </div>
    <div class="check__item">
        <label class="check__checkbox">
            <input type="checkbox">
            <p>Сделать 3</p>
        </label>
        <input class="check__url" type="text" placeholder="URL">
    </div>
    <div class="check__item">
        <label class="check__checkbox">
            <input type="checkbox">
            <p>Сделать 4</p>
        </label>
        <input class="check__url" type="text" placeholder="URL">
    </div>
    <a href="#" class="check__button">
        Выполнить
    </a>
</div>
</template>

<script>
export default {
    name: "CheckListPopup", 
    props: {},
    data: function () {
        return {
            data: []
        };
    },
    created(){
        // this.fetchData()
    },
    methods: {
        fetchData() {
            let loader = this.$loading.show();
            
            axios.post("/checklist", {})
                .then((response) => {
                    this.data = response.data
                    loader.hide();
                });
        },
    }
};
</script>