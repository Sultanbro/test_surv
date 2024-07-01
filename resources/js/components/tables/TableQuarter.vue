<template>
	<div class="quarter-page">
		<p class="call-norm p-0">
			{{ group }}
		</p>
		<span class="mb-2">При <span style="background-color:rgb(193 255 208)">активации</span> в профиле у сотрудника появятся условия получения квартальной Премии</span>

		<div class="d-flex mt-2">
			<table class="table table-bordered table-sm mark">
				<thead style="background: aliceblue;color: #0077e0;">
					<tr>
						<th style="width: 25%">
							Период
						</th>
						<th style="width: 25%">
							Сумма
						</th>
						<th>Комментарии</th>
					</tr>
				</thead>

				<tr
					v-for="(item, index) in arr"
					:key="index"
				>
					<th>
						<!--<span style="cursor: pointer" v-b-popover.hover.right.html="'Количество сотрудников на данный момент'" >-->
						<!--<label v-for="item.quarter">-->
						<span v-if="item.quarter == 1"> Первый </span>
						<span v-if="item.quarter == 2"> Второй </span>
						<span v-if="item.quarter == 3"> Третий </span>
						<span v-if="item.quarter == 4"> Четвертый </span>
						Квартал
						<!--</span>-->
						<!--</label>-->
						<input
							v-model="item.checked"
							style="position: absolute;margin-top:-6px;margin-left: 10px;cursor: pointer"
							type="checkbox"
						>
						<p
							v-if="item.quarter == 1"
							style="font-size: 14px"
						>
							Период с 01.01.{{ item.year }} до 31.03.{{ item.year }}
						</p>
						<p
							v-if="item.quarter == 2"
							style="font-size: 14px"
						>
							Период с 01.04.{{ item.year }} до 30.06.{{ item.year }}
						</p>
						<p
							v-if="item.quarter == 3"
							style="font-size: 14px"
						>
							Период с 01.07.{{ item.year }} до 30.09.{{ item.year }}
						</p>
						<p
							v-if="item.quarter == 4"
							style="font-size: 14px"
						>
							Период с 01.10.{{ item.year }} до 31.12.{{ item.year }}
						</p>
					</th>
					<th>
						<div v-if="item.checked">
							<input
								v-model="item.sum"
								type="number"
								class="form-control mb-1"
								value="0"
							>
						</div>
					</th>
					<th>
						<div v-if="item.checked">
							<textarea
								v-model="item.text"
								class="form-control"
								placeholder="Комментарии"
							/>
						</div>
					</th>
				</tr>
			</table>
		</div>
		<!--selectedQuarter-->
		<div class="col-12 p-0 row">
			<div class="col-6 p-0 ml-3">
				<div v-if="errors.length">
					<p
						v-for="error in errors"
						:key="error"
						style="color: #c75f5f"
					>
						{{ error }}
					</p>
				</div>
				<a
					id="selectedQuarter"
					style="color: white;text-align: center;border-radius: unset"
					class=" btn-block btn btn-success p-0 mt-3"
					@click="selectedQuarter"
				>Сохранить</a>
			</div>
		</div>
	</div>
</template>

<script>
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */

export default {
	name: 'TableQuarter',
	props: {
		group: {
			type: String,
			default: 'Ежегодный квартальный календарь',
		},
		activeuserid: {
			type: Number,
			default: 0,
		},
		is_admin: {
			type: Boolean,
			default: false,
		},
		user_id: {
			type: Number,
			default: 0,
		},
		type: {
			type: String,
			default: 'common',
		},
	},
	data() {
		return{
			arr:{
				sum:[],
				text:[],
				quartal:[],
			},
			errors: [],
			active: false,

		}
	},
	created(){


		// console.log(this.arr,'iks');

		// this.arr = this.selector();
		this.getQuartalBonuses();
	},
	methods:{
		getQuartalBonuses(){
			this.axios
				.post('/timetracking/quarter/get/quarter/', {
					user_id:this.user_id
				})
				.then(response => {
					this.arr = response.data[0];
				});

		},

		selectedQuarter() {
			this.errors = [];

			// console.log(this.errors,'imashev kairat');

			for (let i = 1;i <=4;i++){

				if (this.arr[i]['checked']){
					// console.log(this.arr[i]['text'],'pro');
					if (!this.arr[i]['text'].length > 0) {
						// this.errors.push('Заполните коментарии');
						if (i == 1){
							this.errors[i] = 'Заполните Комментарии Первого Квартала';
						}else if (i == 2){
							this.errors[i] = 'Заполните Комментарии Второго Квартала';
						}else if (i == 3){
							this.errors[i] = 'Заполните Комментарии Третьего Квартала';
						}else if (i == 4){
							this.errors[i] = 'Заполните Комментарии Четвертого Квартала';
						}


					}
				}
			}


			if (this.arr[1]['checked'] === false && this.arr[2]['checked'] === false && this.arr[3]['checked'] === false && this.arr[4]['checked'] === false){

				this.axios.post('/timetracking/quarter/delete', {
					arr:this.arr,
					user_id:this.user_id,
				}).then(response => {

					if (response.data.success == 1){
						this.$toast.success('Успешно удалено');
						document.getElementById('clickQuarter').click();
					}
				})
			}else{
				if (this.errors.length === 0){
					this.axios.post('/timetracking/quarter/store', {
						arr:this.arr,
						user_id:this.user_id,
					}).then(response => {
						if (response.data.success == 1){
							this.$toast.success('Изменения сохранены');
							document.getElementById('clickQuarter').click();
						}
					})
				}
			}





		},

	},
}


</script>

<style lang="scss" scoped>


    .selectedQuarter{
        display:none;
    }
    .mark label{
        cursor: pointer;
    }

    .quarter-page {
        .number_input {
            width: 100px;
            display: inline-block;
            text-align: center;

            &.form-control {
                padding-left: 23px;
            }
        }
        .form-control {
            padding: 2px 7px;
            font-size: 14px;
            border: 0;
        }
        .table-bordered {
            th {
                font-weight: 600;
            }

            td,
            th {
                border: 1px solid #dee2e6;
                vertical-align: middle;
                text-align: center;

                &.left {
                    text-align: left;
                }

                &.bold {
                    font-weight: 600;
                }

                &.mark {
                    background: aliceblue;
                    color: #0077e0;
                }
            }
        }

        .form-control {
            padding: 2px 7px;
            font-size: 14px;
            border: 1px solid #dee2e6;
        }
        .error {color:red}
        .call-norm {
            font-size: 18px;
            font-weight: 700;
            padding: 15px 0;
            color: #333;
            margin-bottom: 0;
        }

        .td-transparent {
            border-bottom-color: transparent !important;
            border-left-color: transparent !important;
        }
        .w-92 {width: 92px;}
        .mw {width: 1px;}
    }

</style>
