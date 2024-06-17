<template>
	<div
		class="faq-list-content"
		:class="{ opened: hasActive || !parentId, nest: nest }"
	>
		<div
			v-for="item in list"
			:key="item.id"
			class="faq-list-item"
		>
			<p
				class="faq-list-item-title"
				:class="{ active: item.id === activeItemId, parent: item.children }"
				@click="onSelect(item)"
			>
				{{ item.title }}
				<i
					v-if="item.children.length"
					:class="
						item.id === activeItemId ? 'fa fa-chevron-up' : 'fa fa-chevron-down'
					"
				/>
			</p>
			<FaqList
				v-if="item.children"
				:list="item.children"
				:nest="true"
				:active="active"
				:parent-id="item.id"
				@select="$emit('select', $event)"
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
			default: () => [],
		},
		nest: {
			type: Boolean,
			default: false,
		},
		active: {
			type: Object,
			default: null,
		},
		parentId: {
			type: Number,
			default: 0,
		},
		isOpen: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			itemId: null,
			openedIndex: null,
		};
	},
	computed: {
		activeItemId() {
			return this.active?.id || 0;
		},
		hasActive() {
			if (!this.active) return false;
			if (this.active.id === this.parentId) return true;
			return this.findActive(this.list);
		},
	},
	methods: {
		findActive(list) {
			for (let i = 0; i < list.length; ++i) {
				if (list[i].id === this.active.id || this.findActive(list[i].children))
					return true;
			}
			return false;
		},
		onSelect(item) {
			this.$emit('select', item);
		},
	},
};
</script>

<style lang="scss">
.faq-list {
	width: 350px;
	min-width: 350px;
	height: calc(100vh - 130px);
	overflow: auto;
	background-color: #ecf0f9;
	padding: 0 0 0 20px;
	&-content {
		display: none;
		&.opened {
			display: block;
		}
		&.nest {
			padding-left: 20px;
		}
	}
	&-item {
		margin-top: 6px;
		&-title {
			height: 40px;
			padding: 0 20px;
			display: flex;
			align-items: center;
			justify-content: space-between;
			cursor: pointer;
			font-size: 16px;
			border-radius: 20px 0 0 20px;
			i {
				color: #999;
				font-size: 12px;
			}

			&:hover {
				background-color: #e2e5ee;
			}
			&.active {
				background-color: rgba(96, 142, 233, 0.2);
				color: #333333;
				i {
					color: #fff;
				}
				&.parent {
					background-color: #608ee9;
					color: #fff;
				}
			}
		}
	}
}
</style>
