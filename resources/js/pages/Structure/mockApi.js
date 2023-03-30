const users = [
	{
		position: 'Консультант',
		fullName: 'Елена Сидорова',
		birthday: '05.06.1990',
		phone: '+7(777)1234567',
		email: 'elena.sidorova@example.com',
		photo: 'https://randomuser.me/api/portraits/women/1.jpg'
	},
	{
		position: 'HR менеджер',
		fullName: 'Иван Петров',
		birthday: '20.02.1988',
		phone: '+7(999)9876543',
		email: 'ivan.petrov@example.com',
		photo: 'https://randomuser.me/api/portraits/men/2.jpg'
	},
	{
		position: 'IT специалист',
		fullName: 'Мария Иванова',
		birthday: '15.09.1992',
		phone: '+7(777)5554321',
		email: 'maria.ivanova@example.com',
		photo: 'https://randomuser.me/api/portraits/women/3.jpg'
	},
	{
		position: 'Менеджер по продажам',
		fullName: 'Александр Смирнов',
		birthday: '01.01.1975',
		phone: '+7(495)5551212',
		email: 'alexandr.smirnov@example.com',
		photo: 'https://randomuser.me/api/portraits/men/4.jpg'
	},
	{
		position: 'Таргетолог',
		fullName: 'Ольга Васильева',
		birthday: '12.12.1987',
		phone: '+7(916)1234567',
		email: 'olga.vasilieva@example.com',
		photo: 'https://randomuser.me/api/portraits/women/5.jpg'
	},
	{
		position: 'Младший помощник',
		fullName: 'Павел Сергеев',
		birthday: '18.03.1991',
		phone: '+7(905)1234567',
		email: 'pavel.sergeev@example.com',
		photo: 'https://randomuser.me/api/portraits/men/24.jpg'
	},
	{
		position: 'Старший рекрутер',
		fullName: 'Татьяна Николаева',
		birthday: '25.05.1995',
		phone: '+7(903)1234567',
		email: 'tatiana.nikolaeva@example.com',
		photo: 'https://randomuser.me/api/portraits/women/7.jpg'
	},
	{
		position: 'Маркетолог',
		fullName: 'Ирина Лагинуш',
		birthday: '12.11.1990',
		phone: '+7(903)1235567',
		email: 'irina90dar@example.com',
		photo: 'https://randomuser.me/api/portraits/women/8.jpg'
	},
	{
		position: 'Программист',
		fullName: 'Максим Иванов',
		birthday: '05.06.1985',
		phone: '+7(903)1236677',
		email: 'max85@example.com',
		photo: 'https://randomuser.me/api/portraits/men/5.jpg'
	},
	{
		position: 'Дизайнер',
		fullName: 'Екатерина Сидорова',
		birthday: '25.01.1993',
		phone: '+7(903)1112255',
		email: 'kate93@example.com',
		photo: 'https://randomuser.me/api/portraits/women/7.jpg'
	},
	{
		position: 'HR-менеджер',
		fullName: 'Дмитрий Смирнов',
		birthday: '15.09.1987',
		phone: '+7(903)6789900',
		email: 'dmitry87@example.com',
		photo: 'https://randomuser.me/api/portraits/men/7.jpg'
	},
	{
		position: 'Бухгалтер',
		fullName: 'Ольга Петрова',
		birthday: '30.04.1980',
		phone: '+7(903)5557788',
		email: 'olga80@example.com',
		photo: 'https://randomuser.me/api/portraits/women/3.jpg'
	},
	{
		position: 'Менеджер по продажам',
		fullName: 'Андрей Кузнецов',
		birthday: '08.07.1992',
		phone: '+7(903)2221122',
		email: 'andrew92@example.com',
		photo: 'https://randomuser.me/api/portraits/men/9.jpg'
	},
	{
		position: 'Аналитик',
		fullName: 'Анастасия Павлова',
		birthday: '23.12.1988',
		phone: '+7(903)4443322',
		email: 'anastasia88@example.com',
		photo: 'https://randomuser.me/api/portraits/women/2.jpg'
	},
	{
		position: 'Менеджер по маркетингу',
		fullName: 'Сергей Ковалев',
		birthday: '01.03.1991',
		phone: '+7(903)9994433',
		email: 'sergey91@example.com',
		photo: 'https://randomuser.me/api/portraits/men/8.jpg'
	}
];
const positions = [
	'Менеджер по продажам',
	'Финансовый аналитик',
	'Дизайнер интерфейсов',
	'Разработчик программного обеспечения',
	'HR-специалист',
	'Маркетолог',
	'Инженер-конструктор',
	'Адвокат',
	'Преподаватель',
	'Архитектор',
	'Менеджер по закупкам',
	'Менеджер по персоналу',
	'Технический писатель',
	'Копирайтер',
	'Аналитик данных',
	'Контент-менеджер',
	'Системный администратор',
	'Тестировщик',
	'Директор',
	'Программист',
	'Редактор',
	'Переводчик',
	'Технический писатель',
	'Строитель',
	'Интернет-магазин',
	'Директор по маркетингу',
	'Директор по продажам',
	'Технический писатель',
	'Директор по развитию',
	'Директор по управлению персоналом'
];
const departments = [
	'Отдел продаж',
	'Отдел маркетинга',
	'Отдел разработки',
	'Отдел тестирования',
	'Финансовый отдел',
	'Отдел кадров',
	'Отдел закупок',
	'Отдел логистики',
	'Отдел качества',
	'Отдел безопасности',
	'Отдел обслуживания клиентов',
	'Отдел PR',
	'Отдел исследований и разработок',
	'Отдел производства',
	'Отдел снабжения',
	'Отдел IT',
	'Отдел аналитики',
	'Отдел дизайна',
	'Отдел технической поддержки',
	'Отдел контроля качества',
	'Отдел управления проектами',
	'Отдел планирования',
	'Отдел стратегического развития',
	'Отдел экономического анализа',
	'Отдел законодательного сопровождения',
	'Отдел корпоративной безопасности',
	'Отдел архитектуры',
	'Отдел криптографии',
	'Отдел научных исследований',
	'Отдел международных отношений'
];
const resultText = 'Жулимэ — это суета материального мира, попытка сделать из жулимэ монету. Если у тебя ворс неблагородный, если ты делаешь монету из плохого ворса, то ты не сигмач.';
const structure = [
	{
		id: Math.floor(Math.random() * 10000),
		department: 'Коммерческий департамент',
		position: 'Коммерческий директор',
		employeesCount: 100,
		director: {
			fullName: 'Анастасия Гришковецкая',
			birthday: '10.10.1985',
			phone: '+7(700)5654323',
			email: 'test.test@gmail.com',
			photo: 'https://randomuser.me/api/portraits/women/1.jpg'
		},
		departmentChildren: [
			{
				id: Math.floor(Math.random() * 10000),
				department: 'Департамент персонала',
				position: 'Директор по персоналу',
				employeesCount: 777,
				result: resultText,
				director: {
					fullName: 'Лилиан Левина',
					birthday: '10.10.1985',
					phone: '+7(700)5654323',
					email: 'test.test@gmail.com',
					photo: 'https://randomuser.me/api/portraits/women/2.jpg'
				},
				departmentChildren: [
					{
						id: Math.floor(Math.random() * 10000),
						department: 'Отдел найма и обучения',
						position: 'Начальник',
						employeesCount: 2,
						group: true,
						director: {
							fullName: 'Дашики Ямшина',
							birthday: '10.10.1985',
							phone: '+7(700)5654323',
							email: 'test.test@gmail.com',
							photo: 'https://randomuser.me/api/portraits/women/3.jpg'
						},
						departmentChildren: [
							{
								id: Math.floor(Math.random() * 10000),
								department: 'Сектор подбора персонала',
								position: 'Руководитель',
								employeesCount: 28,
								result: resultText,
								director: {
									fullName: 'Кирилл Толмацкий',
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
						position: 'Начальник',
						employeesCount: 12,
						group: true,
						director: {
							fullName: 'Светлана Шу',
							birthday: '10.10.1985',
							phone: '+7(700)5654323',
							email: 'test.test@gmail.com',
							photo: 'https://randomuser.me/api/portraits/women/4.jpg'
						},
						departmentChildren: [
							{
								id: Math.floor(Math.random() * 10000),
								department: 'Сектор приёмной',
								position: 'Руководитель',
								employeesCount: 12,
								result: resultText,
								director: {
									fullName: 'Виктор Лампин',
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
								position: 'Дизайнер интерфейсов, UI\\UX',
								employeesCount: 1,
								director: {
									fullName: 'Иван Деловой',
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
				position: 'Коммерческий директор',
				employeesCount: 56,
				result: resultText,
				director: {
					fullName: 'Анастасия Гришковецкая',
					birthday: '10.10.1985',
					phone: '+7(700)5654323',
					email: 'test.test@gmail.com',
					photo: 'https://randomuser.me/api/portraits/women/11.jpg'
				},
				departmentChildren: [
					{
						id: Math.floor(Math.random() * 10000),
						department: 'Отдел маркетинга',
						position: 'Начальник',
						employeesCount: 22,
						group: true,
						director: {
							fullName: 'Майя Топтунова',
							birthday: '10.10.1985',
							phone: '+7(700)5654323',
							email: 'test.test@gmail.com',
							photo: 'https://randomuser.me/api/portraits/women/12.jpg'
						},
						departmentChildren: [
							{
								id: Math.floor(Math.random() * 10000),
								department: 'Сектор PR',
								position: 'Руководитель',
								employeesCount: 22,
								result: resultText,
								director: {
									fullName: 'Казанцева Ася',
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
								position: 'Руководитель',
								employeesCount: 22,
								result: resultText,
								director: {
									fullName: 'Настя Филлипова',
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
						position: 'Начальник',
						employeesCount: 444,
						group: true,
						director: {
							fullName: 'Ефремов Максим',
							birthday: '10.10.1985',
							phone: '+7(700)5654323',
							email: 'test.test@gmail.com',
							photo: 'https://randomuser.me/api/portraits/men/6.jpg'
						},
						departmentChildren: [
							{
								id: Math.floor(Math.random() * 10000),
								department: 'Сектор холодных продаж',
								position: 'Руководитель',
								employeesCount: 22,
								director: {
									fullName: 'Орест Френзенский',
									birthday: '10.10.1985',
									phone: '+7(700)5654323',
									email: 'test.test@gmail.com',
									photo: 'https://randomuser.me/api/portraits/men/8.jpg'
								},
							},
							{
								id: Math.floor(Math.random() * 10000),
								department: 'Группа ХП-01',
								position: 'Руководитель',
								employeesCount: 22,
								director: {
									fullName: 'Орест Френзенский',
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
						position: 'Начальник',
						employeesCount: 56,
						group: true,
						director: {
							fullName: 'Галина Симакина',
							birthday: '10.10.1985',
							phone: '+7(700)5654323',
							email: 'test.test@gmail.com',
							photo: 'https://randomuser.me/api/portraits/women/15.jpg'
						},
						departmentChildren: [
							{
								id: Math.floor(Math.random() * 10000),
								department: 'Сектор по работе с ключевыми клиентами',
								position: 'Руководитель',
								employeesCount: 3,
								result: resultText,
								director: {
									fullName: 'Хасан Фаримов',
									birthday: '10.10.1985',
									phone: '+7(700)5654323',
									email: 'test.test@gmail.com',
									photo: 'https://randomuser.me/api/portraits/men/13.jpg'
								},
							},
							{
								id: Math.floor(Math.random() * 10000),
								department: 'Сектор сопровождения клиетов',
								position: 'Руководитель',
								employeesCount: 4,
								result: resultText,
								director: {
									fullName: 'Василий Медведев',
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
				position: 'Финансовый директор',
				employeesCount: 38,
				result: resultText,
				group: true,
				director: {
					fullName: 'Ольга Залуцкая',
					birthday: '10.10.1985',
					phone: '+7(700)5654323',
					email: 'test.test@gmail.com',
					photo: 'https://randomuser.me/api/portraits/women/17.jpg'
				},
				departmentChildren: [
					{
						id: Math.floor(Math.random() * 10000),
						department: 'Бухгалтерия',
						position: 'Главный бухгалтер',
						employeesCount: 38,
						director: {
							fullName: 'Вера Котельникова',
							birthday: '10.10.1985',
							phone: '+7(700)5654323',
							email: 'test.test@gmail.com',
							photo: 'https://randomuser.me/api/portraits/women/19.jpg'
						},
					},
					{
						id: Math.floor(Math.random() * 10000),
						department: 'Материальный сектор',
						position: 'Руководитель',
						employeesCount: 2,
						result: resultText,
						director: {
							fullName: 'Стася Тринадцатко',
							birthday: '10.10.1985',
							phone: '+7(700)5654323',
							email: 'test.test@gmail.com',
							photo: 'https://randomuser.me/api/portraits/women/18.jpg'
						},
					},
					{
						id: Math.floor(Math.random() * 10000),
						department: 'Сектор заработной платы',
						position: 'Руководитель',
						employeesCount: 22,
						result: resultText,
						director: {
							fullName: 'Надежда Галанова',
							birthday: '10.10.1985',
							phone: '+7(700)5654323',
							email: 'test.test@gmail.com',
							photo: 'https://randomuser.me/api/portraits/women/21.jpg'
						},
					},
					{
						id: Math.floor(Math.random() * 10000),
						department: 'Сектор налогового учёта',
						position: 'Руководитель',
						employeesCount: 5,
						result: resultText,
						director: {
							fullName: 'Катерина Пачковская',
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
				position: 'Директор производства',
				employeesCount: 67,
				director: {
					fullName: 'Игорь Джабраилов',
					birthday: '10.10.1985',
					phone: '+7(700)5654323',
					email: 'test.test@gmail.com',
					photo: 'https://randomuser.me/api/portraits/men/16.jpg'
				},
				departmentChildren: [
					{
						id: Math.floor(Math.random() * 10000),
						department: 'Отдел закупок',
						position: 'Начальник',
						employeesCount: 22,
						group: true,
						director: {
							fullName: 'Дашики Ямшина',
							birthday: '10.10.1985',
							phone: '+7(700)5654323',
							email: 'test.test@gmail.com',
							photo: 'https://randomuser.me/api/portraits/women/23.jpg'
						},
						departmentChildren: [
							{
								id: Math.floor(Math.random() * 10000),
								department: 'Сектор снабжения',
								position: 'Руководитель',
								employeesCount: 28,
								result: resultText,
								director: {
									fullName: 'Кирилл Толмацкий',
									birthday: '10.10.1985',
									phone: '+7(700)5654323',
									email: 'test.test@gmail.com',
									photo: 'https://randomuser.me/api/portraits/men/19.jpg'
								},
							},
							{
								id: Math.floor(Math.random() * 10000),
								department: 'Сектор обслуживания оборудования',
								position: 'Руководитель',
								employeesCount: 56,
								result: resultText,
								director: {
									fullName: 'Надежда Галанова',
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
		position: 'Административный директор',
		employeesCount: 100,
		bgc: '#F9F6FF',
		director: {
			fullName: 'Идрак Мирзализаде',
			birthday: '10.10.1985',
			phone: '+7(700)5654323',
			email: 'test.test@gmail.com',
			photo: 'https://randomuser.me/api/portraits/men/18.jpg'
		},
		departmentChildren: [
			{
				id: Math.floor(Math.random() * 10000),
				department: 'Отдел безопасности',
				position: 'Начальник отдела',
				employeesCount: 22,
				group: true,
				director: {
					fullName: 'Константин Самойлов',
					birthday: '10.10.1985',
					phone: '+7(700)5654323',
					email: 'test.test@gmail.com',
					photo: 'https://randomuser.me/api/portraits/men/20.jpg'
				},
				departmentChildren: [
					{
						id: Math.floor(Math.random() * 10000),
						department: 'Сектор безопасности',
						position: 'Руководитель',
						employeesCount: 8,
						result: resultText,
						director: {
							fullName: 'Кирилл Толмацкий',
							birthday: '10.10.1985',
							phone: '+7(700)5654323',
							email: 'test.test@gmail.com',
							photo: 'https://randomuser.me/api/portraits/men/21.jpg'
						},
					},
					{
						id: Math.floor(Math.random() * 10000),
						department: 'Сектор юристов',
						position: 'Главный юрист',
						employeesCount: 5,
						result: resultText,
						director: {
							fullName: 'Вадим Пастильный',
							birthday: '10.10.1985',
							phone: '+7(700)5654323',
							email: 'test.test@gmail.com',
							photo: 'https://randomuser.me/api/portraits/men/22.jpg'
						},
					},
					{
						id: Math.floor(Math.random() * 10000),
						department: 'Сектор охраны труда',
						position: 'Руководитель',
						employeesCount: 2,
						result: resultText,
						director: {
							fullName: 'Вадим Саликов',
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
				position: 'Директор по персоналу',
				employeesCount: 19,
				result: resultText,
				group: false,
				director: {
					fullName: 'Лилиан Левина',
					birthday: '10.10.1985',
					phone: '+7(700)5654323',
					email: 'test.test@gmail.com',
					photo: 'https://randomuser.me/api/portraits/women/24.jpg'
				},
				departmentChildren: [
					{
						id: Math.floor(Math.random() * 10000),
						department: 'Совет директоров',
						position: 'Председатель совета директоров',
						employeesCount: 9,
						result: resultText,
						director: {
							fullName: 'Никита Косов',
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
						position: 'Начальник отдела',
						employeesCount: 17,
						result: resultText,
						director: {
							fullName: 'Григорий Квадратов',
							birthday: '10.10.1985',
							phone: '+7(700)5654323',
							email: 'test.test@gmail.com',
							photo: 'https://randomuser.me/api/portraits/men/26.jpg'
						},
					},
					{
						id: Math.floor(Math.random() * 10000),
						department: 'Совет руководителей',
						position: 'Начальник отдела',
						employeesCount: 17,
						result: resultText,
						director: {
							fullName: 'Елена Губова',
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
];

export {users, positions, departments, structure};
