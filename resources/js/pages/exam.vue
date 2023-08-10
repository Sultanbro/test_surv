<template>
	<div class="mt-5">
		<div class="mb-0">
			<div class="row mb-3 ">
				<div class="col-3">
					<select
						v-model="currentGroup"
						class="form-control"
						@change="fetchData()"
					>
						<option
							v-for="group in groups"
							:key="group.id"
							:value="group.id"
						>
							{{ group.name }}
						</option>
					</select>
				</div>
				<div class="col-3">
					<select
						v-model="dateInfo.currentMonth"
						class="form-control"
						@change="fetchData()"
					>
						<option
							v-for="month in $moment.months()"
							:key="month"
							:value="month"
						>
							{{ month }}
						</option>
					</select>
				</div>
				<div class="col-2" />
			</div>

			<div v-if="hasPremission">
				<b-tabs
					type="card"
					default-active-key="1"
				>
					<b-tab
						key="1"
						title="Экзамены по книгам"
					>
						<b-table
							id="tabelTable"
							responsive
							striped
							:sticky-header="true"
							class="text-nowrap text-center my-table"
							:small="true"
							:bordered="true"
							:items="items"
							:fields="fields"
							show-empty
							empty-text="Нет данных"
						>
							<template #cell(success)="data">
								<input
									v-model="data.value"
									type="checkbox"
									:disabled="!data.item.link"
									@change="updateExam('success',data)"
								>
							</template>
							<template #cell(exam_date)="data">
								{{ data.value }}
							</template>
							<template #cell(link)="data">
								<input
									v-model="data.value"
									class="form-control cell-input"
									:disabled="dateInfo.currentMonth !== curMonth"
									@change="updateExam('link',data)"
								>
							</template>
						</b-table>
					</b-tab>

					<b-tab
						key="2"
						title="Навыки (Skills)"
					>
						<table class="table b-table table-striped table-bordered table-sm skills-table">
							<tr>
								<th>Сотрудник</th>
								<th>Дата</th>
								<th>Тэги</th>
								<th>Ответы</th>
							</tr>
							<tr
								v-for="skill in data.skills"
								:key="skill.id"
							>
								<td style="background:aliceblue">
									{{ skill.name }}
								</td>
								<td v-html="skill.last_time" />
								<td>
									<b-badge
										v-if="skill.head"
										pill
										variant="success"
									>
										#Руководил
									</b-badge>
								</td>
								<td v-html="skill.text" />
							</tr>
						</table>
					</b-tab>
				</b-tabs>
			</div>

			<div v-else>
				<p>У вас нет доступа к этой группе</p>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'TableExam',
	props: {
		groups: Array,
		fines: Array,
		activeuserid: String
	},
	data() {
		return {
			data: {},
			items: [],
			fields: [],
			group_editors: [],
			users: [],
			hasPremission: false,
			curMonth: null,
			dateInfo: {
				currentMonth: null,
				monthEnd: 0,
				workDays: 0,
				weekDays: 0,
				daysInMonth: 0,
				date: null
			},
			dataLoaded: false,
			currentGroup: null,
		}
	},

	created() {
		//Текущая группа
		this.currentGroup = this.currentGroup ? this.currentGroup : this.groups[0]['id']
		this.setMonth()
		this.fetchData()
	},
	methods: {
		//Установка выбранного месяца
		setMonth() {
			this.curMonth =  this.$moment().format('MMMM')
			let year = this.$moment().format('YYYY')
			this.dateInfo.currentMonth = this.dateInfo.currentMonth ? this.dateInfo.currentMonth : this.$moment().format('MMMM')
			this.dateInfo.date = `${this.dateInfo.currentMonth} ${year}`
		},
		updateExam(from, data) {

			var index = data.index

			if(from == 'success') {
				data.item.success = data.value
			}
			if(from == 'link') {
				data.item.link = data.value
			}

			let loader = this.$loading.show()
			this.axios.post('/timetracking/exam/update', {
				key: data.field.key,
				value: data.item.success,
				link: data.item.link,
				user_id: data.item.user_id,
				book_id: data.item.book_id,
				month: this.$moment(this.dateInfo.currentMonth, 'MMMM').format('M'),
				year: this.$moment(this.dateInfo.date, 'YYYY').format('YYYY'),
				group_id: this.currentGroup
			}).then(response => {
				if (response.data.errors) {
					alert(response.data.errors)
					data.item.link = ''
					loader.hide();
					return;
				}
				if (data.field.key === 'link'){
					// console.log('im here');
					// this.fetchData()
					// this.setFields();
					// this.loadItems();
					data.item.link ? this.items[index]['exam_date'] = this.$moment().format('DD.MM.YYYY') : this.items[index]['exam_date'] = ''
					data.item.success = response.data.success
				}
				loader.hide()
			}).catch(function (error) {
				alert(error)
				console.error(error)
				loader.hide();
			});

		},
		//Установка заголовока таблицы
		setFields() {
			let fields = []
			fields = [{
				key: 'name',
				stickyColumn: true,
				label: 'Имя',
				variant: 'primary',
				sortable: true,
				class: 'text-left px-3 t-name',
			},
			{
				key: 'book_name',
				label: 'Книга',
				class: 'text-left',
			},
			{
				key: 'success',
				label: 'Сдано',
			},
			{
				key: 'exam_date',
				label: 'Дата сдачи',
			},
			{
				key: 'link',
				label: 'Ссылка на файл',
			},
			]
			this.fields = fields
		},
		//Загрузка данных для таблицы
		fetchData() {
			let loader = this.$loading.show();
			this.axios.post('/timetracking/exam', {
				month: this.$moment(this.dateInfo.currentMonth, 'MMMM').format('M'),
				year: this.$moment(this.dateInfo.date, 'YYYY').format('YYYY'),
				group_id: this.currentGroup
			}).then(response => {
				if (response.data.error && response.data.error == 'access') {
					this.hasPremission = false
					loader.hide();
					return;
				}
				this.hasPremission = true

				this.data = response.data

				// console.log(this.dateInfo);
				this.setMonth()
				this.setFields()
				this.loadItems()
				this.dataLoaded = true
				loader.hide()
			})

		},
		//Добавление загруженных данных в таблицу
		loadItems() {
			let items = []
			this.data.users.forEach(item => {
				items.push({
					name: item.full_name,
					book_name: item.book_name,
					book_id: item.book_id,
					user_id: item.id,
					success: item.success,
					exam_date: item.exam_date,
					link: item.link,
				})
			})
			this.items = items
		}
	}
}
</script>

<style lang="scss">

    .b-table-sticky-header {
        max-height: 450px;
    }

    .table-day-1 {
        color: rgb(0, 0, 0);
        background: rgb(204, 204, 204);
    }

    .table-day-2 {
        color: #fff;
        background: blue;
    }

    .table-day-3 {
        color: rgb(0, 0, 0);
        background: aqua;
    }

    .table-day-4 {
        color: rgb(0, 0, 0);
        background: rgb(200, 162, 200);
    }

    .table-day-5 {
        color: rgb(0, 0, 0);
        background: orange;
    }

    .my-table-max {
        max-height: inherit !important;

        .day {
            padding: 0 !important;
            text-align: center;

            &.Sat,
            &.Sun {
                background-color: #FEF2CB;
            }
            &.table-danger{
                background-color: #f5c6cb !important;
            }
        }


    }

    .cell-input {
        background: none;
        border: none;
        text-align: center;
        -moz-appearance: textfield;
        font-size: .8rem;
        font-weight: normal;
        padding: 0;
        color: #000;
        border-radius: 0;

        &:focus {
            outline: none;
        }

        &::-webkit-outer-spin-button,
        &::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    }
.skills-table pre {
    font-family: 'Open sans';
    white-space: -moz-pre-wrap;
    white-space: -o-pre-wrap;
    word-wrap: break-word;
    white-space: pre-wrap;
}
</style>
