export function getRandomInt(min, max) {
	min = Math.ceil(min);
	max = Math.floor(max);
	return Math.floor(Math.random() * (max - min) + min); // The maximum is exclusive and the minimum is inclusive
}

export function getRandomIntInclusive(min, max) {
	min = Math.ceil(min);
	max = Math.floor(max);
	return Math.floor(Math.random() * (max - min + 1) + min); // The maximum is inclusive and the minimum is inclusive
}

export function getRandomArrayItem(array) {
	return array[getRandomInt(0, array.length)]
}

export const namesM = [
	'Агап',
	'Адам',
	'Аким',
	'Боян',
	'Влас',
	'Глеб',
	'Егор',
	'Ефим',
	'Иван',
	'Илья',
	'Ипат',
	'Исай',
	'Карл',
	'Клим',
	'Лавр',
	'Леон',
	'Лука',
	'Марк',
	'Наум',
	'Олег',
	'Осип',
	'Петр',
	'Фома',
	'Фрол',
	'Юлий',
	'Яков',
	'Якуб',

	'Абай',
	'Абыз',
	'Азат',
	'Азат',
	'Амир',
	'Ахан',
	'Ахат',
	'Баят',
	'Гани',
	'Гафу',
	'Дияр',
	'Елеу',
	'Есей',
	'Есен',
	'Заки',
	'Мади',
	'Муса',
	'Наби',
	'Ноян',
	'Омар',
	'Ораз',
	'Уали',
	'Улан',
	'Шона',
	'Шора',
]

export const namesF = [
	'Аида',
	'Алла',
	'Анна',
	'Вера',
	'Воля',
	'Галя',
	'Дана',
	'Дина',
	'Дуня',
	'Заря',
	'Инга',
	'Инна',
	'Иона',
	'Кира',
	'Лада',
	'Лана',
	'Леся',
	'Лиза',
	'Лина',
	'Майя',
	'Маша',
	'Мира',
	'Нана',
	'Ника',
	'Нина',
	'Нона',
	'Нора',
	'Рада',
	'Роза',
	'Шура',
	'Юлия',

	'Адия',
	'Ажар',
	'Аида',
	'Айша',
	'Алия',
	'Алуа',
	'Анар',
	'Асия',
	'Бану',
	'Баян',
	'Дана',
	'Дара',
	'Зара',
	'Зере',
	'Зиба',
	'Сара',

	'Алма',
	'Асем',
]

export const lastNamesM = [
	'Иванов',
	'Смирнов',
	'Кузнецов',
	'Попов',
	'Васильев',
	'Петров',
	'Соколов',
	'Михайлов',
	'Новиков',
	'Фёдоров',
	'Морозов',
	'Волков',
	'Алексеев',
	'Лебедев',
	'Семёнов',
	'Егоров',
	'Павлов',
	'Козлов',
	'Степанов',
	'Николаев',
	'Орлов',
	'Андреев',
	'Макаров',
	'Никитин',
	'Захаров',

	// 'Ахметов',
	// 'Омаров',
	// 'Оспанов',
	// 'Алиев',
	// 'Сулейменов',
	// 'Абдрахманов',
	// 'Ибрагимов',
	// 'Калиев',
	// 'Садыков',
	// 'Ибраев',
	// 'Смагулов',
	// 'Абдуллаев',
	// 'Исаев',
	// 'Султанов',
	// 'Исмаилов',
	// 'Нургалиев',
	// 'Каримов',
	// 'Серiк',
	// 'Амангельды',
	// 'Болат',
	// 'Марат',
	// 'Серiкбай',
	// 'Мурат',
	// 'Кусаинов',
	// 'Абилев',
	// 'Касымов',
]

export const lastNamesF = [
	'Иванова',
	'Смирнова',
	'Кузнецова',
	'Попова',
	'Васильева',
	'Петрова',
	'Соколова',
	'Михайлова',
	'Новикова',
	'Фёдорова',
	'Морозова',
	'Волкова',
	'Алексеева',
	'Лебедева',
	'Семёнова',
	'Егорова',
	'Павлова',
	'Козлова',
	'Степанова',
	'Николаева',
	'Орлова',
	'Андреева',
	'Макарова',
	'Никитина',
	'Захарова',
]

export function getRandomMale(){
	return {
		name: getRandomArrayItem(namesM),
		lastName: getRandomArrayItem(lastNamesM),
	}
}

export function getRandomFemale(){
	return {
		name: getRandomArrayItem(namesF),
		lastName: getRandomArrayItem(lastNamesF),
	}
}

export function getRandomPerson(){
	const isMale = Math.random() < 0.5
	return isMale ? getRandomMale() : getRandomFemale()
}
