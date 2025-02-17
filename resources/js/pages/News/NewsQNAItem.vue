<template>
	<div
		class="NewsQNAItem"
		:class="{
			'NewsQNAItem_responded': isResponded,
			'NewsQNAItem_notresponded': !isResponded || reanswer,
			'NewsQNAItem_public': true,
		}"
	>
		<div class="NewsQNAItem-question">
			{{ item.question }}
		</div>
		<div class="NewsQNAItem-variants">
			<component
				:is="item.multiAnswer ? 'b-form-checkbox-group' : 'div'"
				v-model="selected"
				:name="`NewsQNAItem-check-${item.id}`"
			>
				<div
					v-for="answer, index in item.answers"
					:key="index"
					class="NewsQNAItem-variant"
				>
					<div
						class="NewsQNAItem-content"
						@click="onSelect(answer.id)"
					>
						<div
							v-if="isResponded || isAdmin"
							class="NewsQNAItem-bar"
							:style="`width: ${(answer.votes.length / max) * 100}%;`"
						/>
						<div class="NewsQNAItem-text">
							{{ answer.answer }}
						</div>
					</div>
					<template v-if="!isResponded || reanswer">
						<b-form-checkbox
							v-if="item.multiAnswer"
							:value="answer.id"
							class="NewsQNAItem-check"
						/>
						<b-form-radio
							v-else
							v-model="selected"
							:value="answer.id"
							:name="`NewsQNAItem-radio-${item.id}`"
							class="NewsQNAItem-radio"
						/>
					</template>
					<label
						v-if="isResponded || isAdmin"
						class="NewsQNAItem-count"
					>
						{{ answer.votes.length }}
						<template v-if="true">
							<input
								type="text"
								class="NewsQNAItem-hidden"
								tabindex="-1"
							>
							<PopupMenu
								position="topRight"
								max-height="200px"
							>
								<template #default>
									<template v-for="vote, aIndex in answer.votes">
										<div
											v-if="vote"
											:key="aIndex"
											class="NewsQNAItem-user"
										>
											<JobtronAvatar
												:title="vote.name"
												:image="vote.avatar"
												:size="24"
											/>
											{{ vote.name }} {{ vote.last_name }}
										</div>
									</template>
								</template>
							</PopupMenu>
						</template>
					</label>
					<div
						v-if="isResponded || isAdmin"
						class="NewsQNAItem-percent"
					>
						{{ parseInt((answer.votes.length / max) * 100) }}%
					</div>
				</div>
			</component>
		</div>
	</div>
</template>

<script>
import { mapGetters } from 'vuex'
import { mapState } from 'pinia'
import { usePortalStore } from '@/stores/Portal'

import PopupMenu from '@ui/PopupMenu.vue'
import JobtronAvatar from '@ui/Avatar.vue'


export default {
	name: 'NewsQNAItem',
	components: {
		PopupMenu,
		JobtronAvatar,
	},
	props: {
		item: {
			type: Object,
			required: true
		},
		reanswer: {
			type: Boolean
		},
		isResponded: {
			type: Boolean
		}
	},
	data(){
		return {
			selected: this.item.multiAnswer ? [] : null,
		}
	},
	computed: {
		...mapGetters(['user', 'users']),
		...mapState(usePortalStore, ['isAdmin']),
		usersResponded(){
			const result = []
			this.item.answers.forEach(answer => {
				result.push(...answer.votes.map(vote => vote.id))
			})
			return result
		},
		max(){
			return Math.max(1, this.usersResponded.slice().length)
			// if(!this.item.multiAnswer) return Math.max(1, this.usersResponded.slice().filter(onlyUnique).length)

			// let max = 1
			// this.item.variants.forEach(variant => {
			// 	max = Math.max(max, variant.answers.length)
			// })
			// return max
		}
	},
	watch: {
		selected(){
			this.$emit('change', this.selected)
		}
	},
	methods: {
		onSelect(id){
			if(!this.item.multiAnswer) {
				this.selected = id
				return
			}
			const index = this.selected.indexOf(id)
			if(~index) this.selected.splice(index, 1)
			else this.selected.push(id)
		}
	},
}
</script>

<style lang="scss">
.NewsQNAItem{
	// &_responded{}
	&_notresponded{
		.NewsQNAItem{
			&-content{
				~ .NewsQNAItem{
					&-radio{
						&.custom-radio{
							.custom-control-label:before{
								background-color: #dee2e6 !important;
							}
						}
					}
				}
				&:hover{
					~ .NewsQNAItem{
						&-radio{
							.custom-control-label:before{
								border-color: #000;
							}
						}
					}
					box-shadow: 0 0 16px 4px rgba(#156AE8, 0.25);
				}
			}
		}
	}
	&_public{
		.NewsQNAItem{
			&-count{
				text-decoration: underline dashed;
			}
		}
	}
	&-question{
		font-size: 18px;
		font-weight: 700;
	}
	&-variants{
		padding-top: 10px;
	}
	&-variant{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		justify-content: flex-end;
		gap: 10px;

		margin-bottom: 10px;
		border-radius: 4px;
	}
	&-content{
		flex: 1;
		order: 2;

		position: relative;

		background-color: rgba(#156AE8, 0.125);
		border-radius: 4px;

	}
	&-bar{
		position: absolute;
		z-index: 1;
		top: 0;
		left: 0;
		bottom: 0;

		background-color: #156AE8;
		border-radius: 4px;
		opacity: 0.5;
	}
	&-text{
		padding: 4px 8px;
		position: relative;
		z-index: 2;
		border-radius: 4px;
	}
	&-count{
		order: 3;
		flex: 0 0 24px;
		margin-bottom: 0 !important;

		position: relative;
		text-align: right;
		cursor: pointer;
		.PopupMenu{
			margin-right: -20px;
			opacity: 0;
			visibility: hidden;
			transition: opacity 0.5s;
		}
	}
	&-hidden{
		opacity: 0.001;
		position: absolute;
		left: -9999rem;
		&:focus ~ .PopupMenu{
			margin-right: 0;
			opacity: 1;
			visibility: visible;
		}
	}
	&-user{
		display: flex;
		align-items: center;
		gap: 5px;

		padding: 2px 18px;
		white-space: nowrap;
	}
	&-percent{
		order: 4;
		flex: 0 0 42px;
		text-align: right;
		color: #777;
	}
	&-radio{
		order: 1;
		position: relative;
		.custom-control-label{
			position: absolute;
			z-index: 1;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
		}
		&:hover{
			.custom-control-label:before{
				border-color: #000 !important;
			}
		}
	}
	&-check{
		order: 1;
		margin-right: 0 !important;
	}
}
</style>

