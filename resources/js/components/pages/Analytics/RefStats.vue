<template>
	<div class="RefStats">
		<div class="d-flex gap-4 ais mb-4">
			<RefStatsIndex
				:value="separateNumber(parseInt(userPrice) === userPrice ? userPrice : userPrice.toFixed(2))"
				label="Цена принятого сотрудника"
				unit="₸"
			/>
			<RefStatsIndex
				:value="separateNumber(parseInt(cvResultDealPercent) === cvResultDealPercent ? cvResultDealPercent : cvResultDealPercent.toFixed(2))"
				label="CV лид ➝ сделка"
				unit="%"
			/>
			<RefStatsIndex
				:value="separateNumber(parseInt(cvDealUserPercent) === cvDealUserPercent ? cvDealUserPercent : cvDealUserPercent.toFixed(2))"
				label="CV сделка ➝ сотрудник"
				unit="%"
			/>
			<RefStatsIndex
				:value="separateNumber(earned)"
				label="Заработано"
				unit="₸"
			/>
			<RefStatsIndex
				:value="separateNumber(paid)"
				label="Выплачено"
				unit="₸"
			/>
		</div>

		<RefStatsTable
			:items="users"
			:fields="[
				{key: 'switch', label: '', thClass: 'RefStats-switch'},
				...tableFields,
			]"
			@load-user="loadUser"
			@payment-click="showPaymentDialog"
		/>

		<b-modal
			v-model="paymentDialog.open"
			:title="paymentDialog.title"
			@ok="paymentSave"
		>
			<div class="d-flex aic gap-4 mb-4">
				<JobtronSwitch
					v-model="paymentDialog.paid"
				/>
				Оплачено
			</div>
			<div class="d-flex flex-column gap-4 mb-4">
				<div
					v-if="paymentDialog.date"
					class="RefStats-oldComment"
				>
					{{ paymentDialog.date }}
				</div>
				<div class="RefStats-oldComment">
					{{ paymentDialog.oldComment }}
				</div>
				<b-form-textarea
					v-model="paymentDialog.comment"
					placeholder="комментарий..."
					rows="3"
					max-rows="6"
					class="RefStats-comment"
				/>
			</div>
		</b-modal>
	</div>
</template>

<script>
import {
	tableFields,
	// getFakeReferer,
} from './helper'
import {
	separateNumber,
} from '@/composables/format'
import * as API from '@/stores/api/referral'

import JobtronSwitch from '@ui/Switch.vue'
import RefStatsIndex from './RefStatsIndex.vue'
import RefStatsTable from './RefStatsTable.vue'


const now = new Date()
export default {
	name: 'RefStats',
	components: {
		JobtronSwitch,
		RefStatsIndex,
		RefStatsTable,
	},
	props: {
		filters: {
			type: Object,
			default: () => ({
				year: now.getFullYear(),
				month: now.getMonth(),
			})
		}
	},
	data(){
		return {
			userPrice: 0,
			cvResultDealPercent: 0,
			cvDealUserPercent: 0,
			earned: 0,
			paid: 0,

			users: [],
			tableFields,

			loading: false,

			paymentDialog: {
				open: false,
				title: '',
				id: 0,
				key: '',
				paid: false,
				comment: '',
				date: '',
				sum: 0,
			},
		}
	},
	computed: {
		allReferals(){
			const referals = {}
			this.users.forEach(user => {
				user.users.forEach(ref1 => {
					referals[ref1.id] = ref1
					ref1.users.forEach(ref2 => {
						referals[ref2.id] = ref2
						ref2.users.forEach(ref3 => {
							referals[ref3.id] = ref3
						})
					})
				})
			})
			return referals
		},
		totalMonth(){
			return this.users.reduce((result, user) => result + user.month + user.monthRef, 0)
		},
		totalPaid(){
			return this.users.reduce((result, user) => result + user.monthPaid, 0)
		},
	},
	watch: {
		filters: {
			deep: true,
			handler(){
				this.fetchData()
			}
		}
	},
	mounted(){
		this.fetchData()
	},
	methods: {
		separateNumber,
		async fetchData(){
			if(this.loading) return
			this.loading = true
			const loader = this.$loading.show()

			try {
				const data = await API.referralStat(this.filters)
				this.userPrice = data.userPrice
				this.cvResultDealPercent = data.cvResultDealPercent
				this.cvDealUserPercent = data.cvDealUserPercent
				this.earned = data.earned
				this.paid = data.paid
				this.users = data.users
				// this.users = [
				// 	getFakeReferer(),
				// 	getFakeReferer(),
				// ]
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось получить статистику реферальной программы')
				window.onerror && window.onerror(error)
			}
			loader.hide()
			this.loading = false
			// this.users = [
			// 	getFakeReferer(),
			// 	getFakeReferer(),
			// ]
		},
		showPaymentDialog({item, field}){
			if(!this.$can('referal_edit')) return
			this.paymentDialog.title = `Редактирование оплаты ${item.title} ${field.key}`
			this.paymentDialog.id = item.id
			this.paymentDialog.key = field.key
			this.paymentDialog.paid = item[field.key].paid
			this.paymentDialog.transactionId = item[field.key].id
			this.paymentDialog.oldComment = item[field.key].comment || ''
			this.paymentDialog.comment = ''
			this.paymentDialog.date = item[field.key].day ? `за день стажировки ${this.$moment.utc([this.filters.year, this.filters.month, item[field.key].day]).format('DD.MM.YYYY')}` : ''
			this.paymentDialog.open = true
		},
		async paymentSave(){
			if(!this.paymentDialog.comment.trim() && this.paymentDialog.oldComment.trim()) return this.$toast.error('Добавьте комментарий')
			try {
				const comment = [this.paymentDialog.oldComment.trim(), this.paymentDialog.comment.trim()].join('\n')
				await API.referralStatPay(this.paymentDialog.id, {
					id: this.paymentDialog.transactionId,
					type: this.field2type('' + this.paymentDialog.key),
					paid: this.paymentDialog.paid,
					comment,
				})
				this.paymentDialog.open = false
				this.allReferals[this.paymentDialog.id][this.paymentDialog.key].paid = this.paymentDialog.paid
				this.allReferals[this.paymentDialog.id][this.paymentDialog.key].comment = comment
				this.$toast.success('Сохранено')
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не сохранено')
				window.onerror && window.onerror(error)
			}
		},
		field2type(field){
			if(field === 'attest') return 3
			if(field.substring(field.length - 4) === 'Week') return 2
			return 1
		},
		async loadUser(userId){
			const {users} = await API.referralUserStat(userId)
			const user = this.users.find(user => user.id === userId)

			if(!users?.length) return
			if(!user) return

			user.users = users[0].users || []
		}
	},
}
</script>

<style lang="scss">
.RefStats{
	overflow-x: auto;
	font-size: 14px;
	position: relative;

	&-switch{
		width: 32px;
	}
	&-oldComment{
		font-style: italic;
		white-space: pre-line;
		color: #777;
	}
	&-comment{
		border-radius: 4px;
	}
}
</style>
