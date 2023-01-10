<template>
<div
    id="trainee"
    class="trainee block _anim _anim-no-hide content"
    :class="{
        'hidden': Object.keys(data).length == 0,
        'v-loading': loading
    }"
>
    <div class="trainee__title title mt-5">
        Оценка стажеров
    </div>
    <div class="trainee__subtitle subtitle">
        Подробная информация об оценке стажерами вашего обучения
    </div>
    <div class="trainee__table">
        <div class="tabs custom-scroll">
            <div class="trainee__tabs tabs__wrapper">

                <div
                    v-for="(key, index) in Object.keys(data)"
                    :key="index"
                    class="trainee__tab tab__item"
                    :class="{'is-active': index == 0}"
                    onclick="switchTabs(this)"
                    :data-index="index"
                >
                    {{ key }}
                </div>

            </div>
            <select class="select-css trainee-select mobile-select">
                <option
                    v-for="(key, index) in Object.keys(data)"
                    :key="index"
                    :value="index"
                >
                    {{ key }}
                </option>
            </select>

            <div class="tab__content">
                <!-- Days loop -->
                <div
                    v-for="(key, index) in Object.keys(data)"
                    :key="index"
                    class="trainee__content tab__content-item"
                    :class="{'is-active': index == 0}"
                    :data-content="index"
                >

                    <!-- Items in one day -->
                    <template v-for="(item, item_index) in data[key]">
                        <!-- group name -->
                        <div class="trainee__table-name" :key="item_index">
                            {{ item.group }}
                        </div>

                        <!-- table with answers -->
                        <table class="trainee__quiz" :key="item_index">
                            <thead>
                                <tr>
                                    <th>Суть работы</th>
                                    <th>График работы</th>
                                    <th>Заработная плата</th>
                                    <th>Оценка тренера</th>
                                    <th>Рекомендации</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="top">
                                    <div class="colored" data-color="#DDE9FF" v-for="answer in item['quiz'][1]" :key="answer.id">
                                        <div class="text">
                                            {{ answer.text }} (<span>{{ answer.count }}</span>)
                                            <div class="semibold value">{{ Number(answer.percent) }}%</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="top">
                                    <div class="colored" data-color="#DDE9FF" v-for="answer in item['quiz'][2]" :key="answer.id">
                                        <div class="text">
                                            {{ answer.text }} (<span>{{ answer.count }}</span>)
                                            <div class="semibold value">{{ Number(answer.percent) }}%</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="top">
                                    <div class="colored" data-color="#DDE9FF" v-for="answer in item['quiz'][3]" :key="answer.id">
                                        <div class="text">
                                            {{ answer.text }} (<span>{{ answer.count }}</span>)
                                            <div class="semibold value">{{ Number(answer.percent) }}%</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="td-star top" rowspan="4">
                                    <div class="trainee-star">
                                        <div class="trainee__star-value">
                                            {{ item['quiz'][4].count }} оценок (<span>{{ item['quiz'][4].avg }}</span>)
                                        </div>
                                        <div class="trainee__star-wrapper">

                                            <div class="star__item"><img src="/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                    src="/images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                            <div class="star__item"><img src="/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                    src="/images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                            <div class="star__item"><img src="/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                    src="/images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                            <div class="star__item"><img src="/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                    src="/images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                            <div class="star__item"><img src="/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                    src="/images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                            <div class="star__item"><img src="/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                    src="/images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                            <div class="star__item"><img src="/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                    src="/images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                            <div class="star__item"><img src="/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                    src="/images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                            <div class="star__item"><img src="/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                    src="/images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                            <div class="star__item"><img src="/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                    src="/images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                        </div>
                                    </div>
                                </td>
                                <td rowspan="4" class="top">
                                    <template v-for="(answer, ind) in item['quiz'][5]">
                                        <div class="trainee__review" v-if="answer !== ''"  :key="ind">
                                            <p>{{ answer }}</p>
                                        </div>
                                    </template>
                                </td>

                            </tr>

                            </tbody>



                        </table>

                        <!-- invited table -->
                        <table class="invite mb-5" :key="item_index">
                            <thead>
                                <tr>
                                    <th class="first-td">Приглашенные</th>
                                    <th>1 день</th>
                                    <th>2 день</th>
                                    <th>3 день</th>
                                    <th>4 день</th>
                                    <th>5 день</th>
                                    <th>6 день</th>
                                    <th>7 день</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="first-td">{{ item.presence[0] }}</td>
                                    <td>{{ item.presence[1] }}</td>
                                    <td>{{ item.presence[2] }}</td>
                                    <td>{{ item.presence[3] }}</td>
                                    <td>{{ item.presence[4] }}</td>
                                    <td>{{ item.presence[5] }}</td>
                                    <td>{{ item.presence[6] }}</td>
                                    <td>{{ item.presence[7] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
export default {
	name: 'TraineeEstimation',
	props: {},
	data: function () {
		return {
			data: {},
			loading: false
		};
	},
	created(){
		this.fetchData()
	},
	methods: {
		fetchData() {
			this.loading = true

			this.axios.post('/profile/trainee-report', {})
				.then((response) => {
					this.showBtn(response.data)

					this.data = response.data

					this.$nextTick(() => this.fill())

					this.loading = false
				}).catch(e => {
					console.log(e)
					this.loading = false
				});
		},

		/**
         * private: show btn in introTop
         */
		showBtn(data) {
			if(Object.keys(data).length > 0) {
				this.$emit('init')
			}
		},

		/**
         * fill progress
         */
		fill() {
			/* global VJQuery */
			VJQuery('.colored').each(function(){
				VJQuery(this).css('background',`linear-gradient(to right, ${VJQuery(this).attr('data-color')} ${VJQuery(this).find('.value').text()}, #fff ${VJQuery(this).find('.value').text()})`)
			});

			// Star settings

			VJQuery('.trainee__quiz').each(function(){

				let starLength = VJQuery(this).find('.trainee__star-value span').text();

				if(starLength<=10 && starLength>=0){
					for(let i=0; i<starLength;i++){
						VJQuery(this).find('.trainee__star-wrapper .star__item')[i].classList.add('done');
					}
				}
			})
		}
	}
};
</script>