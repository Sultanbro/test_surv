<template>
	<div>
		<NavBar />
		<div class="section-content">
			<div class="article">
				<h2 class="article__title">
					Будущее возобновляемой энергии: инновации и тенденции 2025 года
				</h2>
				<img
					src="https://www.ixbt.com/img/n1/news/2023/0/1/industrie-energies-nouvelles_large.png"
					alt=""
					class="article__img"
				>
				<p class="article__desc">
					Статья исследует последние достижения в технологиях возобновляемой энергии, включая 
					прорывы в эффективности солнечных панелей, дизайне ветряных турбин и решениях для 
					хранения энергии. Она также анализирует тенденции, которые могут сформировать отрасль в 2025 году 
					и позже, предлагает мнения экспертов и прогнозирует потенциальное влияние на мировые энергетические рынки.
				</p>
			</div>
		</div>
		<SectionFooter />
	</div>
</template>

<script>
import NavBar from '../components/sections/Navbar.vue';
import SectionFooter from '../components/sections/Footer.vue';

export default {
	components: {
		NavBar,
		SectionFooter,
	},
};
</script>

<style lang="scss">
@import "../assets/scss/app.variables.scss";


.article {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 50px 0;
    &__title {
    }
    &__img {
        @media screen and (max-width: 768px) {
            width: 100%;
        }
    }
    &__desc {
    }
}

</style>
