<template>
	<div
		class="access-modal-bg"
		@click.self="toggleAccessModal(false, $event)"
	>
		<div class="access-modal">
			<div class="access-modal__search">
				<img
					class="news-icon"
					src="/icon/news/filter/search.svg"
					alt=""
				>
				<input
					v-model="accessSearch"
					type="text"
					class="access-modal__search-input"
					placeholder="Быстрый поиск"
				>
			</div>

			<div class="access-modal__tabs">
				<div
					:class="'access-modal__tab ' + (currentAccessTab === 1 ?'access-modal__tab--active' : '')"
					@click="changeAccessTab(1)"
				>
					Сотрудники
				</div>
				<div
					:class="'access-modal__tab ' + (currentAccessTab === 2 ?'access-modal__tab--active' : '')"
					@click="changeAccessTab(2)"
				>
					Отделы
				</div>
				<div
					:class="'access-modal__tab ' + (currentAccessTab === 3 ?'access-modal__tab--active' : '')"
					@click="changeAccessTab(3)"
				>
					Должности
				</div>
			</div>

			<div class="user-list">
				<div
					v-show="currentAccessTab === 1"
					class="user-list__container"
				>
					<div
						v-for="item in accessDictionaries.users"
						v-show="item.name ? item.name.toLowerCase().includes(accessSearch.toLowerCase()) : false"
						:key="item.id"
						class="user-item"
						@click="changeAccessList($event, item.id, item.name, 1, item.avatar)"
					>
						<img
							:src="item.avatar"
							class="user-item__avatar"
							alt=""
						>
						<div class="user-item__info">
							<div class="user-item__sub">
								{{ item.position_name }}
							</div>
							<div class="user-item__name">
								{{ item.name }}
							</div>
						</div>
						<label class="news-checkbox">
							<input
								type="checkbox"
								:checked="checked(item, 1) ? 'checked' : ''"
								@click="changeAccessList($event, item.id, item.name, 1, item.avatar)"
							>
							<span class="news-checkmark" />
						</label>
					</div>
				</div>
				<div
					v-show="currentAccessTab === 2"
					class="user-list__container"
				>
					<div
						v-for="item in accessDictionaries.profile_groups"
						v-show="item.name ? item.name.toLowerCase().includes(accessSearch.toLowerCase()) : false"
						:key="item.id"
						class="user-item"
						@click="changeAccessList($event, item.id, item.name, 2, item.avatar)"
					>
						<img
							:src="item.avatar"
							class="user-item__avatar"
							alt=""
						>
						<div class="user-item__info">
							<div class="user-item__name">
								{{ item.name }}
							</div>
						</div>
						<label class="news-checkbox">
							<input
								type="checkbox"
								:checked="checked(item, 2) ? 'checked' : ''"
								@click="changeAccessList($event, item.id, item.name, 2, item.avatar)"
							>
							<span class="news-checkmark" />
						</label>
					</div>
				</div>
				<div
					v-show="currentAccessTab === 3"
					class="user-list__container"
				>
					<div
						v-for="item in accessDictionaries.positions"
						v-show="item.position ? item.position.toLowerCase().includes(accessSearch.toLowerCase()) : false"
						:key="item.id"
						class="user-item"
						@click="changeAccessList($event, item.id, item.position, 3)"
					>
						<div class="user-item__info">
							<div class="user-item__name">
								{{ item.position }}
							</div>
						</div>
						<label class="news-checkbox">
							<input
								type="checkbox"
								:checked="checked(item, 3) ? 'checked' : ''"
								@click="changeAccessList($event, item.id, item.position, 3)"
							>
							<span class="news-checkmark" />
						</label>
					</div>
				</div>
			</div>

			<div class="access-modal__footer">
				<span class="access-modal__selected-count">
					{{ enumerate(accessCount, ['Добавлен', 'Добавлено', 'Добавлено']) + ' ' + accessCount + ' ' + enumerate(accessCount, ['элемент', 'элемента', 'элементов']) }}
				</span>
				<a
					class="access-modal__add-employee hover-pointer"
					@click="submitForm"
				>
					<!-- <img
						src="/icon/news/access-modal/plus-accent.svg"
						alt=""
					> -->
					Пригласить
				</a>
			</div>
		</div>
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';

export default {
	name: 'AddMemberModal',
	data() {
		return {
			title: 'Групповой чат',
			members: [],
			showAccessModal: {
				type: Boolean,
				default: false
			},
			accessSearch: '',
			currentAccessTab: 1,
			availableToEveryone: false,
			accessList: []
		};
	},
	computed: {
		...mapGetters([
			'contacts',
			'newChatContacts',
			'chat',
			'user',
			'users',
			'profileGroups',
			'positions',
		]),
		accessDictionaries() {
			return {
				users: this.users,
				profile_groups: this.profileGroups,
				positions: this.positions,
			}
		},
		accessCount() {
			return this.accessList.filter(item => item.type === 1).length;
		},
	},
	mounted() {
		if (!this.chat.private) {
			this.title = this.chat.title;
			this.accessList = this.chat.users.filter(user => user.id !== this.user.id).map(user => {
				return {
					id: user.id,
					name: user.name,
					avatar: user.avatar,
					type: 1
				}
			});
		}
		this.loadCompany();
	},
	methods: {
		...mapActions(['createChat', 'addMembers', 'removeMembers', 'loadCompany']),

		toggleAccessModal(value, event) {
			event.stopPropagation();
			this.showAccessModal = value;
			this.$emit('close');
		},
		changeAccessTab(tab) {
			this.currentAccessTab = tab
		},
		changeAccessList(event, id, name, type, avatar = null) {
			event.stopPropagation();

			let users = [];
			if (type === 2) {
				users = this.accessDictionaries.users.filter(user => user.profile_group_id === id);
			} else if (type === 3) {
				users = this.accessDictionaries.users.filter(user => user.position_id === id);
			}

			let index = this.accessList.findIndex(i => i.id === id)
			if (index === -1) {
				this.accessList.push({
					id: id,
					name: name,
					avatar: avatar,
					type: type
				});
				this.addUsers(users);
			} else {
				this.accessList.splice(index, 1);
				this.removeUsers(users);
			}
		},
		addUsers(users) {
			for (let user of users) {
				let index = this.accessList.findIndex(i => i.id === user.id && i.type === 1);
				if (index === -1) {
					this.accessList.push({
						id: user.id,
						name: user.name,
						avatar: user.avatar,
						type: 1
					});
				}
			}
		},
		removeUsers(users) {
			for (let user of users) {
				let index = this.accessList.findIndex(i => i.id === user.id && i.type === 1);
				if (index !== -1) {
					this.accessList.splice(index, 1);
				}
			}
		},
		checked(item, type) {
			return this.accessList && this.accessList.findIndex(accessItem => accessItem.id === item.id && accessItem.type === type) !== -1
		},
		enumerate(number, titles) {
			const cases = [2, 0, 1, 1, 1, 2];
			return titles[(number % 100 > 4 && number % 100 < 20) ? 2 : cases[(number % 10 < 5) ? number % 10 : 5]];
		},
		submitForm(e) {
			e.stopPropagation();

			let members = this.accessList.filter(item => item.type === 1);

			if (this.chat.private) {
				// title is a concatenation of first 3 members names
				let title = members.slice(0, 3).map(item => item.name).join(', ');
				this.createChat({
					title: title,
					description: '',
					members: members.map(member => member.id)
				});
			} else {
				// find diff between members and chat.users
				// add new members
				// remove old members
				this.accessList.push(this.user);
				let add_members = members.filter(member => !this.chat.users.find(user => user.id === member.id));
				let remove_members = members.filter(user => !this.accessList.find(member => member.id === user.id));

				if (add_members.length > 0) {
					this.addMembers(add_members);
				}
				if (remove_members.length > 0) {
					this.removeMembers(remove_members);
				}
			}
			this.$emit('close');
		},
	},
}
</script>

<style scoped></style>
