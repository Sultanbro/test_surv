<template>
	<div
		id="courses__anchor"
		class="courses__wrapper block _anim _anim-no-hide mt-4"
	>
		<div
			class="courses__content"
			:class="{'hidden': activeCourse !== null}"
		>
			<div class="courses__title">
				Ваши курсы
			</div>
			<div class="courses__content__wrapper">
				<template v-if="courses.length">
					<div
						v-for="(course, index) in unfinished"
						:key="index"
						class="courses__item"
						:class="{'current': index == 0}"
					>
						<img
							v-if="course.img"
							:src="course.img"
							alt="курс"
							class="courses__image"
							@click="selectCourse(index)"
						>
						<img
							v-else
							src="/images/course.jpg"
							alt="курс"
							class="courses__image"
							@click="selectCourse(index)"
						>
						<div class="courses__name">
							{{ course.name }}
						</div>
						<div class="courses__progress">
							<div
								v-if="courseInfo[course.id]"
								class="courses__line"
								:style="`width: ${courseInfo[course.id].progress}%`"
							/>
						</div>
						<!-- Линия зависит от процентов в span-->
						<div class="courses__percent">
							<template v-if="courseInfo[course.id]">
								Пройдено: <span>{{ courseInfo[course.id].progress }}%</span>
							</template>
							<template v-else>
								&nbsp;
							</template>
						</div>
						<div
							v-if="isRegressed(course)"
							class="courses__regress"
						>
							<div class="courses__regress-message">
								Курс обнулен!
							</div>
						</div>
						<a
							:href="'/my-courses?id=' + course.id"
							class="courses__button"
						>
							<span>{{ results[course.id] ? 'Продолжить курс' : 'Начать курс' }}</span>
						</a>
					</div>
				</template>
				<template v-else-if="canAddCourses">
					<router-link
						to="/courses"
						class="courses-add"
					>
						Добавить курсы
					</router-link>
				</template>
			</div>
		</div>

		<div
			v-if="activeCourse !== null"
			class="profit__info active"
		>
			<div class="profit__info-title">
				Информация о курсе: {{ activeCourse.name }}
			</div>
			<div
				class="profit__info-back"
				@click="back"
			>
				Назад
			</div>
			<div class="profit__info-back-mobile" />
			<div class="profit__info__inner">
				<div class="profit__info__item">
					<img
						:src="activeCourse.img || '/images/course.jpg'"
						alt="info image"
						class="profit__info-image"
					>
					<div class="profit__info-about">
						<div
							class="profit__info-text"
							v-html="activeCourse.text"
						/>
						<div
							class="profit__info-text mobile"
							v-html="activeCourse.text"
						/>
						<div class="profit__info__wrapper">
							<template v-if="courseInfo[activeCourse.id] && courseInfo[activeCourse.id].items">
								<div
									v-for="(item, index) in courseInfo[activeCourse.id].items"
									:key="index"
									class="info__wrapper-item"
									:class="{'done': item.status == 1}"
								>
									<a
										:href="`/my-courses?id=${activeCourse.id}`"
										class="info__item-box"
									>
										<i
											class="info__item-icon"
											:class="[modelIcon[item.item_model]]"
										/>
										<p class="info__item-stages">{{ item.completed_stages }} / {{ item.all_stages }}</p>
									</a>
									<div class="info__item-value">
										{{ itemProgress(item) }}%
									</div>
									<div class="info__item-value">
										{{ modelName[item.item_model] }}
									</div>
									<div class="info__item-value">
										{{ item.title }}
									</div>
								</div>
							</template>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useProfileCoursesStore } from '@/stores/ProfileCourses'
export default {
	name: 'ProfileCourses',
	props: {},
	data: function () {
		return {
			activeCourse: null,
			loading: false,
			modelName: {
				'App\\Models\\Books\\Book': 'Книга',
				'App\\Models\\Videos\\VideoPlaylist': 'Видеоплейлист',
				'App\\KnowBase': 'База знаний'
			},
			modelIcon: {
				'App\\Models\\Books\\Book': 'icon-ci-book',
				'App\\Models\\Videos\\VideoPlaylist': 'icon-ci-play',
				'App\\KnowBase': 'icon-ci-database'
			},
			slickCount: {
				520: 2,
				940: 3,
				1200: 4,
				1360: 3,
				1600: 4,
				1800: 5,
				2140: 6,
			}
		};
	},
	computed: {
		...mapState(useProfileCoursesStore, ['courses', 'courseInfo', 'results']),
		coursesMap(){
			return this.courses.reduce((map, item) => {
				map[item.id] = item
				return map
			}, {})
		},
		unfinished(){
			return this.courses.reduce((list, course) => {
				if(!this.courseInfo[course.id]){
					// пока неизветсно прошел курс или нет считаем что не прошел
					list.push(course)
					return list
				}
				const info = this.courseInfo[course.id]
				if(info.progress === 100) return list
				list.push(course)
				return list
			}, [])
		},
		viewportWidth(){
			return this.$viewportSize.width
		},
		slidesToShow(){
			let slidesToShow = 1
			Object.keys(this.slickCount).forEach(key => {
				if(this.viewportWidth > key) slidesToShow = this.slickCount[key]
			})
			return slidesToShow
		},
		canAddCourses(){
			return this.$laravel.is_admin
		},
	},
	watch: {
		courses(){
			this.initCourses()
		},
		slidesToShow(){
			this.resizeCarousel()
		}
	},
	created(){},
	mounted(){
		if(this.courses.length) this.initCourses()
	},
	methods: {
		...mapActions(useProfileCoursesStore, ['fetchCourseInfo']),
		initCourses(){
			this.courses.forEach(course => {
				this.fetchCourseInfo(course.id)
			})
			this.$nextTick(() => this.initSlider())
			window.addEventListener('resize', this.resizeCarousel)
		},
		resizeCarousel(){
			const $coursesWrapper = VJQuery('.courses__content__wrapper')
			if($coursesWrapper[0] && $coursesWrapper[0].slick) $coursesWrapper.slick('slickSetOption', 'slidesToShow', this.slidesToShow, true)
		},
		isRegressed(course){
			if(!this.results[course.id] || !this.results[course.id][0]) return false
			return !!this.results[course.id][0].is_regressed
		},

		/**
		 * select active course info
		 */
		selectCourse(index) {
			this.activeCourse = this.courses[index]
		},

		/**
		 * back to all courses
		 */
		back() {
			this.activeCourse = null;
		},

		/**
		 * init slider
		 */
		initSlider() {
			if(!this.courses.length) return
			/* global VJQuery */
			VJQuery('.courses__content__wrapper').slick({
				variableWidth: false,
				infinite: false,
				slidesToShow: 6
			});

			// https://github.com/kenwheeler/slick/issues/3694
			// but it's better to replace slick with native for vue
			const $slick_slider = VJQuery('.courses__content__wrapper')
			$slick_slider.on('afterChange', function (e, slick) {
				var lElRect = slick.$slides[slick.slideCount - 1].getBoundingClientRect()
				var rOffset = lElRect.x + lElRect.width
				var wraRect = $slick_slider.find('.slick-list').get(0).getBoundingClientRect()
				if (rOffset < (wraRect.x + wraRect.width)) {
					$slick_slider.find('.slick-next').addClass('slick-disabled')
				}
			})
			$slick_slider.on('breakpoint', () => {
				setTimeout(() => {
					$slick_slider.find('.slick-slide').forEach(el => {
						el.style.width = (parseFloat(el.style.width) - 4) + 'px'
					})
				}, 1)
			})
			this.resizeCarousel()
		},

		/**
		 * init inner slider that opens when click concrete course
		 */
		initInnerSlider() {
			VJQuery('.profit__info__wrapper').slick({
				variableWidth: false,
				infinite: false,
				slidesToScroll: 2,
				slidesToShow: 10,
				responsive: [
					{
						breakpoint: 2140,
						settings: {
							variableWidth: false,
							infinite:false,
							swipeToSlide: false,
							slidesToScroll: 2,
							slidesToShow: 9,
						}
					},
					{
						breakpoint: 2000,
						settings: {
							variableWidth: false,
							infinite:false,
							swipeToSlide: false,
							slidesToScroll: 2,
							slidesToShow: 6,
						}
					},
					{
						breakpoint: 1800,
						settings: {
							variableWidth: false,
							infinite:false,
							swipeToSlide: false,
							slidesToScroll: 2,
							slidesToShow: 5,
						}
					},
					{
						breakpoint: 1600,
						settings: {
							infinite: true,
							variableWidth: true,
							swipeToSlide: true,
							slidesToShow: 1,
						}
					},

					{
						breakpoint: 780,
						settings: {
							variableWidth: false,
							infinite:false,
							slidesToShow: 2,
							slidesToScroll: 2,
							swipeToSlide: false,
						}
					},
					{
						breakpoint: 500,
						settings: {
							variableWidth: false,
							infinite:false,
							slidesToShow: 1,
							slidesToScroll: 1,
							swipeToSlide: false,
						}
					},

				]

			});
		},

		/**
		 * private: helper for template
		 * count progress of course item
		 */
		itemProgress(item) {
			return item.all_stages > 0
				? Number((item.completed_stages / item.all_stages) * 100).toFixed(1)
				: 0;
		}
	}
};
</script>

<style lang="scss">
// https://github.com/kenwheeler/slick/issues/3694
.slick-disabled {
	cursor: no-drop;
	opacity: 0.5;
	pointer-events: none;
}
.courses__content{
	.slick-list{
		width: 100%;
	}
}

// .courses__content__wrapper{}
.courses__item{
	position: relative;
	text-align: center;
	box-sizing: border-box;
	&:hover{
		box-shadow: inset 0 0 5px #8FAF00;
		.courses__regress{
			display: block;
		}
	}
}
.courses__regress{
	display: none;
	border-radius: 2rem;

	position: absolute;
	z-index: 10;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;

	background-color: rgba(0,0,0,0.25);

	pointer-events: none;

	&-message{
		position: absolute;
		top: 50%;
		left: 50%;

		text-align: center;
		color: red;
		font-size: 1.4rem;
		font-weight: 700;
		text-shadow: 0 -2px 1px #fff, 0 2px 1px #fff, 2px 0 1px #fff, -2px 0 1px #fff;

		transform: translate(-50%, -50%);
	}
}
</style>
