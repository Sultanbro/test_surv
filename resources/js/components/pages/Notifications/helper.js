/* eslint-disable camelcase */

const tenant = location.hostname.split('.')[0]
const titleTip = ['bp', 'test'].includes(tenant) ? '* к уведомлению будет автоматически прикреплена ссылка на опросник' : '* вставьте в сообщение ссылку на опросник которая будет отправляться'

export const services = [
	{
		value: 'in-app',
		title: 'Отправить уведомление в личный кабинет',
		short: 'Личный кабинет',
	},
	// {
	// 	value: 'mail',
	// 	title: 'Отправить письмо на почту'
	// 	short: 'email',
	// },
	// {
	// 	value: 'sms',
	// 	title: 'Отправить смс',
	// 	short: 'sms',
	// },
	// {
	// 	value: 'telegram',
	// 	title: 'Отправить сообщение в телеграм'
	// 	short: 'telegram',
	// },
	{
		value: 'whatsapp',
		title: 'Отправить сообщение в вацап',
		short: 'Whatsapp',
	},
	// {
	// 	value: 'bitrix',
	// 	title: 'Отправить сообщение в битрикс'
	// 	short: 'bitrix',
	// },
]


export const templateSettings = {
	apply_employee: {
		targets: ['Сотрудники', 'Отделы', 'Должности', 'Все'],
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
		titleTip,
		when: [
			{
				value: 'fired_employee',
				text: 'Через день после отметки об увольнении (триггер)'
			}
		]
	},
	absent_internship: {
		targets: ['Сотрудники', 'Отделы', 'Должности', 'Все'],
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
		titleFixed: '',
		titleTip,
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
		titleFixed: '',
		titleTip,
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
		text: 'Выберите шаблон',
		class: 'hidden',
	},
	{
		value: 'apply_employee',
		text: 'Оформление нового сотрудника',
		class: '',
	},
	{
		value: 'fired_employee',
		text: 'Анкета уволенного',
		class: '',
	},
	{
		value: 'absent_internship',
		text: 'Не присутствовал на стажировке',
		class: '',
	},
]
if(['bp', 'test'].includes(tenant)) templates.push(
	{
		value: 'manager_assessment',
		text: 'Оцените вашего руководителя',
		class: '',
	},
	{
		value: 'coach_assessment',
		text: 'Оцените вашего тренера',
		class: '',
	},
)

export const periods = [
	{
		value: 'daily',
		text: 'Каждый день'
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
