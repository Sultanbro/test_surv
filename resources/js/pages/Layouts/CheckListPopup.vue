<template>
<div class="popup__con">
    <div class="check__title popup__content-title">
        Каримов Адиль
    </div>
    <div class="check__items">
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
    </div>
    <a href="#" class="check__button">
        Выполнить
    </a>
</div>
</template>

<script>
export default {
    name: 'CheckListPopup', 
    props: {},
    data: function () {
        return {
            data: []
        };
    },
    created(){
        this.fetchData()
    },
    methods: {
        fetchData() {
            let loader = this.$loading.show();
            
            axios.post('/checklist', {})
                .then((response) => {
                    this.data = response.data
                    loader.hide();
                });
        },
    }
};
</script>

<style lang="scss">
.check__title{
    padding-bottom: 2rem;
    border-bottom: 1px solid #E6E6E6;

    font-size: 2.7rem;
    font-weight: 600;
    text-transform: uppercase;
}

.check__item{
    display: flex;
    min-height: 4rem;
    height: 4rem;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;

    .check__url{    
        display: block;
        flex: 0 0 46rem;
        width: 100%;
        max-width: 100%;
        height: 100%;
        padding-left: 1.5rem;
        border: 1px solid #E7EAEA;
        border-radius: 1.5rem;
        font-size: 1.3rem;
        font-family: "Inter",sans-serif;
    }
}

.check__checkbox{
    display: flex;
    gap: 1.5rem;
    align-items: center;
    width: 100%;
    max-width: 12.5rem;
    height: 100%;
    margin-right: 8rem;
    color: #5b6166;
    font-size: 1.5rem;

    p{
        white-space: nowrap;
    }
}

.check__button{
    display: flex;
    justify-content: center;
    align-items: center;
    background: #AEBEE0;
    color: #fff;
    min-height: 5rem;
    width: 100%;
    max-width: 26.5rem;
    font-size: 1.4rem;
    font-weight: 600;
}

@media(max-width:650px){
    .check__item {
        display: block;
        height: auto;
    }
    .check__url{
        height: 4rem;
    }
}
</style>