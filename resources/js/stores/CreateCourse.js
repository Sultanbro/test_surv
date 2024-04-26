import Vuex from 'vuex';

export default new Vuex.Store({
	state: {
		courses: [],
	},
	mutations: {
		addCourse(state, newCourse) {
			const index = state.courses.findIndex((course) =>
				Object.keys(newCourse).some((key) => course[key] !== undefined)
			);
			if (index !== -1) {
				state.courses.splice(index, 1, newCourse);
			} else {
				state.courses.push(newCourse);
			}
		},
	},
});
