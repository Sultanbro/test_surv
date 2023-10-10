export type KBBook = {
	id: number // id
	parent_id: number | null // родительская статься в дереве
	title: string // заголовок
	user_id: number // id автора
	editor_id: number // id редактора
	text: string // текст стать html
	is_deleted: number  // ???
	hash: string // ???
	order: number  // порядок показа статей
	created_at: string | null
	updated_at: string | null
	deleted_at?: string | null
	access?: number // ???
	pass_grade?: number // ???
	opened: boolean // ???
	children?: Array<KBBook> // массив подстатей
	questions?: Array<Question> // вопросы
	book?: KBBook // родительская статья
}

export type Question = {
	id: number // шв
	type: number // ???
	text: string // текст вопроса
	order: number // порядок
	points: number // баллы за ответ?
	page: number // ???
	testable_id: number // ???
	testable_type: string  // ???
	created_at: string | null
	updated_at: string | null
	editable: boolean // ???
	checked: boolean // ???
	variants: Array<Answer> // варианты ответов
}

export type Answer = {
	text: string // ответ
	right: number | boolean // правильный ли ответ
	before: string // ???
}

export type AccessItem = {
	id: number // id сотрудника, отдела, должности
	name: string // имя сотрудника, отдела, должности
	type: number // 1 - сотрудник, 2 - отдел, 3 - должность
}

export type GlossaryTerm = {
	id: number // id
	word: string // термин
	definition: string // определение
	created_at?: string | null
	updated_at?: string | null
}

declare function fetchKBBooks(): Promise<Array<KBBook>>
declare function fetchKBArchived(): Promise<Array<KBBook>>
declare function fetchKBBook(id: number): Promise<{
	trees: Array<KBBook>
	book: KBBook
	item_models: Array<unknown> | {[key: string]: unknown}
	can_save: boolean
}>
declare function deleteKBBook(id: number): Promise<null>
declare function restoreKBBook(id: number): Promise<null>
declare function searchKBBook(text: string): Promise<{
	items: Array<KBBook>
}>
declare function createKBBook(name: string): Promise<KBBook>
declare function updateKBBook(request: {
	id: number
	title: string
	who_can_read: Array<AccessItem>
	who_can_edit: Array<AccessItem>
}): Promise<unknown>
declare function updateKBOrder(request: {
	id: number
	order: number
	parent_id: number | null
}): Promise<unknown>
declare function fetchKBAccess(id: number): Promise<{
	who_can_read: Array<AccessItem>
	who_can_edit: Array<AccessItem>
}>
declare function fetchGlossary(): Promise<Array<GlossaryTerm>>
declare function saveGlossaryTerm(word: GlossaryTerm): Promise<number>
declare function deleteGlossaryTerm(id: number): Promise<number>

