export const services = [
	{
		value: 'in-app',
		title: 'Отправить уведомление в личный кабинет'
	},
	// {
	// 	value: 'mail',
	// 	title: 'Отправить письмо на почту'
	// },
	// {
	// 	value: 'sms',
	// 	title: 'Отправить смс'
	// },
	// {
	// 	value: 'telegram',
	// 	title: 'Отправить сообщение в телеграм'
	// },
	{
		value: 'whatsapp',
		title: 'Отправить сообщение в вацап'
	},
	{
		value: 'bitrix',
		title: 'Отправить сообщение в битрикс'
	},
]


export const templateSettings = {
	apply_employee: {
		targets: ['Сотрудники', 'Отделы', 'Должности'],
		recipients: [],
		title: 'Подготовьте документы для оформления нового сотрудника в штат',
		titleFixed: '"Имя и Фамилия указанные в профиле сотрудника"\n"График работы указанный в табеле при приеме сотрудника в штат после стажировки"',
		titleTip: '',
		when: [
			{
				value: 'apply_employee',
				text: 'Уведомлять в момент принятия (триггер)'
			},
			{
				value: 'period',
				text: 'Периодичность отправки'
			}
		]
	},
	fired_employee: {
		targets: 'Статус сотрудника "Уволенный"',
		recipients: [],
		title: 'Уважаемый коллега! Какими бы ни были причины расставания, мы благодарим Вас за время, силы, знания и энергию, которые Вы отдали для успешной работы и развития нашей организации, и просим заполнить эту небольшую анкету.',
		titleFixed: '',
		titleTip: '* дополнительно укажите тут коротку ссылку на имеющийся у вас опросник',
		when: [
			{
				value: 'fired_employee',
				text: 'Через день после отметки об увольнении (триггер)'
			}
		]
	},
	absent_internship: {
		targets: ['Сотрудники', 'Отделы', 'Должности'],
		recipients: [],
		title: 'Стажер отстутствовал на обучении. Созвонитсь и верните его на стажировку',
		titleFixed: '"Имя и Фамилия указанные в профиле сотрудника"',
		titleTip: '',
		when: [
			{
				value: 'absent_internship',
				text: 'В момент отметки в табеле об отсутствии (триггер)'
			}
		]
	},
	manager_assessment: {
		targets: 'Все сотрудники отделов',
		recipients: [],
		title: 'Оцените работу Вашего руководителя и старшего специалиста за текущий месяц.',
		titleFixed: '"Ссылка на опрос"',
		titleTip: '',
		when: [
			{
				value: 'manager_assessment',
				text: 'За 2 дня до окончания календарного месяца (триггер)'
			}
		]
	},
	coach_assessment: {
		targets: 'Стажеры первого дня',
		recipients: [],
		title: 'Добрый день!\nПройдите небольшой опрос, чтобы оценить Вашего тренера.\nБыстро. Анонимно. Для Дела.',
		titleFixed: '"Ссылка на опрос"',
		titleTip: '',
		when: [
			{
				value: 'coach_assessment',
				text: 'В период 17:00 - 19:00 в первый день обучения стажера, если он не отмечен, как отсутствовал (триггер)'
			}
		]
	},
}

export const templates = [
	{
		value: '',
		text: 'Выберите шаблон'
	},
	{
		value: 'apply_employee',
		text: 'Оформление нового сотрудника'
	},
	{
		value: 'fired_employee',
		text: 'Анкета уволенного'
	},
	{
		value: 'absent_internship',
		text: 'Не присутствовал на стажировке'
	},
]
if(location.hostname.split('.')[0] === 'bp') templates.push(
	{
		value: 'manager_assessment',
		text: 'Оцените вашего руководителя'
	},
	{
		value: 'coach_assessment',
		text: 'Оцените вашего тренера'
	},
)

export const periods = [
	{
		value: 'daily',
		text: 'Каждый жень'
	},
	{
		value: 'weekly',
		text: 'По дням недели'
	},
	{
		value: 'monthly',
		text: 'По числам месяца'
	}
]

export const templateFrequency = [
	'apply_employee',
	'fired_employee',
	'absent_internship',
	'manager_assessment',
	'coach_assessment',
]
