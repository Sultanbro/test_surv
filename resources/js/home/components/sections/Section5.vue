<!-- eslint-disable -->
<template>
	<section id="articles">
		<div class="articles section-content">
			<h2 class="articles__title">{{ $lang(lang, 'articles') }}</h2>
			<div class="articles__row">
				<div v-for="item in articles" :key="item.id" class="articles__item">
					<img
						:src="item.img"
						:alt="`Image for ${item.title}`"
						class="articles__item__img"
					/>
					<div class="articles__item__content">
						<RouterLink :to="`/articles/${item.id}`">
							<h3 class="articles__item__content__title">{{ item.title }}</h3>
						</RouterLink>
						<div class="articles__item__content__header">
							<time class="articles__item__content__date">{{ item.date }}</time>
							<!-- <div class="article__item__content__likes">
								<img src="../../assets/img/articles/thumb-up.svg" alt="" />aeaef
							</div> -->
						</div>
						<p class="articles__item__content__desc">{{ item.desc }}</p>
					</div>
				</div>
			</div>
		</div>
	</section>
</template>

<script>
// eslint-disable
export default {
	components: {},
	data() {
		return {
			articles: [
				{
					id: 1,
					title:
						'Будущее возобновляемой энергии: инновации и тенденции 2025 года',
					date: '12.11.2024',
					img: 'https://www.ixbt.com/img/n1/news/2023/0/1/industrie-energies-nouvelles_large.png',
					desc: 'Статья исследует последние достижения в технологиях возобновляемой энергии, включая прорывы в эффективности солнечных панелей, дизайне ветряных турбин и решениях для хранения энергии. Она также анализирует тенденции, которые могут сформировать отрасль в 2025 году и позже, предлагает мнения экспертов и прогнозирует потенциальное влияние на мировые энергетические рынки.',
					likes: 21,
					dislikes: 9,
				},
				{
					id: 2,
					title:
						'Озеленение городов: как миру удается превращать бетонные джунгли в зеленые райские уголки',
					date: '12.11.2024',
					img: 'https://turanpress.kz/upload/000/u2/8/c/zelyonoe-buduschee-kak-ozelenenie-gorodov-vlijaet-na-kachestvo-zhizni-naselenija-photo-big.jpg',
					desc: 'Статья рассматривает растущий тренд инициатив по озеленению в крупных городах мира. От крыш с садами в Токио до обширных зеленых поясов в Берлине, статья выделяет успешные примеры, обсуждает экологические и социальные преимущества таких проектов и предлагает идеи для их реализации в других городах.',
					likes: 35,
					dislikes: 5,
				},
				{
					id: 3,
					title:
						'Цифровая детоксикация: как мы возвращаем контроль над своим умом, освобождаясь от оков технологий',
					date: '12.11.2024',
					img: 'https://s3.eu-central-1.amazonaws.com/life4me.plus/uploads/photos/1489077308_58c1843c4f2ce.jpeg',
					desc: 'В эпоху, когда цифровое потребление достигло рекордных высот, эта статья погружается в движение цифровой детоксикации. Она включает личные истории людей, которые решили отступить от технологий, психологические и здоровьесберегающие преимущества, которые они испытали, а также советы для тех, кто заинтересован в подобном эксперименте. Статья также содержит комментарии психологов и экспертов по технологиям о важности поддержания здорового баланса между жизнью и цифровым миром.',
					likes: 40,
					dislikes: 3,
				},
			],
		};
	},
	computed: {
		lang() {
			return this.$root.$data.lang
		},
	},
	mounted() {},
	methods: {},
};
</script>

<style lang="scss">
@import "../../assets/scss/app.variables.scss";
.articles {
	&__title{
		text-align:center;
	}
	&__row {
		display: flex;
		flex-direction: column;
	}
	&__item {
		display: flex;
		margin-bottom: 40px;
		@media screen and (max-width: 768px) {
			flex-direction: column;
			align-items: center;
		}
		&__img {
			width: 300px;
			object-fit: cover;
			@media screen and (max-width: 768px) {
				margin-bottom: 20px;
			}
			@media screen and (max-width: 425px) {
				width: 100%;
			}
		}
		&__content {
			margin-left: 30px;
			@media screen and (max-width: 768px) {
				margin-left: 0px;
			}
			a{
				text-decoration: none;
			}
			&__title {
				margin: 0;
				text-decoration: none;
			}
			&__header {
				margin-top: 20px;
				display: flex;
				justify-content: space-between;
			}
			&__date {
			}
			&__likes {
				background: red;
			}
			&__desc {
				@media screen and (max-width: 768px) {
					font-size: 14px;
				}
			}
		}
	}
}
</style>
