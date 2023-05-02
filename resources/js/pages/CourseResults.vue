<template>
	<div class="course-results mt-4 CourseResults">
		<div class="d-flex mb-2">
			<JobtronButton
				class="mr-3"
				@click="type = BY_USER"
				:fade="type !== BY_USER"
			>
				По сотрудникам
			</JobtronButton>
			<JobtronButton
				class="mr-3"
				@click="type = BY_GROUP"
				:fade="type !== BY_GROUP"
			>
				По отделам
			</JobtronButton>
		</div>

		<div
			v-if="type == BY_USER"
			class="by_user"
		>
			<div
				v-if="users.items.length > 0"
				class="table-responsive table-container"
			>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th
								v-for="(field, index) in users.fields"
								:key="index"
								:class="field.class"
							>
								<div>{{ field.name }}</div>
							</th>
						</tr>
					</thead>
					<tbody>
						<template v-for="(item, i) in users.items">
							<tr
								:key="i"
								class="pointer"
								:class="{
									'expanded-title': item.expanded
								}"
							>
								<td
									v-for="(field, f) in users.fields"
									:key="f"
									:class="field.class"
									@click="expandUser(item)"
								>
									<div
										v-if="field.key == 'progress'"
										class="d-flex jcc aic gap-2"
									>
										<p class="mb-0 CourseResults-progress">
											{{ item[field.key] }}
										</p>
										<ProgressBar
											:progress="item[field.key]"
										/>
									</div>
									<div v-else>
										{{ item[field.key] }}
									</div>
								</td>
							</tr>

							<template v-for="(course, c) in item.courses">
								<tr
									v-if="item.expanded"
									:key="'c' + c"
									class="expanded"
								>
									<td
										v-for="(field, f) in users.fields"
										:key="f"
										:class="[field.class, {pointer: course.items && course.items.length > 1}]"
										@click="expandCourse(course, item)"
									>
										<div
											v-if="field.key == 'progress'"
											class="d-flex jcc aic gap-2"
										>
											<p class="mb-0 CourseResults-progress">
												{{ course[field.key] }}
											</p>
											<ProgressBar
												:progress="course[field.key]"
											/>
										</div>
										<div
											v-else-if="field.key == 'name'"
											class="nullify-wrap relative"
										>
											{{ course[field.key] }}
											<i
												class="absolute nullify fa fa-broom"
												title="Обнулить прогресс"
												@click="nullify(i, c)"
											/>
										</div>
										<div v-else>
											{{ course[field.key] }}
										</div>
									</td>
								</tr>

								<template v-if="course.items && course.items.length > 1 && course.expanded">
									<tr
										v-for="(courseItem, ci) in course.items"
										:key="'ci' + ci"
										class="expanded-course-item"
									>
										<td
											v-for="(field, f) in users.fields"
											:key="f"
											:class="field.class"
										>
											<template v-if="courseItemsTable[item.user_id] && courseItemsTable[item.user_id][courseItem.item_id]">
												<div
													v-if="field.key === 'name'"
													class="nullify-wrap relative"
												>
													{{ courseItem[course2item[field.key]] || field.key }}
													<i
														class="absolute nullify fa fa-broom"
														title="Обнулить прогресс"
														@click="regress(item.user_id, course.course_id, courseItem)"
													/>
												</div>
												<div
													v-else-if="field.key === 'progress'"
													class="d-flex jcc aic gap-2"
												>
													<p class="mb-0 CourseResults-progress">
														{{ courseItemsTable[item.user_id][courseItem.item_id][course2item[field.key]] }}%
													</p>
													<ProgressBar
														:progress="courseItemsTable[item.user_id][courseItem.item_id][course2item[field.key]] + '%'"
													/>
												</div>
												<div v-else-if="field.key === 'progress_on_week'">
													<p class="mb-0 mr-1">
														{{ courseItemsTable[item.user_id][courseItem.item_id][course2item[field.key]] }}%
													</p>
												</div>
												<template v-else>
													{{ courseItemsTable[item.user_id][courseItem.item_id][course2item[field.key]] }}
												</template>
											</template>
										</td>
									</tr>
								</template>
							</template>
						</template>
					</tbody>
				</table>
			</div>
		</div>

		<div
			v-else
			class="by_group"
		>
			<div
				class="table-responsive table-container"
				v-if="groups.items.length > 0"
			>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th
								v-for="(field, index) in groups.fields"
								:key="index"
								:class="field.class"
							>
								<div>{{ field.name }}</div>
							</th>
						</tr>
					</thead>
					<tbody>
						<template v-for="(item, i) in groups.items">
							<tr :key="i">
								<td
									v-for="(field, f) in groups.fields"
									:key="f"
									:class="field.class"
									@click="expandUser(item)"
								>
									<div>{{ item[field.key] }}</div>
								</td>
							</tr>
						</template>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</template>

<script>
import JobtronButton from '@ui/Button'
import ProgressBar from '@ui/ProgressBar'
const BY_USER = 1;
const BY_GROUP = 2;

function formatProgress(num, precision = 2){
	return parseFloat(num.toFixed(precision))
}

export default {
	name: 'CourseResults',
	components: {
		ProgressBar,
		JobtronButton,
	},
	props: {
		monthInfo: {
			required: false
		},
		currentGroup: {
			required: false
		}
	},
	data() {
		return {
			data: [],
			type: BY_USER,
			first: true,
			BY_USER: BY_USER,
			BY_GROUP: BY_GROUP,
			users: {
				items: [],
				fields: [],
			},
			groups: {
				items: [],
				fields: [],
			},
			course2item: {
				name: 'title',
				status: 'status',
				points: 'points',
				progress: 'progress',
				progress_on_week: 'progress_on_week',
				started_at: 'started_at',
				ended_at: 'ended_at'
			},
			courses: {},
			courseItems: {}
		}
	},
	computed: {
		courseItemsTable(){
			const result = {}
			for(let [userId, userResult] of Object.entries(this.courseItems)){
				for(let [courseId, courseResult] of Object.entries(userResult)){
					const course = this.courses[courseId]
					if(!course) continue

					if(!result[userId]) result[userId] = {}
					courseResult.forEach(courseItem => {
						const passedCount = courseItem.passed_stages ? courseItem.passed_stages.length : 0
						const status = (passedCount ? (courseItem.stages && courseItem.stages > passedCount ? 'Начат' : 'Завершен') : 'Запланирован')
						const progress = formatProgress(((passedCount / courseItem.stages) * 100) || 0)
						const points = courseItem.bonuses

						result[userId][courseItem.item_id] = {
							status,
							points,
							progress,
							progress_on_week: 0,
							started_at: new Date(),
							ended_at: new Date(0)
						}
						const res = result[userId][courseItem.item_id]

						const weekAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)
						courseItem.passed_stages.forEach(stage => {
							const updated = new Date(stage.updated_at)
							if(res.started_at > updated) res.started_at = updated
							if(res.ended_at < updated) res.ended_at = updated
							if(updated > weekAgo) res.progress_on_week += 1
						})

						res.started_at = passedCount ? this.$moment(res.started_at).format('DD.MM.YYYY') : ''
						res.ended_at = courseItem.stages && courseItem.stages > passedCount ? '' : this.$moment(res.ended_at).format('DD.MM.YYYY')
						res.progress_on_week = formatProgress(((res.progress_on_week / courseItem.stages) * 100) || 0)
					})
				}
			}
			return result
		}
	},
	watch: {
		monthInfo() {
			this.first = true;
			if(this.type == this.BY_GROUP) {
				this.fetchData('groups');
				this.first = false;
			} else {
				this.fetchData('users');
			}
		},
		currentGroup() {
			this.first = true;
			if(this.type == this.BY_GROUP) {
				this.fetchData('groups');
				this.first = false;
			} else {
				this.fetchData('users');
			}
		},
		type(val) {
			if(val == this.BY_GROUP && this.first) {
				this.fetchData('groups');
				this.first = false;
			}
		},
	},
	created() {
		this.fetchData();
	},
	methods: {
		fetchData(type = 'users') {
			let loader = this.$loading.show();

			this.axios
				.post('/course-results/get', {
					type: type,
					month: this.monthInfo.month,
					year: this.monthInfo.currentYear,
					group_id: this.currentGroup !== undefined ? this.currentGroup :  null,
				})
				.then((response) => {

					if(type == 'users') {
						this.users = response.data.items;
					}
					if(type == 'groups') {
						this.groups = response.data.items;
					}


					loader.hide();
				});
		},

		fetchCourseItems(userId, courseId) {
			this.axios.get('/course/progress', {
				params: { userId, courseId }
			}).then(({ data }) => {
				if(!this.courseItems[userId]) this.$set(this.courseItems, userId, {})
				this.$set(this.courseItems[userId], courseId, data.data.courseItems)
				this.courses[data.data.course.id] = data.data.course
			})
		},

		expandUser(item) {
			let ex = item.expanded;
			this.users.items.forEach(i => {
				i.expanded = false
				if(i.courses) i.courses.forEach(c => this.$set(c, 'expanded', false))
			});
			item.expanded = !ex;
		},

		expandCourse(course, item) {
			if(course.items && course.items.length > 1){
				if(!(this.courseItems[item.user_id] && this.courseItems[item.user_id][course.course_id])){
					this.fetchCourseItems(item.user_id, course.course_id)
				}
				this.users.items.every(el => {
					// console.log('el.user_id', el.user_id, item.user_id)
					if(el.user_id !== item.user_id) return true
					item.courses.forEach(c => {
						// console.log('c.course_id', c.course_id, course.course_id)
						if(c.course_id === course.course_id){
							this.$set(c, 'expanded', !c.expanded)
						}
						else{
							this.$set(c, 'expanded', false)
						}
					})
				})
			}
		},

		nullify(i, c) {

			if(!confirm('Вы уверены? Потом прогресс не восстановить')) {
				return;
			}

			let course = this.users.items[i].courses[c];
			// course
			// ended_at:""
			// name:"Знакомство с нашей компанией"
			// points:"185 / 762 / 24.3%"
			// progress:"30%"
			// progress_on_week:"0%"
			// started_at:"28.07.2022"
			// status:"Запланирован"
			// user_id: 5

			this.nullifyRequest({
				user_id: course.user_id,
				course_id: course.course_id,
			}, () => {
				this.$toast.success('Прогресс по курсу Обнулен');

				course.progress = '0%';
				course.started_at = '';
				course.ended_at = '';
				course.status = 'Запланирован';
				course.points = '0 / 0 / 0%';
				course.progress_on_week = '0%';
			});

		},

		nullifyRequest({user_id, course_id}, callback) {
			let loader = this.$loading.show();

			this.axios
				.post('/course/regress', {
					type: 'course',
					user_id,
					course_id
				})
				.then((response) => {
					callback(response);
				})
				.catch(e => console.error(e));

			loader.hide();
		},

		regress(user_id, course_id, courseItem){
			if(!confirm('Вы уверены? Потом прогресс не восстановить')) return

			const loader = this.$loading.show()

			this.axios.post('/course/regress', {
				type: 'item',
				user_id,
				course_item_id: courseItem.id
			}).then(() => {
				this.fetchCourseItems(user_id, course_id)
				this.$toast.success('Прогресс по разделу курса обнулен')
			}).catch(e => console.error(e))

			loader.hide()
		},
	}
}
</script>

<style lang="scss">
.CourseResults{
	&-progress{
		width: 3em;
		text-align: right;
	}
	.ProgressBar{
		flex: 1;
		min-width: 100px;
	}
}
</style>

<style scoped lang="scss">
.nullify {
	right: -6px;
	top: -2px;
	z-index: 2;
	background: aliceblue;
	padding: 6px 4px;
	border-radius: 50px;
	display: none;
	cursor: pointer;
	position: absolute;
}
.nullify-wrap{
	padding: 5px 10px;
	margin: -5px -10px;
}
.nullify-wrap:hover .nullify {
	display: block;
}

.expanded-course-item{
	background: lighten(#c0def2, 5%);
}
</style>
