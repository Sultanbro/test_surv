import { defineStore } from 'pinia'

export const useAuthStore = defineStore('authStore', {
	state: () => ({
		mode: 'login'
	}),
	actions: {
		setMode(value) {
			this.mode = value
		},
		getMode() {
			return this.mode
		}
	},
})