<template>
	<div class="PageCourses d-flex courses">
		<div class="PageCourses-aside lp">
			<h1 class="page-title">
				Курсы
			</h1>
			<Draggable
				:id="0"
				:list="courses"
				:group="{ name: 'g1' }"
				:handle="'.PageCourses-listBars'"
				class="PageCourses-list"
				tag="div"
				@end="saveOrder"
			>
				<div
					v-for="(course, c_index) in courses"
					:id="course.id"
					:key="course.id"
					class="PageCourses-listItem d-flex aic my-2"
					:class="{'PageCourses-listItem_active': activeCourse && activeCourse.id === course.id}"
					@click="selectCourse(c_index)"
				>
					<i class="PageCourses-listBars fa fa-bars mr-2 mt-1 pointer" />
					<p class="PageCourses-listTitle mb-0">
						{{ course.name }}
					</p>
					<i
						v-if="course.id"
						class="PageCourses-listDelete fa fa-trash btn btn-danger btn-icon ml-2"
						@click.stop="deleteCourse(c_index)"
					/>
				</div>
			</Draggable>

			<button
				class="btn-add"
				@click="modals.add_course.show = true"
			>
				Добавить курс
			</button>
		</div>


		<div class="PageCourses-body rp">
			<div class="hat">
				<div class="d-flex hat-top">
					<BreadCrumbs :items="bnreadCrumbsItems" />
				</div>
			</div>

			<div class="content mt-3">
				<div
					v-if="activeCourse"
					class="p-3"
				>
					<Course :id="activeCourse.id" />
				</div>
			</div>
		</div>

		<b-modal
			v-model="modals.add_course.show"
			title="Новый курс"
			size="md"
			class="modalle"
			hide-footer
		>
			<input
				v-model="modals.add_course.name"
				type="text"
				placeholder="Название курса..."
				class="form-control mb-2"
			>
			<button
				class="btn btn-primary rounded m-auto"
				:disabled="modals.add_course.name.length < 2"
				@click="createCourse"
			>
				<span>Сохранить</span>
			</button>
		</b-modal>
	</div>
</template>

<script>
import Draggable from 'vuedraggable'
import Course from '@/pages/Course.vue'
import BreadCrumbs from '@ui/BreadCrumbs.vue'

export default {
	name: 'PageCourses',
	components: {
		Draggable,
		Course,
		BreadCrumbs,
	},
	data() {
		return {
			courses: [],
			activeCourse: null,
			modals: {
				add_course: {
					show: false,
					name: ''
				}
			},
		};
	},
	computed: {
		bnreadCrumbsItems(){
			const items = [
				{title: 'Курсы'}
			]
			if(this.activeCourse) items.push({title: this.activeCourse.name})
			return items
		}
	},
	created() {
		this.$emit('init')
		this.fetchData();
	},
	mounted() {},
	methods: {
		async saveOrder(event) {
			await this.axios.post('/courses/save-order', {
				id: event.item.id,
				order: event.newIndex, // oldIndex
			})
			this.$toast.success('Очередь сохранена');
		},
		selectCourse(i) {
			this.activeCourse = this.courses[i];
			window.history.replaceState({ id: '100' }, 'Курсы', '/courses?id=' + this.activeCourse.id);
		},

		editAccess() {
			alert('Видимость и назначение курса отделам');
		},

		async createCourse() {
			if (this.modals.add_course.name.length <= 2) return alert('Слишком короткое название!')

			const loader = this.$loading.show();

			try {
				const {data} = await this.axios.post('/admin/courses/create', {
					name: this.modals.add_course.name,
				})
				this.modals.add_course.show = false;
				this.modals.add_course.name = '';

				this.courses.push({
					id: data.id,
					name: data.name,
					items: [],
				});

				this.activeCourse = this.courses[this.courses.length - 1]

				this.$toast.success('Курс успешно создан!');
			}
			catch (error) {
				alert(error);
			}

			loader.hide();
		},

		async deleteCourse(i) {
			if (!confirm('Вы уверены удалить курс?')) return
			const loader = this.$loading.show();

			try {
				await this.axios.post('/admin/courses/delete', {
					id: this.courses[i].id
				})
				this.$toast.success('Курс успешно удален!');
				this.courses.splice(i, 1)
				this.activeCourse = null;
			}
			catch (error) {
				alert(error);
			}

			loader.hide();
		},
		async fetchData() {
			const loader = this.$loading.show();

			try {
				const {data} = await this.axios.get('/admin/courses/get', {})
				this.courses = data.courses;

				const urlParams = new URLSearchParams(window.location.search);
				const course_id = urlParams.get('id');

				if(course_id) {
					const i = this.courses.findIndex(el => el.id == course_id)
					if(i != -1) this.activeCourse = this.courses[i]
				}
				else if (this.courses.length > 0) {
					this.activeCourse = this.courses[0];
				}
			}
			catch (error) {
				alert(error);
			}

			loader.hide();
		},
	},
};
</script>

<style lang="scss">
.PageCourses{
	// &-list{}
	&-listItem{
		font-size: 13px;
		font-weight: 600;
		cursor: pointer;

		&_active{
			.PageCourses{
				&-listBars,
				&-listTitle{
					color: #1b71f9;
				}
			}
		}
		&:hover{
			background-color: #eee;
		}
	}
	&-listBars,
	&-listDelete{
		flex: 0;
	}
	// &-listBars{}
	&-listTitle{
		flex: 1;
	}
	&-listDelete{
		margin-right: -10px;
	}
	&-body{
		flex: 1 1 0%;
		padding-bottom: 50px;
	}
}
</style>
