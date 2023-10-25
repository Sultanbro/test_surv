<template>
	<div
		id="profit"
		class="profit block _anim _anim-no-hide"
		:class="{
			'hidden': slides.length == 0,
			'v-loading': loading
		}"
	>
		<div class="profit__title title mt-6">
			Как можно зарабатывать больше
		</div>
		<div class="profit__subtitle subtitle">
			Информация, которая может быть полезна для Вашего карьерного роста
		</div>
		<div class="row profit__inner mr-1 ml-1">
			<div
				v-if="hasGroups"
				class="col profit__carousel profit__carousel_left"
				:class="{'col-md-6': hasPositions}"
			>
				<div
					v-for="(slide, i) in groups"
					:key="i"
					class="profit__inner-item left-slide"
				>
					<div
						class="profit__inner__left"
						:class="{'profit__inner__one': !hasPositions}"
					>
						<div class="profit__left-wrapper">
							<div class="profit__inner-title">
								{{ slide.title }}
							</div>
							<a href="javascript:void(0)">
								<img
									v-b-popover.hover.right.html="'Тут описано именно то, за что в Вашем отделе оплачивается работа'"
									src="/images/dist/profit-info.svg"
									alt="info icon"
								>
							</a>
						</div>
						<!-- eslint-disable vue/no-v-html -->
						<div
							class="profit__inner-text"
							v-html="slide.text"
						/>
						<!-- eslint-enable vue/no-v-html -->
					</div>
					<div class="profit__arrows">
						<a
							href="javascript:void(0)"
							class="profit__prev"
						/>
						<a
							href="javascript:void(0)"
							class="profit__next"
						/>
					</div>
				</div>
			</div>
			<div
				v-if="hasPositions"
				class="col profit__carousel profit__carousel_right"
				:class="{'col-md-6': hasGroups}"
			>
				<div
					v-for="(slide, i) in positions"
					:key="i"
					class="profit__inner-item right-slide"
				>
					<div
						class="profit__inner-right"
						:class="{'profit__inner__one': !hasGroups}"
					>
						<div class="profit__left-wrapper">
							<div class="profit__inner-title">
								{{ slide.title }}
								<a href="javascript:void(0)">
									<img
										v-b-popover.hover.right.html="'У Вас обязательно будет карьерный рост в компании, и здесь описаны требования, необходимые знания и навыки для перехода на следующую ступень карьерной лестницы. Обязательно ознакомьтесь с разделом и задайте возникшие вопросы по карьерному росту Вашему руководителю.'"
										src="/images/dist/profit-info.svg"
										alt="info icon"
									>
								</a>
							</div>
						</div>
						<!-- eslint-disable vue/no-v-html -->
						<div
							class="profit__inner-text profit-right"
							v-html="slide.text"
						/>
						<!-- eslint-enable vue/no-v-html -->
					</div>
					<div class="profit__arrows">
						<a
							href="javascript:void(0)"
							class="profit__prev"
						/>
						<a
							href="javascript:void(0)"
							class="profit__next"
						/>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { mapState } from 'pinia'
import { usePaymentTermsStore } from '@/stores/PaymentTerms'
// слайдер с условиями оплаты для отделов и должности
export default {
	name: 'ProfileProfit',
	props: {},
	data: function () {
		return {
			slides: [],
			loading: false
		};
	},
	computed: {
		...mapState(usePaymentTermsStore, ['groups', 'position']),
		hasGroups(){
			return !!(this.groups && this.groups.length)
		},
		hasPositions(){
			return !!this.position
		},
		positions(){
			if(!this.position) return []
			return [
				{
					title: 'Следующая ступень карьерного ростa',
					text: this.position.next_step,
				},
				{
					title: 'Требования к кандидату',
					text: this.position.require,
				},
				{
					title: 'Что нужно делать',
					text: this.position.actions,
				},
				{
					title: 'График работы',
					text: this.position.time,
				},
				{
					title: 'Заработная плата',
					text: this.position.salary,
				},
				{
					title: 'Нужные знания для перехода на следующую должность',
					text: this.position.knowledge,
				},
			]
		}
	},
	watch: {
		groups(){
			this.init()
		}
	},
	created(){
		this.init()
	},
	methods: {
		init(){
			if(this.hasGroups || this.hasPositions){
				this.$emit('init')
				this.form()
			}
		},

		/**
		 * form array for slider
		 */
		form() {
			/**
			 * groups' terms
			 */
			let to = Math.ceil(this.groups.length / 2);

			let lastKey = 0
			let lastLeftBlock = null

			for(let i = 0; i < to; i++) {
				let left = null,
					right = null

				/**
				 * define left and right side of slide
				 */
				left = {
					title: this.groups[lastKey].title,
					text: this.groups[lastKey].text
				}


				lastKey++;

				if(this.groups[lastKey] !== undefined) {
					right = {
						title: this.groups[lastKey].title,
						text: this.groups[lastKey].text
					}
				}

				/**
				 * push to slides
				 */
				if(right !== null) {
					this.slides.push({
						left: left,
						right: right,
					})
				} else {
					lastLeftBlock = left;
				}
			}

			/**
			 * position terms
			 */
			if (this.position !== null) {
				this.addPositionSlides(lastLeftBlock)
			}
			else if (lastLeftBlock !== null) {
				this.slides.push({
					left: lastLeftBlock,
					right: {title: '', text: ''},
				})
			}

			/**
			 * init slider
			 */
			this.$nextTick(() => this.initSlider())
		},

		/**
		 * private: continue form slides
		 */
		addPositionSlides(lastLeftBlock) {

			// if(lastLeftBlock !== null) items.unshift(lastLeftBlock);
			if(lastLeftBlock !== null) {
				this.slides.push({
					left: lastLeftBlock,
					right: this.positions[0]
				});

				this.slides.push({left: this.positions[1], right: this.positions[2]});
				this.slides.push({left: this.positions[3], right: this.positions[4]});
				this.slides.push({left: this.positions[5], right: {title:'', text: ''}});
			} else {
				this.slides.push({left: this.positions[0], right: this.positions[1]});
				this.slides.push({left: this.positions[2], right: this.positions[3]});
				this.slides.push({left: this.positions[4], right: this.positions[5]});
			}
		},

		/**
		 * init slider for this block
		 */
		initSlider() {
			/* global VJQuery */
			VJQuery('.profit__carousel').slick({
				infinite: true,
				speed: 400,
				fade: true,
				adaptiveHeight: this.$viewportSize.width <= 768
			});
			VJQuery('.profit__prev').on('click', function(e) {
				e.preventDefault();
				VJQuery('.profit__inner').slick('slickPrev');
			});
			VJQuery('.profit__next').on('click', function(e) {
				e.preventDefault();
				VJQuery('.profit__inner').slick('slickNext');
			});

			if(this.$viewportSize.width > 767){
				const $slickSliders = VJQuery('.profit__carousel')
				$slickSliders.on('afterChange', () => {
					this.evenSlides($slickSliders)
				})
				this.evenSlides($slickSliders)
			}

			/**
			 * set some style
			 */
			// if(this.$viewportSize.width > 767){
			// 	let leftSlides = document.getElementsByClassName('left-slide');
			// 	let rightSlides = document.getElementsByClassName('right-slide');
			// 	let height = 0;

			// 	for(let i = 0; i < leftSlides.length; i++) {
			// 		for(let j = 0; j < rightSlides.length; j++) {
			// 			const leftHeight = leftSlides[i].offsetHeight;
			// 			const rightHeight = rightSlides[j].offsetHeight;
			// 			height = leftHeight > rightHeight ? leftHeight : rightHeight;
			// 		}
			// 	}

			// 	// const arr = [1,1,1,2,3,4];
			// 	[...leftSlides].forEach(data => {data.style.minHeight = height + 'px'});
			// 	[...rightSlides].forEach(data => {data.style.minHeight = height + 'px'});
			// }
		},
		evenSlides($slickSliders){
			let height = 0
			$slickSliders.each((i, el) => {
				const $slider = VJQuery(el)
				const slideIndex = $slider.slick('slickCurrentSlide')
				const h = $slider.find('.profit__inner-item').eq(slideIndex).height()
				if(h > height) height = h
			})
			$slickSliders.find('.slick-list').height(height)
			$slickSliders.find('.slick-slide').height(height)
		}
	}
};
</script>

<style lang="scss">
.profit__inner{
	.col-6, .col-md-6{
		padding:0!important;
	}
}
</style>
