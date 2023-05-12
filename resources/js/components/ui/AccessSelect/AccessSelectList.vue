<template>
	<div class="AccessSelectList">
		<AccessSelectListItem
			v-for="item in items"
			:key="item.id"
			:item="item"
			:type="type ? type : item.type"
			:position="type ? position : item.type === 1"
			:avatar="type ? avatar : item.type === 1"
			:search="lowerSearch"
			:checked="checked(item, type ? type : item.type)"
			@change="$emit('change', $event)"
		/>
	</div>
</template>

<script>
import AccessSelectListItem from './AccessSelectListItem'
export default {
	name: 'AccessSelectList',
	components: {
		AccessSelectListItem,
	},
	props: {
		search: {
			type: String,
			default: ''
		},
		selected: {
			type: Array,
			default: () => []
		},
		items: {
			type: Array,
			default: () => []
		},
		avatar: {
			type: Boolean,
			default: false
		},
		position: {
			type: Boolean,
			default: false
		},
		type: {
			type: Number,
			default: 2
		},
	},
	computed: {
		lowerSearch(){
			return this.search.toLowerCase()
		}
	},
	methods: {
		checked(item, type) {
			return this.selected.some(el => {
				return el.id === item.id && el.type === type
			});
		},
	}
}
</script>

<style>
.AccessSelectList {
	display: flex;
	flex-direction: column;
	gap: 5px;

	padding: 2px 0;

	overflow-y: scroll;
}
</style>
