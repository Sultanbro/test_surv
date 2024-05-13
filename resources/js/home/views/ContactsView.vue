<template>
	<div id="ContactsView">
		<NavBar />
		<div class="section-content ContactsView-content">
			<div class="ContactsView-col">
				<h1>Контакты</h1>
				<h3>Отдел продаж:</h3>
				<p>8 (700) 991 01 30</p>
				<p>8 495 136 42 82</p>
				<p>sales@jobtron.com</p>
				<h3>Отдел поддержки:</h3>
				<p>support@jobtron.com</p>
			</div>
			<div class="ContactsView-col">
				<h1>Реквизиты</h1>
				<h3>Товарищество с ограниченной ответственностью “ВЭБТРОН”</h3>
				<table class="table">
					<tbody>
						<tr>
							<th>
								БИН
							</th>
							<td>
								220740014763
							</td>
						</tr>
						<tr>
							<th />
							<td>
								АО "Банк ЦентрКредит"
							</td>
						</tr>
						<tr>
							<th>
								ИИК
							</th>
							<td>
								KZ648562203119363628
							</td>
						</tr>
						<tr>
							<th>
								БИК
							</th>
							<td>
								KCJBKZKX
							</td>
						</tr>
						<tr>
							<th>
								БИН Банка
							</th>
							<td>
								980640000093
							</td>
						</tr>
						<tr>
							<th>
								Директор
							</th>
							<td>
								Каримов А.Д. на основании Приказа No1 от 20.07.2022г.
							</td>
						</tr>
						<tr>
							<th>
								Электронная почта
							</th>
							<td>
								Ariche.A@yandex.ru
							</td>
						</tr>
						<tr>
							<th>
								Юридический адрес
							</th>
							<td>
								Республика Казахстан, г.Шымкент, ул.Рыскулова, 10а
							</td>
						</tr>
					</tbody>
				</table>
				<br>
				<h3>РЕКВИЗИТЫ  ИП Руденко Д.Н.</h3>
				<table class="table">
					<tbody>
						<tr>
							<th>
								Полное наименование
							</th>
							<td>
								Индивидуальный предприниматель Руденко Денис Николаевич
							</td>
						</tr>
						<tr>
							<th>
								ИИН
							</th>
							<td>
								860209482290
							</td>
						</tr>
						<tr>
							<th>
								Адрес регистрации
							</th>
							<td>
								Ханты-Мансийский автономный округ – Югра, г.Сургут, пр-кт Ленина, д.38, кв. 244
							</td>
						</tr>
						<tr>
							<th>
								ОГРНИП
							</th>
							<td>
								320861700061035
							</td>
						</tr>
						<tr>
							<th>
								Расчетный счет
							</th>
							<td>
								40802810470010285487
							</td>
						</tr>
						<tr>
							<th>
								Банк
							</th>
							<td>
								МОСКОВСКИЙ ФИЛИАЛ АО КБ "МОДУЛЬБАНК"
							</td>
						</tr>
						<tr>
							<th>
								Корр. счет
							</th>
							<td>
								30101810645250000092
							</td>
						</tr>
						<tr>
							<th>
								БИК
							</th>
							<td>
								044525092
							</td>
						</tr>
						<tr>
							<th>
								Электронная почта
							</th>
							<td>
								4117722@mail.ru
							</td>
						</tr>
					</tbody>
				</table>
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
		width: 40%;
	}
	&-dd{
		margin-inline-start: 10px;
		width: 60%;
	}
	&-content{
		padding-bottom: 2rem;
	}
}
.contact-block{
	margin-bottom: 1rem;
	display: flex;
}
h3{
	margin-bottom: 10px;
}
.table{
	caption-side: bottom;
	border-collapse: collapse;
	tr{
		th, td{
			border: 1px solid #ddd;
			padding: 5px 10px;
			text-align: left;
		}
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
