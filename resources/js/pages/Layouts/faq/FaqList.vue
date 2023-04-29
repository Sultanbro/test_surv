<template>
	<div
		class="faq-list-content"
		:class="{'opened' : isOpen,'nest' : nest}"
	>
		<div
			class="faq-list-item"
			v-for="(item, index) in list"
			:key="item.id"
		>
			<p
				class="faq-list-item-title"
				@click="openNested(index, item)"
				:class="{'active': item.id === activeItemId, 'parent' : item.children}"
			>
				{{ item.title }}
				<i
					class="fa fa-chevron-down"
					v-if="item.children && item.id !== activeItemId"
				/>
				<i
					class="fa fa-chevron-up"
					v-if="item.children && item.id === activeItemId"
				/>
			</p>
			<FaqList
				:list="item.children"
				v-if="item.children"
				:nest="true"
				:active-item-id="itemId"
				:is-open="isOpen && index === openedIndex"
				@update-active-id="updateActiveId"
				@item-clicked="handleItemClick"
			/>
		</div>
	</div>
</template>

<script>
export default {
	name: 'FaqList',
	props: {
		list: {
			type: Array,
			default: () => []
		},
		nest: {
			type: Boolean,
			default: false
		},
		activeItemId: {
			type: Number,
			default: 1
		},
		isOpen: {
			type: Boolean,
			default: false
		}
	},
	data() {
		return {
			itemId: null,
			openedIndex: null,
		}
	},
	methods: {
		openNested(index, item) {
			if (this.openedIndex === index) {
				this.openedIndex = null;
				return;
			}
			if (this.openedIndex !== null) {
				this.$emit('close-nested');
			}
			this.openedIndex = index;
			this.$emit('update-active-id', item.id);
			this.handleItemClick(item);
		},
		updateActiveId(itemId) {
			this.itemId = itemId;
		},
		handleItemClick(item) {
			this.$emit('item-clicked', item);
		}
	},
}
</script>
