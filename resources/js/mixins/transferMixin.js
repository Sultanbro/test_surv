export default {
	data(){
		return {
			transfaredInfo: '',
			transfaredLoadingText: '<i class="fas fa-spinner fa-pulse"></i>',
			isTransfaredPopup: false,
		}
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
			if(!transfers.length) return '<data>Перевод: Не было переводов</data>'
			return '<data class="pb-1">Перевод: </data>' + transfers.map(item => {
				const date = this.$moment(item.to).format('DD.MM.YYYY')
				return `<div class="py-1">${date} из ${item.profile_group.name}</div>`
			}).join('')
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
