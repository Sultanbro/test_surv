const users = [
	{
		id: 1,
		position_id: 42,
		name: 'Иван',
		last_name: 'Иванов',
		birthday: '05.06.1990',
		phone: '+7(777)1234567',
		email: 'director@example.com',
		avatar: 'https://randomuser.me/api/portraits/men/1.jpg',
		last_seen: '2023-07-26'
	},
	{
		id: 2,
		position_id: 43,
		name: 'Анастасия',
		last_name: 'Гришковецкая',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/women/1.jpg',
		last_seen: '2023-07-26'
	},
	{
		id: 3,
		position_id: 44,
		name: 'Лилиан',
		last_name: 'Левина',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/women/2.jpg',
		last_seen: '2023-07-26'
	},
	{
		id: 4,
		position_id: 45,
		name: 'Дашики',
		last_name: 'Ямшина',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/women/3.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 1 } ],
	},
	{
		id: 5,
		position_id: 46,
		name: 'Кирилл',
		last_name: 'Толмацкий',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/men/2.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 2 } ],
	},
	{
		id: 6,
		position_id: 22,
		name: 'Нииколай',
		last_name: 'Королев',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/men/50.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 3 } ],
	},
	{
		id: 7,
		position_id: 22,
		name: 'Светлана',
		last_name: 'Шу',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/women/4.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 4 } ],
	},
	{
		id: 8,
		position_id: 46,
		name: 'Виктор',
		last_name: 'Лампин',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/men/3.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 5 } ],
	},
	{
		id: 9,
		position_id: 3,
		name: 'Роман',
		last_name: 'Павлов',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/men/51.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 6 } ],
	},
	{
		id: 10,
		position_id: 47,
		name: 'Иван',
		last_name: 'Деловой',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/men/8.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 7 } ],
	},
	{
		id: 11,
		position_id: 43,
		name: 'Анастасия',
		last_name: 'Гришковецкая',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/women/11.jpg',
		last_seen: '2023-07-26',
		profile_groups: [],
	},
	{
		id: 12,
		position_id: 45,
		name: 'Майя',
		last_name: 'Топтунова',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/women/12.jpg',
		last_seen: '2023-07-26',
		profile_groups: [{id: 8}],
	},
	{
		id: 13,
		position_id: 46,
		name: 'Ася',
		last_name: 'Казанцева',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/women/13.jpg',
		last_seen: '2023-07-26',
		profile_groups: [{id: 9}],
	},
	{
		id: 14,
		position_id: 41,
		name: 'Ирина',
		last_name: 'Пашкова',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/women/50.jpg',
		last_seen: '2023-07-26',
		profile_groups: [{id: 10}],
	},
	{
		id: 15,
		position_id: 46,
		name: 'Настя',
		last_name: 'Филлипова',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/women/14.jpg',
		last_seen: '2023-07-26',
		profile_groups: [{id: 11}],
	},
	{
		id: 16,
		position_id: 45,
		name: 'Ефремов',
		last_name: 'Максим',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/men/6.jpg',
		last_seen: '2023-07-26',
		profile_groups: [{id: 12}],
	},
	{
		id: 17,
		position_id: 46,
		name: 'Орест',
		last_name: 'Френзенский',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/men/8.jpg',
		last_seen: '2023-07-26',
		profile_groups: [{id: 13}],
	},
	{
		id: 18,
		position_id: 46,
		name: 'Орест',
		last_name: 'Френзенский',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/men/9.jpg',
		last_seen: '2023-07-26',
		profile_groups: [{id: 13}],
	},
	{
		id: 19,
		position_id: 45,
		name: 'Галина',
		last_name: 'Симакина',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/women/15.jpg',
		last_seen: '2023-07-26',
		profile_groups: [{id: 13}],
	},
	{
		id: 20,
		position_id: 45,
		name: 'Хасан',
		last_name: 'Фаримов',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/men/13.jpg',
		last_seen: '2023-07-26',
		profile_groups: [{id: 13}],
	},
	{
		id: 21,
		position_id: 45,
		name: 'Василий',
		last_name: 'Медведев',
		birthday: '10.10.1985',
		phone: '+7(700)5654323',
		email: 'test.test@gmail.com',
		avatar: 'https://randomuser.me/api/portraits/men/5.jpg',
		last_seen: '2023-07-26',
		profile_groups: [{id: 13}],
	},
	{
		id: 101,
		position_id: 30,
		position_name: 'Консультант',
		name: 'Елена',
		last_name: 'Сидорова',
		fullName: 'Елена Сидорова',
		birthday: '05.06.1990',
		phone: '+7(777)1234567',
		email: 'elena.sidorova@example.com',
		avatar: 'https://randomuser.me/api/portraits/women/27.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 12 } ],
	},
	{
		id: 102,
		position_id: 31,
		position_name: 'HR менеджер',
		name: 'Иван',
		last_name: 'Петров',
		fullName: 'Иван Петров',
		birthday: '20.02.1988',
		phone: '+7(999)9876543',
		email: 'ivan.petrov@example.com',
		avatar: 'https://randomuser.me/api/portraits/men/2.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 12 } ],
	},
	{
		id: 103,
		position_id: 32,
		position_name: 'IT специалист',
		name: 'Мария',
		last_name: 'Иванова',
		fullName: 'Мария Иванова',
		birthday: '15.09.1992',
		phone: '+7(777)5554321',
		email: 'maria.ivanova@example.com',
		avatar: 'https://randomuser.me/api/portraits/women/3.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 12 } ],
	},
	{
		id: 104,
		position_id: 33,
		position_name: 'Менеджер по продажам',
		name: 'Александр',
		last_name: 'Смирнов',
		fullName: 'Александр Смирнов',
		birthday: '01.01.1975',
		phone: '+7(495)5551212',
		email: 'alexandr.smirnov@example.com',
		avatar: 'https://randomuser.me/api/portraits/men/4.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 12 } ],
	},
	{
		id: 105,
		position_id: 34,
		position_name: 'Таргетолог',
		name: 'Ольга',
		last_name: 'Васильева',
		fullName: 'Ольга Васильева',
		birthday: '12.12.1987',
		phone: '+7(916)1234567',
		email: 'olga.vasilieva@example.com',
		avatar: 'https://randomuser.me/api/portraits/women/5.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 12 } ],
	},
	{
		id: 106,
		position_id: 35,
		position_name: 'Младший помощник',
		name: 'Павел',
		last_name: 'Сергеев',
		fullName: 'Павел Сергеев',
		birthday: '18.03.1991',
		phone: '+7(905)1234567',
		email: 'pavel.sergeev@example.com',
		avatar: 'https://randomuser.me/api/portraits/men/24.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 12 } ],
	},
	{
		id: 107,
		position_id: 36,
		position_name: 'Старший рекрутер',
		name: 'Татьяна',
		last_name: 'Николаева',
		fullName: 'Татьяна Николаева',
		birthday: '25.05.1995',
		phone: '+7(903)1234567',
		email: 'tatiana.nikolaeva@example.com',
		avatar: 'https://randomuser.me/api/portraits/women/7.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 12 } ],
	},
	{
		id: 108,
		position_id: 5,
		position_name: 'Маркетолог',
		name: 'Ирина',
		last_name: 'Лагинуш',
		fullName: 'Ирина Лагинуш',
		birthday: '12.11.1990',
		phone: '+7(903)1235567',
		email: 'irina90dar@example.com',
		avatar: 'https://randomuser.me/api/portraits/women/8.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 12 } ],
	},
	{
		id: 109,
		position_id: 19,
		position_name: 'Программист',
		name: 'Максим',
		last_name: 'Иванов',
		fullName: 'Максим Иванов',
		birthday: '05.06.1985',
		phone: '+7(903)1236677',
		email: 'max85@example.com',
		avatar: 'https://randomuser.me/api/portraits/men/5.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 12 } ],
	},
	{
		id: 110,
		position_id: 37,
		position_name: 'Дизайнер',
		name: 'Екатерина',
		last_name: 'Сидорова',
		fullName: 'Екатерина Сидорова',
		birthday: '25.01.1993',
		phone: '+7(903)1112255',
		email: 'kate93@example.com',
		avatar: 'https://randomuser.me/api/portraits/women/7.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 12 } ],
	},
	{
		id: 111,
		position_id: 38,
		position_name: 'HR-менеджер',
		name: 'Дмитрий',
		last_name: 'Смирнов',
		fullName: 'Дмитрий Смирнов',
		birthday: '15.09.1987',
		phone: '+7(903)6789900',
		email: 'dmitry87@example.com',
		avatar: 'https://randomuser.me/api/portraits/men/7.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 12 } ],
	},
	{
		id: 112,
		position_id: 39,
		position_name: 'Бухгалтер',
		name: 'Ольга',
		last_name: 'Петрова',
		fullName: 'Ольга Петрова',
		birthday: '30.04.1980',
		phone: '+7(903)5557788',
		email: 'olga80@example.com',
		avatar: 'https://randomuser.me/api/portraits/women/3.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 12 } ],
	},
	{
		id: 113,
		position_id: 33,
		position_name: 'Менеджер по продажам',
		name: 'Андрей',
		last_name: 'Кузнецов',
		fullName: 'Андрей Кузнецов',
		birthday: '08.07.1992',
		phone: '+7(903)2221122',
		email: 'andrew92@example.com',
		avatar: 'https://randomuser.me/api/portraits/men/9.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 12 } ],
	},
	{
		id: 113,
		position_id: 40,
		position_name: 'Аналитик',
		name: 'Анастасия',
		last_name: 'Павлова',
		fullName: 'Анастасия Павлова',
		birthday: '23.12.1988',
		phone: '+7(903)4443322',
		email: 'anastasia88@example.com',
		avatar: 'https://randomuser.me/api/portraits/women/2.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 12 } ],
	},
	{
		id: 114,
		position_id: 41,
		position_name: 'Менеджер по маркетингу',
		name: 'Сергей',
		last_name: 'Ковалев',
		fullName: 'Сергей Ковалев',
		birthday: '01.03.1991',
		phone: '+7(903)9994433',
		email: 'sergey91@example.com',
		avatar: 'https://randomuser.me/api/portraits/men/8.jpg',
		last_seen: '2023-07-26',
		profile_groups: [ { id: 12 } ],
	},
];
const positions = [
	{
		id: 1,
		name: 'Финансовый аналитик'
	},
	{
		id: 2,
		name: 'Дизайнер интерфейсов'
	},
	{
		id: 3,
		name: 'Разработчик программного обеспечения'
	},
	{
		id: 4,
		name: 'HR-специалист'
	},
	{
		id: 5,
		name: 'Маркетолог'
	},
	{
		id: 6,
		name: 'Инженер-конструктор'
	},
	{
		id: 7,
		name: 'Адвокат'
	},
	{
		id: 8,
		name: 'Преподаватель'
	},
	{
		id: 9,
		name: 'Архитектор'
	},
	{
		id: 10,
		name: 'Менеджер по закупкам'
	},
	{
		id: 11,
		name: 'Менеджер по персоналу'
	},
	{
		id: 12,
		name: 'Технический писатель'
	},
	{
		id: 13,
		name: 'Копирайтер'
	},
	{
		id: 14,
		name: 'Аналитик данных'
	},
	{
		id: 15,
		name: 'Контент-менеджер'
	},
	{
		id: 16,
		name: 'Системный администратор'
	},
	{
		id: 17,
		name: 'Тестировщик'
	},
	{
		id: 18,
		name: 'Директор'
	},
	{
		id: 19,
		name: 'Программист'
	},
	{
		id: 20,
		name: 'Редактор'
	},
	{
		id: 21,
		name: 'Переводчик'
	},
	{
		id: 22,
		name: 'Технический писатель'
	},
	{
		id: 23,
		name: 'Строитель'
	},
	{
		id: 24,
		name: 'Интернет-магазин'
	},
	{
		id: 25,
		name: 'Директор по маркетингу'
	},
	{
		id: 26,
		name: 'Директор по продажам'
	},
	{
		id: 27,
		name: 'Технический писатель'
	},
	{
		id: 28,
		name: 'Директор по развитию'
	},
	{
		id: 29,
		name: 'Директор по управлению персоналом'
	},
	{
		id: 30,
		name: 'Консультант'
	},
	{
		id: 31,
		name: 'HR менеджер'
	},
	{
		id: 32,
		name: 'IT специалист'
	},
	{
		id: 33,
		name: 'Менеджер по продажам'
	},
	{
		id: 34,
		name: 'Таргетолог'
	},
	{
		id: 35,
		name: 'Младший помощник'
	},
	{
		id: 36,
		name: 'Старший рекрутер'
	},
	{
		id: 37,
		name: 'Дизайнер'
	},
	{
		id: 38,
		name: 'HR-менеджер'
	},
	{
		id: 39,
		name: 'Бухгалтер'
	},
	{
		id: 40,
		name: 'Аналитик'
	},
	{
		id: 41,
		name: 'Менеджер по маркетингу'
	},
	{
		id: 42,
		name: 'Генеральный директор'
	},
	{
		id: 43,
		name: 'Коммерческий директор'
	},
	{
		id: 44,
		name: 'Директор по персоналу'
	},
	{
		id: 45,
		name: 'Начальник'
	},
	{
		id: 46,
		name: 'Руководитель'
	},
	{
		id: 47,
		name: 'Дизайнер интерфейсов, UI\\UX'
	},
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
const profile_groups = [
	{
		id: 1,
		name: 'Отдел найма и обучения',
		active: 1,
	},
	{
		id: 2,
		name: 'Сектор подбора персонала',
		active: 1,
	},
	{
		id: 3,
		name: 'Call-центр',
		active: 1,
	},
	{
		id: 4,
		name: 'Отдел заботы',
		active: 1,
	},
	{
		id: 5,
		name: 'Сектор приёмной',
		active: 1,
	},
	{
		id: 6,
		name: 'IT-отдел',
		active: 1,
	},
	{
		id: 7,
		name: 'IT-сектор',
		active: 1,
	},
	{
		id: 8,
		name: 'Отдел маркетинга',
		active: 1,
	},
	{
		id: 9,
		name: 'Сектор PR',
		active: 1,
	},
	{
		id: 10,
		name: 'Сектор интернет-продвижения',
		active: 1,
	},
	{
		id: 11,
		name: 'Сектор дизайна',
		active: 1,
	},
	{
		id: 12,
		name: 'Отдел продаж',
		active: 1,
	},
	{
		id: 13,
		name: 'Сектор холодных продаж',
		active: 1,
	},
	{
		id: 14,
		name: 'Группа ХП-01',
		active: 1,
	},
	{
		id: 15,
		name: 'Менеджеры ХП-01',
		active: 1,
	},
	{
		id: 16,
		name: 'Отдел по работе с клиентами',
		active: 1,
	},
	{
		id: 17,
		name: 'Сектор сопровождения клиетов',
		active: 1,
	},
]
const resultText = 'Жулимэ — это суета материального мира, попытка сделать из жулимэ монету. Если у тебя ворс неблагородный, если ты делаешь монету из плохого ворса, то ты не сигмач.';
const structure = [
	{
		id: 1,
		parent_id: null,
		name: 'Генеральный директор',
		group_id: null,
		status: 0,
		is_group: 0,
		description: resultText,
		color: 'rgb(124, 174, 243)',
		manager: {
			user_id: 1,
			position_id: 42,
		},
		users: [{id: 1}]
	},
	{
		id: 2,
		parent_id: 1,
		name: 'Коммерческий департамент',
		group_id: null,
		status: 0,
		is_group: 0,
		description: resultText,
		color: '#EBF3FB',
		manager: {
			user_id: 2,
			position_id: 43,
		},
		users: [{id: 2}]
	},
	{
		id: 3,
		parent_id: 2,
		name: 'Департамент персонала',
		group_id: null,
		status: 0,
		is_group: 0,
		description: resultText,
		color: '#F7FDFF',
		manager: {
			user_id: 3,
			position_id: 44,
		},
		users: [{id: 3}]
	},
	{
		id: 4,
		parent_id: 3,
		name: null,
		group_id: 1,
		status: 0,
		is_group: 1,
		description: resultText,
		color: '#F7FDFF',
		manager: {
			user_id: 4,
			position_id: 45,
		},
		users: [{id: 4}]
	},
	{
		id: 5,
		parent_id: 4,
		name: null,
		group_id: 2,
		status: 0,
		is_group: 0,
		description: resultText,
		color: '#F7FDFF',
		manager: {
			user_id: 5,
			position_id: 46,
		},
		users: [{id: 5}, ...getUsers(28)]
	},
	{
		id: 6,
		parent_id: 4,
		name: null,
		group_id: 3,
		status: 0,
		is_group: 0,
		description: resultText,
		color: '#F7FDFF',
		manager: {
			user_id: 6,
			position_id: 22,
		},
		users: [{id: 6}, ...getUsers(56)]
	},
	{
		id: 7,
		parent_id: 3,
		name: null,
		group_id: 4,
		status: 0,
		is_group: 1,
		description: resultText,
		color: '#F7FDFF',
		manager: {
			user_id: 7,
			position_id: 45,
		},
		users: [{id: 7}, ...getUsers(12)]
	},
	{
		id: 8,
		parent_id: 7,
		name: null,
		group_id: 5,
		status: 0,
		is_group: 0,
		description: resultText,
		color: '#F7FDFF',
		manager: {
			user_id: 8,
			position_id: 46,
		},
		users: [{id: 8}, ...getUsers(13)]
	},
	{
		id: 9,
		parent_id: 7,
		name: null,
		group_id: 6,
		status: 0,
		is_group: 0,
		description: resultText,
		color: '#F7FDFF',
		manager: {
			user_id: 9,
			position_id: 3,
		},
		users: [{id: 9}, ...getUsers(12)]
	},
	{
		id: 10,
		parent_id: 7,
		name: null,
		group_id: 7,
		status: 0,
		is_group: 0,
		description: resultText,
		color: '#F7FDFF',
		manager: {
			user_id: 10,
			position_id: 47,
		},
		users: [{id: 10}, ...getUsers(12)]
	},
	{
		id: 11,
		parent_id: 2,
		name: 'Коммерческий департамент',
		group_id: null,
		status: 0,
		is_group: 0,
		description: resultText,
		color: '#F6FFFE',
		manager: {
			user_id: 11,
			position_id: 43,
		},
		users: [{id: 11}]
	},
	{
		id: 12,
		parent_id: 11,
		name: null,
		group_id: 8,
		status: 0,
		is_group: 1,
		description: resultText,
		color: '#F6FFFE',
		manager: {
			user_id: 12,
			position_id: 45,
		},
		users: [{id: 12}, ...getUsers(22)]
	},
	{
		id: 13,
		parent_id: 12,
		name: null,
		group_id: 9,
		status: 0,
		is_group: 0,
		description: resultText,
		color: '#F6FFFE',
		manager: {
			user_id: 13,
			position_id: 46,
		},
		users: [{id: 13}, ...getUsers(22)]
	},
	{
		id: 14,
		parent_id: 12,
		name: null,
		group_id: 10,
		status: 0,
		is_group: 0,
		description: resultText,
		color: '#F6FFFE',
		manager: {
			user_id: 14,
			position_id: 41,
		},
		users: [{id: 14}, ...getUsers(22)]
	},
	{
		id: 15,
		parent_id: 12,
		name: null,
		group_id: 11,
		status: 0,
		is_group: 0,
		description: resultText,
		color: '#F6FFFE',
		manager: {
			user_id: 15,
			position_id: 46,
		},
		users: [{id: 15}, ...getUsers(22)]
	},
	{
		id: 16,
		parent_id: 11,
		name: null,
		group_id: 12,
		status: 1,
		is_group: 1,
		description: resultText,
		color: '#F6FFFE',
		manager: {
			user_id: 16,
			position_id: 45,
		},
		users: [{id: 16}]
	},
	{
		id: 17,
		parent_id: 16,
		name: null,
		group_id: 13,
		status: 0,
		is_group: 0,
		description: resultText,
		color: '#F6FFFE',
		manager: {
			user_id: 17,
			position_id: 46,
		},
		users: [{id: 17}, ...getUsers(22)]
	},
	{
		id: 18,
		parent_id: 16,
		name: null,
		group_id: 14,
		status: 0,
		is_group: 0,
		description: resultText,
		color: '#F6FFFE',
		manager: {
			user_id: 18,
			position_id: 46,
		},
		users: [{id: 18}, ...getUsers(22)]
	},
	{
		id: 19,
		parent_id: 16,
		name: null,
		group_id: 15,
		status: 0,
		is_group: 0,
		description: resultText,
		color: '#F6FFFE',
		manager: null,
		is_vacant: true,
		users: [...getUsers(5)]
	},
	{
		id: 20,
		parent_id: 11,
		name: null,
		group_id: 16,
		status: 0,
		is_group: 1,
		description: resultText,
		color: '#F6FFFE',
		manager: {
			user_id: 19,
			position_id: 45,
		},
		users: [{id: 19}]
	},
	{
		id: 21,
		parent_id: 20,
		name: null,
		group_id: 16,
		status: 0,
		is_group: 0,
		description: resultText,
		color: '#F6FFFE',
		manager: {
			user_id: 20,
			position_id: 45,
		},
		users: [{id: 20}, ...getUsers(3)]
	},
	{
		id: 22,
		parent_id: 20,
		name: null,
		group_id: 17,
		status: 0,
		is_group: 0,
		description: resultText,
		color: '#F6FFFE',
		manager: {
			user_id: 21,
			position_id: 45,
		},
		users: [{id: 21}, ...getUsers(3)]
	},
]
const structure_old = [
	{
		id: 2,
		departmentChildren: [
			{
				id: 3,
				departmentChildren: [
					{
						id: 4,
						group: true,
						departmentChildren: [
							{
								id: 5,
								employeesCount: 28,
							},
							{
								id: 6,
								employeesCount: 56,
							}
						]
					},
					{
						id: 7,
						employeesCount: 12,
						group: true,
						departmentChildren: [
							{
								id: 8,
								employeesCount: 12,
							},
							{
								id: 9,
								employeesCount: 12,
							},
							{
								id: 10,
								employeesCount: 1,
							}
						]
					}
				]
			},
			{
				id: 11,
				departmentChildren: [
					{
						id: 12,
						employeesCount: 22,
						group: true,
						departmentChildren: [
							{
								id: 13,
								employeesCount: 22,
							},
							{
								id: 14,
								department: '',
								employeesCount: 22,
								result: resultText,
								users: users
							},
							{
								id: 15,
								employeesCount: 22,
							},
						]
					},
					{
						id: 16,
						employeesCount: 444,
						group: true,
						departmentChildren: [
							{
								id: 17,
								employeesCount: 22,
							},
							{
								id: 18,
								employeesCount: 22,
							},
							{
								id: 19,
								employeesCount: 5,
							},
						]
					},
					{
						id: 20,
						employeesCount: 56,
						group: true,
						departmentChildren: [
							{
								id: 21,
								employeesCount: 3,
								result: resultText,
							},
							{
								id: 22,
								employeesCount: 4,
								result: resultText,
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
				id: 23,
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
						id: 24,
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
								id: 25,
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
								id: 26,
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
						id: 27,
						department: 'Отдел производства',
						employeesCount: 0
					}
				]
			}
		]
	},
	{
		id: 28,
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
				id: 29,
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
						id: 30,
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
						id: 31,
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
						id: 32,
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
				id: 33,
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
						id: 34,
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
								id: 35,
								department: 'Участники совета',
								employeesCount: 5,
								users: users
							}
						]
					},
					{
						id: 36,
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
						id: 37,
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

function getUsers(count){
	const rUsers = users.slice(-14);
	const result = []
	for(let i = 0; i < count; ++i){
		const user = rUsers[Math.floor(Math.random() * rUsers.length)];
		result.push(user)
	}
	return result
}

const dictionaries = {
	users,
	profile_groups,
	positions,
}

export {
	structure_old,
	users,
	positions,
	departments,
	structure,
	profile_groups,
	dictionaries,
};
