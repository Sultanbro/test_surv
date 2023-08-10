<template>
	<div v-if="positions">
		<b-row class="align-items-center">
			<b-col
				cols="12"
				lg="3"
				md="4"
			>
				<b-form-group v-if="data.length">
					<multiselect
						ref="positionMultiselect"
						v-model="activebtn"
						:options="data"
						placeholder="Выберите должность"
						track-by="position"
						label="position"
						@select="selectPosition"
					>
						<template slot="afterList">
							<li class="multiselect-add-li">
								<span
									class="multiselect-add-btn"
									@click="addNewPosition"
								>Добавить новую должность</span>
							</li>
						</template>
					</multiselect>
				</b-form-group>
				<button
					v-else
					class="btn btn-success"
					@click="addNewPosition"
				>
					Добавить новую должность
				</button>
			</b-col>
		</b-row>

		<template v-if="activebtn != null || addNew">
			<hr class="my-4">
			<h4
				v-if="addNew"
				class="position-title-new"
			>
				Создание новой должности
			</h4>
			<b-row class="align-items-center mt-4">
				<b-col
					cols="12"
					md="4"
				>
					<b-form-group
						label="Название должности"
						class="add-grade"
					>
						<b-form-input
							v-model="new_position"
							type="text"
						/>
					</b-form-group>
				</b-col>
			</b-row>
			<b-row class="align-items-center mt-4">
				<b-col
					cols="12"
					md="4"
				>
					<b-form-group>
						<b-form-checkbox
							v-model="isHead"
							:value="1"
							:unchecked-value="0"
							switch
						>
							Руководящая должность
							<img
								v-b-popover.hover.right="'Сотрудника с такой должностью можно будет назначить руководителем отдела'"
								src="/images/dist/profit-info.svg"
								class="img-info"
							>
						</b-form-checkbox>
					</b-form-group>
				</b-col>
			</b-row>
			<b-row class="align-items-center">
				<b-col
					cols="12"
					md="4"
				>
					<b-form-group label="Сумма индексации">
						<b-form-input
							v-if="indexation"
							v-model="sum"
							type="text"
							class="form-control group-select"
						/>
						<b-form-input
							v-else
							v-model="sum"
							type="text"
							class="form-control group-select"
							disabled
						/>
					</b-form-group>
				</b-col>
				<b-col
					cols="12"
					md="4"
				>
					<b-form-group class="mt-5">
						<b-form-checkbox
							v-model="indexation"
							:value="1"
							:unchecked-value="0"
							switch
						>
							Индексация зарплаты
							<img
								v-b-popover.hover.right="'Каждые 90 дней оклад сотрудника будет увеличиваться сумму индексации'"
								src="/images/dist/profit-info.svg"
								class="img-info"
							>
						</b-form-checkbox>
					</b-form-group>
				</b-col>
			</b-row>
			<div class="card position-card mt-4">
				<div class="card-header">
					<b-form-checkbox
						v-model="desc.show"
						:value="1"
						switch
						:unchecked-value="0"
					>
						Показывать в профиле
						<img
							v-b-popover.hover.right="'Активировав эту функцию, у сотрудников данной должности в профиле будет показан данный блок'"
							src="/images/dist/profit-info.svg"
							class="img-info"
						>
					</b-form-checkbox>
				</div>
				<div class="card-body">
					<b-row>
						<b-col
							cols="12"
							lg="6"
						>
							<b-form-group label="Следующая ступень карьерного роста">
								<b-textarea v-model="desc.next_step" />
							</b-form-group>
						</b-col>
						<b-col
							cols="12"
							lg="6"
						>
							<b-form-group label="Требования к кандидату">
								<b-textarea v-model="desc.require" />
							</b-form-group>
						</b-col>
						<b-col
							cols="12"
							lg="6"
						>
							<b-form-group label="Что нужно делать">
								<b-textarea v-model="desc.actions" />
							</b-form-group>
						</b-col>
						<b-col
							cols="12"
							lg="6"
						>
							<b-form-group label="График работы">
								<b-textarea v-model="desc.time" />
							</b-form-group>
						</b-col>
						<b-col
							cols="12"
							lg="6"
						>
							<b-form-group label="Заработная плата">
								<b-textarea v-model="desc.salary" />
							</b-form-group>
						</b-col>
						<b-col
							cols="12"
							lg="6"
						>
							<b-form-group label="Нужные знания для перехода на следующую должность">
								<b-textarea v-model="desc.knowledge" />
							</b-form-group>
						</b-col>
					</b-row>
				</div>
			</div>
			<div class="text-right mt-3">
				<button
					class="btn btn-success mr-2"
					@click="savePosition"
				>
					Сохранить
				</button>
				<button
					v-if="!addNew"
					class="btn btn-danger mr-2"
					@click.stop="deletePosition"
				>
					<i
						class="fa fa-trash mr-2"
					/> Удалить
				</button>
			</div>
		</template>
	</div>
</template>

<script>
// если в БД таблица position пустая, то в props: ['positions'] прилетает пустой массив
// если есть, то прилетает объект, где ключи - id (число), а значение - position (название дложности)
export default {
	name: 'CompanyProfessions',
	props: ['positions'],
	data() {
		return {
			data: [],
			new_position: '',
			addNew: false,
			position_id: 0,
			indexation: 0,
			sum: 0,
			activebtn: null,
			isHead: false,
			desc: {
				require: '',
				actions: '',
				time: '',
				salary:'',
				knowledge:'',
				next_step:'',
				show: 0
			}
		}
	},
	watch:{
		positions(value) {
			//конвертирую прилетевший объект в массив для работы vue-multiselect
			// если данных нет, то в this.data будет пустой массив
			Object.keys(value).forEach(item => {
				this.data.push({
					id: item,
					position: value[item]
				})
			})
		}
	},
	methods: {
		resetState(){
			this.new_position = '';
			this.position_id = 0;
			this.indexation = 0;
			this.sum = 0;
			this.activebtn = null;
			this.desc = {
				require: '',
				actions: '',
				time: '',
				salary: '',
				knowledge: '',
				next_step: '',
				show: 0
			}
		},
		addNewPosition() {
			if(this.$refs.positionMultiselect){
				this.$refs.positionMultiselect.toggle();
			}
			this.addNew = true;
			this.resetState();
		},
		selectPosition(value) {
			this.addNew = false
			this.axios.post('/timetracking/settings/positions/get-new', {
				name: value.id,
			}).then(response => {
				//this.$toast.info('Добавлена');
				const data = response.data?.data
				if(!data[0]) return console.error(response)
				this.new_position = data[0].position;
				this.position_id = data[0].id;
				this.indexation = data[0].indexation;
				this.isHead = data[0].is_head;
				this.sum = data[0].sum;
				this.desc = {
					require: data[0].require,
					actions: data[0].actions,
					time: data[0].time,
					salary: data[0].salary,
					knowledge: data[0].knowledge,
					next_step: data[0].next_step,
					show: data[0].show,
				}
			}).catch(error => {
				console.error(error.response)
			})
		},
		async addPosition() {
			const responseAdd = await this.axios.post('/timetracking/settings/positions/add-new', {position: this.new_position})
			if(responseAdd.data.code === 201) return this.$toast.error('Должность с таким названием уже существует!');

			const data = responseAdd.data.data;
			const dataPush = {
				id: data.id,
				position: data.position
			};
			this.position_id = data.id;
			this.new_position = data.position;

			this.activebtn = dataPush;
			this.data.push(dataPush);

			localStorage.setItem('event.updatePositions', JSON.stringify({command: 'addPosition'}))
			localStorage.removeItem('event.updatePositions')
		},
		async savePosition() {
			try{
				if(!this.new_position.length) return this.$toast.error('Введите нзвание должности!');
				if(this.addNew) await this.addPosition()

				const responseSave = await this.axios.post('/timetracking/settings/positions/save-new', {
					id: this.activebtn.id,
					new_name: this.new_position,
					indexation: this.indexation,
					sum: this.sum,
					desc: this.desc,
					is_head: this.isHead,
				})
				if(responseSave.data.status !== 200) return this.$toast.error('Упс! Что-то пошло не так');
				this.$toast.success(this.addNew ? 'Новая должность создана!' : 'Изменения сохранены');
				this.addNew = false

				localStorage.setItem('event.updatePositions', JSON.stringify({command: 'savePosition'}))
				localStorage.removeItem('event.updatePositions')
			}
			catch(error){
				console.error(error.message);
			}
		},
		async deletePosition() {
			if (confirm('Вы уверены что хотите удалить должность?')) {
				await this.axios.post('/timetracking/settings/positions/delete', {
					position: +this.activebtn.id,
				})
				this.$toast.info('Удалена');

				const ind = this.data.findIndex(item => item.id === this.activebtn.id);
				this.data.splice(ind, 1);
				this.addNew = false
				this.resetState();

				localStorage.setItem('event.updatePositions', JSON.stringify({command: 'deletePosition'}))
				localStorage.removeItem('event.updatePositions')
			}
		},

	}
}
</script>

<style scoped lang="scss">
.position-card{
		border: 1px solid #ddd;
		.card-header, .card-body{
				padding: 10px 20px;
		}
		textarea.form-control{
				padding: 5px 20px !important;
				min-height: 80px!important;
		}
}
.position-title-new{
		color: rgb(0 128 0);
		display: inline-block;
		padding: 5px 20px;
		border-radius: 6px;
		background-color: rgba(0,128,0,0.2)
}
.listprof {
	display: flex;
	margin-top: 20px;
}

.profitem {
	margin-right: 10px;
}

.add-grade {
	display: flex;
	max-width: 500px;
}.ant-tabs {
		overflow: visible;
}
.listprof {
		display: flex;
		flex-wrap: wrap;
		margin-top: 20px;
}

.profitem {
		margin-right: 10px;
		margin-bottom: 5px;
}

.add-grade {
		display: flex;
		max-width: 500px;
}

.dialerlist {
		display: flex;
		align-items: center;
		margin: 0;
		&.bg {
				background: #f1f1f1;
				padding-left: 15px;
		}
		.fl {
				flex: 1;
				display: flex;
				align-items: center;
		}
}

.group-select {
		border-radius: 0;
		max-width: 100%;
}

p.choose {
		line-height: 31px;
		margin-right: 15px;
}
span.before {
		padding: 0 10px;
}
.multiselect__tags {
		border-radius: 0 !important;
}
.multiselect__tag {
		display: block !important;
		max-width: max-content !important;
}
.blu .multiselect__tag {
		background: #017cff !important;
}
.pos-desc td {
	padding: 0;
}
.pos-desc td textarea {
			font-size: 12px;
		resize: none;
		overflow: auto;
		min-height: 350px;
}

@media(min-width: 1000px) {
		.multiselect__tags-wrap {
				flex-wrap: wrap;
				display: flex !important;
		}
		.multiselect__tag {
				flex: 0 0 49%;
				/* margin-left: 1% !important; */
				margin-right: 1% !important;
				max-width: 49% !important;
		}
}

@media(min-width: 1300px) {
		.multiselect__tag {
				flex: 0 0 32%;
				/* margin-left: 1% !important; */
				margin-right: 1% !important;
				max-width: 32% !important;
		}
}
@media(min-width: 1700px) {
		.multiselect__tag {
				flex: 0 0 24%;
				/* margin-left: 1% !important; */
				margin-right: 1% !important;
				max-width: 24% !important;
		}
}
.scscsc {
		margin-left: 15px;
}
.sssz button {
		margin-top: 1px;
}
.add-grade input {
		border-radius: 0;
}
.p {
	font-size: 14px;
	width: 200px;
	color: #5a5a5a;
}
</style>
