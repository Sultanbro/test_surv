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
		<template v-for="item in items">
			<KBNavItem
				v-if="
					sectionsMode && !isEditMode
						? !parent && item.canRead
						: (opened || !parent) && item.canRead
				"
				:key="`${parent ? parent.id : ''}-${item.id}`"
				:item="item"
				:parent="parent"
				:mode="mode"
				:sections-mode="sectionsMode"
				:class="[
					'KBNavItems-item',
					{ 'KBNavItems-item_active': activeItem === item.id || active === item.id },
				]"
				@open="toggleOpen"
				@click="setActiveItem(item.id)"
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
			</KBNavItem>
		</template>
	</Draggable>
</template>

<script>
import Draggable from 'vuedraggable';
import KBNavItem from './KBNavItem.vue';

export default {
	name: 'KBNavItems',
	components: {
		Draggable,
		KBNavItem,
	},
	props: {
		items: {
			type: Array,
			required: true,
		},
		parent: {
			validator(value) {
				return ['[object Null]', '[object Object]'].includes(
					Object.prototype.toString.call(value)
				);
			},
			required: true,
		},
		opened: {
			type: Boolean,
		},
		mode: {
			type: String,
			default: 'read',
		},
		input: {
			type: String,
			default: '',
		},
		active: {
			type: Number,
			default: 0,
		},
		sectionsMode: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			key: 0,
			activeItem: null,
		};
	},
	computed: {
		isEditMode() {
			return this.mode === 'edit';
		},
	},
	watch: {
		items() {
			++this.key;
		},
	},
	methods: {
		toggleOpen(item) {
			this.$emit('update-input');
			item.opened = !item.opened;
			this.showPage(item, false, true);
			++this.key;
		},
		showPage(page) {
			this.$emit('show-page', page);
		},
		addPage(item) {
			this.$emit('add-page', item);
		},
		onDrop(event) {
			this.$emit('page-order', event);
			this.$nextTick(() => {
				this.$forceUpdate();
			});
		},
		setActiveItem(id) {
			this.activeItem = id;
		},
	},
};
</script>

<style lang="scss">
.KBNavItems {
	&-item {
		&_active {
			color: #3361ff;
		}
	}
}
</style>
