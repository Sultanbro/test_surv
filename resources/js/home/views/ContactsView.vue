<template>
	<div id="ContactsView">
		<NavBar />
		<div class="section-content ContactsView-content">
			<div class="ContactsView-col">
				<h1>Контакты</h1>
				<h3>Отдел продаж:</h3>
				<p>8 777 788 08 00</p>
				<p>8 495 136 42 82</p>
				<p>sales@jobtron.com</p>
				<h3>Отдел поддержки:</h3>
				<p>support@jobtron.com</p>
			</div>
			<div class="ContactsView-col">
				<h1>Реквизиты</h1>
				<h3>Товарищество с ограниченной ответственностью “ВЭБТРОН”</h3>
				<dl>
					<dt class="ContactsView-dt">
						БИН:
					</dt>
					<dd class="ContactsView-dd">
						220740014763
					</dd>
					<dt class="ContactsView-dt">
						АО "Банк ЦентрКредит"
					</dt>
					<dd class="ContactsView-dd" />
					<dt class="ContactsView-dt">
						ИИК:
					</dt>
					<dd class="ContactsView-dd">
						KZ648562203119363628
					</dd>
					<dt class="ContactsView-dt">
						БИК:
					</dt>
					<dd class="ContactsView-dd">
						KCJBKZKX
					</dd>
					<dt class="ContactsView-dt">
						БИН Банка:
					</dt>
					<dd class="ContactsView-dd">
						980640000093
					</dd>
					<dt class="ContactsView-dt">
						Директор
					</dt>
					<dd class="ContactsView-dd">
						Каримов А.Д. на основании Приказа No1 от 20.07.2022г.
					</dd>
					<dt class="ContactsView-dt">
						Электронная почта:
					</dt>
					<dd class="ContactsView-dd">
						Ariche.A@yandex.ru
					</dd>
					<dt class="ContactsView-dt">
						Юридический адрес:
					</dt>
					<dd class="ContactsView-dd">
						Республика Казахстан, г.Шымкент, ул.Рыскулова, 10а
					</dd>
				</dl>
			</div>
		</div>
		<SectionFooter />
	</div>
</template>

<script>
import NavBar from '../components/sections/Navbar.vue'
import SectionFooter from '../components/sections/Footer.vue'

export default {
	name: 'ContactsView',
	components: {
		NavBar,
		SectionFooter,
	},
}
</script>

<style lang="scss">
@import '../assets/scss/app.variables.scss';
.ContactsView{
	&-dt{
		font-weight: 700;
	}
	&-dd{
		margin-bottom: 1rem;
	}
	&-content{
		padding-bottom: 2rem;
	}
}
@media screen and (min-width: $small) {
	#ContactsView{
		.section-content{
			display: flex;
			flex-flow: row nowrap;
		}
	}
	.ContactsView{
		&-col{
			flex: 0 0 50%;
			gap: 1rem;
		}
	}
}
</style>
