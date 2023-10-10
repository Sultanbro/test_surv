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
			<div class="GlossaryComponent-cover" />
			<div
				v-if="mode === 'edit'"
				class="GlossaryComponent-add mb-3"
			>
				<JobtronButton
					@click="$emit('addTerm')"
				>
					Добавить термин
				</JobtronButton>
			</div>
			<!-- words -->
			<div class="GlossaryComponent-items">
				<div
					v-for="(term, i) in filteredTerms"
					:key="i"
					class="GlossaryComponent-item mb-3"
					:class="{
						'GlossaryComponent-item_unsave': term.id < 0
					}"
				>
					<div class="GlossaryComponent-word">
						<input
							v-if="mode === 'edit'"
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
							v-if="mode === 'edit'"
							class="form-control"
						>
							<JobtronTextarea
								v-model="term.definition"
							/>
						</div>
						<p v-else>
							{{ term.definition }}
						</p>
					</div>
					<div
						v-if="mode === 'edit'"
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
	},
	data(){
		return {
			searchText: '',
		}
	},
	computed: {
		filteredTerms() {
			if(!this.searchText) return this.terms
			const lowerSearch = this.searchText.toLowerCase()
			return this.terms.filter(term => (
				term.word.toLowerCase().indexOf(lowerSearch) > -1)
				|| (term.definition.toLowerCase().indexOf(lowerSearch) > -1)
			)
		}
	},

	created(){},

	methods: {}
}
</script>

<style lang="scss">
.GlossaryComponent{
	&-search{
		padding: 15px;
		margin: -15px;

		position: sticky;
		top: 15px;

		background-color: #fff;
		box-shadow: 0px 0.5px 0.5px 1.5px rgba(0,0,0,0.25);
	}
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
	&-items{}
	&-item{
		display: flex;
		align-items: start;
		gap: 10px;
		&_unsave{
			.GlossaryComponent{
				&-word,
				&-definition{
					.form-control{
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
