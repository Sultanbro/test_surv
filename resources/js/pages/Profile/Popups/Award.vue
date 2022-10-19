<template>
<div class="popup award js-popup">
    <div class="popup__content">
    <div class="popup-header">
        <a class="popup-close js-close-popup" href="#" ><img src="images/dist/popup-close.svg" alt="Close icon" ></a>
        <div class="popup__header-content">
            <div class="popup__title">
                Квартальная премия
            </div>
            <div class="popup__subtitle">
                Дополнительное поле с описанием функционала данного окна
            </div>
        </div>
    </div>
        <div class="popup__body">
            <div class="popup__filter">
                <select class="select-css">
                    <option>Май</option>
                    <option>Апрель</option>
                    <option>Март</option>
                    <option>Февраль</option>
                    <option>Январь</option>
                </select>
            </div>
            <div class="popup__award">
                <div class="award__title popup__content-title">
                    За период с 01.03.2020 до 31.06.2022
                </div>
                <table class="award__table">
                    <tr>
                        <td class="blue">Сумма</td>
                        <td>400.000</td>
                    </tr>
                    <tr>
                        <td class="blue">Комментарии</td>
                        <td>Нужно выполнить условие: 1.хх 2.хх</td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</div>
</template>

<script>
export default {
    name: "PopupAward", 
    props: {},
    data: function () {
        return {
            fields: [], 
        };
    },
    methods: {
        
    }
};
</script>