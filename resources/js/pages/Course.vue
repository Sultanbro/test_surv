<template>
	<div class="PageCourse p-3">
		<div class="d-flex relative align-items-start gap-3">
			<div class="w-full namer">
				<input
					v-model="course.name"
					type="text"
					class="PageCourse-title mb-3"
					placeholder="Название курса"
				>
			</div>
			<button
				class="btn btn-success"
				@click="saveCourse"
			>
				Сохранить
			</button>
		</div>

		<div class="PageCourse-meta mb-3 fz-12">
			<div class="d-flex gap-3 mb-2">
				<b>Автор:</b>
				<span>{{ course.author }}</span>
			</div>
			<div class="d-flex gap-3">
				<b>Создано:</b>
				<span>{{ course.created }}</span>
			</div>
		</div>

		<div class="PageCourse-description d-flex gap-3 mb-3">
			<textarea
				v-model="course.text"
				class="PageCourse-desc form-control"
				placeholder="Описание курса"
			/>

			<!-- profile image -->
			<div>
				<img
					width="250px"
					height="250px"
					:src="course.img"
					alt=""
					@click="onImage"
				>
			</div>
		</div>

		<div class="PageCourse-items">
			<p class="PageCourse-itemsTitle">
				Курс состоит из ({{ course.elements.length }}):
			</p>

			<div class="PageCourse-itemsArea">
				<Draggable
					class="ml-0 mr-5"
					tag="ul"
					handle=".fa-bars"
					:list="course.elements"
					:group="{ name: 'g1' }"
					@end="saveOrder"
				>
					<template v-for="(el, e_index) in course.elements">
						<li
							:id="el.id"
							:key="e_index"
							class="PageCourse-item chapter opened d-flex aic mb-2"
							:class="{'deleted' : el.deleted}"
						>
							<div class="handles">
								<i class="fa fa-bars mover" />
								<i class="fa fa-caret-right pointer shower" />
							</div>
							<div>
								<i
									class="fa pointer mr-2"
									:class="[`fa-${['book', 'play', 'database'][el.type - 1]}`]"
								/>
							</div>
							<p
								class="mb-0"
								@click="toggleOpen(el)"
							>
								{{ el.name }}
								<i
									v-if="el.deleted"
									v-b-popover.hover.right.html="'Элемент был удален'"
									class="fa fa-info-circle pointer ml-2"
									title="Не найдено"
								/>
							</p>
							<i
								class="fa fa-trash btn btn-danger btn-icon btn-sm pointer ml-2"
								@click.stop="deleteItem(e_index)"
							/>
						</li>
					</template>
				</Draggable>
				<SuperSelectAlt
					:key="1"
					:values="course.elements"
					:hide_selected="true"
					class="PageCourse-itemsAdd w-full"
				>
					<template #beforeSelected>
						<button
							class="btn btn-success btn-sm"
							@click="saveCourse"
						>
							+ Добавить
						</button>
					</template>
				</SuperSelectAlt>
			</div>

			<div class="mt-4 pr-5">
				Курс проходят:
				<AccessSelectFormControl
					:items="course.targets"
					class="mt-3"
					@click="isAccessOverlay = true"
				/>
			</div>
		</div>

		<JobtronCropper
			v-if="isCropper"
			:image="image"
			:options="{
				viewport: {
					width: 250,
					height: 250,
					type: 'square'
				}
			}"
			@result="onCrop"
			@close="isCropper = false"
		/>

		<JobtronOverlay
			v-if="isAccessOverlay"
			@close="isAccessOverlay = false"
		>
			<AccessSelect
				v-model="course.targets"
				:access-dictionaries="accessDictionaries"
				submit-button=""
				absolute
			/>
		</JobtronOverlay>
	</div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import Draggable from 'vuedraggable'
import SuperSelectAlt from '@/components/SuperSelectAlt'
// import SuperSelect from '@/components/SuperSelect'
import JobtronCropper from '@ui/Cropper.vue'
import AccessSelect from '@ui/AccessSelect/AccessSelect.vue'
import AccessSelectFormControl from '@ui/AccessSelect/AccessSelectFormControl.vue'
import JobtronOverlay from '@ui/Overlay.vue'

export default {
	name: 'PageCourse',
	components: {
		Draggable,
		SuperSelectAlt,
		// SuperSelect,
		JobtronCropper,
		AccessSelect,
		AccessSelectFormControl,
		JobtronOverlay,
	},
	props: {
		id: {
			type: Number,
			default: 0
		}
	},
	data() {
		return {
			test: 'dsa',
			hover: false,
			file: null,
			newItem: null,
			course: {
				id: 0,
				elements: [],
				img: '/users_img/noavatar.png'
			},
			superselectKey: 1,

			isCropper: false,
			image: null,
			isAccessOverlay: false,
		};
	},
	computed: {
		...mapGetters([
			'users',
			'accessDictionaries',
		])
	},
	watch: {
		id() {
			this.get();
		},
	},
	created() {
		this.get();
		if(!this.users.length){
			this.loadCompany()
		}
	},
	mounted() {},
	methods: {
		...mapActions([
			'loadCompany',
		]),
		async get() {
			const loader = this.$loading.show()

			try {
				const {data} = await this.axios.post('/admin/courses/get-item', {
					id: this.id,
				})
				this.course = data.course
				this.superselectKey++
			}
			catch (error) {
				alert(error);
			}

			loader.hide()
		},

		toggleOpen(/* el */) {},

		saveOrder(/* e */) {},

		deleteItem(i) {
			this.course.elements.splice(i, 1)
		},

		async saveCourse() {
			const loader = this.$loading.show()

			try {
				await this.axios.post('/admin/courses/save', {
					course: this.course,
				})
				this.$toast.success('Успешно сохранено!')
			}
			catch (error) {
				alert(error)
			}

			loader.hide()
		},

		limitText(count) {
			return `и еще ${count}`
		},

		onImage(){
			const input = document.createElement('input')
			input.type = 'file'
			input.accept = 'image/*'

			input.onchange = e => {
				const file = e.target.files[0]
				if (file) {
					this.image = file
					this.isCropper = true
				}
			}
			input.click()
		},

		async onCrop(blob){
			this.isCropper = false
			const loader = this.$loading.show()
			const formData = new FormData()
			formData.append('file', blob);
			formData.append('course_id', this.course.id);

			try {
				const {data} = await this.axios.post('/admin/courses/upload-image', formData)
				this.course.img = data.img;
				this.$toast.success('Сохранено');
			}
			catch (error) {
				this.$toast.error('Изображение не сохранено');
			}

			loader.hide();
		}
	},
};
</script>

<style lang="scss">
.PageCourse {
	&-title {
		width: 100%;
		padding: 3px 0;
		border:none;

		font-size: 20px;
		font-weight: 600;
		color: #262626;
	}

	&-desc{
		border: none;
		resize: none;
	}

	// &-items {}
	&-itemsTitle {
		margin-bottom: 10px;
		font-size: 16px;
		font-weight: 600;
	}

	&-itemsArea {
		padding: 15px 30px;
		border: 1px dashed #ccc;

		border-radius: 5px;
		background: #f8f8f8;
	}

	&-item{
		// min-height: 35px;
		.btn.btn-icon{
			$size: 28px;
			width: $size;
			height: $size;
			min-width: $size;
		}
	}

	&-itemsAdd{
		&.super-select{
			min-height: 16px;
			border: none;
			background: none;
			.selected-items{
				min-height: 16px;
				padding: 0;
			}
		}
	}

	.chapter {
		.fa-trash {
			display: none;
		}
		&:hover {
			.fa-trash {
				display: inline-flex;
			}
		}
	}

	.deleted {
		p {
			color: red !important;
		}
	}
}
</style>
