<template>
	<div>
		<div class="b-flex">
			<div class="b-50">
				<div class="chhose">
					Выберите должность для ее настройки
				</div>

				<b-alert
					v-if="message!=null"
					variant="info"
				>
					{{ message }}
				</b-alert>

				<div class="listprof">
					<div
						v-for="(position,index) in positions"
						:key="position.id"
						class="profitem btn btn-primary"
						:class="{ 'activiti' : activebtn.position == position.position }"
						@click="positionselect(position, index)"
					>
						{{ position.position }}
					</div>
				</div>
			</div>
			<div class="b-50">
				<div v-if="activebtn.id != null">
					<div class="dialerlist">
						<div
							class="fl"
							style="flex-direction: column;"
						>
							<label class="typo__label">Выберите группы книг для должности <b>{{ activebtn.position }}</b></label>
							<multiselect
								v-model="value"
								:options="options"
								:multiple="true"
								:close-on-select="false"
								:clear-on-select="false"
								:preserve-search="true"
								placeholder="Выберите"
								label="name"
								track-by="name"
								:taggable="true"
								@tag="addTag"
							/>
						</div>
					</div>

					<button
						class="btn btn-success"
						@click="saveGroups"
					>
						Сохранить
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'GroupsToPositions',
	data() {
		return {
			message: null,
			activebtn: {
				id: null,
				position: ''
			},
			positions: [],
			new_position: '',
			value: [],
			options: [],
			position_id: 0
		}
	},
	created() {
		this.getPositions();
	},
	mounted() {
		//this.getPositions();
	},
	methods: {
		addTag(newTag) {
			const tag = {
				position: newTag,
				id: newTag
			}
			this.options.push(tag)
			this.value.push(tag)
		},
		messageoff() {
			setTimeout(() => {
				this.message = null
			}, 3000)
		},
		positionselect(position) {
			this.activebtn = {
				id: position.id,
				position: position.position
			}
			this.axios.post('/bp_books/position_groups', {
				position_id: position.id,
			})
				.then(response => {
					if (response.data) {
						this.value = response.data.groups
						this.position_id = response.data.position_id
					} else {
						this.value = []
					}

				})
		},
		saveGroups() {
			this.axios.post('/bp_books/position_groups/save', {
				position: this.activebtn.id,
				groups: this.value,
			})
				.then(() => {
					this.$toast.info('Успешно сохранено');
					this.messageoff()
				})
				.catch(error => {
					console.error(error.response)
					this.$toast.info(error.response);
				});
		},
		getPositions() {
			this.axios.post('/bp_books/position_groups', {})
				.then(response => {
					this.options = response.data.groups
					this.positions = response.data.positions
				})
		}
	}
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style><style lang="scss" scoped>
.listprof {
    display: flex;
    flex-wrap: wrap;
}

.profitem {
    margin-right: 10px;
}

.add-grade {
    display: flex;
    max-width: 500px;
}

.dialerlist {
    display: flex;
    align-items: center;
    margin: 15px 0;
    max-width: 100%;
    margin-top: 0;
}

.dialerlist .fl {
    flex: 1;
    display: flex;
    align-items: center;
}
.chhose {margin-bottom: 15px;}
</style>
