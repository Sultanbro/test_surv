<template>
<div class="popup balance js-popup">
    <div class="popup__content">
        <div class="popup-header">
            <a class="popup-close js-close-popup" href="#" ><img src="images/dist/popup-close.svg" alt="Close icon" ></a>
            <div class="popup__header-content">
                <div class="popup__title">
                    Баланс оклада
                </div>
                <div class="popup__subtitle">
                    Дополнительное поле с описанием функционала данного окна
                </div>
            </div>
        </div>
        <div class="popup__body">
            <div class="popup__filter">
                <div class="popup__filter-title">
                    Ваши начисления за период работы
                </div>
                <select class="select-css">
                    <option>Май</option>
                    <option>Апрель</option>
                    <option>Март</option>
                    <option>Февраль</option>
                    <option>Январь</option>
                </select>
            </div>
            <div class="balance__content custom-scroll">
                <table class="balance__table">
                    <thead>
                        <tr>
                            <th>Дни</th>
                            <th>К выдаче</th>
                            <th></th>
                            <th></th>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
                            <th>7</th>
                            <th>8</th>
                            <th>9</th>
                            <th>10</th>
                            <th>11</th>
                            <th>12</th>
                            <th>13</th>
                            <th>14</th>
                            <th>15</th>
                            <th>16</th>
                            <th>17</th>
                            <th>18</th>
                            <th>19</th>
                            <th>20</th>
                            <th>21</th>
                            <th>22</th>
                            <th>23</th>
                            <th>24</th>
                            <th>25</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Время прихода</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="yellow"></td>
                        <td>09:25</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="yellow"></td>
                        <td class="pink"></td>
                        <td class="pink"></td>
                        <td></td>
                        <td></td>
                        <td>15:45</td>
                        <td></td>
                        <td class="yellow"></td>
                        <td class="pink"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="yellow"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Начисления</td>
                        <td>15841</td>
                        <td>0</td>
                        <td>12000</td>
                        <td class="pink"></td>
                        <td class="pink">21496</td>
                        <td></td>
                        <td class="green-pink"></td>
                        <td></td>
                        <td></td>
                        <td class="yellow"></td>
                        <td class="pink"></td>
                        <td class="pink"></td>
                        <td></td>
                        <td></td>
                        <td class="pink">6345</td>
                        <td></td>
                        <td class="yellow"></td>
                        <td class="pink"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="yellow"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Отработанные часы</td>
                        <td>9.8</td>
                        <td>0</td>
                        <td>12000</td>
                        <td class="yellow"></td>
                        <td>7.57</td>
                        <td class="blue"></td>
                        <td></td>
                        <td class="orange"></td>
                        <td></td>
                        <td class="yellow"></td>
                        <td class="pink"></td>
                        <td class="pink"></td>
                        <td></td>
                        <td></td>
                        <td>2.23</td>
                        <td></td>
                        <td class="yellow"></td>
                        <td class="pink"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="yellow"></td>
                        <td class="yellow"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            <!--						<div class="balance__title">-->
            <!--							ИСТОРИЯ-->
            <!--						</div>-->
            <!--						<div class="balance__inner">-->
            <!--							<div class="balance__item">-->
            <!--								<div class="balance__item-title">Начислено</div>-->
            <!--								<div class="balance__item-value">0</div>-->
            <!--							</div>-->
            <!--							<div class="balance__item">-->
            <!--								<div class="balance__item-title">Депремирование</div>-->
            <!--								<div class="balance__item-value">Нет штрафов</div>-->
            <!--							</div>-->
            <!--							<div class="balance__item">-->
            <!--								<div class="balance__item-title">Бонусы</div>-->
            <!--								<div class="balance__item-value">Нет бонусов </div>-->
            <!--							</div>-->
            <!--							<div class="balance__item">-->
            <!--								<div class="balance__item-title">Авансы</div>-->
            <!--								<div class="balance__item-value">Нет авансов</div>-->
            <!--							</div>-->
            <!--						</div>-->
            </div>

        </div>
    </div>
</div>
</template>

<script>
export default {
    name: "PopupBalance", 
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