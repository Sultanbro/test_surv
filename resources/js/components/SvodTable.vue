<template>
	<div class="trainee_report">
		<div>
			<div
				v-for="(g, rdate) in all_groups"
				:key="rdate"
				class="ramka mt-2"
			>
				<b-tabs type="card">
					<b-tab
						key="1"
						title="Сводная"
						card
					>
						<p class="mt-2">
							<b>{{ g.group }}</b>
						</p>
						<table class="table b-table table-striped table-bordered table-sm">
							<thead>
								<th
									class="text-left t-name table-title"
									style="background:#90d3ff;min-width: 170px;"
								>
									Суть работы
								</th>
								<th
									class="text-left t-name table-title"
									style="background:#90d3ff;min-width: 170px;"
								>
									График работы
								</th>
								<th
									class="text-left t-name table-title"
									style="background:#90d3ff;min-width: 200px;"
								>
									Какая ЗП?
								</th>

								<th
									class="text-left t-name table-title"
									style="background:#90d3ff;min-width: 230px;"
								>
									Общая ценка тренера
								</th>
							</thead>

							<tbody>
								<tr>
									<td class="text-left t-name table-title align-middle">
										<div
											v-for="answer in g['quiz'][1]"
											:key="answer.id"
											class="d-flex"
										>
											<ProgressBar
												:percentage="Number(Math.ceil(answer.percent))"
												:label="answer.text + ' (' + answer.count + ')'"
												:class="'active'"
											/>
										</div>
									</td>
									<td class="text-left t-name table-title align-middle">
										<div
											v-for="answer in g['quiz'][2]"
											:key="answer.id"
											class="d-flex"
										>
											<ProgressBar
												:percentage="Number(Math.ceil(answer.percent))"
												:label="answer.text + ' (' + answer.count + ')'"
												:class="'active'"
											/>
										</div>
									</td>
									<td class="text-left t-name table-title align-middle">
										<div
											v-for="answer in g['quiz'][3]"
											:key="answer.id"
											class="d-flex"
										>
											<ProgressBar
												:percentage="Number(Math.ceil(answer.percent))"
												:label="answer.text + ' (' + answer.count + ')'"
												:class="'active'"
											/>
										</div>
									</td>
									<td class="text-left t-name table-title align-middle">
										<div
											class="d-flex"
											style="flex-direction:column"
										>
											<Rating
												:grade="Number(g['quiz'][4]['avg']).toFixed(0)"
												:max-stars="10"
												:has-counter="false"
											/>
											<p class="mb-0">
												{{ g['quiz'][4]['avg'].toFixed(2) + ' (' + g['quiz'][4]['count'] + ')' }}
											</p>
										</div>
									</td>
								</tr>
							</tbody>
						</table>

						<table class="table b-table table-striped table-bordered table-sm">
							<thead>
								<th
									class="text-left t-name table-title"
									style="background:#90d3ff"
								>
									Приглашенные
								</th>
								<th class="text-center t-name table-title">
									1 день
								</th>
								<th class="text-center t-name table-title">
									2 день
								</th>
								<th class="text-center t-name table-title">
									3 день
								</th>
								<th class="text-center t-name table-title">
									4 день
								</th>
								<th class="text-center t-name table-title">
									5 день
								</th>
								<th class="text-center t-name table-title">
									6 день
								</th>
								<th class="text-center t-name table-title">
									7 день
								</th>
							</thead>
							<tbody>
								<tr>
									<td
										class="text-left t-name table-title align-middle"
										style="background:#90d3ff"
									>
										{{ g['presence'][0] }}
									</td>
									<td class="text-center t-name table-title align-middle">
										{{ g['presence'][1] }}
									</td>
									<td class="text-center t-name table-title align-middle">
										{{ g['presence'][2] }}
									</td>
									<td class="text-center t-name table-title align-middle">
										{{ g['presence'][3] }}
									</td>
									<td class="text-center t-name table-title align-middle">
										{{ g['presence'][4] }}
									</td>
									<td class="text-center t-name table-title align-middle">
										{{ g['presence'][5] }}
									</td>
									<td class="text-center t-name table-title align-middle">
										{{ g['presence'][6] }}
									</td>
									<td class="text-center t-name table-title align-middle">
										{{ g['presence'][7] }}
									</td>
								</tr>
							</tbody>
						</table>
					</b-tab>

					<template v-for="(item, index) in trainee_report">
						<b-tab
							v-if="item.group_id == g.group_id"
							:key="index"
							:title="item.date"
							card
						>
							<p class="mt-2">
								<b>{{ item.group }}</b>
							</p>
							<table class="table b-table table-striped table-bordered table-sm">
								<thead>
									<th
										class="text-left t-name table-title"
										style="background:#90d3ff;min-width: 170px;"
									>
										Суть работы
									</th>
									<th
										class="text-left t-name table-title"
										style="background:#90d3ff;min-width: 170px;"
									>
										График работы
									</th>
									<th
										class="text-left t-name table-title"
										style="background:#90d3ff;min-width: 200px;"
									>
										Какая ЗП?
									</th>
									<th
										class="text-left t-name table-title"
										style="background:#90d3ff;min-width: 230px;"
									>
										Общая ценка тренера
									</th>
									<th
										class="text-left t-name table-title"
										style="background:#90d3ff"
									>
										Рекомендации
									</th>
								</thead>

								<tbody>
									<tr>
										<td class="text-left t-name table-title align-middle">
											<div
												v-for="answer in item['quiz'][1]"
												:key="answer.id"
												class="d-flex"
											>
												<ProgressBar
													:percentage="Number(Math.ceil(answer.percent))"
													:label="answer.text + ' (' + answer.count + ')'"
													:class="'active'"
												/>
											</div>
										</td>
										<td class="text-left t-name table-title align-middle">
											<div
												v-for="answer in item['quiz'][2]"
												:key="answer.id"
												class="d-flex"
											>
												<ProgressBar
													:percentage="Number(Math.ceil(answer.percent))"
													:label="answer.text + ' (' + answer.count + ')'"
													:class="'active'"
												/>
											</div>
										</td>
										<td class="text-left t-name table-title align-middle">
											<div
												v-for="answer in item['quiz'][3]"
												:key="answer.id"
												class="d-flex"
											>
												<ProgressBar
													:percentage="Number(Math.ceil(answer.percent))"
													:label="answer.text + ' (' + answer.count + ')'"
													:class="'active'"
												/>
											</div>
										</td>
										<td class="text-left t-name table-title align-middle">
											<div
												class="d-flex"
												style="flex-direction:column"
											>
												<Rating
													:grade="Number(item['quiz'][4]['avg']).toFixed(0)"
													:max-stars="10"
													:has-counter="false"
												/>
												<p class="mb-0">
													{{ item['quiz'][4]['avg'].toFixed(2) + ' (' + item['quiz'][4]['count'] + ')' }}
												</p>
											</div>
										</td>
										<td class="text-left t-name table-title align-middle">
											<div
												v-for="(answer, ind) in item['quiz'][5]"
												:key="ind"
												class="d-flex"
											>
												<p class="fz12">
													{{ answer }}
												</p>
											</div>
										</td>
									</tr>
								</tbody>
							</table>

							<table class="table b-table table-striped table-bordered table-sm">
								<thead>
									<th
										class="text-left t-name table-title"
										style="background:#90d3ff"
									>
										Приглашенные
									</th>
									<th class="text-center t-name table-title">
										1 день
									</th>
									<th class="text-center t-name table-title">
										2 день
									</th>
									<th class="text-center t-name table-title">
										3 день
									</th>
									<th class="text-center t-name table-title">
										4 день
									</th>
									<th class="text-center t-name table-title">
										5 день
									</th>
									<th class="text-center t-name table-title">
										6 день
									</th>
									<th class="text-center t-name table-title">
										7 день
									</th>
								</thead>
								<tbody>
									<tr>
										<td
											class="text-left t-name table-title align-middle"
											style="background:#90d3ff"
										>
											{{ item['presence'][0] }}
										</td>
										<td class="text-center t-name table-title align-middle">
											{{ item['presence'][1] }}
										</td>
										<td class="text-center t-name table-title align-middle">
											{{ item['presence'][2] }}
										</td>
										<td class="text-center t-name table-title align-middle">
											{{ item['presence'][3] }}
										</td>
										<td class="text-center t-name table-title align-middle">
											{{ item['presence'][4] }}
										</td>
										<td class="text-center t-name table-title align-middle">
											{{ item['presence'][5] }}
										</td>
										<td class="text-center t-name table-title align-middle">
											{{ item['presence'][6] }}
										</td>
										<td class="text-center t-name table-title align-middle">
											{{ item['presence'][7] }}
										</td>
									</tr>
								</tbody>
							</table>
						</b-tab>
					</template>
				</b-tabs>

				<!--<tbody>
                    <tr>
                        <td class="text-left t-name table-title align-middle">
                            <div v-for="answer in report_date['quiz'][1]" :key="answer.id" class="d-flex">
                                <ProgressBar
                                    :percentage="Number(answer.percent)"
                                    :label="answer.text + ' (' + answer.count + ')'"
                                    :class="'active'"
                                />
                            </div>
                        </td>
                        <td class="text-left t-name table-title align-middle">
                            <div v-for="answer in report_date['quiz'][2]" :key="answer.id" class="d-flex">
                                <ProgressBar
                                    :percentage="Number(answer.percent)"
                                    :label="answer.text + ' (' + answer.count + ')'"
                                    :class="'active'"
                                />
                            </div>
                        </td>
                        <td class="text-left t-name table-title align-middle">
                            <div v-for="answer in report_date['quiz'][3]" :key="answer.id" class="d-flex">
                                <ProgressBar
                                    :percentage="Number(answer.percent)"
                                    :label="answer.text + ' (' + answer.count + ')'"
                                    :class="'active'"
                                />
                            </div>
                        </td>
                        <td class="text-left t-name table-title align-middle" >
                            <div class="d-flex" style="flex-direction:column">
                                <Rating :grade="Number(report_date['quiz'][4]['avg']).toFixed(0)" :maxStars="10" :hasCounter="false" />
                                <p class="mb-0">{{ report_date['quiz'][4]['avg'] + ' (' + report_date['quiz'][4]['count'] + ')' }}</p>
                            </div>
                        </td>
                        <td class="text-left t-name table-title align-middle">
                            <div v-for="(answer, ind) in report_date['quiz'][5]"  :key="ind" class="d-flex">
                                <p class="fz12">{{ answer }}</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>




            <table class="table b-table table-striped table-bordered table-sm">
                <thead>
                    <th class="text-left t-name table-title" style="background:#90d3ff">Приглашенные</th>
                    <th class="text-center t-name table-title">1 день</th>
                    <th class="text-center t-name table-title">2 день</th>
                    <th class="text-center t-name table-title">3 день</th>
                    <th class="text-center t-name table-title">4 день</th>
                    <th class="text-center t-name table-title">5 день</th>
                    <th class="text-center t-name table-title">6 день</th>
                    <th class="text-center t-name table-title">7 день</th>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-left t-name table-title align-middle" style="background:#90d3ff">
                            {{ report_date['presence'][0] }}
                        </td>
                        <td class="text-center t-name table-title align-middle">{{ report_date['presence'][1] }}</td>
                        <td class="text-center t-name table-title align-middle">{{ report_date['presence'][2] }}</td>
                        <td class="text-center t-name table-title align-middle">{{ report_date['presence'][3] }}</td>
                        <td class="text-center t-name table-title align-middle">{{ report_date['presence'][4] }}</td>
                        <td class="text-center t-name table-title align-middle">{{ report_date['presence'][5] }}</td>
                        <td class="text-center t-name table-title align-middle">{{ report_date['presence'][6] }}</td>
                        <td class="text-center t-name table-title align-middle">{{ report_date['presence'][7] }}</td>
                    </tr>
                </tbody>
            </table>-->
			</div>
		</div>
	</div>
</template>

<script>
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */

import ProgressBar from '@/components/ProgressBar' // в ответах quiz
import Rating from './ui/Rating.vue'

export default {
	name: 'TraineeReport',
	components: { Rating, ProgressBar },
	props: {
		groups: {
			type: Array,
			default: () => []
		},
		trainee_report: {
			type: Array,
			default: () => []
		},
	},
	data() {
		return {
			report_group_id: 0,
			all_groups: [],
		}
	},
	watch: {
		trainee_report(){
			this.extractUniqueGroups()
		}
	},
	created() {
		this.extractUniqueGroups();
	},
	methods:{
		extractUniqueGroups(){
			var groups = [];
			var items =[];
			var helper = [];
			this.trainee_report.forEach(item => {
				if(!groups.includes(item.group)){
					groups.push(item.group);
					helper.push({group : item.group, repeated : 1});
					items.push(structuredClone(item));
				}
				else{
					if(item['quiz'][4]['avg'] > 0){
						helper[groups.indexOf(item.group)].repeated++;
						for(let i = 1; i < 4; i++){
							for(let j = 0; j < 3; j++){
								items.filter(my_item => my_item.group == item.group)[0]['quiz'][i][j].count += item['quiz'][i][j].count;
								items.filter(my_item => my_item.group == item.group)[0]['quiz'][i][j].percent += item['quiz'][i][j].percent;
							}
						}
						items.filter(my_item => my_item.group == item.group)[0]['quiz'][4]['avg'] += item['quiz'][4]['avg'];
						items.filter(my_item => my_item.group == item.group)[0]['quiz'][4]['count'] += item['quiz'][4]['count'];
						items.filter(my_item => my_item.group == item.group)[0]['presence'][0] += item['presence'][0];

						for(let i = 1; i < 8; i++){
							items.filter(my_item => my_item.group == item.group)[0]['presence'][i] += item['presence'][i];
						}
					}
					else{
						items.filter(my_item => my_item.group == item.group)[0]['presence'][0] += item['presence'][0];
						for(let i = 1; i < 8; i++){
							items.filter(my_item => my_item.group == item.group)[0]['presence'][i] += item['presence'][i];
						}
					}
					/*sorted_array.forEach(function(answer, index){
						answer.percent += item['quiz'][1][index].percent;
						answer.count += item['quiz'][1][index].count;
					});*/
				}
			});
			this.all_groups = items;

			this.setAverageData(helper);
		},
		setAverageData(helper){
			let counter = 0;
			this.all_groups.forEach(function(object){
				object['quiz'][1][0].percent /= helper[counter].repeated;
				object['quiz'][1][1].percent /= helper[counter].repeated;
				object['quiz'][1][2].percent /= helper[counter].repeated;

				object['quiz'][2][0].percent /= helper[counter].repeated;
				object['quiz'][2][1].percent /= helper[counter].repeated;
				object['quiz'][2][2].percent /= helper[counter].repeated;

				object['quiz'][3][0].percent /= helper[counter].repeated;
				object['quiz'][3][1].percent /= helper[counter].repeated;
				object['quiz'][3][2].percent /= helper[counter].repeated;

				object['quiz'][4]['avg'] /= helper[counter].repeated;
				counter++;
			});
		}
	}
}
</script>
<style>

.wrap {
    background: aliceblue;
    margin-bottom: 10px;
    padding-top: 15px;
}
.ramka {
    border: 1px solid #dee2e6;
    box-shadow: 0 8px 10px 10px #f7f7f7;
    padding: 15px;
}
.date-select {
    width: 250px;
}
.fz12 {
    font-size: 12px;
    margin-bottom: 0;
    line-height: 20px;
    color: #000 !important;
}
.ramka .rating {
    padding: 0;
}
</style>
