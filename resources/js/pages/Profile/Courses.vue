<template>
	<div
		id="courses__anchor"
		class="courses__wrapper block _anim _anim-no-hide mt-4 Courses"
	>
		<div
			class="courses__content"
			:class="{'hidden': activeCourse}"
		>
			<div class="courses__title">
				Ваши курсы
			</div>
			<div
				v-if="canSlide"
				class="Courses-actions"
			>
				<div
					class="Courses-action Courses-action_prev"
					:class="{'Courses-action_disabled': !(currentSlide > 0)}"
					@click="prevSlide"
				/>
				<div
					class="Courses-action Courses-action_next"
					:class="{'Courses-action_disabled': !(currentSlide < canSlide)}"
					@click="nextSlide"
				/>
			</div>
			<div
				ref="slides"
				class="courses__content__wrapper"
			>
				<template v-if="courses.length">
					<div
						v-for="(course, index) in unfinished"
						:key="index"
						class="courses__item"
						:class="{'current': !index}"
					>
						<img
							:src="course.img ? course.img : '/images/course.jpg'"
							alt="курс"
							class="courses__image pointer"
							@click="selectCourse(index)"
						>
						<div
							class="courses__name pointer"
							@click="selectCourse(index)"
						>
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
						<!-- <div
							v-if="isRegressed(course)"
							class="courses__regress"
						>
							<div class="courses__regress-message">
								Курс обнулен!
							</div>
						</div> -->
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
			v-if="activeCourse"
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
						<!-- eslint-disable vue/no-v-html -->
						<div
							class="profit__info-text"
							v-html="activeCourse.text"
						/>
						<div
							class="profit__info-text mobile"
							v-html="activeCourse.text"
						/>
						<!-- eslint-enable vue/no-v-html -->
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
				680: 3,
				1200: 4,
				1360: 3,
				1480: 4,
				1600: 5,
				2140: 6,
			},
			currentSlide: 0,
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
		canSlide(){
			return Math.max(this.unfinished.length - this.slidesToShow, 0)
		}
	},
	watch: {
		courses(){
			this.initCourses()
		},
		slidesToShow(){
			// this.resizeCarousel()
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
			// this.$nextTick(() => this.initSlider())
			window.addEventListener('resize', this.resizeCarousel)
		},
		resizeCarousel(){
			// const $coursesWrapper = VJQuery('.courses__content__wrapper')
			// if($coursesWrapper[0] && $coursesWrapper[0].slick) $coursesWrapper.slick('slickSetOption', 'slidesToShow', this.slidesToShow, true)
			this.$nextTick(this.scrollToSlide)
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
			const $slickSlider = VJQuery('.courses__content__wrapper')
			$slickSlider.on('afterChange', function (e, slick) {
				var lElRect = slick.$slides[slick.slideCount - 1].getBoundingClientRect()
				var rOffset = lElRect.x + lElRect.width
				var wraRect = $slickSlider.find('.slick-list').get(0).getBoundingClientRect()
				if (rOffset < (wraRect.x + wraRect.width)) {
					$slickSlider.find('.slick-next').addClass('slick-disabled')
				}
			})
			$slickSlider.on('breakpoint', () => {
				setTimeout(() => {
					$slickSlider.find('.slick-slide').forEach(el => {
						el.style.width = (parseFloat(el.style.width) - 4) + 'px'
					})
				}, 1)
			})
			this.resizeCarousel()
		},

		/**
		 * private: helper for template
		 * count progress of course item
		 */
		itemProgress(item) {
			return item.all_stages > 0
				? Number((item.completed_stages / item.all_stages) * 100).toFixed(1)
				: 0;
		},

		nextSlide(){
			if(!(this.currentSlide < this.canSlide)) return
			++this.currentSlide
			this.scrollToSlide()
		},
		prevSlide(){
			if(!(this.currentSlide > 0)) return
			--this.currentSlide
			this.scrollToSlide()
		},
		scrollToSlide(){
			const slides = this.$refs.slides
			const width = slides.offsetWidth
			const slideWidth = width / this.slidesToShow
			slides.scrollTo({
				left: slideWidth * this.currentSlide,
				behavior: 'smooth',
			})
		},
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
.courses__content__wrapper{
	.slick-slide{
		> div{
			height: 100%;
		}
	}
	.courses__item{
		display: flex;
		flex-flow: column nowrap;
		height: 100%;
		margin: 10px;
	}
}

// slickCount: {
// 	520: 2,
// 	680: 3,
// 	1200: 4,
// 	1360: 3,
// 	1480: 4,
// 	1600: 5,
// 	2140: 6,
// }
.courses__content{
	&:after{
		content: '';
		height: 30px;
		position: absolute;
		z-index: 5;
		left: 0;
		right: 0;
		bottom: 0;
		background: #F8F9FD;
	}
}
.courses__content__wrapper{
	display: flex;
	flex-flow: row nowrap;

	margin: -10px;
	// padding: 10px;
	padding-bottom: 30px;

	position: relative;

	overflow-x: auto;
	scroll-snap-type: x mandatory;
	scroll-padding: 10px;
	scroll-behavior: smooth;

	.courses__item{
		scroll-snap-align: start;
		flex: 0 0 calc(100% - 20px);
	}
}
@media (min-width: 520px) {
	.courses__content__wrapper{
		.courses__item{
			flex: 0 0 calc(50% - 20px);
		}
	}
}
@media (min-width: 680px) {
	.courses__content__wrapper{
		.courses__item{
			flex: 0 0 calc(33.333% - 20px);
		}
	}
}
@media (min-width: 1200px) {
	.courses__content__wrapper{
		.courses__item{
			flex: 0 0 calc(25% - 20px);
		}
	}
}
@media (min-width: 1360px) {
	.courses__content__wrapper{
		.courses__item{
			flex: 0 0 calc(33.333% - 20px);
		}
	}
}
@media (min-width: 1480px) {
	.courses__content__wrapper{
		.courses__item{
			flex: 0 0 calc(25% - 20px);
		}
	}
}
@media (min-width: 1600px) {
	.courses__content__wrapper{
		.courses__item{
			flex: 0 0 calc(20% - 20px);
		}
	}
}
@media (min-width: 2140px) {
	.courses__content__wrapper{
		.courses__item{
			flex: 0 0 calc(16.666% - 20px);
		}
	}
}

.Courses{
	&-actions{
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 5px;

		position: absolute;
		top: 2rem;
		right: 2rem;
	}
	&-action{
		width: 2rem;
		height: 2rem;
		opacity: 0.9;
		cursor: pointer;
		&:hover{
			opacity: 1;
		}
		&_prev{
			background: url(/images/info-arrow-prev.svg?892948cfd03d132d274cabe5a3073b99) 50% no-repeat;
			background-size: cover;
		}
		&_next{
			background: url(/images/info-arrow-next.svg?e2f543056600235aa0b8104c3af5ac0e) 50% no-repeat;
			background-size: cover;
		}
		&_disabled{
			opacity: 0.5;
			pointer-events: none;
		}
	}
}
@media (min-width: 778px) {
	.Courses{
		&-actions{
			top: 3rem;
			right: 3rem;
		}
	}
	.courses__content{
		&:after{
			bottom: 3rem;
		}
	}
}
</style>
