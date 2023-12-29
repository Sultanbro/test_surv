<template>
	<Draggable
		:id="'KBNavItems' + (parent ? parent.id : 0)"
		:key="key"
		class="KBNavItems dragArea"
		tag="ul"
		:handle="'.fa-bars'"
		:group="{ name: 'g1' }"
		:data-id="parent ? parent.id : 0"
		@end="onDrop"
	>
		<template v-for="item in sorted">
			<KBNavItem
				v-if="sectionsMode && !isEditMode ? !parent && item.canRead : (opened || !parent) && item.canRead"
				:key="`${parent ? parent.id : ''}-${item.id}`"
				:item="item"
				:parent="parent"
				:mode="mode"
				:sections-mode="sectionsMode"
				:class="['KBNavItems-item', {
					'KBNavItems-item_active': active === item.id
				}]"
				@open="toggleOpen"
				@add-page="addPage"
				@add-book="$emit('add-book', $event)"
				@remove-book="$emit('remove-book', $event)"
				@settings="$emit('settings', $event)"
			>
				<template
					v-if="item.children && item.children.length"
					#nested
				>
					<KBNavItems
						:items="item.children"
						:opened="item.opened"
						:mode="mode"
						:active="active"
						:parent="item"
						:sections-mode="sectionsMode"
						@show-page="showPage"
						@add-page="addPage"
						@add-book="$emit('add-book', $event)"
						@page-order="$emit('page-order', $event)"
						@remove-book="$emit('remove-book', $event)"
						@settings="$emit('settings', $event)"
					/>
				</template>
				<template
					v-else-if="isEditMode"
					#nested
				>
					<Draggable
						:id="'KBNavItems' + (item ? item.id : 0)"
						class="KBNavItems dragArea"
						tag="ul"
						:handle="'.fa-bars'"
						:group="{ name: 'g1' }"
						:data-id="item ? item.id : 0"
						@end="onDrop"
					/>
				</template>
			</KBNavItem>
		</template>
	</Draggable>
</template>

<script>
import Draggable from 'vuedraggable'
import KBNavItem from './KBNavItem.vue'

const KBNavItems = {
	name: 'KBNavItems',
	props: {
		items: {
			type: Array,
			required: true,
		},
		parent: {
			validator(value){
				return ['[object Null]', '[object Object]'].includes(Object.prototype.toString.call(value))
			},
			required: true,
		},
		opened: {
			type: Boolean,
		},
		mode: {
			type: String,
			default: 'read'
		},
		active: {
			type: Number,
			default: 0
		},
		sectionsMode: {
			type: Boolean
		}
	},
	data(){
		return {
			key: 0,
		}
	},
	computed: {
		isEditMode(){
			return this.mode === 'edit'
		},
		sorted(){
			return this.items
		}
	},
	watch: {
		items(){
			++this.key
		}
	},
	created(){},
	mounted(){},
	methods: {
		toggleOpen(item) {
			this.showPage(item, false, true)
			item.opened = !item.opened
		},
		showPage(page) {
			this.$emit('show-page', page)
		},
		addPage(item) {
			this.$emit('add-page', item)
		},
		onDrop(event){
			this.$emit('page-order', event)
			this.$nextTick(() => {
				this.$forceUpdate()
			})
		},
	},
}

KBNavItems.components = {
	Draggable,
	KBNavItem,
	KBNavItems,
}

export default KBNavItems
</script>

<style lang="scss">
.KBNavItems{
	&-item{
		&_active{
			color: #3361ff;
		}
	}
}
</style>
