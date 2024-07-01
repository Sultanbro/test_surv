<template>
	<div>
		<p class="mb-4">
			<b>Генерация реферальных ссылок <i
				class="fa fa-redo-alt"
				@click="get"
			/></b>
		</p>


		<input
			:ref="'mylink'"
			type="text"
			class="hider"
		>

		<div v-if="loading">
			Загружаются...
		</div>

		<div v-else>
			<div
				v-for="(item, i) in items"
				:key="i"
				class="d-flex mb-3 gap-4"
			>
				<div class="ws-100 mr-2">
					<input v-model="item.name">
				</div>

				<div class="ws-050 mr-2">
					<input v-model="item.info">
				</div>



				<div class="d-flex">
					<i
						class="btn px-3 fa fa-copy RefLinker-green"
						@click="copyLink(i)"
					/>
					<i
						class="btn px-3 fa fa-save RefLinker-blue"
						@click="save(i)"
					/>
					<i
						class="btn px-3 fa fa-trash RefLinker-red"
						@click="deletes(i)"
					/>
				</div>
			</div>
			<JobtronButton
				class="mt-4"
				@click="add"
			>
				Добавить
			</JobtronButton>
		</div>
	</div>
</template>

<script>
import JobtronButton from '@ui/Button'
export default {
	name: 'RefLinker',
	components: {
		JobtronButton,
	},
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
				.catch(() => console.error('Error'))
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
				.catch(() => console.error('Error'))
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
				.catch(() => console.error('Error'))
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

<style lang="scss">
.RefLinker{
	&-red{
		color: red;
	}
	&-blue{
		color: blue;
	}
	&-green{
		color: green;
	}
}
.ws-100 {
  width:100px;
}
.ws-150 {
  min-width:150px;
}
</style>
