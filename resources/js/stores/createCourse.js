import { defineStore } from 'pinia';

export const useCourseStore = defineStore('courseStore', {
	state: () => ({
		courses: [],
		materialBlocks: [],
	}),
	actions: {
		addCourse(newCourse) {
			const index = this.courses.findIndex((course) =>
				Object.keys(newCourse).some((key) => course[key] !== undefined)
			);
			if (index !== -1) {
				this.courses.splice(index, 1, newCourse);
			} else {
				this.courses.push(newCourse);
			}
		},
		addMaterialsBlock(newMaterials) {
			Array.prototype.push.apply(this.materialBlocks, newMaterials);
		},
	},
});
