<template>
	<div
		class="questions"
		:class="{
			hide:
				mode == 'read' && (questions === undefined || questions.length == 0),
		}"
		@click="hideAll($event)"
	>
		<div
			v-if="
				(mode == 'read' && type == 'book') || ['kb', 'video'].includes(type)
			"
			class="title"
		>
			Проверочные вопросы
		</div>
		<div
			v-if="questions.length"
			class="quetions__wrapper"
		>
			<div
				v-for="(q, q_index) in questions"
				:key="q_index"
				class="question mt-4"
				:class="{ show: q.editable }"
			>
				<div
					v-if="mode == 'edit'"
					class="title d-flex jcsb"
					@click.stop="editQuestion(q_index)"
				>
					{{ q.text || "Текст вопроса..." }}
				</div>

				<div
					v-if="mode == 'read'"
					class="title d-flex jcsb aic quetion__title"
				>
					<p class="mb-0">
						{{ q_index + 1 }}. {{ q.text }}
					</p>
					<i
						v-if="scores && q.success == false"
						class="fa fa-times-circle wrong"
					/>
					<i
						v-if="scores && q.success == true"
						class="fa fa-check-circle right"
					/>
				</div>

				<div v-if="q.editable || mode == 'read'">
					<template v-if="mode == 'edit'">
						<div class="d-flex align-items-center gap-3">
							<textarea
								v-model="q.text"
								placeholder="Напишите текст тестового вопроса"
								class="form-control mt-4"
								@keyup="changed = true"
							/>
							<div class="btns aic ml-auto">
								<i
									class="far fa-trash-alt pointer text-danger"
									@click.stop="deleteQuestion(q_index)"
								/>
							</div>
						</div>
						<div class="row">
							<div class="col-12 col-md-4" />
						</div>
					</template>
					<div
						v-if="q.type == 0"
						class="variants"
					>
						<div
							v-for="(v, v_index) in q.variants"
							:key="v_index"
							class="d-flex aic mt-4"
							@mouseover="handleMouseOver(v_index)"
							@mouseleave="handleMouseLeave"
						>
							<div class="variant">
								<label
									v-if="mode == 'edit'"
									class="d-flex"
								>
									<input
										v-model="v.right"
										type="checkbox"
										class="mr-2 checkbox"
										title="Отметьте галочкой, если думаете, что ответ правильный. Правильных вариантов может быть несколько"
										@change="changed = true"
									>

									<input
										:ref="`variant${q_index}_${v_index}`"
										v-model="v.text"
										type="text"
										placeholder="Введите вариант ответа..."
										@keyup.enter="addVariant(q_index, v_index)"
										@keyup.delete="deleteVariant(q_index, v_index)"
									>
								</label>

								<div class="question-form-group">
									<input
										:id="'v-' + v_index + 'q' + q_index"
										v-model="v.checked"
										type="checkbox"
										class="mr-2"
										title="Отметьте галочкой, если думаете, что ответ правильный. Правильных вариантов может быть несколько"
										@change="changed = true"
									>
									<label
										v-if="mode == 'read'"
										class="d-flex w-100 justify-content-between"
										:class="{ right: scores && v.right == true }"
										:for="'v-' + v_index + 'q' + q_index"
									>
										<p class="mb-0">{{ v.text }}</p>

										<i
											v-if="scores && v.right == true"
											class="fa fa-check right ml-2 mt-1"
										/>
									</label>
								</div>
							</div>
							<div
								v-if="mode == 'edit' && showDeleteVariant === v_index"
								class="btns aic ml-auto"
							>
								<i
									class="far fa-trash-alt pointer text-danger"
									@click.stop="deleteVariant(q_index, v_index)"
								/>
							</div>
						</div>
						<button
							v-if="mode == 'edit'"
							class="btn btn-default mt-2 mb-2"
							@click.stop="addVariant(q_index, -1)"
						>
							+ вариант
						</button>
					</div>
					<div v-else>
						<input
							v-model="q.success"
							type="text"
						>
					</div>
				</div>
			</div>
		</div>

		<template v-if="mode == 'read'">
			<div class="d-flex">
				<button
					v-if="points == -1 || !scores"
					class="btn mr-2 mt-4 quetion__button"
					:disabled="timer_turned_on"
					@click.stop="checkAnswers"
				>
					Проверить <span v-if="timer_turned_on">({{ timer }})</span>
				</button>
				<button
					v-if="points != -1 && scores && type == 'book'"
					class="btn btn-primary"
					@click.stop="$emit('continueRead')"
				>
					Читать дальше
				</button>
			</div>

			<div class="d-flex jcsb aic">
				<p
					v-if="points != -1 && mode == 'read'"
					class="mt-3 scores mr-3"
				>
					<span v-if="scores" />
					<span v-else>Вы не набрали проходной балл...</span>
				</p>
				<button
					v-if="mode == 'read' && passed"
					class="net-btn btn btn-primary mt-4"
					@click="$emit('nextElement')"
				>
					Продолжить
					<i class="fa fa-angle-double-right ml-2" />
				</button>
			</div>
		</template>

		<template v-if="mode == 'edit'">
			<button
				v-if="!questions.length"
				class="btn"
				@click.stop="addQuestion"
			>
				Добавить вопрос
			</button>

			<div
				v-if="questions.length"
				class="d-flex jcsb aifs mt-4"
			>
				<div>
					<button
						v-if="['kb', 'video'].includes(type)"
						class="btn btn-success mr-2"
						@click.stop="saveTest"
					>
						Сохранить
					</button>

					<button
						class="btn"
						@click.stop="addQuestion"
					>
						Добавить вопрос
					</button>
				</div>

				<div class="d-flex aic pass__ball">
					<p
						class="mr-3"
						style="width: 200px"
					>
						Проходной балл:
						<img
							v-b-popover.hover.right.html="'Правильных ответов для прохода'"
							src="/images/dist/profit-info.svg"
							title="Проходной балл"
							alt=""
						>
					</p>

					<div class="d-flex aic">
						<input
							v-model="pass_grade_local"
							class="form-control mr-2"
							type="number"
							:min="0"
							:max="100"
							@change="$emit('changePassGrade', pass_grade_local)"
							@focus="$event.target.select()"
						>
						<span>
							<b>из {{ questions.length }}</b></span>
					</div>
				</div>
			</div>
		</template>
	</div>
</template>

<script>
/* eslint-disable vue/no-mutating-props */
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */

export default {
	name: 'PageQuestions',
	props: {
		questions: {
			type: Array,
			default: () => [],
		},
		type: {
			type: String,
			default: '',
		},
		course_item_id: {
			type: Number,
			default: 0,
		},
		id: {
			type: Number,
			default: 0,
		},
		mode: {
			type: String,
			default: 'read',
		},
		pass: {
			type: Boolean,
			default: false,
		},
		pass_grade: {
			type: Number,
			default: 1,
		},
		dontRepat: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			can_save: true,
			changed: false,
			total: 0,
			points: -1,
			count_points: false,
			pass_grade_local: 1,
			timer: 60,
			timer_turned_on: false,
			passed: false,
			right_ans: 0, // правильно отвеченные
			showDeleteVariant: null,
		};
	},
	computed: {
		scores() {
			let pass_grade_local =
				this.pass_grade_local > this.questions.length
					? this.questions.length
					: this.pass_grade_local;
			return Number(this.right_ans) - Number(pass_grade_local) >= 0;
		},
	},
	watch: {
		pass_grade_local(/* grade */) {
			// let len = this.questions.length;

			// if(grade > len) this.pass_grade_local = len;
			// if(grade < 1) this.pass_grade_local = 1;
			// this.$emit('changePassGrade', this.pass_grade_local)
			this.changed = true;
		},

		pass_grade() {
			this.pass_grade_local = this.pass_grade;
		},

		mode: {
			handler(val) {
				if (val == 'edit') {
					this.questions.forEach((q) => {
						q.editable = false;
					});
				}
			},
		},

		timer: {
			handler(value) {
				if (value > 0) {
					setTimeout(() => {
						this.timer--;
					}, 1000);
				} else {
					this.timer_turned_on = false;
				}
			},
			immediate: true, // This ensures the watcher is triggered upon creation
		},
	},

	created() {
		this.pass_grade_local = this.pass_grade;
		this.setResults();
		this.prepareVariants();

		if (this.mode == 'read') {
			this.questions.forEach((q) => {
				q.editable = true;
				this.total += Number(q.points);
			});

			if (this.pass) {
				this.points = this.total;
			}

			if (this.$cookie.get('q_timer') != null) {
				this.$cookie.set('q_timer', 60, { expires: '60s' });
				this.timer_turned_on = true;
				this.timer = 60;
			}
		} else {
			this.questions.forEach((q) => {
				q.editable = false;
			});
		}

		if (this.count_points) {
			this.checkAnswers();
		}
	},
	mounted() {},
	methods: {
		handleMouseOver(index) {
			this.showDeleteVariant = index;
		},
		handleMouseLeave() {
			this.showDeleteVariant = null;
		},
		setResults() {
			this.questions.forEach((q) => {
				if (q.result == null) return;
				this.count_points = true;
				if (q.type == 0) {
					q.variants.forEach((v, vi) => {
						if (q.result.answer === undefined) return;
						if (q.result.answer[vi] !== undefined)
							v.checked = q.result.answer[vi];
					});
				}
			});
		},

		prepareVariants() {
			if (this.questions === undefined) this.questions = [];
			this.questions.forEach((q) => {
				if (q.type == 0) {
					q.variants.forEach(() => {
						q.before = q.text;
					});
				}
			});
		},

		hideAll(event) {
			const IS_QUESTIONS_DIV =
				event.target.classList.length > 0 &&
				event.target.classList[0] == 'questions';

			if (IS_QUESTIONS_DIV) {
				this.questions.forEach((q) => (q.editable = false));
			}
		},

		checkAnswers() {
			// read

			if (this.timer_turned_on && this.$cookie.get('q_timer') != null) {
				this.$toast.error(
					'Вы не можете пока ответить еще ' + this.timer + ' секунд'
				);
				return;
			}

			// start count
			this.points = 0;
			this.right_ans = 0;

			let not_answered_question = false;

			this.questions.forEach((q) => {
				let answer = {};

				if (q.type == 0) {
					let right_answers = 0;
					let wrong_answers = 0;
					let checked_answers = 0;
					let not_answered = true;

					q.variants.forEach((v, vi) => {
						answer[vi] = v.checked;

						if (v.checked == 1 && v.checked == v.right) {
							checked_answers++;
							not_answered = false;
						}
						if (v.checked == 1 && v.right == 0) {
							wrong_answers++;
							not_answered = false;
						}
						if (v.right == 1) {
							right_answers++;
							not_answered = false;
						}
					});

					if (not_answered) not_answered_question = true;

					if (right_answers == checked_answers && wrong_answers == 0) {
						this.points += q.points;
						this.right_ans++;
						q.success = true;
					} else {
						q.success = false;
					}
				} else {
					this.points += Number(q.points);
					q.success = true;
				}

				q.result = {
					test_question_id: q.id,
					answer: answer,
					status: 1,
					course_item_model_id: this.course_item_id,
				};
			});

			//
			if (not_answered_question) {
				this.$toast.error('Ответьте на все вопросы!');
				return;
			}

			if (this.scores) {
				if (this.count_points) {
					this.count_points = false;
				}
				// else {
				//   this.$emit('passed');
				// }
				this.$emit('passed');
				this.passed = true;
			} else {
				if (this.dontRepat) {
					this.$emit('failed');
				} else {
					this.timer_turned_on = true;
					this.timer = 60;
					this.$cookie.set('q_timer', 60, { expires: '60s' });

					this.$toast.error(
						'Вы ответили неверно. Вот Вам еще минутка чтобы найти на странице правильный ответ!'
					);
					this.points = -1;
				}
			}
		},

		addVariant(q_index, v_index = -1) {
			this.questions[q_index].variants.push({
				text: '',
				before: '',
				right: 0,
			});

			if (v_index != -1) {
				this.$nextTick(() => {
					let input = this.$refs['variant' + q_index + '_' + (v_index + 1)][0];

					input.focus();
				});
			} else {
				this.$nextTick(() => {
					let input =
						this.$refs[
							'variant' +
								q_index +
								'_' +
								(this.questions[q_index].variants.length - 1)
						][0];

					input.focus();
				});
			}

			this.changed = true;
		},

		saveQuestion(q_index) {
			if (
				this.questions[q_index].text == '' ||
				this.questions[q_index].text == null
			) {
				alert('Вопрос  №' + (q_index + 1) + ' не заполнен!');
				this.$emit('validate', false);
				return false;
			}

			if (
				this.questions[q_index].variants.findIndex((v) => v.text == '') != -1 &&
				this.questions[q_index].type == 0
			) {
				alert('Не оставляйте варианты пустыми! Вопрос №' + (q_index + 1));
				this.$emit('validate', false);
				return false;
			}

			if (
				this.questions[q_index].variants.findIndex((v) => v.right == 1) == -1 &&
				this.questions[q_index].type == 0
			) {
				alert('Выберите один правильный вариант! Вопрос №' + (q_index + 1));
				this.$emit('validate', false);
				return false;
			}

			this.questions[q_index].editable = false;
			this.$emit('validate', true);
			return true;
		},

		editQuestion(q_index) {
			this.questions.forEach((q) => (q.editable = false));
			this.questions[q_index].editable = true;
		},

		defaultQuestion() {
			return {
				id: 0,
				text: '',
				order: 0,
				points: 10,
				type: 0, // abc
				editable: false,
				variants: [
					{
						text: '',
						right: 0,
					},
				],
			};
		},

		addQuestion() {
			this.questions.forEach((q) => (q.editable = false));
			this.questions.push(this.defaultQuestion());
			this.questions[this.questions.length - 1].editable = true;
			this.can_save = true;
			this.changed = true;
			this.$emit('changePassGrade', this.questions.length);
		},

		deleteQuestion(q_index) {
			if (confirm('Удалить вопрос?')) {
				if (this.questions[q_index].id == 0) {
					this.questions.splice(q_index, 1);
				} else {
					this.axios
						.post('/playlists/delete-question', {
							id: this.questions[q_index].id,
						})
						.then(() => {
							this.questions.splice(q_index, 1);
						});
				}
				this.changed = true;
				this.$emit('changePassGrade', this.questions.length);
			}
		},

		deleteVariant(q, v) {
			if (this.questions[q] && this.questions[q].variants) {
				this.questions[q].variants.splice(v, 1);
			}
			// if (
			// 	el.text == el.before &&
			// 	el.before == "" &&
			// 	this.questions[q].variants.length > 1
			// ) {
			// 	this.questions[q].variants.splice(v, 1);
			// 	if (v > 0) this.$refs["variant" + q + "_" + (v - 1)][0].focus();
			// } else {
			// 	this.questions[q].variants[v].before =
			// 		this.questions[q].variants[v].text;
			// }

			// this.changed = true;
		},

		validate() {
			let passed = true;

			this.questions.every((q, index) => {
				if (!this.saveQuestion(index)) {
					passed = false;
					return false;
				}
				return true;
			});

			if (!passed) return false;
		},

		saveTest() {
			let passed = true;

			this.questions.every((q, index) => {
				if (!this.saveQuestion(index)) {
					passed = false;
					return false;
				}
				return true;
			});

			if (!passed) return false;

			this.$emit('changePassGrade', this.pass_grade_local);

			let loader = this.$loading.show();

			let url =
				this.type == 'kb' ? '/kb/page/save-test' : '/playlists/save-test';

			this.can_save = false;

			// remove checked prop from variants
			let _questions = this.questions;

			_questions.forEach((q) => {
				q.variants.forEach((v) => {
					delete v['checked'];
				});
			});

			// save
			this.axios
				.post(url, {
					id: this.id,
					pass_grade: this.pass_grade,
					questions: _questions,
				})
				.then((response) => {
					this.$toast.success('Вопросы сохранены!');
					this.questions.forEach((item, index) => {
						item.id = response.data[index];
					});
					loader.hide();
					this.can_save = true;
				})
				.catch((error) => {
					loader.hide();
					alert(error);
				});
		},
	},
};
</script>

<style scoped lang="scss">
.questions {
  margin-bottom: 1%;
	background-color: #ffffff;
	.title {
		color: #8da0c1;
		font-size: 20px;
	}
}
.quetions__wrapper {
	padding: 3%;
	background-color: #f2f4ff;
	border-radius: 20px;
	.quetion__title {
		font-size: 20px;
		font-weight: 500;
		color: #152136;
	}
	.question-form-group {
	}
}

.variant {
	background-color: #fff;
	border-radius: 8px;
	padding: 0.7%;
	width: 97.5%;
}

.questions .question-form-group label:before {
	content: "";
	-webkit-appearance: none;
	width: 25px;
	min-width: 25px;
	height: 25px;
	min-height: 20px;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	border-radius: 50%;
	background-color: #f5f5f5;
	border: 1px solid #eff5fc;
	position: relative;
	cursor: pointer;
	margin-right: 10px;
	transition: 0.3s all ease;
}
.questions .question-form-group label:after {
	content: "";
	display: block;
	position: absolute;
	top: 6px;
	left: 10px;
	width: 5px;
	height: 11px;
	border: solid #fff;
	border-width: 0 2px 2px 0;
	opacity: 0;
	transform: rotate(30deg);
	transition: 0.3s all ease;
}

.checkbox {
	-webkit-appearance: none;
	width: 25px;
	min-width: 25px;
	height: 25px;
	min-height: 20px;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	border-radius: 50%;
	background-color: #ffffff;
	border: 1px solid #f2f4ff;
	position: relative;
	cursor: pointer;
	margin-right: 10px;
	transition: background-color 0.3s ease;
}

.checkbox:checked {
	background-color: #3781ef;
	border-color: #3781ef;
}

.checkbox:checked:after {
	content: "";
	display: block;
	position: absolute;
	top: 6px;
	left: 9px;
	width: 5px;
	height: 11px;
	border: solid #fff;
	border-width: 0 2px 2px 0;
	opacity: 1;
	transform: rotate(40deg);
}

.checkbox:hover {
	border-color: #ccc; /* изменяем цвет границы при наведении курсора */
}

.quetion__button {
	background-color: #156ae8;
	color: white;
	border-radius: 8px;
}
</style>
