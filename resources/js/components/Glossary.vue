<template>
	<div class="content">
		<div class="GlossaryComponent p-4">
			<div class="GlossaryComponent-search d-flex mb-3">
				<input
					v-model="searchText"
					type="text"
					class="search form-control form-control-sm"
					placeholder="Поиск термина"
				>
			</div>
			<div
				v-if="mode === 'edit' && canEdit"
				class="GlossaryComponent-add mb-3"
			>
				<JobtronButton
					@click="onAdd"
				>
					Добавить термин
				</JobtronButton>
			</div>
			<!-- words -->
			<div class="GlossaryComponent-items">
				<div
					v-for="(term, i) in sortedTerms"
					:key="i"
					class="GlossaryComponent-item mb-3"
					:class="{
						'GlossaryComponent-item_unsave': term.id < 0
					}"
				>
					<div class="GlossaryComponent-word">
						<input
							v-if="mode === 'edit' && canEdit"
							v-model="term.word"
							type="text"
							class="form-control"
						>
						<!-- <router-link
							v-else
							:to="`/kb?search=${term.word}`"
						>
							{{ term.word }}
						</router-link> -->
						<div v-else>
							{{ term.word }}
						</div>
					</div>
					<div class="GlossaryComponent-definition">
						<div
							v-if="mode === 'edit' && canEdit"
							class="form-control"
						>
							<JobtronTextarea
								v-model="term.definition"
								block
							/>
						</div>
						<p v-else>
							{{ term.definition }}
						</p>
					</div>
					<div
						v-if="mode === 'edit' && canEdit"
						class="GlossaryComponent-actions"
					>
						<button
							class="btn btn-success btn-icon btn-secondary btn-sm"
							@click="$emit('saveTerm', term)"
						>
							<i class="fa fa-save" />
						</button>
						<button
							class="btn btn-danger btn-icon btn-secondary btn-sm ml-2"
							@click="$emit('deleteTerm', term)"
						>
							<i class="fa fa-trash" />
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { mapGetters } from 'vuex'

import JobtronButton from '@ui/Button.vue';
import JobtronTextarea from '@ui/Textarea.vue';

export default {
	name: 'GlossaryComponent',
	components: {
		JobtronButton,
		JobtronTextarea,
	},

	props: {
		mode: {
			type: String,
			default: 'read'
		},
		terms: {
			type: Array,
			default: () => []
		},
		access: {
			type: Array,
			default: () => []
		},
	},
	data(){
		return {
			searchText: '',
		}
	},
	computed: {
		...mapGetters(['user', 'profileGroups']),
		filteredTerms() {
			if(!this.searchText) return this.terms
			const lowerSearch = this.searchText.toLowerCase()
			return this.terms.filter(term => (
				term.word.toLowerCase().indexOf(lowerSearch) > -1)
				|| (term.definition.toLowerCase().indexOf(lowerSearch) > -1)
			)
		},
		sortedTerms(){
			return this.filteredTerms.slice().sort((a, b) => {
				const ats = new Date(a.created_at).valueOf()
				const bts = new Date(b.created_at).valueOf()
				return ats - bts
			})
		},
		currentUserGroups(){
			return this.profileGroups.slice().filter(group => ~group.users?.findIndex(user => user.id === this.user.id))
		},
		canEdit(){
			/* global Laravel */
			if(Laravel.is_admin) return true
			return ~this.access.findIndex(access => {
				switch(access.type){
				case 1:
					return access.id === this.user?.id
				case 2:
					return ~this.currentUserGroups.findIndex(group => group.id === access.id)
				case 3:
					return access.id === this.user?.position_id
				}
			})
		}
	},

	created(){},

	methods: {
		onAdd(){
			this.$emit('addTerm')
			this.$nextTick(() => {
				if(!this.$el) return
				const lastItem = this.$el.querySelector('.GlossaryComponent-item:last-child')
				if(!lastItem) return
				lastItem.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'nearest' })
			})
		}
	}
}
</script>

<style lang="scss">
.GlossaryComponent{
	// &-search{}
	&-cover{
		width: 100%;
		height: 3px;
		position: absolute;
		background: white;
	}
	&-add{
		display: flex;
		align-items: center;
		justify-content: center;
	}
	// &-items{}
	&-item{
		display: flex;
		align-items: start;
		gap: 10px;
		&_unsave{
			.GlossaryComponent{
				&-word,
				&-definition{
					.form-control{
            height: 35px;
						// background: repeating-linear-gradient(
						// 	-45deg,
						// 	#fff,
						// 	#fff 10px,
						// 	rgba(250, 0, 0, 0.1) 10px,
						// 	rgba(250, 0, 0, 0.1) 20px
						// );
						background: repeating-linear-gradient(
							-45deg,
							rgba(250, 0, 0, 0.1) 0,
							rgba(250, 0, 0, 0.1) 20px
						);
					}
				}
			}
		}
	}
	&-word{
		flex: 1 1 30%;
	}
	&-definition{
		flex: 1 1 70%;
	}
	&-actions{
		flex: 0 0 content;
	}

	a{
		&:hover{
			color: darken(#007bff, 15);
		}
	}
	.form-control{
		padding: 0 20px !important;
		border: 1px solid #e8e8e8;

		font-size: 14px;

		border-radius: 6px !important;
		background-color: #F7FAFC !important;
	}
}
</style>
