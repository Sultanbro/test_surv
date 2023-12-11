<template>
	<li
		class="KBNavItem"
		:class="[
			'KBNavItem_' + type,
			'KBNavItem_' + mode,
		]"
		:data-id="item.id"
	>
		<div class="KBNavItem-button">
			<div class="d-flex aic gap-2">
				<template v-if="type === 'book'">
					<i
						v-if="isEditMode && (parent ? parent.canEdit : true)"
						class="KBNavItem-mover fa fa-bars"
					/>
				</template>
				<template v-else>
					<div
						class="KBNavItem-handler"
					>
						<div
							v-if="isEditMode && (parent ? parent.canEdit : true)"
							class="KBNavItem-handlerIcon KBNavItem-mover"
						>
							<i class="fa fa-bars" />
						</div>
						<div
							class="KBNavItem-handlerIcon KBNavItem-shower"
							@click.stop="$emit('open', item)"
						>
							<i :class="handler" />
						</div>
					</div>
				</template>
				<div
					class="KBNavItem-name"
					@click.stop="$emit('open', item)"
				>
					{{ item.title }}
				</div>
			</div>
			<div
				v-if="isEditMode && item.canEdit"
				class="KBNavItem-actions"
			>
				<i
					v-if="!sectionsMode"
					class="KBNavItem-action fa fa-plus"
					title="Добавить страницу"
					@click.stop="$emit('add-page', item)"
				/>
				<i
					v-if="type === 'book'"
					title="Добавить раздел"
					class="KBNavItem-action far fa-plus-square"
					@click.stop="$emit('add-book', item)"
				/>
				<i
					v-if="type === 'book' && (parent ? parent.canEdit : true)"
					title="Удалить раздел"
					class="KBNavItem-action fa fa-trash"
					@click.stop="$emit('remove-book', item)"
				/>
				<i
					v-if="type === 'book' && (parent ? parent.canEdit : true)"
					title="Настройки"
					class="KBNavItem-action fa fa-cog"
					@click.stop="$emit('settings', item)"
				/>
			</div>
		</div>
		<slot name="nested" />
	</li>
</template>

<script>
export default {
	name: 'KBNavItem',
	components: {},
	props: {
		item: {
			type: Object,
			required: true
		},
		parent: {
			validator(value){
				return ['[object Null]', '[object Object]'].includes(Object.prototype.toString.call(value))
			},
			required: true,
		},
		mode: {
			type: String,
			default: 'read'
		},
		sectionsMode: {
			type: Boolean
		}
	},
	data(){
		return {}
	},
	computed: {
		type(){
			return !this.item.parent_id || this.item.is_category ? 'book' : 'page'
		},
		hasChildren(){
			return this.item.children && this.item.children.length
		},
		isEditMode(){
			return this.mode === 'edit'
		},
		handler(){
			if(this.hasChildren && this.item.opened) return 'fa fa-chevron-down'
			if(this.hasChildren) return 'fa fa-chevron-right'
			return 'fa fa-circle'
		}
	},
	watch: {},
	created(){},
	mounted(){},
	methods: {},
}
</script>

<style lang="scss">
.KBNavItem{
	color: #212529;

	&-button{
		position: relative;

		font-size: 13px;
		font-weight: 400;
		white-space: nowrap;
		text-overflow: ellipsis;

		overflow: hidden;
		cursor: pointer;
		&:hover{
			background-color: #f1f1f1;
			.KBNavItem{
				&-actions{
					display: flex;
				}
			}
		}
	}
	&-handler{
		display: inline-flex;
		align-items: center;
		justify-content: center;

		height: 28px;
		min-width: 25px;
		width: 25px;
		.KBNavItem{
			&-mover{
				display: none;
			}
		}
		.fa-circle{
			font-size: 5px;
		}
	}
	&-handlerIcon{
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 100%;
		height: 100%;
	}
	&-mover{
		color: #1db332 !important;
		cursor: move;
	}
	// &-shower{}
	&-name{
		flex: 1;
		padding: 8px 0;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	&-actions{
		display: none;

		position: absolute;
		top: 50%;
		right: 0;

		text-align: center;
		transform: translateY(-50%);
	}
	&-action{
		width: 27px;
		padding: 5px;
		margin-left: 5px;

		color: #333;
		background: #ddd;
		border-radius: 4px;
		cursor: pointer;

		&:hover{
			color: #007bff;
		}
	}

	.fa-plus-square{
		padding: 2px;
		font-size: 20px;
	}

	&_page{
		.KBNavItem{
			&-button{
				padding: 0;
			}
			&-name{
				padding: 6px 0;
			}
		}
	}

	&_edit{
		.KBNavItem{
			&-handler{
				&:hover{
					.KBNavItem{
						&-mover{
							display: flex;
						}
						&-shower{
							display: none;
						}
					}
				}
			}
		}
	}
}
</style>
