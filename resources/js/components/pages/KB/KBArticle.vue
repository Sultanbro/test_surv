<template>
	<div class="KBArticle">
		<div
			class="KBArticle-favorite"
			@click="$emit('favorite', activeBook)"
		>
			<i
				class="fa-heart"
				:class="[
					activeBook.isFavorite ? 'fas' : 'far',
				]"
			/>
		</div>
		<div class="KBArticle-title">
			{{ activeBook.title }}
		</div>
		<div class="KBArticle-meta">
			<JobtronAvatar
				:image="activeBook.editor_avatar"
				:title="activeBook.editor || 'Неизвестный'"
				:size="48"
			/>
			<div class="KBArticle-authors">
				<div class="KBArticle-author">
					<p class="KBArticle-authorTime">
						<span>Cоздан:</span> {{ activeBook.created }}
					</p>
					<i class="fa fa-chevron-right" />
					<p class="KBArticle-authorName">
						{{ activeBook.author }}
					</p>
				</div>
				<div class="KBArticle-author">
					<p class="KBArticle-authorTime">
						<span>Изменен:</span> {{ activeBook.edited_at }}
					</p>
					<i class="fa fa-chevron-right" />
					<p class="KBArticle-authorName">
						{{ activeBook.editor }}
					</p>
				</div>
			</div>
		</div>
		<!-- eslint-disable -->
		<div
			class="KBArticle-body"
			v-html="markedText"
		/>
		<!-- eslint-enable -->

		<Questions
			:id="activeBook.id"
			:questions="activeBook.questions"
			type="kb"
			:mode="mode"
			:count_points="true"
			:pass="activeBook.item_model !== null"
			:pass_grade="activeBook.pass_grade"
			@passed="passed"
			@nextElement="nextElement"
			@changePassGrade="onChangePassGrade"
		/>
		<div class="pb-5" />
	</div>
</template>

<script>
import Mark from 'mark.js/dist/mark.es6.js'

import Questions from '@/pages/Questions'
import JobtronAvatar from '@ui/Avatar.vue'

// const quotes = ['«»', '“”', '""', '()']
// const enders = '.,!?:;'.split('')
const markOptions = {
	element: 'span',
	className: 'KBArticle-mark',
	exclude: ['.KBArticle-definition'],
	accuracy: 'exactly',
}
function createDefinition(text){
	const span = document.createElement('span')
	span.innerText = text
	span.classList.add('KBArticle-definition')
	return span
}
// function getSynonims(term){
// 	const result = []
// 	enders.forEach(char => {
// 		result.push(term + char)
// 	})
// 	quotes.forEach(pair => {
// 		result.push(pair[0] + term)
// 		result.push(term + pair[1])
// 		result.push(pair[0] + term + pair[1])
// 		enders.forEach(char => {
// 			result.push(pair[0] + term + char)
// 			result.push(term + pair[1] + char)
// 			result.push(pair[0] + term + pair[1] + char)
// 		})
// 	})
// 	return result
// }

export default {
	name: 'KBArticle',
	components: {
		Questions,
		JobtronAvatar,
	},
	props: {
		mode: {
			type: String,
			default: 'read',
		},
		activeBook: {
			type: Object,
			default: null
		},
		glossary: {
			type: Array,
			default: () => []
		}
	},
	data(){
		return {}
	},
	computed: {
		markedText(){
			if(!this.activeBook) return ''
			const div = document.createElement('div')
			const hl = this.$route.query.hl
			div.innerHTML = this.activeBook.text
			const instance = new Mark(div)

			const glossary = this.glossary || []
			glossary.forEach(term => {
				instance.mark(term.word, {
					...markOptions,
					each: el => {
						el.appendChild(createDefinition(term.definition))
					}
				})
				// getSynonims(term.word).forEach(word => {
				// 	instance.mark(word, {
				// 		...markOptions,
				// 		each: el => {
				// 			el.appendChild(createDefinition(term.definition))
				// 		}
				// 	})
				// })
			})

			if(hl){
				instance.mark(hl, {
					...markOptions,
					accuracy: 'partially',
					each: el => {
						el.classList.add('KBArticle-mark_justmark')
						// this.$nextTick(() => el.classList.add('KBArticle-mark_justmark'))
					}
				})
			}
			return div.innerHTML
		}
	},
	watch: {
		activeBook: {
			handler(){
				this.updateMarks()
			},
			deep: true
		}
	},
	methods: {
		passed() {
			// pass if its not course.  cos there not nextElement button
			if(!this.activeBook.item_model) {
				this.setSegmentPassed()
			}
		},
		nextElement(){},
		setSegmentPassed() {
			/* eslint-disable camelcase */
			this.axios.post('/my-courses/pass', {
				id: this.activeBook.id,
				type: 3,
				course_item_id: 0,
				questions: this.activeBook.questions,
				all_stages: 0,
				completed_stages: 1,
			}).then(() => {}).catch((error) => {
				alert(error)
			})
			/* eslint-enable camelcase */
		},
		onChangePassGrade(){},
		updateMarks(){

		}
	},
}
</script>

<style lang="scss">
.KBArticle{
	max-width: 1000px;
	padding: 20px;
	margin: 0 auto;

	position: relative;

	&-favorite{
		width: 27px;
		padding: 5px;

		position: absolute;
		z-index: 5;
		top: 10px;
		left: 10px;

		color: #333;
		background: #ddd;
		border-radius: 4px;
		cursor: pointer;

		&.fas,
		&:hover{
			color: #007bff;
		}
	}
	&-title{
		padding: 15px 0;
		text-align: center;
		font-size: 20px;
		font-weight: 600;
	}
	&-meta{
		display: flex;
		justify-content: flex-end;
		align-items: center;
		gap: 10px;
		margin-bottom: 30px;
	}
	// &-authors{}
	&-author{
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 15px;

		margin-bottom: 5px;

		font-size: 12px;
		.fa-chevron-right{
			color: #e4ecf7;
		}
	}
	&-authorTime{
		color: #a0aec0;
	}
	&-authorName{
		color: #156ae8;
		font-weight: 500;
	}

	&-mark{
		display: inline-flex;

		position: relative;
		font-weight: inherit;
		font-size: inherit;
		font-family: inherit;
		cursor: help;
		&:after{
			content: '*';
			color: #00F;
		}
		&:hover{
			.KBArticle{
				&-definition{
					transform: translate(-50%, 0);
					visibility: visible;
					opacity: 1;
				}
			}
		}
		&_justmark{
			background-color: #fcf8e3;
			padding: 0 0.2em;
			color: default;
			&:after{
				content: none;
			}
		}
	}
	&-definition{
		flex: 0 1 content;
		width: max-content;
		max-width: 400px;
		padding: 5px 10px;
		border: 1px solid #000;

		position: absolute;
		bottom: 100%;
		left: 50%;

		font-size: 14px;
		font-weight: 400;
		color: #333;

		background-color: #fff;
		transform: translate(-50%, -50%);
		visibility: hidden;
		opacity: 0;
		transition: all 0.2s;
	}
}
</style>
