<template>
	<div class="con">
		<div class="first-step">
			<div class="row">
				<div class="col-md-12">
					<p class="heading">
						Шаг 1: Загрузите Excel файл
						<b-button
							id="btn"
							variant="default"
							size="sm"
							class="ml-2 rounded"
						>
							<i class="fa fa-info" />
						</b-button>
					</p>

					<b-tooltip
						target="btn"
						placement="bottom"
					>
						<div v-if="group_id == 42">
							<div>Excel файл для импорта должен содержать в первом ряду поля:</div>
							<div>ФИО сотрудника</div>
							<div>Минуты</div>
						</div>
					</b-tooltip>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<form
						id="import-contact"
						method="post"
						enctype="multipart/form-data"
					>
						<div class="message" />
						<div class="files">
							<span>Файл</span>
							<b-form-file
								v-model="file"
								:state="Boolean(file)"
								placeholder="Выберите или перетащите файл сюда..."
								drop-placeholder="Перетащите файл сюда..."
								accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
								class="mt-3"
							/>
						</div>
						<b-button
							variant="primary"
							class="mt-2 mb-2"
							@click="uploadFile"
						>
							<i class="fa fa-file" /> Загрузить
						</b-button>
					</form>
				</div>
				<div class="col-md-12">
					<p
						v-for="error in errors"
						:key="error"
						style="color:red"
					>
						{{ error }}
					</p>
				</div>
			</div>
		</div>



		<div v-if="showStepTwo">
			<div class="row">
				<div class="col-md-12">
					<p class="heading mt-4">
						Шаг 2: Соедините поля
					</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<p>Сотрудник в Excel</p>
				</div>
				<div class="col-md-4">
					<p>Сотрудник в табели</p>
				</div>
				<div class="col-md-4">
					<p>Данные</p>
				</div>
			</div>
			<div
				v-for="item in items"
				:key="item.name"
				class="row my-2 py-3 border-b"
			>
				<div class="col-md-4">
					<input
						type="text"
						disabled
						:value="item.name"
						class="form-control"
					>
				</div>

				<div class="col-md-4">
					<select
						v-model="item.id"
						class="form-control"
					>
						<option
							v-for="user in users"
							:key="user.id"
							:value="user.id"
						>
							{{ user.last_name }} {{ user.name }} ID {{ user.id }}
						</option>
					</select>
				</div>
				<div class="col-md-4">
					<div
						v-if="(group_id == 88 && activity_id == 164) || (group_id == 42 && activity_id == 1) || (group_id == 71)"
						class=""
					>
						<div v-if="(group_id == 42 && activity_id == 1) || (group_id == 88 && activity_id == 164)">
							<b>Дата:</b> {{ item.data }}
						</div>
						<div>
							<b>Часы:</b> {{ item.hours }}
						</div>
					</div>

					<div
						v-if="(group_id == 88 && activity_id == 166) || (group_id == 42 && activity_id == 13) || (group_id == 71)"
						class=""
					>
						<div>
							<b>Сборы:</b> {{ item.gatherings }}
						</div>
					</div>

					<div
						v-if="(group_id == 88 && activity_id == 165) || (group_id == 42 && activity_id == 94) || (group_id == 71)"
						class=""
					>
						<div>
							<b>Ср. время:</b> {{ item.avg_time }}
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div
					v-if="group_id == 71 || group_id == 88"
					class="col-sm-6 mt-2"
				>
					<b-form-datepicker
						id="example-datepicker"
						v-model="date"
						v-bind="datepickerLabels"
						locale="ru"
						:start-weekday="1"
					/>
				</div>
				<div
					v-if="activity_id == 94"
					class="col-sm-6 mt-2"
				>
					<b-form-datepicker
						id="example-datepicker"
						v-model="date"
						v-bind="datepickerLabels"
						locale="ru"
						:start-weekday="1"
					/>
				</div>
				<div class="col-md-12 mt-2">
					<b-button
						variant="primary"
						@click="saveConnects"
					>
						<i class="fa fa-save" /> Сохранить
					</b-button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */

export default {
	name: 'ActivityExcelImport',
	props: {
		group_id: {
			type: Number,
			default: 0
		},
		table: {
			type: String,
			default: 'minutes',
		},
		activity_id: {
			type: Number,
			default: 0
		}
	},
	data() {
		return {
			fileIsCorrect: false,
			message: null,
			activebtn: null,
			file: undefined,
			items: [],
			date: null,
			filename: '',
			showStepTwo: false,
			errors: [],
			users: [],
		}
	},
	watch:{
		file(before){
			if(before.size && before.size > 1024000)
			{
				alert('Файл слишком большой!');
				this.file = undefined;
			}else{
				this.fileIsCorrect = true;
			}
		}
	},
	created() {
		this.datepickerLabels =  {
			labelPrevDecade: 'Пред 10 лет',
			labelPrevYear: 'Предыдущий год',
			labelPrevMonth: 'Предыдущий месяц',
			labelCurrentMonth: 'Текущий месяц',
			labelNextMonth: 'Следующий месяц',
			labelNextYear: 'Следующий год',
			labelNextDecade: 'След 10 лет',
			labelToday: 'Cегодня',
			labelSelected: 'Выбранная дата',
			labelNoDateSelected: 'Дата не выбрана',
			labelCalendar: 'Календарь',
			labelNav: 'Навигация',
			labelHelp: 'Перемещайтесь по календарю с помощью клавиш со стрелками'
		};
	},
	methods: {
		saveConnects() {
			let loader = this.$loading.show();
			this.axios.post('/timetracking/analytics/activity/importexcel/save', {
				date: this.date,
				items: this.items,
				filename: this.filename,
				activity_id: this.activity_id,
				group_id: this.group_id,
			}).then(() => {
				this.$toast.info('Сохранено');
				this.$emit('close');
				this.file = undefined
				this.showStepTwo = false
				this.items = []
				loader.hide()
			}).catch(function(e){
				loader.hide()
				alert(e)
			})
		},
		uploadFile(){
			let loader = this.$loading.show();

			let formData = new FormData();
			formData.append('file', this.file);
			formData.append('group_id', this.group_id);
			formData.append('activity_id', this.activity_id);

			var _this = this;

			this.axios.post( '/timetracking/analytics/activity/importexcel', formData, {
				headers: {
					'Content-Type': 'multipart/form-data'
				}
			}).then(function(response){
				if(response.data.errors.length > 0) {
					_this.errors = response.data.errors
					loader.hide()
					return;
				}
				_this.items = response.data.items.map(item => {
					if(typeof item.id === 'object') item.id = 0
					return item
				});

				_this.items.sort((a, b) => (a.name > b.name) ? 1 : -1);

				_this.errors = []
				if(response.data.items.length > 0) _this.showStepTwo = true;
				_this.users = response.data.users
				_this.users.unshift({
					id: 0,
					name: '',
					last_name: '',
				})
				_this.date = response.data.date
				_this.filename = response.data.filename
				loader.hide();
			}).catch(function(e){
				loader.hide()
				alert(e)
			})

		},



	}
}
</script>

<style lang="scss">
.con {
    padding: 15px;
}
p.heading {
font-size: 1.25rem;
    color: #007bff;
    font-weight: 700;
    margin-bottom: 20px;
}

.custom-file {
    height: 80px;
    .custom-file-input ~ .custom-file-label::after {
        content: 'Обзор';
    }
    .custom-file-label{
        height: 80px;
        &::after {
            height: 100%;
        }
    }
}

.b-tooltip {
    min-width: 300px;
    text-align: left  !important;
}
.cb  {
    background: #f1f1f1;
    padding: 3px 0;
    width: 37px;
    height: 37px;
    border-radius: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    input{
        width: 30px;
        height: 20px;
        display: block;
    }
}
.p {
    margin-bottom: 0;
    line-height: 33px;
}
.border-b {
    border-bottom: 1px solid #e6e6e6;
}
</style>
