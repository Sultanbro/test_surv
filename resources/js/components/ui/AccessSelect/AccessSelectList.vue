<template>
	<div class="AccessSelectList">
		<AccessSelectListItem
			v-for="item in itemsChecked"
			:key="(type ? type : item.type) + '' + item.id"
			:item="item"
			:type="type ? type : item.type"
			:position="type ? position : item.type === types.USER"
			:avatar="type ? avatar : item.type === types.USER"
			:search="lowerSearch"
			:checked="true"
			@change="$emit('change', $event)"
		/>
		<AccessSelectListItem
			v-for="item in itemsUnchecked"
			:key="(type ? type : item.type) + '' + item.id"
			:item="item"
			:type="type ? type : item.type"
			:position="type ? position : item.type === types.USER"
			:avatar="type ? avatar : item.type === types.USER"
			:search="lowerSearch"
			:checked="false"
			@change="$emit('change', $event)"
		/>
	</div>
</template>

<script>
import AccessSelectListItem from './AccessSelectListItem'
import {types} from './types.js'

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
	data(){
		return {
			types
		}
	},
	computed: {
		lowerSearch(){
			return this.search.toLowerCase()
		},
		itemsChecked(){
			return this.items.slice().filter(item => this.checked(item, this.type ? this.type : item.type))
		},
		itemsUnchecked(){
			return this.items.slice().filter(item => !this.checked(item, this.type ? this.type : item.type))
		},
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
