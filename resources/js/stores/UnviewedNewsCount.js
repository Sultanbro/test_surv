import { defineStore } from 'pinia'
import axios from 'axios';

export const useUnviewedNewsStore = defineStore('unviewedNews', {
	state: () => ({
		unviewedNewsCount: null,
		unviewedNewsTimer: null
	}),
	actions: {
		getUnviewedNewsCount() {
			axios.get('/news/count-unviewed')
				.then(res => this.unviewedNewsCount = res.data.count)
				.catch(err => console.error(err));
		},
		startAutoCheck() {
			if(this.unviewedNewsTimer) return;
			this.unviewedNewsTimer = setInterval(() => {
				this.getUnviewedNewsCount()
			}, 300000);
		},
		stopAutoCheck() {
			clearInterval(this.unviewedNewsTimer);
			this.unviewedNewsTimer = null;
		}
	}
})
