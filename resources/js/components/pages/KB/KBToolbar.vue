<template>
	<nav class="KBToolbar">
		<div class="KBToolbar-breadcrumbs">
			<router-link
				to="/kb"
				class="KBToolbar-breadcrumb"
			>
				База знаний
			</router-link>
			<template v-for="breadcrumb, index in breadcrumbs">
				<i
					:key="index"
					class="fa fa-chevron-right"
				/>
				<router-link
					:key="'l' + index"
					:to="breadcrumb.link"
					class="KBToolbar-breadcrumb"
				>
					{{ breadcrumb.title }}
				</router-link>
			</template>
		</div>
		<div class="KBToolbar-actions">
			<button
				v-if="canEdit"
				v-b-popover.hover.top="'Включить редактирование Базы знаний'"
				class="KBToolbar-action"
				:class="{'KBToolbar-action_active': isEdit}"
				@click="$emit('mode', isEdit ? 'read' : 'edit')"
			>
				<i class="fa fa-pen" />
			</button>
			<template v-if="activeBook">
				<button
					v-if="isActiveCategory && isEdit"
					class="KBToolbar-action KBToolbar-action_info"
					@click="$emit('settings', activeBook)"
				>
					<i class="fa fa-cog" />
				</button>
				<template
					v-if="editBook"
				>
					<button
						class="KBToolbar-action KBToolbar-action_info"
						@click="$emit('upload-image')"
					>
						<i class="far fa-image" />
					</button>

					<button
						class="KBToolbar-action KBToolbar-action_info"
						@click="$emit('upload-audio')"
					>
						<i class="fas fa-volume-up" />
					</button>

					<button
						v-if="!isActiveCategory"
						class="KBToolbar-action KBToolbar-action_remove"
						@click="$emit('delete-page', activeBook)"
					>
						Удалить
					</button>

					<button
						class="KBToolbar-action KBToolbar-action_save"
						@click="$emit('save-page')"
					>
						Сохранить
					</button>
				</template>
				<template v-else>
					<button
						v-if="!isActiveCategory"
						class="KBToolbar-action KBToolbar-action_info"
						title="Поделиться ссылкой"
						@click="copyLink()"
					>
						<i class="fa fa-clone" />
					</button>

					<button
						v-if="isEdit && !isActiveCategory"
						class="KBToolbar-action KBToolbar-action_remove"
						@click="$emit('delete-page', activeBook)"
					>
						<i class="fa fa-trash" />
					</button>

					<button
						v-if="isEdit && !isActiveCategory"
						class="KBToolbar-action KBToolbar-action_save"
						@click="$emit('edit-page')"
					>
						Редактировать
					</button>
				</template>
			</template>
			<!-- <template v-else>
				<div
					v-if="canEdit"
					class="KBToolbar-action"
				>
					<i
						class="icon-nd-settings"
						@click="$emit('settings')"
					/>
				</div>
			</template> -->
		</div>
	</nav>
</template>

<script>
import { mapState } from 'pinia'
import { usePortalStore } from '@/stores/Portal'
import { copy2clipboard } from '@/composables/copy2clipboard.js'

export default {
	name: 'KBToolbar',
	components: {},
	props: {
		mode: {
			type: String,
			default: 'read',
		},
		activeBook: {
			type: Object,
			default: null
		},
		parentBook: {
			type: Object,
			default: null
		},
		breadcrumbs: {
			type: Array,
			default: () => []
		},
		canEdit: {
			type: Boolean
		},
		editBook: {
			type: Boolean
		},
	},
	data(){
		return {}
	},
	computed: {
		...mapState(usePortalStore, ['isOwner', 'isAdmin']),
		isEdit(){
			return this.mode === 'edit'
		},
		isActiveCategory(){
			if(!this.activeBook) return false
			return !this.activeBook.parent_id || this.activeBook.is_category
		},
	},
	watch: {},
	created(){},
	mounted(){},
	methods: {
		copyLink(){
			try {
				copy2clipboard(window.location.origin + '/corp_book/' + this.activeBook.hash)
				this.$toast.info('Ссылка на страницу скопирована')
			}
			catch (error) {
				this.$toast.error('Не удалось скопировать ссылку')
			}
		}
	},
}
</script>

<style lang="scss">
.KBToolbar{
	display: flex;
	flex-flow: row nowrap;
	align-items: center;
	&-breadcrumbs{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		gap: 10px;
		.fa-chevron-right{
			position: relative;
			top: 1px;
			font-size: 8px;
			color: #ccc;
		}
	}
	&-breadcrumb{
		display: block;
		font-size: 12px;
		color: #858585;
		&:last-child{
			color: #000;
		}
		&:hover{
			color: #ddd;
		}
	}
	&-actions{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		gap: 10px;

		margin-left: auto;
	}
	&-action{
		display: flex;
		padding: 6px;
		border: 1px solid #dcdee5;

		font-size: 14px;

		background: #eaebef;
		border-radius: 5px;
		cursor: pointer;
		&_info{
			color: #fff;
			background: #285ba7;
			border-color: #285ba7;
			.fa-cog{
				color: #fff;
			}
			&:hover{
				background: darken(#285ba7, 5);
				border-color: darken(#285ba7, 5);
			}
		}
		&_remove{
			color: #fff;
			background-color: #dc3545;
			border-color: #dc3545;
			&:hover{
				background-color: #c82333;
				border-color: #bd2130;
			}
		}
		&_save{
			color: #fff;
			background: #28a745;
			border-color: #28a745;
			&:hover{
				background-color: darken(#28a745, 5);
				border-color: darken(#28a745, 5);
			}
		}
		&_active{
			background: #bbd6ff;
			color: #007bff;
		}
	}
}
</style>
