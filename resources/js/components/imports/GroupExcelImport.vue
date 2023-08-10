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
							class="ml-2"
						>
							<i class="fa fa-info" />
						</b-button>
					</p>

					<b-tooltip
						target="btn"
						placement="bottom"
					>
						<div>
							<div>Excel файл для импорта должен содержать в первом ряду поля:</div>
							<div>Имя сотрудника</div>
							<div>Фамилия сотрудника</div>
							<div>Длительность разговора</div>
							<div>Дата и время создания</div>
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


		<div
			v-if="!showStepTwo"
			class="mt-4"
		>
			<h5>Примечания</h5>
			<p>1. В табель можно импортировать только тех сотрудников, которые в должности "Оператор"</p>
			<p>
				2. Если у оператора в табели уже есть какая-то цифра, то есть уже отмечено какое-то количество часов,
				а в файле импорта нет по нему данных, то в табели за импортируемую дату обнулятся часы: так как у него в файле импорта нет минут разговора
			</p>
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
				<div class="col-md-5">
					<p>Сотрудник в Excel</p>
				</div>
				<div class="col-md-5">
					<p>Сотрудник в табели</p>
				</div>
				<div class="col-md-2">
					<p>Время</p>
				</div>
			</div>
			<div
				v-for="item in items"
				:key="item.name"
				class="row"
			>
				<div class="col-md-5">
					<input
						type="text"
						disabled
						:value="item.name"
						class="form-control"
					>
				</div>

				<div class="col-md-5">
					<select
						v-model="item.id"
						class="form-control"
					>
						<option
							v-for="user in users"
							:key="user.id"
							:value="user.id"
						>
							{{ user.name }} {{ user.last_name }} ID {{ user.id }}
						</option>
					</select>
				</div>
				<div class="col-md-2">
					<p class="p">
						{{ Number(item.hours) }} часов
					</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 mt-2">
					<b-button
						variant="primary"
						@click="saveConnects"
					>
						<i class="fa fa-save" /> Сохранить за {{ date }}
					</b-button>
				</div>
			</div>
		</div>
	</div>
</template>


<script>
export default {
	name: 'GroupExcelImport',
	props: {
		status: {
			type: String,
		},
		group_id: {
			type: Number,
		}
	},
	data() {
		return {
			message: null,
			activebtn: null,
			file: undefined,
			items: [],
			date: null,
			filename: '',
			showStepTwo: false,
			errors: [],
		}
	},
	created() {

	},

	methods: {
		saveConnects() {
			let loader = this.$loading.show();
			this.axios.post('/timetracking/settings/groups/importexcel/save', {
				date: this.date,
				items: this.items,
				filename: this.filename,
				group: this.group_id,
			}).then(() => {
				this.$toast.info('Сохранено');
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

			var _this = this;

			this.axios.post( '/timetracking/settings/groups/importexcel', formData, {
				headers: {
					'Content-Type': 'multipart/form-data'
				}
			}).then(function(response){
				if(response.data.errors.length > 0) {
					_this.errors = response.data.errors
					loader.hide()
					return;
				}
				_this.items = response.data.items;

				_this.items.sort((a, b) => (a.name > b.name) ? 1 : -1);

				_this.errors = []
				if(response.data.items.length > 0) _this.showStepTwo = true;
				_this.users = response.data.users
				_this.users.push({
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
</style>
