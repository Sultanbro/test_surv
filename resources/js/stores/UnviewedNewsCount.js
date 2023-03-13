import { defineStore } from 'pinia'
import axios from 'axios';

export const useUnviewedNewsStore = defineStore('unviewedNews', {
	state: () => ({
		unviewedNewsCount: null,
	}),
	actions: {
		getUnviewedNewsCount() {
			axios.get('/news/count-unviewed')
				.then(res => this.unviewedNewsCount = res.data.count)
				.catch(err => console.log(err))
		}
	}
})
