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
							if(user.profile_group && user.profile_group.id === group.id){
								users.push(user.id)
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
	}
}
