<template>
	<div>
		<p class="mb-4">
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
				@click="add"
				class="mt-4"
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
			this.items = [{'id':1,'name':'marina','info':'для марины какой'},{'id':4,'name':'abik','info':'тестовый'},{'id':5,'name':'jjkz080322fb','info':'ФБ мой кабинет'},{'id':10,'name':'bpjkz080722fb','info':'ФБ мой кабинет'},{'id':11,'name':'bpjkg120722fb','info':'ФБ мой кабинет'},{'id':12,'name':'juz261121fb','info':'ФБ мой кабинет'},{'id':13,'name':'ds1fb','info':'ФБ Диас'},{'id':14,'name':'ds2fb','info':'ФБ Диас'},{'id':15,'name':'ds3fb','info':'ФБ Диас'},{'id':16,'name':'ds4fb','info':'ФБ Диас'},{'id':17,'name':'ds5fb','info':'ФБ Диас'},{'id':18,'name':'ds6fb','info':'ФБ Диас'},{'id':19,'name':'vk','info':'ВК, разбросали посты'},{'id':20,'name':'fb','info':'ФБ, разбросали посты'},{'id':21,'name':'fb','info':'ФБ, разбросали посты'},{'id':22,'name':'tg','info':'телеграмм, разбросали посты'},{'id':23,'name':'ok','info':'одноклассники, разбросали посты'},{'id':24,'name':'ds7fb','info':'ФБ Диас'},{'id':25,'name':'ds8fb','info':'ФБ Диас'},{'id':26,'name':'ds9fb','info':'ФБ Диас'},{'id':27,'name':'ds12fb','info':'ФБ Диас'},{'id':28,'name':'ds11fb','info':'ФБ Диас'},{'id':29,'name':'ds10fb','info':'ФБ Диас'},{'id':30,'name':'ds13fb','info':'ФБ Диас'}]
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
