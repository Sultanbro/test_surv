export default {
	data(){
		return {
			transfaredInfo: '',
			transfaredLoadingText: '<i class="fas fa-spinner fa-pulse"></i>',
			isTransfaredPopup: false,
		}
	},
	computed: {
		transfers(){
			return this.items.reduce((result, item) => {
				result[item.id] = this.parseTransfers(item.group_users)
				return result
			}, {})
		},
	},
	methods: {
		async loadTransfers(item){
			const id = item.id || item.user_id
			try {
				const {data} = await this.axios('/timetracking/salaries/get-transfers', { params: {id} })
				/* eslint-disable require-atomic-updates */
				item.transfaredInfo = this.parseTransfers(data || [])
				/* eslint-enable require-atomic-updates */
				const index = this.items.findIndex(itm => itm.id === id)
				this.$set(this.items, index, {
					...item,
				})
				this.items = this.items.slice()
			}
			catch (error) {
				console.error(error)
				window.onerror && window.onerror(error)
			}
		},
		parseTransfers(transfers){
			if(!transfers) return null
			if(!transfers.length) return null
			for(let i = 0; i < transfers.length; ++i){
				const item = transfers[i]
				const prev = transfers[i - 1]
				if(prev && prev.group_id === item.group_id) continue
			}
			return transfers.map(item => {
				const date = this.$moment(item.to).format('DD.MM.YYYY')
				return `<div class="py-1">${date} из ${item.profile_group.name}</div>`
			})
		},
		showTransfaredPopup($event, item){
			if(!item) return
			const id = item.id || item.user_id
			if(!item.transfaredInfo) this.loadTransfers(item)

			const currentState = item.isTransfaredPopup
			setTimeout(() => {
				item.isTransfaredPopup = !currentState
				const index = this.items.findIndex(itm => itm.id === id)
				this.$set(this.items, index, {
					...item,
				})
			}, 100)
		},
		hideTransfaredPopup(){
			this.items.forEach(item => {
				item.isTransfaredPopup = false
			})
			this.items = this.items.slice()
		},
	},
}
