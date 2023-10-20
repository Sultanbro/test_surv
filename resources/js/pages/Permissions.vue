<template>
	<div class="p-3 permissions PagePermissions">
		<h4 class="title d-flex">
			<div>
				Настройка доступов
				<span v-if="role != null">: Роли</span>
			</div>
		</h4>
		<b-form-group class="add-grade">
			<b-form-input
				v-if="role == null"
				v-model="searchText"
				type="text"
				placeholder="Поиск..."
			/>
			<button
				v-if="role == null"
				class="btn btn-success ml-4"
				@click="addItem"
			>
				Добавить
			</button>
		</b-form-group>


		<!-- Главная страница -->
		<section>
			<b-row>
				<b-col
					cols="12"
					md="9"
				>
					<div
						v-if="role == null"
						class="table-container"
					>
						<b-table-simple class="table-bordered custom-table-permissions">
							<b-thead>
								<b-tr>
									<b-th />
									<b-th>Роль</b-th>
									<b-th>
										Отделы
										<i
											v-b-popover.hover.right.html="'Выберите только те отделы, которые будет видеть сотрудник(-и)'"
											class="fa fa-info-circle ml-2"
											title="Доступ к отделам"
										/>
									</b-th>
									<b-th />
								</b-tr>
							</b-thead>
							<b-tbody>
								<PermissionItem
									v-for="(item, i) in filteredItems"
									:key="item.id"
									:item="item"
									:groups="groups"
									:users="users"
									:roles="roles"
									:access-dictionaries="accessDictionaries"
									@deleteItem="deleteItem(i)"
									@updateItem="updateItem(i)"
								/>
							</b-tbody>
						</b-table-simple>
					</div>

					<!-- Edit роль -->
					<div
						v-if="role"
						class="edit-role"
					>
						<div class="d-flex mb-3">
							<button
								class="btn btn-primary mr-2"
								@click="back"
							>
								Назад
							</button>
						</div>

						<input
							v-model="role.name"
							type="text"
							class="role-title form-control mb-3"
						>

						<div class="pages table-container">
							<div class="item d-flex contrast">
								<div class="name mr-3">
									Страница
								</div>
								<div class="check d-flex">
									Просмотр
								</div>
								<div class="check d-flex">
									Редактирование
								</div>
							</div>

							<template v-for="(page, index) in filteredPages">
								<div
									:key="index"
									class="item d-flex"
								>
									<div
										class="name mr-3"
										@click="page.opened = !page.opened"
									>
										{{ page.name }}
										<i
											v-if="page.opened && page.children.length > 0"
											class="fa fa-chevron-up"
										/>
										<i
											v-if="!page.opened && page.children.length > 0"
											class="fa fa-chevron-down"
										/>
									</div>
									<div class="check d-flex">
										<label
											v-if="!ignoreRules.includes(page.key + '_view')"
											class="mb-0 pointer"
										>
											<input
												v-model="role.perms[page.key + '_view']"
												class="pointer"
												type="checkbox"
												@change="checkParent(index, 'view')"
											>
										</label>
									</div>
									<div class="check d-flex">
										<label
											v-if="!ignoreRules.includes(page.key + '_edit')"
											class="mb-0 pointer"
										>
											<input
												v-model="role.perms[page.key + '_edit']"
												class="pointer"
												type="checkbox"
												@change="checkParent(index, 'edit')"
											>
										</label>
									</div>
								</div>

								<template v-if="page.children.length > 0">
									<template v-if="page.opened">
										<div
											v-for="children in filteredPageChildren(index)"
											:key="children.key"
											class="item d-flex child"
										>
											<div class="name mr-3">
												{{ children.name }}
											</div>
											<div class="check d-flex">
												<label
													v-if="!ignoreRules.includes(children.key + '_view')"
													class="mb-0 pointer"
												>
													<input
														v-model="role.perms[children.key + '_view']"
														class="pointer"
														type="checkbox"
														@change="checkChild(index, 'view')"
													>
												</label>
											</div>
											<div class="check d-flex">
												<label
													v-if="!ignoreRules.includes(children.key + '_edit')"
													class="mb-0 pointer"
												>
													<input
														v-model="role.perms[children.key + '_edit']"
														class="pointer"
														type="checkbox"
														@change="checkChild(index, 'edit')"
													>
												</label>
											</div>
										</div>
									</template>
								</template>
							</template>
						</div>

						<div class="mt-3">
							<button
								class="btn btn-success"
								@click="updateRole"
							>
								Сохранить
							</button>
						</div>
					</div>
				</b-col>
				<b-col
					cols="12"
					md="3"
				>
					<!-- Показать все роли -->
					<div class="roles-list">
						<div class="roles">
							<div class="contrast role-title">
								<b>Список ролей</b>
								<img
									v-b-popover.hover.bottom="'В Роли указывается что доступно для просмотра и Редактирования'"
									src="/images/dist/profit-info.svg"
									class="img-info"
								>
							</div>
							<div class="role-body">
								<div
									v-for="(item, i) in roles"
									:key="i"
									class="role-item"
								>
									<div
										class="name"
										@click="editRole(i)"
									>
										{{ item.name }}
									</div>
									<div class="actions">
										<span
											class="btn btn-primary btn-sm btn-icon"
											@click="editRole(i)"
										><i class="far fa-edit" /></span>
										<span
											class="btn btn-danger btn-sm btn-icon"
											@click="deleteRole(i)"
										><i class="fa fa-times" /></span>
									</div>
								</div>
							</div>
							<div class="role-footer">
								<button
									class="btn btn-success"
									@click="addRole"
								>
									Добавить роль
								</button>
							</div>
						</div>
					</div>
				</b-col>
			</b-row>
		</section>
	</div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'

import PermissionItem from '@/components/PermissionItem.vue'

const types = [
	'all',
	'users',
	'profile_groups',
	'positions',
]

export default {
	name: 'PagePermissions',
	components: {
		PermissionItem,
	},
	data() {
		return {
			role: null,
			searchText: '',
			// users: [], // all select
			groups: [], // all select
			items: [],
			roles: [],
			values: [],
			pages: [],
			permissions: [],
			showRoles: false,
			isBp: ['bp', 'test'].includes(window.location.hostname.split('.')[0]),
			ignoreRules: [
				'news_view',
				'structure_view',
			]
		};
	},
	computed: {
		...mapGetters([
			'users',
			'accessDictionaries',
		]),
		filteredPages(){
			return this.isBp ? this.pages : this.pages.filter(p => p.key !== 'faq');
		},
		filteredItems(){
			if(!this.searchText) return this.items
			const lowerText = this.searchText.toLowerCase()
			return this.items.filter(el => {
				if(el.targets){
					const inTarget = el.targets.findIndex(target => target.name.toLowerCase().indexOf(lowerText) > -1)
					if(inTarget > -1) return true
				}

				if(el.groups){
					const inGroups = el.groups.findIndex(target => target.name.toLowerCase().indexOf(lowerText) > -1)
					if(inGroups > -1) return true
				}

				if(el.roles){
					const inRoles = el.roles.findIndex(target => target.name.toLowerCase().indexOf(lowerText) > -1)
					if(inRoles > -1) return true
				}

				return false
			});
		}
	},
	created() {
		if(!this.users.length) this.loadCompany()
		this.fetchData();
	},
	mounted() {},
	methods: {
		...mapActions(['loadCompany']),
		filteredPageChildren(index){
			return this.isBp ? this.pages[index].children : this.pages[index].children.filter(c => c.key !== 'top' && c.key !== 'hr');
		},
		fetchData() {
			const loader = this.$loading.show();

			this.axios.get('/permissions/get').then((response) => {
				if(!response.data?.roles) this.$toast.error('Не удалось загрузить данные')

				// this.users = response.data.users || [];
				this.roles = response.data.roles || [];
				this.groups = response.data.groups || [];
				this.pages = response.data.pages || [];
				this.items = response.data.items || [];

				this.removeInactiveTargets()

				loader.hide();
			}).catch((error) => {
				loader.hide();
				console.error(error);
				this.$toast.error('Не удалось загрузить данные')
			});
		},

		addItem() {
			this.searchText = '';
			this.items.unshift(
				{
					id: 0,
					/* eslint-disable-next-line camelcase */
					groups_all: false,
					targets: [],
					roles: [],
					groups: []
				}
			);
		},

		deleteItem(i) {
			if(!confirm('Вы точно хотите удалить доступ этой цели?')) return false

			if(this.filteredItems[i].id == 0) {
				const index = this.items.findIndex(it => it.id == this.filteredItems[i].id);
				if(index != -1) this.items.splice(index, 1);
				return false;
			}

			let loader = this.$loading.show();
			this.axios.post( '/permissions/delete-target', {
				id: this.filteredItems[i].id
			}).then(() => {
				let index = this.items.findIndex(it => it.id == this.filteredItems[i].id);
				if(index != -1) this.items.splice(index, 1);

				loader.hide();
				this.$toast.success('Доступ удален');
			}).catch((error) => {
				loader.hide();
				console.error(error);
				this.$toast.success('Не удалось удалить доступ');
			});
		},

		updateItem(i, silent) {
			const loader = this.$loading.show();
			this.axios.post( '/permissions/update-target', {
				item: this.filteredItems[i],
			}).then((response) => {

				const index = this.items.findIndex(it => it.id == this.filteredItems[i].id);
				if(index != -1) this.items.id = response.data.id;

				loader.hide();
				if(!silent) this.$toast.success('Цели сохранены');
			}).catch((error) => {
				loader.hide();
				if(error.response?.data?.error === 'Duplicate entry for unique key.') {
					if(!silent) this.$toast.warning('Внесите изменения перед сохранением');
				}
				else{
					console.error(error);
					if(!silent) this.$toast.error('Не удалось сохранить доступ')
				}
			});
		},

		removeInactiveTargets(){
			if(!this.accessDictionaries || !this.users.length) return
			this.items.forEach((item, index) => {
				const hasEmpty = item.tergets.find(target => !~this.accessDictionaries[types[target.type]].findIndex(item => item.id === target.id))
				if(!hasEmpty) return
				console.warn('remove item', index, item)
				// item = {
				// 	...item,
				// 	targets: item.targets.filter(target => ~this.accessDictionaries[types[target.type]].findIndex(item => item.id === target.id))
				// }
				// this.updateItem(index, true)
			})
		},


		back() {
			this.showRoles = false;
			this.role = null;
		},

		editRole(i) {
			this.showEditRole = true;

			let role = this.roles[i];
			this.role = role;
			this.showRoles = false;
		},

		showRolesPage() {
			this.role = null;
			this.showRoles = true;
		},

		addRole() {
			this.role = {
				name: 'Test',
				id: null,
				perms: {}
			}
		},

		updateRole() {
			const loader = this.$loading.show();

			this.permissions = [];

			Object.keys(this.role.perms).forEach(key => {
				if(this.role.perms[key]) this.permissions.push(key)
			});

			this.axios.post('/permissions/update-role', {
				role: this.role,
				permissions: this.permissions
			}).then((response) => {
				if(this.role.id == null) {
					this.roles.push({
						id: response.data.id,
						name: response.data.name,
						perms: this.role.perms,
						permissions: []
					});
				}

				this.role = null;
				loader.hide();
				this.$toast.success('Роль сохранена');
			}).catch((error) => {
				loader.hide();
				console.error(error);
				this.$toast.error('Не удалось сохранить роль')
			});
		},

		deleteRole(i) {
			if(!confirm('Вы уверены удалить роль?')) return false

			if(this.roles[i].id == null) {
				this.roles.splice(i,1);
				return false;
			}

			const loader = this.$loading.show();
			this.axios.post( '/permissions/delete-role', {
				role: this.roles[i]
			}).then(() => {
				loader.hide();
				this.roles.splice(i,1);
				this.$toast.success('Роль удалена');
			}).catch((error) => {
				loader.hide();
				console.error(error);
				this.$toast.error('Не удалось удалить роль')
			});
		},

		checkParent(i, ability) {
			const page = this.pages[i];
			const checked = this.role.perms[page.key + '_' + ability];

			if(ability == 'edit') {
				this.role.perms[page.key + '_view'] = checked;
				page.children.forEach(c => {
					this.role.perms[c.key + '_view'] = checked;
					this.role.perms[c.key + '_edit'] = checked;
				});
			} else {
				page.children.forEach(c => {
					this.role.perms[c.key + '_view'] = checked;
				});
			}
		},

		checkChild(i, ability) {
			const page = this.pages[i];
			const checked = this.role.perms[page.key + '_' + ability];

			if(ability == 'edit') {
				this.role.perms[page.key + '_view'] = checked;
			}
		}
	},
};
</script>

<style lang="scss">
.permissions{
	.img-info{
		vertical-align: middle;
	}
}
.custom-table-permissions {
	table-layout: fixed;
	thead{
		tr{
			th,td {
				&:last-child{
					width: 100px;
				}
			}
		}
	}
}
</style>
