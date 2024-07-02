import { defineStore } from 'pinia';
import axios from 'axios';
export const useCurrentFetchStore = defineStore('currentFetch', {
	state: () => ({
		current: '',
	}),
	actions: {
		currentFetch() {
			axios.get('/portal/current').then((res) => {
				this.current = res.data.data.tenant_id;
			});
		},
	},
});
