<template>
	<div>
		<p>
			<b>Генерация реферальных ссылок <i
				class="fa fa-redo-alt"
				@click="get"
			/></b>
		</p>


		<input
			type="text"
			:ref="'mylink'"
			class="hider"
		>

		<div v-if="loading">
			Загружаются...
		</div>

		<div v-else>
			<div
				class="d-flex mb-2"
				v-for="(item, i) in items"
				:key="i"
			>
				<div class="ws-100 mr-2">
					<input v-model="item.name">
				</div>

				<div class="ws-050 mr-2">
					<input v-model="item.info">
				</div>



				<div class="d-flex">
					<i
						class="btn px-1 fa fa-copy"
						@click="copyLink(i)"
					/>
					<i
						class="btn px-1 fa fa-save"
						@click="save(i)"
					/>
					<i
						class="btn px-1 fa fa-trash"
						@click="deletes(i)"
					/>
				</div>
			</div>
			<button
				class="btn btn-primary rounded"
				@click="add"
			>
				Добавить
			</button>
		</div>
	</div>
</template>
<script>
export default {
	name: 'RefLinker',
	props: {},
	data() {
		return {
			items: [],
			loading: true,
		}
	},
	created() {
		this.get()
	},
	methods: {
		get() {
			this.loading = true;
			this.axios.get('/hr/ref-links')
				.then(response => {
					this.items = response.data
					this.loading = false;
				})
				.catch(() => console.log('Error'))
		},

		save(i) {
			this.axios.post('/hr/ref-links/save', {
				id: this.items[i].id,
				name: this.items[i].name,
				info: this.items[i].info,
				method: 'save'
			})
				.then(response => {

					this.items[i].id = response.data;
					this.$toast.success('Сохранено')
				})
				.catch(() => console.log('Error'))
		},

		deletes(i) {
			if(!confirm('Вы уверены?')) {
				return ;
			}
			this.axios.post('/hr/ref-links/save', {
				id: this.items[i].id,
				name: this.items[i].name,
				info: this.items[i].info,
				method: 'delete'
			})
				.then(() => {
					this.items.splice(i,1)
					this.$toast.success('Удалено')
				})
				.catch(() => console.log('Error'))
		},

		add() {
			this.items.push({
				id: 0,
				name: '',
				info: ''
			});
		},

		copyLink(i) {
			var Url = this.$refs.mylink;
			Url.value = 'http://job.bpartners.kz/' + this.items[i].name;

			Url.select();
			document.execCommand('copy');

			this.$toast.info('Ссылка на страницу скопирована!');
		},

	},
};
</script>
<style>
.ws-100 {
  width:100px;
}
.ws-150 {
  min-width:150px;
}
</style>
