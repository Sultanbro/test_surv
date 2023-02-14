/** @module stores/api/pricing */
import axios from 'axios'

/**
 * Получене менеджера по текущему кабинету
 * @return {PricingManager}
 */
export async function fetchPricingManager(){
	// const { data } = await axios.get('/pricing/manager')
	const data = {
		name: 'Александра',
		last_name: 'Воскресенская',
		phone: '+7 (707) 123-45-67',
		email: 'voskresenskaya.alex@gmail.com',
		photo: 'https://placekitten.com/200/200',
		title: 'Приветствую!',
		text: 'Далеко-далеко за словесными горами в стране гласных и согласных живут рыбные тексты. Все родного, предложения осталось большой буквенных маленький дорогу семантика от всех?'
	}
	return data
}

/**
 * Получене текущего тарифа
 * @return {PricingCurrent}
 */
export async function fetchPricingCurrent(){
	// const { data } = await axios.get('/pricing/current')
	const data = {
		id: 1,
		name: 'Бесплатный',
		paid_up_to: '03.02.2024',
		users: 5
	}
	return data
}

/**
 * Получене возможных тарифов
 * @return {PricingItem[]}
 */
export async function fetchPricing(){
	// const { data } = await axios.get('/pricing')
	const data = [
		{
			id: 1,
			name: 'Бесплатный',
			users: 5,
			space: '5 Гб',
			kb: true,
			news: true,
			education: true,
			analytics: true,
			quality_control: true,
			chat: true,
			structure: true,
			support: true,
			domain: false,
			monthly: 0,
			annual: 0,
			discount: 0
		},
		{
			id: 2,
			name: 'База',
			users: 20,
			space: '20 Гб',
			kb: true,
			news: true,
			education: true,
			analytics: true,
			quality_control: true,
			chat: true,
			structure: true,
			support: true,
			domain: true,
			monthly: 1200,
			annual: 15500,
			discount: 0
		},
		{
			id: 3,
			name: 'Стандарт',
			users: 50,
			space: '50 Гб',
			kb: true,
			news: true,
			education: true,
			analytics: true,
			quality_control: true,
			chat: true,
			structure: true,
			support: true,
			domain: true,
			monthly: 3850,
			annual: 37000,
			discount: 20
		},
		{
			id: 4,
			name: 'PRO',
			users: 100,
			space: '1 Тб',
			kb: true,
			news: true,
			education: true,
			analytics: true,
			quality_control: true,
			chat: true,
			structure: true,
			support: true,
			domain: true,
			monthly: 14250,
			annual: 137000,
			discount: 20
		}
	]
	return data
}

/**
 * Активация промокода
 * @return {PricingPromo}
 */
export async function fetchPricingPromo(code){
	// const { data } = await axios.get('/pricing/promo', {params: {code}})
	const data = {
		code: 'secret' + code,
		value: 100,
	}
	return data
}

/**
 * Создание ссылки на оплату
 * @param {ApiRequest.PricingPaymentRequest} params
 * @return {string} - ссылка на оплату
 */
export async function postPaymentData(params){
	const { data } = await axios.post('/payment', params)
	return data
}

/**
 * @typedef PricingManagerResponse
 * @type {object}
 * @property {?PricingManager} data
 * @memberof ApiResponse
 */

/**
 * @typedef PricingManager
 * @type {object}
 * @property {string} name - имя менеджера
 * @property {string} last_name - фамилия менеджера
 * @property {string} phone - телефон менеджера
 * @property {string} email - email менеджера
 * @property {string} photo - фото менеджера
 * @property {string} title - зоголовок для приветственного сообщения
 * @property {string} text - текст приветственного сообщения
 */

/**
 * @typedef PricingCurrent
 * @type {object}
 * @property {number} id - id тарифа
 * @property {string} name - название тарифа
 * @property {string} paid_up_to - оплачен до (DD.MM.YYYY)
 * @property {number} users - кол-во оплаченных пользователей (может быть больше чем в тарифе т.к. можно докупать)
 */

/**
 * @typedef PricingItem
 * @type {object}
 * @property {number} id - id тарифа
 * @property {string} name - название тарифа
 * @property {number} users - кол-во пользователей
 * @property {string} space - объем под файлы
 * @property {boolean} kb -
 * @property {boolean} news -
 * @property {boolean} education -
 * @property {boolean} analytics -
 * @property {boolean} quality_control -
 * @property {boolean} chat -
 * @property {boolean} structure -
 * @property {boolean} support -
 * @property {boolean} domain -
 * @property {number} monthly - цена за месяц в рублях
 * @property {number} annual - цена за год в рублях
 * @property {number} discount - скидка в % прри оплате за год
*/

/**
 * @typedef PricingPromo
 * @type {object}
 * @property {string} code - секретный код для формы оплаты
 * @property {number} value - скидка в рублях
 */

/**
 * @typedef PricingPaymentRequest
 * @memberof ApiRequest
 * @type {object}
 * @property {string} currency - валюта kzt|rub|dollar
 * @property {number} tariff_id -
 * @property {number} extra_users_limit -
 * @property {boolean} [auto_payment] - автооплата
 */
