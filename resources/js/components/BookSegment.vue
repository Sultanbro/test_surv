<template>
	<!-- eslint-disable vue/no-mutating-props -->
	<div class="test">
		<div class="row">
			<div class="col-3">
				<div class="form-group">
					<label>Страница</label>
					<input
						type="number"
						min="1"
						max="9999"
						v-model="segment.page_start"
						placeholder="Страница"
						class="form-control mb-2"
					>
				</div>
				<!-- <i
					class="fa fa-save ml-1 pointer"
					@click="saveSegment"
				/> -->
				<b-btn
					variant="success"
					@click="saveSegment"
				>
					Сохранить сегмент
				</b-btn>
				<b-btn
					variant="danger"
					@click="deleteSegment"
				>
					<i class="fa fa-trash ml-1 pointer" />
				</b-btn>
			</div>
			<div class="col-9">
				<questions
					ref="questions"
					:questions="segment.questions"
					:id="segment.id"
					:pass_grade="segment.pass_grade"
					@changePassGrade="changePassGrade"
					@validate="validate"
					type="book"
					mode="edit"
				/>
			</div>
		</div>
	</div>
</template>
<script>
/* eslint-disable vue/no-mutating-props */
export default {
	props: {
		segment: {
			required: true,
		},
		book_id: {
			type: Number
		}
	},
	data() {
		return {
			validated: false,
		}
	},
	created() {

	},
	methods: {

		validate(status) {
			this.validated = status
		},

		deleteSegment() {
			if(!confirm('Вы уверены? Их потом не восстановить')) {
				return false;
			}

			this.axios
				.post('/admin/upbooks/segments/delete', {
					id: this.segment.id,
				})
				.then(() => {
					this.$toast.success('Удалено');
					this.$emit('deleteSegment');
				})
				.catch((error) => {
					alert(error);
				});
		},

		saveSegment() {

			this.$refs.questions.validate();
			if(!this.validated) {
				return;
			}

			if(this.segment.questions.length == 0) {
				this.$toast.error('Добавьте минимум 1 вопрос');
				return;
			}

			this.axios
				.post('/admin/upbooks/segments/save', {
					item: this.segment,
					book_id: this.book_id
				})
				.then((response) => {
					this.segment.id = response.data.id;
					this.segment.questions.forEach((item, index) => {
						item.id = response.data.ids[index];
					});
					this.$toast.success('Сохранено');
				})
				.catch((error) => {
					alert(error);
				});
		},

		changePassGrade(grade) {
			console.log('pass grade')

			this.segment.pass_grade = grade;
			let len = this.segment.questions.length;

			if(grade > len) this.segment.pass_grade = len;
			if(grade < 1) this.segment.pass_grade = 1;
		},
	},
	name: 'BookSegment'
};
</script>
