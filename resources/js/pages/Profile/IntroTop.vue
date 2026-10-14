<template>
	<div class="intro__top _anim _anim-no-hide block">
		<a
			href="javascript:void(0)"
			class="intro__top-burger"
		>
			<img
				src="images/dist/intro-burger.svg"
				alt="burger menu"
				class=""
			>
		</a>
		<div class="intro__top-buttons">
			<a
				href="#courses__anchor"
				class="intro__button button-blue"
				@click.prevent="scrollTo('#courses__anchor')"
			>Ваши курсы</a>
			<a
				v-if="profit"
				href="#profit"
				class="intro__button button-blue"
				@click.prevent="scrollTo('#profit')"
			>Как можно зарабатывать больше</a>
			<a
				v-if="estimation"
				href="#trainee"
				class="intro__button button-blue"
				@click.prevent="scrollTo('#trainee')"
			>Вот как стажеры оценили ваше обучение</a>
			<a
				v-if="indicators"
				href="#index"
				class="intro__button button-blue"
				@click.prevent="scrollTo('#index')"
			>Ваши показатели</a>
		</div>
	</div>
</template>

<script>

export default {
	name: 'IntroTop',
	props: {
		courses: Boolean,
		profit: Boolean,
		estimation: Boolean,
		indicators: Boolean,
	},
	data: function () {
		return {
			fields: [],
		};
	},
	methods: {
		scrollTo(el){
			document.querySelector(el).scrollIntoView({behavior: 'smooth'})
		}
	}
};
</script>