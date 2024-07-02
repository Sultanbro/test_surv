<template>
	<span
		v-if="filtered.length"
		class="TransfersInfo"
	>
		<img
			src="/images/dist/profit-info.svg"
			class="TransfersInfo-i img-info"
			width="20"
			alt="info icon"
			tabindex="-1"
			@click="open"
		>
		<PopupMenu
			v-if="isOpen"
			v-click-outside="close"
			position="rightBottom"
			max-height="75px"
		>
			<div
				v-for="item, index in filtered"
				:key="index"
				class="TransfersInfo-item py-2"
			>
				{{ $moment(item.to).format('DD.MM.YYYY') }} из {{ groupsMap[item.group_id] ? groupsMap[item.group_id].name : 'Архивный отдел' }}
			</div>
		</PopupMenu>
	</span>
</template>

<script>
import PopupMenu from '@ui/PopupMenu.vue'

export default {
	name: 'TransfersInfo',
	components: {
		PopupMenu,
	},
	props: {
		transfers: {
			type: Array,
			default: () => []
		},
		groupId: {
			type: Number,
			default: 0
		},
		groups: {
			type: Array,
			default: () => []
		}
	},
	data(){
		return {
			isOpen: false
		}
	},
	computed: {
		onlyDrops(){
			return this.transfers.filter(trf => trf.status === 'drop')
		},
		filtered(){
			const result = []
			for(let i = 0; i < this.onlyDrops.length; ++i){
				const item = this.onlyDrops[i]
				const prev = this.onlyDrops[i - 1]
				const isLast = i === this.onlyDrops.length - 1
				if(prev && prev.group_id === item.group_id) continue
				if(isLast && item.group_id === this.groupId) continue
				result.push(this.onlyDrops[i])
			}
			return result
		},
		groupsMap(){
			return this.groups.reduce((result, group) => {
				result[group.id] = group
				return result
			}, {})
		},
	},
	watch: {},
	created(){},
	mounted(){},
	methods: {
		open(){
			if(this.isOpen){
				this.isOpen = false
				return
			}
			setTimeout(() => {
				this.isOpen = true
			}, 300)
		},
		close(){
			this.isOpen = false
		},
	},
}
</script>

<style lang="scss">
.TransfersInfo{
	position: relative;

}
</style>
