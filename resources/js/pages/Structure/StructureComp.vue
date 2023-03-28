<template>
	<div
		class="structure-container"
		ref="container"
		@mousedown="startDrag"
		@mouseup="stopDrag"
		@mousemove="onDrag"
		:class="{'is-dragging': isDragging}"
	>
		<div
			class="structure-company-controls"
			mousemove.stop
			@mousedown.stop
		>
			<div class="actions">
				<button
					class="remove-demo"
					@click="departments = []"
				>
					Удалить демо данные
				</button>
				<button class="icon-btn">
					<i class="fa fa-pen" />
				</button>
				<button class="icon-btn">
					<i class="fa fa-cog" />
				</button>
			</div>
			<div class="range-zoom">
				<input
					id="range-input"
					class="range-input"
					v-model="zoom"
					min="10"
					max="500"
					step="1"
					type="range"
				>
			</div>
		</div>
		<div
			class="structure-company-area"
			:style="{zoom: zoom / 100}"
		>
			<div
				class="structure-card"
				:style="{marginLeft: leftMarginMainCard}"
			>
				<div class="structure-card-header">
					<p class="department">
						Коммерческий департамент
					</p>
					<p class="count">
						100 сотрудников
					</p>
				</div>
				<div class="structure-card-body">
					<img
						src="https://randomuser.me/api/portraits/women/1.jpg"
						alt="photo"
						class="director-photo"
					>
					<p class="position">
						Коммерческий директор
					</p>
					<p class="full-name">
						Анастасия Гришковецкая
					</p>
				</div>
				<i class="fa fa-plus-circle structure-add" />
			</div>
			<div
				class="departments-area"
				ref="departmentsArea"
			>
				<template v-for="department in departments">
					<StructureItem
						:department="department"
						:key="department.id"
						:level="1"
						:bgc="''"
					/>
				</template>
			</div>
		</div>
	</div>
</template>

<script>
import StructureItem from './StructureItem';

const resultText = 'Жулимэ — это суета материального мира, попытка сделать из жулимэ монету. Если у тебя ворс неблагородный, если ты делаешь монету из плохого ворса, то ты не сигмач.';
const users = [
	{
		fullName: 'Елена Сидорова',
		position: 'Менеджер по продажам',
		birthday: '05.06.1990',
		phone: '+7(777)1234567',
		email: 'elena.sidorova@example.com',
		photo: 'https://randomuser.me/api/portraits/women/1.jpg'
	},
	{
		fullName: 'Иван Петров',
		position: 'Разработчик',
		birthday: '20.02.1988',
		phone: '+7(999)9876543',
		email: 'ivan.petrov@example.com',
		photo: 'https://randomuser.me/api/portraits/men/2.jpg'
	},
	{
		fullName: 'Мария Иванова',
		position: 'Дизайнер',
		birthday: '15.09.1992',
		phone: '+7(777)5554321',
		email: 'maria.ivanova@example.com',
		photo: 'https://randomuser.me/api/portraits/women/3.jpg'
	},
	{
		fullName: 'Александр Смирнов',
		position: 'Бухгалтер',
		birthday: '01.01.1975',
		phone: '+7(495)5551212',
		email: 'alexandr.smirnov@example.com',
		photo: 'https://randomuser.me/api/portraits/men/4.jpg'
	},
	{
		fullName: 'Ольга Васильева',
		position: 'HR-специалист',
		birthday: '12.12.1987',
		phone: '+7(916)1234567',
		email: 'olga.vasilieva@example.com',
		photo: 'https://randomuser.me/api/portraits/women/5.jpg'
	},
	{
		fullName: 'Павел Сергеев',
		position: 'Маркетолог',
		birthday: '18.03.1991',
		phone: '+7(905)1234567',
		email: 'pavel.sergeev@example.com',
		photo: 'https://randomuser.me/api/portraits/men/6.jpg'
	},
	{
		fullName: 'Татьяна Николаева',
		position: 'Администратор',
		birthday: '25.05.1995',
		phone: '+7(903)1234567',
		email: 'tatiana.nikolaeva@example.com',
		photo: 'https://randomuser.me/api/portraits/women/7.jpg'
	},
	{
		fullName: 'Ирина Лагинуш',
		position: 'Администратор',
		birthday: '12.11.1990',
		phone: '+7(903)1235567',
		email: 'irina90dar@example.com',
		photo: 'https://randomuser.me/api/portraits/women/8.jpg'
	},
];
export default {
	name: 'StructureComp',
	components: {
		StructureItem,
	},
	data() {
		return {
			isDragging: false,
			startX: 0,
			startY: 0,
			scrollLeft: 0,
			scrollTop: 0,
			zoom: 100,
			leftMarginMainCard: 0,
			ceo: {
				id: Math.floor(Math.random() * 10000),
				department: 'Коммерческий департамент',
				employeesCount: 192,
				director: {
					fullName: 'Адиль Каримов',
					position: 'Генеральный директор',
					birthday: '10.10.1985',
					phone: '+7(700)5654323',
					email: 'test.test@gmail.com',
					photo: 'https://randomuser.me/api/portraits/men/1.jpg'
				},
			},
			departments: [
				{
					id: Math.floor(Math.random() * 10000),
					department: 'Коммерческий департамент',
					employeesCount: 100,
					director: {
						fullName: 'Анастасия Гришковецкая',
						position: 'Коммерческий директор',
						birthday: '10.10.1985',
						phone: '+7(700)5654323',
						email: 'test.test@gmail.com',
						photo: 'https://randomuser.me/api/portraits/women/1.jpg'
					},
					departmentChildren: [
						{
							id: Math.floor(Math.random() * 10000),
							department: 'Департамент персонала',
							employeesCount: 777,
							result: resultText,
							director: {
								fullName: 'Лилиан Левина',
								position: 'Директор по персоналу',
								birthday: '10.10.1985',
								phone: '+7(700)5654323',
								email: 'test.test@gmail.com',
								photo: 'https://randomuser.me/api/portraits/women/2.jpg'
							},
							departmentChildren: [
								{
									id: Math.floor(Math.random() * 10000),
									department: 'Отдел найма и обучения',
									employeesCount: 2,
									group: true,
									director: {
										fullName: 'Дашики Ямшина',
										position: 'Начальник',
										birthday: '10.10.1985',
										phone: '+7(700)5654323',
										email: 'test.test@gmail.com',
										photo: 'https://randomuser.me/api/portraits/women/3.jpg'
									},
									departmentChildren: [
										{
											id: Math.floor(Math.random() * 10000),
											department: 'Сектор подбора персонала',
											employeesCount: 28,
											result: resultText,
											director: {
												fullName: 'Кирилл Толмацкий',
												position: 'Руководитель',
												birthday: '10.10.1985',
												phone: '+7(700)5654323',
												email: 'test.test@gmail.com',
												photo: 'https://randomuser.me/api/portraits/men/2.jpg'
											},
										},
										{
											id: Math.floor(Math.random() * 10000),
											department: 'Call-центр',
											employeesCount: 56,
											result: resultText,
											users: users
										}
									]
								},
								{
									id: Math.floor(Math.random() * 10000),
									department: 'Отдел заботы',
									employeesCount: 12,
									group: true,
									director: {
										fullName: 'Светлана Шу',
										position: 'Начальник',
										birthday: '10.10.1985',
										phone: '+7(700)5654323',
										email: 'test.test@gmail.com',
										photo: 'https://randomuser.me/api/portraits/women/4.jpg'
									},
									departmentChildren: [
										{
											id: Math.floor(Math.random() * 10000),
											department: 'Сектор приёмной',
											employeesCount: 12,
											result: resultText,
											director: {
												fullName: 'Виктор Лампин',
												position: 'Руководитель',
												birthday: '10.10.1985',
												phone: '+7(700)5654323',
												email: 'test.test@gmail.com',
												photo: 'https://randomuser.me/api/portraits/men/3.jpg'
											},
										},
										{
											id: Math.floor(Math.random() * 10000),
											department: 'IT-отдел',
											employeesCount: 12,
											result: resultText,
											users: users
										},
										{
											id: Math.floor(Math.random() * 10000),
											department: 'IT-сектор',
											employeesCount: 1,
											director: {
												fullName: 'Иван Деловой',
												position: 'Дизайнер интерфейсов, UI\\UX',
												birthday: '10.10.1985',
												phone: '+7(700)5654323',
												email: 'test.test@gmail.com',
												photo: 'https://randomuser.me/api/portraits/men/8.jpg'
											},
										}
									]
								}
							]
						},
						{
							id: Math.floor(Math.random() * 10000),
							department: 'Коммерческий департамент',
							employeesCount: 56,
							result: resultText,
							director: {
								fullName: 'Анастасия Гришковецкая',
								position: 'Коммерческий директор',
								birthday: '10.10.1985',
								phone: '+7(700)5654323',
								email: 'test.test@gmail.com',
								photo: 'https://randomuser.me/api/portraits/women/11.jpg'
							},
							departmentChildren: [
								{
									id: Math.floor(Math.random() * 10000),
									department: 'Отдел маркетинга',
									employeesCount: 22,
									group: true,
									director: {
										fullName: 'Майя Топтунова',
										position: 'Начальник',
										birthday: '10.10.1985',
										phone: '+7(700)5654323',
										email: 'test.test@gmail.com',
										photo: 'https://randomuser.me/api/portraits/women/12.jpg'
									},
									departmentChildren: [
										{
											id: Math.floor(Math.random() * 10000),
											department: 'Сектор PR',
											employeesCount: 22,
											result: resultText,
											director: {
												fullName: 'Казанцева Ася',
												position: 'Руководитель',
												birthday: '10.10.1985',
												phone: '+7(700)5654323',
												email: 'test.test@gmail.com',
												photo: 'https://randomuser.me/api/portraits/women/13.jpg'
											},
										},
										{
											id: Math.floor(Math.random() * 10000),
											department: 'Сектор интернет-продвижения',
											employeesCount: 22,
											result: resultText,
											users: users
										},
										{
											id: Math.floor(Math.random() * 10000),
											department: 'Сектор дизайна',
											employeesCount: 22,
											result: resultText,
											director: {
												fullName: 'Настя Филлипова',
												position: 'Руководитель',
												birthday: '10.10.1985',
												phone: '+7(700)5654323',
												email: 'test.test@gmail.com',
												photo: 'https://randomuser.me/api/portraits/women/14.jpg'
											},
										},
									]
								},
								{
									id: Math.floor(Math.random() * 10000),
									department: 'Отдел продаж',
									employeesCount: 444,
									group: true,
									director: {
										fullName: 'Ефремов Максим',
										position: 'Начальник',
										birthday: '10.10.1985',
										phone: '+7(700)5654323',
										email: 'test.test@gmail.com',
										photo: 'https://randomuser.me/api/portraits/men/6.jpg'
									},
									departmentChildren: [
										{
											id: Math.floor(Math.random() * 10000),
											department: 'Сектор холодных продаж',
											employeesCount: 22,
											director: {
												fullName: 'Орест Френзенский',
												position: 'Руководитель',
												birthday: '10.10.1985',
												phone: '+7(700)5654323',
												email: 'test.test@gmail.com',
												photo: 'https://randomuser.me/api/portraits/men/8.jpg'
											},
										},
										{
											id: Math.floor(Math.random() * 10000),
											department: 'Группа ХП-01',
											employeesCount: 22,
											director: {
												fullName: 'Орест Френзенский',
												position: 'Руководитель',
												birthday: '10.10.1985',
												phone: '+7(700)5654323',
												email: 'test.test@gmail.com',
												photo: 'https://randomuser.me/api/portraits/men/9.jpg'
											},
										},
										{
											id: Math.floor(Math.random() * 10000),
											department: 'Менеджеры ХП-01',
											employeesCount: 5,
											result: resultText,
											users: users
										},
									]
								},
								{
									id: Math.floor(Math.random() * 10000),
									department: 'Отдел по работе с клиентами',
									employeesCount: 56,
									group: true,
									director: {
										fullName: 'Галина Симакина',
										position: 'Начальник',
										birthday: '10.10.1985',
										phone: '+7(700)5654323',
										email: 'test.test@gmail.com',
										photo: 'https://randomuser.me/api/portraits/women/15.jpg'
									},
									departmentChildren: [
										{
											id: Math.floor(Math.random() * 10000),
											department: 'Сектор по работе с ключевыми клиентами',
											employeesCount: 3,
											result: resultText,
											director: {
												fullName: 'Хасан Фаримов',
												position: 'Руководитель',
												birthday: '10.10.1985',
												phone: '+7(700)5654323',
												email: 'test.test@gmail.com',
												photo: 'https://randomuser.me/api/portraits/men/13.jpg'
											},
										},
										{
											id: Math.floor(Math.random() * 10000),
											department: 'Сектор сопровождения клиетов',
											employeesCount: 4,
											result: resultText,
											director: {
												fullName: 'Василий Медведев',
												position: 'Руководитель',
												birthday: '10.10.1985',
												phone: '+7(700)5654323',
												email: 'test.test@gmail.com',
												photo: 'https://randomuser.me/api/portraits/men/5.jpg'
											},
										},
									]
								}
							]
						},
						{
							id: Math.floor(Math.random() * 10000),
							department: 'Финансовый департамент',
							employeesCount: 38,
							result: resultText,
							group: true,
							director: {
								fullName: 'Ольга Залуцкая',
								position: 'Финансовый директор',
								birthday: '10.10.1985',
								phone: '+7(700)5654323',
								email: 'test.test@gmail.com',
								photo: 'https://randomuser.me/api/portraits/women/17.jpg'
							},
							departmentChildren: [
								{
									id: Math.floor(Math.random() * 10000),
									department: 'Бухгалтерия',
									employeesCount: 38,
									director: {
										fullName: 'Вера Котельникова',
										position: 'Главный бухгалтер',
										birthday: '10.10.1985',
										phone: '+7(700)5654323',
										email: 'test.test@gmail.com',
										photo: 'https://randomuser.me/api/portraits/women/19.jpg'
									},
								},
								{
									id: Math.floor(Math.random() * 10000),
									department: 'Материальный сектор',
									employeesCount: 2,
									result: resultText,
									director: {
										fullName: 'Стася Тринадцатко',
										position: 'Руководитель',
										birthday: '10.10.1985',
										phone: '+7(700)5654323',
										email: 'test.test@gmail.com',
										photo: 'https://randomuser.me/api/portraits/women/18.jpg'
									},
								},
								{
									id: Math.floor(Math.random() * 10000),
									department: 'Сектор заработной платы',
									employeesCount: 22,
									result: resultText,
									director: {
										fullName: 'Надежда Галанова',
										position: 'Руководитель',
										birthday: '10.10.1985',
										phone: '+7(700)5654323',
										email: 'test.test@gmail.com',
										photo: 'https://randomuser.me/api/portraits/women/21.jpg'
									},
								},
								{
									id: Math.floor(Math.random() * 10000),
									department: 'Сектор налогового учёта',
									employeesCount: 5,
									result: resultText,
									director: {
										fullName: 'Катерина Пачковская',
										position: 'Руководитель',
										birthday: '10.10.1985',
										phone: '+7(700)5654323',
										email: 'test.test@gmail.com',
										photo: 'https://randomuser.me/api/portraits/women/22.jpg'
									},
								}
							]
						},
						{
							id: Math.floor(Math.random() * 10000),
							department: 'Производственный департамент',
							employeesCount: 67,
							director: {
								fullName: 'Игорь Джабраилов',
								position: 'Директор производства',
								birthday: '10.10.1985',
								phone: '+7(700)5654323',
								email: 'test.test@gmail.com',
								photo: 'https://randomuser.me/api/portraits/men/16.jpg'
							},
							departmentChildren: [
								{
									id: Math.floor(Math.random() * 10000),
									department: 'Отдел закупок',
									employeesCount: 22,
									group: true,
									director: {
										fullName: 'Дашики Ямшина',
										position: 'Начальник',
										birthday: '10.10.1985',
										phone: '+7(700)5654323',
										email: 'test.test@gmail.com',
										photo: 'https://randomuser.me/api/portraits/women/23.jpg'
									},
									departmentChildren: [
										{
											id: Math.floor(Math.random() * 10000),
											department: 'Сектор снабжения',
											employeesCount: 28,
											result: resultText,
											director: {
												fullName: 'Кирилл Толмацкий',
												position: 'Руководитель',
												birthday: '10.10.1985',
												phone: '+7(700)5654323',
												email: 'test.test@gmail.com',
												photo: 'https://randomuser.me/api/portraits/men/19.jpg'
											},
										},
										{
											id: Math.floor(Math.random() * 10000),
											department: 'Сектор обслуживания оборудования',
											employeesCount: 56,
											result: resultText,
											director: {
												fullName: 'Надежда Галанова',
												position: 'Руководитель',
												birthday: '10.10.1985',
												phone: '+7(700)5654323',
												email: 'test.test@gmail.com',
												photo: 'https://randomuser.me/api/portraits/women/24.jpg'
											},
										}

									]
								},
								{
									id: Math.floor(Math.random() * 10000),
									department: 'Отдел производства',
									employeesCount: 0
								}
							]
						}
					]
				},
				{
					id: Math.floor(Math.random() * 10000),
					department: 'Коммерческий департамент',
					employeesCount: 100,
					bgc: '#F9F6FF',
					director: {
						fullName: 'Идрак Мирзализаде',
						position: 'Административный директор',
						birthday: '10.10.1985',
						phone: '+7(700)5654323',
						email: 'test.test@gmail.com',
						photo: 'https://randomuser.me/api/portraits/men/18.jpg'
					},
					departmentChildren: [
						{
							id: Math.floor(Math.random() * 10000),
							department: 'Отдел безопасности',
							employeesCount: 22,
							group: true,
							director: {
								fullName: 'Константин Самойлов',
								position: 'Начальник отдела',
								birthday: '10.10.1985',
								phone: '+7(700)5654323',
								email: 'test.test@gmail.com',
								photo: 'https://randomuser.me/api/portraits/men/20.jpg'
							},
							departmentChildren: [
								{
									id: Math.floor(Math.random() * 10000),
									department: 'Сектор безопасности',
									employeesCount: 8,
									result: resultText,
									director: {
										fullName: 'Кирилл Толмацкий',
										position: 'Руководитель',
										birthday: '10.10.1985',
										phone: '+7(700)5654323',
										email: 'test.test@gmail.com',
										photo: 'https://randomuser.me/api/portraits/men/21.jpg'
									},
								},
								{
									id: Math.floor(Math.random() * 10000),
									department: 'Сектор юристов',
									employeesCount: 5,
									result: resultText,
									director: {
										fullName: 'Вадим Пастильный',
										position: 'Главный юрист',
										birthday: '10.10.1985',
										phone: '+7(700)5654323',
										email: 'test.test@gmail.com',
										photo: 'https://randomuser.me/api/portraits/men/22.jpg'
									},
								},
								{
									id: Math.floor(Math.random() * 10000),
									department: 'Сектор охраны труда',
									employeesCount: 2,
									result: resultText,
									director: {
										fullName: 'Вадим Саликов',
										position: 'Руководитель',
										birthday: '10.10.1985',
										phone: '+7(700)5654323',
										email: 'test.test@gmail.com',
										photo: 'https://randomuser.me/api/portraits/men/23.jpg'
									},
								}
							]
						},
						{
							id: Math.floor(Math.random() * 10000),
							department: 'Административный отдел',
							employeesCount: 19,
							result: resultText,
							group: false,
							director: {
								fullName: 'Лилиан Левина',
								position: 'Директор по персоналу',
								birthday: '10.10.1985',
								phone: '+7(700)5654323',
								email: 'test.test@gmail.com',
								photo: 'https://randomuser.me/api/portraits/women/24.jpg'
							},
							departmentChildren: [
								{
									id: Math.floor(Math.random() * 10000),
									department: 'Совет директоров',
									employeesCount: 9,
									result: resultText,
									director: {
										fullName: 'Никита Косов',
										position: 'Председатель совета директоров',
										birthday: '10.10.1985',
										phone: '+7(700)5654323',
										email: 'test.test@gmail.com',
										photo: 'https://randomuser.me/api/portraits/men/25.jpg'
									},
									departmentChildren: [
										{
											id: Math.floor(Math.random() * 10000),
											department: 'Участники совета',
											employeesCount: 5,
											users: users
										}
									]
								},
								{
									id: Math.floor(Math.random() * 10000),
									department: 'Служба стратегического управления',
									employeesCount: 17,
									result: resultText,
									director: {
										fullName: 'Григорий Квадратов',
										position: 'Начальник отдела',
										birthday: '10.10.1985',
										phone: '+7(700)5654323',
										email: 'test.test@gmail.com',
										photo: 'https://randomuser.me/api/portraits/men/26.jpg'
									},
								},
								{
									id: Math.floor(Math.random() * 10000),
									department: 'Совет руководителей',
									employeesCount: 17,
									result: resultText,
									director: {
										fullName: 'Елена Губова',
										position: 'Начальник отдела',
										birthday: '10.10.1985',
										phone: '+7(700)5654323',
										email: 'test.test@gmail.com',
										photo: 'https://randomuser.me/api/portraits/women/26.jpg'
									},
								}
							]
						}
					]
				},
			]
		}
	},
	mounted(){
		this.drawLines();
	},
	methods: {
		drawLines() {
			if (this.$refs.departmentsArea) {
				this.leftMarginMainCard = `${Math.round(this.$refs.departmentsArea.scrollWidth / 2)}px`;
			}
		},
		startDrag(event) {
			this.isDragging = true;
			this.startX = event.clientX;
			this.startY = event.clientY;
			this.scrollLeft = this.$refs.container.scrollLeft;
			this.scrollTop = this.$refs.container.scrollTop;
		},
		stopDrag() {
			this.isDragging = false;
		},
		onDrag(event) {
			if (!this.isDragging) {
				return;
			}

			const deltaX = event.clientX - this.startX;
			const deltaY = event.clientY - this.startY;

			this.$refs.container.scrollLeft = this.scrollLeft - deltaX;
			this.$refs.container.scrollTop = this.scrollTop - deltaY;
		},
	}
}
</script>
