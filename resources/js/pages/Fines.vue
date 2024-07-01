<template>
	<div>
		<b-row>
			<b-col
				cols="12"
				lg="8"
			>
				<div class="table-container">
					<b-table-simple class="table-striped table-hover">
						<b-thead>
							<b-tr>
								<b-th class="text-left">
									Система депремирования
								</b-th>
								<b-th class="text-left">
									Сумма
								</b-th>
								<b-th />
							</b-tr>
						</b-thead>
						<b-tbody>
							<b-tr
								v-for="(fine, index) in fines"
								:key="index"
							>
								<b-td>
									<b-textarea
										v-model.trim="fine.name"
										class="in-table-textarea"
									/>
								</b-td>
								<b-td>
									<b-form-input
										v-model.trim="fine.penalty_amount"
										type="number"
									/>
								</b-td>
								<b-td>
									<button
										type="button"
										:data-id="fine.id"
										title="Удалить штраф"
										class="btn btn-danger"
										@click="deleteFine(index)"
									>
										<i
											class="fa fa-trash"
											aria-hidden="true"
										/>
									</button>
								</b-td>
							</b-tr>
						</b-tbody>
					</b-table-simple>
				</div>
			</b-col>
		</b-row>
		<div class="row">
			<div class="col mt-2 mb-4">
				<button
					type="button"
					title="Добавить новый штраф"
					class="btn btn-primary"
					@click="addFine()"
				>
					Добавить
				</button>
				<button
					type="button"
					title="Сохранить изменения в штрафах"
					class="btn btn-success"
					@click="saveFines()"
				>
					Сохранить <img
						v-if="preloaderShow"
						src="/images/preloader.gif"
						width="20"
						height="20"
					>
				</button>
			</div>
		</div>
		<div
			v-if="alert.show"
			class="row"
		>
			<div class="col-8">
				<div
					class="alert mt-2 mb-3"
					:class="alert.class"
				>
					{{ alert.message }}
					<button
						type="button"
						class="close mb-3"
						@click="closeAlert()"
					>
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'PageFines',
	data() {
		return {
			fines: [],
			savedfines: [],
			alert: {
				show: false,
				message: '',
				class: 'alert-danger'
			},
			preloaderShow: false
		}
	},
	created() {
		this.getFines();
	},
	methods:{
		getFines() {
			this.axios.get('/timetracking/fine', {}).then(response => {
				this.savedfines = JSON.parse(JSON.stringify(response.data.fines))
				this.fines = response.data.fines

			}).catch(error => {
				console.error(error)
			});
		},


		addFine() {
			this.fines.push({
				id: 0,
				name: 'Новый штраф',
				/* eslint-disable-next-line camelcase */
				penalty_amount: 0
			});
		},


		saveFines() {
			if (this.validateFields() && !this.preloaderShow) {
				this.alert.show = false;
				let newfines = this.getNewFines()
				let deletedfines = this.getDeletedFines()
				let editedfines = this.getEditedFines()
				this.preloaderShow = true;
				this.axios.put('/timetracking/fine', {
					newfines,
					editedfines,
					deletedfines
				}).then(response => {
					this.alert.message = response.data.message
					this.alert.class = 'alert-success'
					this.alert.show = true
					this.getFines()
					this.preloaderShow = false;
				}).catch(error => {
					console.error(error)
				});
			}
		},

		deleteFine(index) {
			this.fines.splice(index, 1)
		},
		closeAlert() {
			this.alert.show = false;
		},
		validateFields() {
			let result = true;
			this.fines.forEach((element) => {
				if (!element.name || !element.penalty_amount) {
					this.alert.message = 'Заполните все поля!'
					this.alert.show = true
					this.alert.class = 'alert-danger'
					result = false
					return false
				}
			})

			return result
		},
		getNewFines() {
			let newFines = [];
			this.fines.forEach((element) => {
				if (element.id === 0) {
					newFines.push(element)
				}
			})

			return newFines
		},
		getDeletedFines() {
			let deletedFines = [];
			this.savedfines.forEach((element) => {
				if (!this.fines.find(fine => fine.id === element.id)) {
					deletedFines.push(element.id)
				}
			})

			return deletedFines
		},
		getEditedFines() {
			let editedFines = [];
			this.savedfines.forEach((element) => {
				let foundFine = this.fines.find(fine => fine.id === element.id)
				if (foundFine && (foundFine.name !== element.name || foundFine.penalty_amount !== element.penalty_amount)) {
					editedFines.push(foundFine)
				}
			})

			return editedFines
		}
	}
}
</script>

<style lang="scss" scoped>
    .table-container{
        .in-table-textarea{
            padding: 5px 20px!important;
            min-width: 500px;
        }
    }
</style>
