/** @module stores/api/pricing */
import axios from 'axios';

/**
 * Получене менеджера по текущему кабинету
 * @return {PricingManager}
 */
export async function fetchPricingManager() {
	const { data } = await axios.get('/owner/manager');
	return data;
}

/**
 * Получене текущего тарифа
 * @return {ApiResponse.OwnerInfoResponse}
 */
export async function fetchOwnerInfo() {
	const { data } = await axios.get('/owner/info');
	return data;
}

/**
 * Получене возможных тарифов
 * @return {ApiResponse.PricingResponse}
 */
export async function fetchPricing() {
	const { data } = await axios.get('/tariff/get');
	return data;
}

/**
 * Активация промокода
 * @return {PricingPromo}
 */
export async function fetchPricingPromo(code) {
	// const { data } = await axios.get('/pricing/promo', {params: {code}})
	const data = {
		code: 'secret' + code,
		value: 0,
	};
	return data;
}

/**
 * Создание ссылки на оплату
 * @param {ApiRequest.PricingPaymentRequest} params
 * @return {string} - ссылка на оплату
 */
export async function postPaymentData(params) {
	const { data } = await axios.post('/tariff/subscriptions', params);
	return data;
}

/**
 * Получение статуса оплаты
 * @return {ApiResponse.PaymentStatusResponse} - ссылка на оплату
 */
export async function fetchPaymentStatus() {
	// опять пост там где гет должен быть, АААААААА
	const { data } = await axios.post('/payment/status');
	return data;
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
 * @typedef PricingResponse
 * @memberof ApiResponse
 * @type {object}
 * @property {string} message
 * @property {object} data
 * @property {PricingItem[]} data.tariffs
 * @property {PricingItemMultiCurrency} data.priceForOnePerson
 */

/**
 * @typedef OwnerInfoResponse
 * @memberof ApiResponse
 * @type {object}
 * @property {string} message
 * @property {object} data
 * @property {Owner} data.owner
 * @property {PricingCurrent} data.tariff
 */

/**
 * @typedef Owner
 * @type {object}
 * @property {number} id
 * @property {string} email
 * @property {string} phone
 * @property {string} name
 * @property {string} last_name
 * @property {string?} remember_token
 * @property {string?} created_at
 * @property {string?} updated_at
 * @property {string?} deleted_at
 * @property {string?} full_name
 */

/**
 * @typedef PricingCurrent
 * @type {object}
 * @property {number} id - id данной связки?
 * @property {number} owner_id - id владельца
 * @property {number} tariff_id - id тарифа
 * @property {number} extra_user_limit - дополнительные пользователи
 * @property {string} expire_date - оплачен до (DD.MM.YYYY)
 * @property {number} auto_payment - автооплата (1 - есть)
 * @property {string} payment_id - id оплаты в стистеме оплаты
 * @property {string} status - статус оплаты
 * @property {string} service_for_payment - система оплаты
 * @property {string} created_at - дата создания (DD.MM.YYYY hh:mm)
 * @property {string} updated_at - дата обновления (DD.MM.YYYY hh:mm)
 * @property {PricingItem} tariff - объект тарифа
 */

/**
 * @typedef PricingItem
 * @type {object}
 * @property {number} id - id тарифа
 * @property {string} kind - название тарифа
 * @property {string} validity - monthly|annual
 * @property {number} users_limit - кол-во пользователей в тарифе
 * @property {string} price - цена в рублях
 * @property {string} created_at - дата создания (DD.MM.YYYY hh:mm)
 * @property {string} updated_at - дата обновления (DD.MM.YYYY hh:mm)
 * @property {PricingItemMultiCurrency?} multiCurrencyPrice
 */

/**
 * @typedef PricingItemMultiCurrency
 * @type {object}
 * @property {number} kzt
 * @property {number} rub
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

/**
 * @typedef PaymentStatusResponse
 * @memberof ApiResponse
 * @property {string} message
 * @property {boolean} data - успешность оплаты
 */
