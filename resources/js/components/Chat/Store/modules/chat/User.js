import API from '../../API.vue';
import { useCompanyStore } from '@/stores/Company'

export default {
	actions: {
		async updateUser({commit, getters, dispatch}, user) {
			commit('setUser', user);
			if (!getters.initialized) {
				dispatch('init', true);
			}
		},
		async loadCompany({commit, getters}) {
			await API.fetchCompany(company => {
				const companyStore = useCompanyStore()
				commit('setUsers', company.users);
				commit('setProfileGroups', company.profile_groups.map(group => {
					return {
						...group,
						activeUsers: companyStore.dictionaries.users.reduce((users, user) => {
							// serchForUserGroups in news dictionaries
							if(user.deleted_at) return users
							if(user.id === getters.user.id) return users
							if(user.profile_group){
								user.profile_group.forEach(group => {
									if(user.profile_group.id === group.id){
										users.push(user.id)
									}
								});
							}

							return users
						}, [])
					}
				}));
				commit('setPositions', company.positions);
			});
		},
	},
	mutations: {
		setUser(state, user) {
			state.user = user;
		},
		setUsers(state, users) {
			state.users = users;
		},
		setProfileGroups(state, groups) {
			state.profile_groups = groups;
		},
		setPositions(state, positions) {
			state.positions = positions;
		},
	},
	state: {
		user: [],
		users: [],
		profile_groups: [],
		positions: [],
	},
	getters: {
		user: state => state.user,
		users: state => state.users,
		profileGroups: state => state.profile_groups,
		positions: state => state.positions,
		positionNames(state, getters){
			return getters.positions.reduce((result, pos) => {
				result[pos.id] = pos.position
				return result
			}, {})
		},
		accessDictionaries(state, getters){
			return {
				users: getters.users.reduce((users, user) => {
					if(user.deleted_at) return users
					if(!user.last_seen) return users
					users.push({
						id: user.id,
						name: `${user.name} ${user.last_name}`,
						avatar: `/users_img/${user.img_url}`,
						position: getters.positionNames[user.position_id]
					})
					return users
				}, []),
				profile_groups: getters.profileGroups.filter(group => group.active),
				positions: getters.positions.filter(pos => !pos.deleted_at).map(pos => ({
					id: pos.id,
					name: pos.position
				})),
			}
		},
	}
}
