<template>
	<div class="PromotionalMaterial">
		<div class="PromotionalMaterial-jumbotron mb-4">
			<h1 class="PromotionalMaterial-header">
				Зарабатывайте
				<br>
				<span class="PromotionalMaterial-headerHighlight">
					вмесе с нами
				</span>
			</h1>
			<div class="PromotionalMaterial-headerDescription">
				Мы подготовили для Вас
				<br>
				все необходимое
				<br>
				<span class="PromotionalMaterial-headerSmall">
					Готовые помощники для ваших клиентов
				</span>
			</div>
		</div>

		<div class="PromotionalMaterial-content">
			<b-tabs>
				<!-- <b-tab title="Документы">
					<div class="d-flex gap-4 mt-4">
						<PromotionalMaterialFile
							name="test"
							href="javascript:void(0)"
						/>
						<PromotionalMaterialFile
							name="test"
							href="javascript:void(0)"
						/>
						<PromotionalMaterialFile
							name="test"
							href="javascript:void(0)"
						/>
					</div>
				</b-tab> -->
				<b-tab title="Баннеры">
					<div class="d-flex gap-4 mt-4">
						<PromotionalMaterialFile
							name="Баннеры"
							type="archive"
							href="/static/banners.rar"
						/>
					</div>
				</b-tab>
				<!-- <b-tab title="Mail рассылки и соцсети">
					<div class="d-flex gap-4 mt-4">
						<PromotionalMaterialFile
							name="test"
							href="javascript:void(0)"
						/>
					</div>
				</b-tab> -->
				<!-- <b-tab title="HTML шаблоны для Mail рассылки">
					<div class="d-flex gap-4 mt-4">
						<PromotionalMaterialFile
							name="test"
							href="javascript:void(0)"
						/>
					</div>
				</b-tab> -->
			</b-tabs>
		</div>
	</div>
</template>

<script>
import PromotionalMaterialFile from '../components/pages/PromotionalMaterial/PromotionalMaterialFile.vue'
export default {
	name: 'PromotionalMaterial',
	components: {
		PromotionalMaterialFile,
	},
}
</script>

<style lang="scss">
.PromotionalMaterial{
	max-width: 1200px;
	padding: 2rem;
	margin: 0 auto;
	&-jumbotron{
		container: PromotionalMaterialJumbotron / inline-size;

		padding-bottom: 48.46905537459283%;

		position: relative;

		background-image: url(/images/promotional-material.png);
		background-size: cover;
	}
	&-header{
		position: absolute;
		font-weight: 700;
		color: #fff;
		text-shadow: -2px 2px #333;
	}
	&-headerHighlight{
		background-color: #91C837;
	}
	&-headerDescription{
		position: absolute;
		color: #fff;
		text-shadow: -2px 2px #333;
	}
	// &-headerSmall{}
}
@container PromotionalMaterialJumbotron (min-width: 100px){
	.PromotionalMaterial{
		&-header{
			top: 5cqi;
			left: 5cqi;
			font-size: 5.5cqi;
		}
		&-headerDescription{
			left: 5cqi;
			bottom: 5cqi;
			font-size: 4cqi;
		}
		&-headerSmall{
			font-size: 2.6cqi;
		}
	}
}
</style>
