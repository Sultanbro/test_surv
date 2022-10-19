<template>
<div class="popup kpi js-popup">
    <div class="popup__content">
        <div class="popup-header">
            <a class="popup-close js-close-popup" href="#" ><img src="images/dist/popup-close.svg" alt="Close icon" ></a>
            <div class="popup__header-content">
                <div class="popup__title">
                    KPI
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
            <div class="kpi__content">
                <div class="kpi__kaspi">
                    <div class="kpi__title popup__content-title">
                        Kaspi
                    </div>
                    <div class="kpi__kaspi-wrapper">
                        <div class="kpi__kaspi-left">
                            <table>
                                <tr>
                                    <td class="blue">Выполнение KPI от 80-99%</td>
                                    <td>20.000</td>
                                </tr>
                                <tr>
                                    <td class="blue">Выполнение KPI на 100%</td>
                                    <td >40.000</td>
                                </tr>
                            </table>
                        </div>
                        <div class="kpi__kaspi-right">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Нижний порог отсечения премии, %</th>
                                        <th>Верхний порог отсечения премии, %</th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td>80</td>
                                    <td >100</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                </div>

                <div class="kpi__activities">
                    <div class="kpi__title popup__content-title">
                        Активности KPI
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Наименование активности</th>
                                <th>Целевое значение
                                    за мес</th>
                                <th>Выполнено</th>
                                <th>Удельный
                                    вес %</th>
                                <th>Сумма премии при
                                    выполнении плана, тг</th>
                                <th>% выполнения</th>
                                <th>Сумма премии за
                                    % выполнения</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Минуты разговора</td>
                                <td>7020</td>
                                <td>0</td>
                                <td>70</td>
                                <td>28.000</td>
                                <td>0.00</td>
                                <td>0.0</td>
                            </tr>
                            <tr>
                                <td>Оценка диалога</td>
                                <td>100%</td>
                                <td>0</td>
                                <td>30</td>
                                <td>12.000</td>
                                <td>0.00</td>
                                <td>0.0</td>
                            </tr>
                            <tr>
                                <td class="none"></td>
                                <td class="none"></td>
                                <td class="none"></td>
                                <td class="none"></td>
                                <td class="none"></td>
                                <td class="none"></td>
                                <td><b>0</b></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="kpi__activities-tip">
                        * сумма премии за выполнение показателей начнет меняться после достижения 80% от целевого значения на месяц
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
</template>

<script>
export default {
    name: "PopupKpi", 
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