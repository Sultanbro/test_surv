<template>
	<div>
		<BRow class="m-0 cestificates-constructor">
			<BCol cols="3">
				<div class="settings">
					<BFormGroup class="custom-switch custom-switch-sm">
						<b-form-checkbox
							v-model="bgOp"
							switch
						>
							Затемнить фон
						</b-form-checkbox>
					</BFormGroup>
					<div v-if="selectedEdit === 1">
						<BFormGroup
							label="Имя и Фамилия (Впишите любое)"
							description="Это поле будет брать имя и фамилию того сотрудника, который пройдет курс"
						>
							<BFormInput v-model="textFullName" />
						</BFormGroup>
						<BFormGroup label="Жирность текста">
							<BFormSelect
								v-model="fullName.fontWeight"
								:options="fontWeightList"
							/>
						</BFormGroup>
						<BFormGroup label="Размер текста">
							<BFormInput v-model="fullName.size" />
						</BFormGroup>
						<BFormGroup
							class="custom-switch"
							label="Ширина блока"
						>
							<b-form-checkbox
								v-model="fullName.fullWidth"
								switch
							>
								На всю ширину
							</b-form-checkbox>
							<b-form-input
								v-model="fullName.width"
								:disabled="fullName.fullWidth"
							/>
						</BFormGroup>
						<BFormGroup label="Цвет текста">
							<input
								v-model="fullName.color"
								type="color"
								class="color-picker"
							>
						</BFormGroup>
						<b-form-group label="Формат текста">
							<b-form-radio
								v-model="fullName.uppercase"
								name="some-radios"
								value="none"
								:checked="true"
							>
								По умолчанию
							</b-form-radio>
							<b-form-radio
								v-model="fullName.uppercase"
								name="some-radios"
								value="uppercase"
							>
								Все
								заглавные
							</b-form-radio>
						</b-form-group>
						<b-form-group label="Курсив">
							<b-form-radio
								v-model="fullName.fontStyle"
								name="font-style-fullname"
								value="italic"
							>
								Да
							</b-form-radio>
							<b-form-radio
								v-model="fullName.fontStyle"
								name="font-style-fullname"
								value="normal"
								:checked="true"
							>
								Нет
							</b-form-radio>
						</b-form-group>
					</div>
					<div v-if="selectedEdit === 2">
						<BFormGroup
							label="Название курса"
							description="Это поле будет брать название курса, который пройдет сотрудник"
						>
							<BFormInput v-model="textCourseName" />
						</BFormGroup>
						<BFormGroup label="Жирность текста">
							<BFormSelect
								v-model="courseName.fontWeight"
								:options="fontWeightList"
							/>
						</BFormGroup>
						<BFormGroup label="Размер текста">
							<BFormInput v-model="courseName.size" />
						</BFormGroup>
						<BFormGroup
							class="custom-switch"
							label="Ширина блока"
						>
							<b-form-checkbox
								v-model="courseName.fullWidth"
								switch
							>
								На всю ширину
							</b-form-checkbox>
							<b-form-input
								v-model="courseName.width"
								:disabled="courseName.fullWidth"
							/>
						</BFormGroup>
						<BFormGroup label="Цвет текста">
							<input
								v-model="courseName.color"
								type="color"
								class="color-picker"
							>
						</BFormGroup>
						<b-form-group label="Формат текста">
							<b-form-radio
								v-model="courseName.uppercase"
								name="some-radios"
								value="none"
							>
								По умолчанию
							</b-form-radio>
							<b-form-radio
								v-model="courseName.uppercase"
								name="some-radios"
								value="uppercase"
							>
								Все
								заглавные
							</b-form-radio>
						</b-form-group>
						<b-form-group label="Курсив">
							<b-form-radio
								v-model="courseName.fontStyle"
								name="font-style-course"
								value="italic"
							>
								Да
							</b-form-radio>
							<b-form-radio
								v-model="courseName.fontStyle"
								name="font-style-course"
								value="normal"
								:checked="true"
							>
								Нет
							</b-form-radio>
						</b-form-group>
					</div>
					<div v-if="selectedEdit === 4">
						<BFormGroup
							label="Дата"
							description="Здесь будет дата окончания курса"
						>
							<BFormInput v-model="textDate" />
						</BFormGroup>
						<BFormGroup label="Жирность текста">
							<BFormSelect
								v-model="date.fontWeight"
								:options="fontWeightList"
							/>
						</BFormGroup>
						<BFormGroup label="Размер текста">
							<BFormInput
								v-model="date.size"
								type="number"
							/>
						</BFormGroup>
						<BFormGroup
							class="custom-switch"
							label="Ширина блока"
						>
							<b-form-checkbox
								v-model="date.fullWidth"
								switch
							>
								На всю ширину
							</b-form-checkbox>
							<b-form-input
								v-model="date.width"
								type="number"
								:disabled="date.fullWidth"
							/>
						</BFormGroup>
						<BFormGroup label="Цвет текста">
							<input
								v-model="date.color"
								type="color"
								class="color-picker"
							>
						</BFormGroup>
						<b-form-group label="Курсив">
							<b-form-radio
								v-model="date.fontStyle"
								name="font-style-date"
								value="italic"
							>
								Да
							</b-form-radio>
							<b-form-radio
								v-model="date.fontStyle"
								name="font-style-date"
								value="normal"
								:checked="true"
							>
								Нет
							</b-form-radio>
						</b-form-group>
					</div>
				</div>
			</BCol>
			<BCol cols="9">
				<div class="draggable-container">
					<div class="draggable-edit">
						<div
							ref="fullName"
							name="fullName"
							class="draggable no-border"
							:data-x="fullName.screenX"
							:data-y="fullName.screenY"
							follow-text="Имя и фамилия"
							:style="[styleFullName, transformFullName]"
							style="margin-top: 40px;"
							:class="{'darkened': bgOp, 'active': selectedEdit === 1}"
							@click="selectEdit(1)"
						>
							{{ textFullName }}
						</div>
						<div
							ref="courseName"
							name="courseName"
							class="draggable no-border"
							:data-x="courseName.screenX"
							:data-y="courseName.screenY"
							follow-text="Название курса"
							:style="[styleCourseName, transformCourseName]"
							style="margin-top: 120px;"
							:class="{'darkened': bgOp, 'active': selectedEdit === 2}"
							@click="selectEdit(2)"
						>
							{{ textCourseName }}
						</div>
						<div
							ref="date"
							name="date"
							class="draggable no-border"
							:data-x="date.screenX"
							:data-y="date.screenY"
							follow-text="Дата завершения курса"
							:style="[styleDate, transformDateName]"
							style="margin-top: 280px;"
							:class="{'darkened': bgOp, 'active': selectedEdit === 4}"
							@click="selectEdit(4)"
						>
							{{ textDate }}
						</div>
						<div
							ref="pdfpdf"
							class="vue-pdf-container"
							:class="bgOp ? 'darkened' : ''"
						>
							<vue-pdf-embed
								v-if="img"
								:source="img"
							/>
						</div>
					</div>
				</div>
			</BCol>
		</BRow>
		<div class="modal-footer">
			<BButton
				variant="success"
				@click="saveChanges"
			>
				Сохранить
			</BButton>
		</div>
	</div>
</template>

<script>
/* eslint-disable vue/no-side-effects-in-computed-properties */
import interact from 'interactjs';
import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed';

export default {
	name: 'UploadSertificateModal',
	components: {
		VuePdfEmbed
	},
	props: {
		img: {
			type: String,
			default: ''
		},
		styles: {
			type: String,
			default: ''
		},
		modalCertificate: Boolean
	},
	data() {
		return {
			selectedUppercase: [],
			bgOp: false,
			selectedEdit: 1,
			textFullName: 'Иван Иванович Иванов',
			textCourseName: 'Название курса',
			textHours: 'В объеме 100 часа(ов) с домашними заданиями',
			textDate: new Date().toLocaleDateString(),
			transformFullName: {},
			transformCourseName: {},
			// transformHoursName: {},
			transformDateName: {},
			fullName: {
				screenX: 0,
				screenY: 0,
				size: 32,
				fontWeight: 700,
				uppercase: 'none',
				fullWidth: false,
				color: '#000000',
				fontStyle: 'normal',
				width: 500
			},
			courseName: {
				screenX: 0,
				screenY: 0,
				size: 24,
				fontWeight: 400,
				uppercase: 'none',
				fullWidth: false,
				color: '#000000',
				width: 230
			},
			date: {
				screenX: 0,
				screenY: 0,
				size: 24,
				fontWeight: 400,
				uppercase: 'none',
				fullWidth: false,
				color: '#000000',
				width: 170
			},
			fontWeightList: [200, 300, 400, 500, 600, 700, 800, 900]
		}
	},
	computed: {
		styleFullName() {
			if (this.fullName.fullWidth) {
				this.fullName.width = 1000
			}
			else if(this.fullName.width === 1000){
				this.fullName.width = 500
			}

			return {
				fontWeight: this.fullName.fontWeight,
				fontSize: `${this.fullName.size}px`,
				textTransform: this.fullName.uppercase,
				width: this.fullName.width + 'px',
				color: this.fullName.color,
				fontStyle: this.fullName.fontStyle
			}
		},
		styleCourseName() {
			if (this.courseName.fullWidth) {
				this.courseName.width = 1000;
			}
			else if(this.courseName.width === 1000){
				this.courseName.width = 230;
			}

			return {
				fontWeight: this.courseName.fontWeight,
				fontSize: `${this.courseName.size}px`,
				textTransform: this.courseName.uppercase,
				width: this.courseName.width + 'px',
				color: this.courseName.color,
				fontStyle: this.courseName.fontStyle
			}
		},
		styleDate() {
			if (this.date.fullWidth) {
				this.date.width = 1000
			}
			else if(this.date.width === 1000){
				this.date.width = 170
			}

			return {
				fontWeight: this.date.fontWeight,
				fontSize: `${this.date.size}px`,
				width: this.date.width + 'px',
				color: this.date.color,
				fontStyle: this.date.fontStyle
			}
		}
	},
	watch: {
		fullName: {
			handler(val){
				this.styleChangeHandler('fullName', val)
			},
			deep: true
		},
		courseName: {
			handler(val){
				this.styleChangeHandler('courseName', val)
			},
			deep: true
		},
		date: {
			handler(val){
				this.styleChangeHandler('date', val)
			},
			deep: true
		}
	},
	mounted() {
		this.$refs.pdfpdf.style.filter = 'brightness(0.3)'

		this.$refs.fullName.style.transition = '0.5s all ease'
		this.$refs.fullName.style.backgroundColor = '#fff'
		this.$refs.fullName.style.color = '#333'
		this.$refs.fullName.style.padding = '10px 20px'
		this.$refs.fullName.style.marginLeft = '20px'
		this.$refs.fullName.style.borderRadius = '10px'

		this.$refs.courseName.style.transition = '0.5s all ease'
		this.$refs.courseName.style.backgroundColor = '#fff'
		this.$refs.courseName.style.color = '#333'
		this.$refs.courseName.style.padding = '10px 20px'
		this.$refs.courseName.style.marginLeft = '20px'
		this.$refs.courseName.style.borderRadius = '10px'

		this.$refs.date.style.transition = '0.5s all ease'
		this.$refs.date.style.backgroundColor = '#fff'
		this.$refs.date.style.color = '#333'
		this.$refs.date.style.padding = '10px 20px'
		this.$refs.date.style.marginLeft = '20px'
		this.$refs.date.style.borderRadius = '10px'

		setTimeout( () => {
			this.$refs.pdfpdf.style.filter = 'brightness(1)'

			this.$refs.fullName.style.backgroundColor = 'transparent'
			this.$refs.fullName.style.color = '#000000'
			this.$refs.fullName.style.padding = 0
			this.$refs.fullName.style.marginLeft = 0
			this.$refs.fullName.style.borderRadius = 0

			this.$refs.courseName.style.backgroundColor = 'transparent'
			this.$refs.courseName.style.color = '#000000'
			this.$refs.courseName.style.padding = 0
			this.$refs.courseName.style.marginLeft = 0
			this.$refs.courseName.style.borderRadius = 0

			this.$refs.date.style.backgroundColor = 'transparent'
			this.$refs.date.style.color = '#000000'
			this.$refs.date.style.padding = 0
			this.$refs.date.style.marginLeft = 0
			this.$refs.date.style.borderRadius = 0
		}, 1000)

		setTimeout( () => {
			this.$refs.fullName.style.transition = 'none'
			this.$refs.courseName.style.transition = 'none'
			this.$refs.date.style.transition = 'none'
		}, 1500)

		if (this.styles.length > 0) {
			const getStyles = JSON.parse(this.styles)
			this.fullName = getStyles.fullName
			this.courseName = getStyles.courseName
			this.date = getStyles.date
			this.transformFullName = {transform: `translate(${this.fullName.screenX}px, ${this.fullName.screenY}px)`}
			this.transformCourseName = {transform: `translate(${this.courseName.screenX}px, ${this.courseName.screenY}px)`}
			this.transformDateName = {transform: `translate(${this.date.screenX}px, ${this.date.screenY}px)`}
		}
		let fullNameEdit = this.$refs.fullName
		let courseNameEdit = this.$refs.courseName
		let dateEdit = this.$refs.date
		this.initInteract(fullNameEdit)
		this.initInteract(courseNameEdit)
		this.initInteract(dateEdit)
		this.$emit('save-changes', this.fullName, this.courseName, this.date)
	},
	methods: {
		styleChangeHandler(name, val){
			if(!['fullName', 'courseName', 'date'].includes(name)) return
			if(val.fullWidth){
				this[name].screenX = 0
				this.$refs[name].setAttribute('data-x', 0)
				this.$refs[name].style.transform = `translate(0px, ${this[name].screenY}px)`
			}
			if(val.size > 200){
				this[name].size = 200
			}
			if(val.width > 999 && !val.fullWidth){
				this[name].width = 999
			}
		},
		saveChanges() {
			this.$emit('save-changes', this.fullName, this.courseName, this.date)
			this.$emit('update:modalCertificate', false)
		},
		selectEdit(val) {
			this.selectedEdit = val
		},
		initInteract(selector) {
			interact(selector).draggable({
				inertia: true,
				restrict: {
					restriction: 'parent',
					endOnly: true,
					elementRect: {top: 0, left: 0, bottom: 1, right: 1}
				},
				autoScroll: true,

				onmove: this.dragMoveListener,
				onend: this.onDragEnd
			})
		},
		dragMoveListener(event) {
			const target = event.target
			const name = target.getAttribute('name')
			let x = null
			let y = null

			if(['fullName', 'courseName', 'date'].includes(name)){
				x = parseFloat(target.getAttribute('data-x') || this[name].screenX) + event.dx
				y = parseFloat(target.getAttribute('data-y') || this[name].screenY) + event.dy
				if(this[name].fullWidth) x = 0
			}

			target.style.webkitTransform = target.style.transform = 'translate(' + x + 'px, ' + y + 'px)'

			target.setAttribute('data-x', x)
			target.setAttribute('data-y', y)
		},
		onDragEnd: function (event) {
			const target = event.target
			const name = target.getAttribute('name')
			if(!['fullName', 'courseName', 'date'].includes(name)) return

			this[name].screenX = parseFloat(target.getAttribute('data-x') || this[name].screenX)
			this[name].screenY = parseFloat(target.getAttribute('data-y') || this[name].screenY)
		}
	}
}
</script>

<style lang="scss">
.cestificates-constructor {
	.vue-pdf-container{
		transition: 0.5s all ease;
		&.darkened{
			filter: brightness(0.5) !important;
		}
	}
	canvas {
		width: 1000px !important;
		height: auto !important;
		border: 3px solid #333;
	}

	.form-control, .custom-select{
		height: 40px;
		&:disabled{
			background-color: #f2f2f2;
			border: none;
			color: #999;
		}
	}

	.draggable-container {
		padding: 40px 0;
		margin: 0 auto;
		max-width: 1010px;
		max-height: calc(100vh - 95px);
		width: 100%;
		overflow: auto;
	}

	.form-group {
		margin-top: 15px;
		margin-bottom: 0 !important;
		padding-bottom: 20px;
		position: relative;

		&:before {
			content: '';
			position: absolute;
			bottom: 0;
			left: 0;
			width: 100%;
			height: 1px;
			background-color: #ddd;
		}

		&:last-child {
			&:before {
				content: none;
			}
		}
	}

	.draggable-edit {
		position: relative;

		img {
			width: 100%;
			height: auto;
		}

		.draggable {
			position: absolute;
			top: 0;
			left: 0;
			z-index: 12;
			text-align: center;
			display: inline-block;
			padding: 5px;
			border-radius: 6px;
			border: 1px dashed #ddd;
			box-shadow: 0 0 5px 0 #333;
			background-color: #fff;
			&.darkened{
				background: rgba(255,255,255,0.35) !important;
			}
			&:before {
				content: attr(follow-text);
				position: absolute;
				top: -22px;
				left: 0;
				font-size: 12px;
				color: #000;
				background-color: #fff;
				padding: 3px 6px;
				border-radius: 4px;
				z-index: 2;
				white-space: nowrap;
			}

			&.no-border {
				border: none;
				padding: 0;
				border-radius: 0;
				background: transparent;
				box-shadow: none;

				&.active {
					border: 1px dashed #000;
					background-color: rgba(255, 255, 255, 0.4);
				}

				&:hover {
					border: 1px dashed #000;
					background-color: rgba(255, 255, 255, 0.7);
				}

				&:active {
					border: 1px dashed #000;
					background-color: rgba(255, 255, 255, 0.9);
				}
			}

			&:hover {
				border: 1px dashed #000;
			}

			&:active {
				border: 1px dashed #000;
				background-color: #d4d4d4;
				&:before{
					background-color: green;
					color: #fff;
				}
			}
			&.active{
				&:before{
					background-color: green;
					color: #fff;
				}
			}
		}
	}

	.settings {
		max-height: calc(100vh - 95px);
		min-height: calc(100vh - 95px);
		padding: 20px 10px;
		overflow: auto;
		border-right: 1px solid #ddd;
		position: sticky;
		top: 0;
	}

	.color-picker {
		width: 100%;
		height: 40px;
	}
	.custom-switch {
		padding-left: 0;

		input[type="checkbox"] {
			position: absolute;
			margin: 8px 0 0 16px;
		}

		input[type="checkbox"] + label {
			position: relative;
			padding: 5px 0 0 50px;
			line-height: 1;
			margin: 10px 0;
		}

		input[type="checkbox"] + label:before {
			content: "";
			position: absolute;
			display: block;
			left: 0;
			top: 0;
			width: 40px; /* x*5 */
			height: 24px; /* x*3 */
			border-radius: 16px; /* x*2 */
			background: #fff;
			border: 1px solid #d9d9d9;
			-webkit-transition: all 0.3s;
			transition: all 0.3s;
		}

		input[type="checkbox"] + label:after {
			content: "";
			position: absolute;
			display: block;
			left: 0px;
			top: 0px;
			width: 24px; /* x*3 */
			height: 24px; /* x*3 */
			border-radius: 16px; /* x*2 */
			background: #fff;
			border: 1px solid #d9d9d9;
			-webkit-transition: all 0.3s;
			transition: all 0.3s;
		}

		input[type="checkbox"] + label:hover:after {
			box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
		}

		input[type="checkbox"]:checked + label:after {
			margin-left: 16px;
		}

		input[type="checkbox"]:checked + label:before {
			background: #55D069;
		}

		&.custom-switch-small {
			input[type="checkbox"] {
				margin: 5px 0 0 10px;
			}

			input[type="checkbox"] + label {
				position: relative;
				padding: 0 0 0 32px;
				line-height: 1.3em;
			}

			input[type="checkbox"] + label:before {
				width: 25px; /* x*5 */
				height: 15px; /* x*3 */
				border-radius: 10px; /* x*2 */
			}

			input[type="checkbox"] + label:after {
				width: 15px; /* x*3 */
				height: 15px; /* x*3 */
				border-radius: 10px; /* x*2 */
			}

			input[type="checkbox"] + label:hover:after {
				box-shadow: 0 0 3px rgba(0, 0, 0, 0.3);
			}

			input[type="checkbox"]:checked + label:after {
				margin-left: 10px; /* x*2 */
			}
		}
	}
}
</style>
