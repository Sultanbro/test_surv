<template>
<div class="intro__stats _anim _anim-no-hide block">
    <div class="stat__item" data-item="balance">
        <div class="stat__image">
            <div class="back">

                <img src="images/dist/image-1.svg" alt="stat image" class="stat__front">
            </div>
            <div class="front">
                <img src="images/dist/image-1.svg" alt="stat image" class="stat__front">
            </div>
        </div>
        <div class="stat__about">
            <div class="stat__name">Баланс оклада</div>
            <div class="stat__value"><span>35,841</span> KZT</div>
        </div>
    </div>
    <div class="stat__item" data-item="kpi">
        <div class="stat__image">
            <div class="back">
                <img src="images/dist/image-2.svg" alt="stat image" class="stat__back">
            </div>
            <div class="front">
                <img src="images/dist/image-2.svg" alt="stat image" class="stat__front">
            </div>
        </div>
        <div class="stat__about">
            <div class="stat__name">KPI</div>
            <div class="stat__value"><span>30000</span> KZT</div>
        </div>
    </div>
    <div class="stat__item" data-item="kaspi">
        <div class="stat__image">
            <div class="back">
                <img src="images/dist/image-3.svg" alt="stat image" class="stat__back">
            </div>
            <div class="front">
                <img src="images/dist/image-3.svg" alt="stat image" class="stat__front">
            </div>
        </div>
        <div class="stat__about">
            <div class="stat__name">Бонусы</div>
            <div class="stat__value"><span>30</span> KZT</div>
        </div>
    </div>
    <div class="stat__item" data-item="award">
        <div class="stat__image">
            <div class="back">
                <img src="images/dist/image-4.svg" alt="stat image" class="stat__back">
            </div>
            <div class="front">
                <img src="images/dist/image-4.svg" alt="stat image" class="stat__front">
            </div>
        </div>
        <div class="stat__about">
            <div class="stat__name">Квартальный</div>
            <div class="stat__value"><span>25,932</span> KZT</div>
        </div>
    </div>
    <div class="stat__item" data-item="nominations">
        <div class="stat__image">
            <div class="back">
                <img src="images/dist/image-5.svg" alt="stat image" class="stat__back">
            </div>
            <div class="front">
                <img src="images/dist/image-5.svg" alt="stat image" class="stat__front">
            </div>
        </div>
        <div class="stat__about">
            <div class="stat__name">Номинации</div>
            <div class="stat__value"><span>20</span></div>
        </div>
    </div>

    <popup-kpi></popup-kpi>
</div>
</template>

<script>
export default {
    name: "ProfileSalary", 
    props: {},
    data: function () {
        return {
            user_earnings: [], 
            has_qp: false
        };
    },
    methods: {
        fetch() {
            let loader = this.$loading.show();

            axios.post('/profile/salary/get', {
                month: this.$moment(this.month, 'MMMM').format('M')
            }).then(response => {
                this.user_earnings = response.data.user_earnings
                this.has_qp = response.data.has_qp
                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        },
    }
};
</script>