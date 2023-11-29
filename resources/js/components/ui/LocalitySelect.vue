<template>
	<div class="LocalitySelect">
		<input
			v-model="localValue"
			type="text"
			placeholder="Поиск городов"
			class="LocalitySelect-input form-control"
			autocomplete="off"
			@input="onInput"
			@focus="onFocus"
			@blur="onBlur"
		>

		<div
			v-if="!isFocus"
			class="LocalitySelect-fake"
			:title="outValue"
		>
			{{ outValue }}
		</div>

		<ul
			v-if="isSelect"
			class="LocalitySelect-select"
		>
			<li
				v-if="isloading"
				class="LocalitySelect-loading"
			>
				<i
					class="fa fa-spinner fa-pulse"
				/>
				Загрузка вариантов
			</li>
			<template v-if="!isloading && options.length">
				<li
					v-for="opt, index in options"
					:key="index"
					class="LocalitySelect-option"
					:title="`${opt.country}, ${opt.text}`"
					@click="onSelect(opt)"
				>
					<div class="LocalitySelect-name">
						{{ opt.country }}, {{ opt.name }}
					</div>
					<div class="LocalitySelect-text">
						{{ opt.text }}
					</div>
				</li>
			</template>
			<template v-if="!isloading && !options.length">
				<li class="LocalitySelect-noResults">
					Нет результатов
				</li>
			</template>
		</ul>
	</div>
</template>

<script>
/* global ymaps */

export default {
	name: 'LocalitySelect',
	components: {},
	props: {
		value: {
			type: String,
			default: '',
		},
		delay: {
			type: Number,
			default: 500
		},
	},
	data(){
		return {
			localValue: this.value ? this.value.split(':').reverse()[0].trim() : '',
			outValue: this.value || '',
			options: [],

			lastValue: '',
			timeout: null,

			isloading: false,
			isSelect: false,
			isFocus: false,
		}
	},
	watch: {
		value(){
			this.localValue = this.value ? this.value.split(':').reverse()[0].trim() : ''
			this.outValue = this.value || ''
		}
	},
	methods: {
		search(value){
			if(!value) return (this.isSelect = false)
			if(this.lastValue === value) return (this.isSelect = true)
			this.lastValue = value

			this.isSelect = true
			this.isloading = true
			const options = []
			ymaps.geocode(value, {}).then(({geoObjects}) => {
				geoObjects.each(geoObj => {
					const meta = geoObj.properties.get('metaDataProperty.GeocoderMetaData')
					if(meta.kind !== 'locality') return
					const country = geoObj.getCountry()
					const name = geoObj.properties.get('name')
					const text = geoObj.properties.get('text')
					const coords = geoObj.geometry.getCoordinates()
					options.push({
						name,
						country,
						text,
						coords,
					})
				})
				this.options = options
				this.isloading = false
			})
		},
		onInput(event){
			if(this.timeout) clearTimeout(this.timeout)
			const value = event.target.value
			this.timeout = setTimeout(() => {
				this.search(value)
			}, this.delay)
			return
		},
		onFocus(){
			this.isFocus = true
			if(this.options.length) this.isSelect = true
		},
		onBlur(){
			setTimeout(() => {
				this.isFocus = false
				this.isSelect = false
			}, 300)
		},
		onSelect(opt){
			this.localValue = opt.name
			this.outValue = `Страна: ${opt.country}, Город: ${opt.name}`
			this.$emit('change', opt)
		}
	}
}
</script>

<style lang="scss">
.LocalitySelect{
	position: relative;
	// &-input{}
	&-select{
		max-width: 100%;
		max-height: 450px;
		padding: 8px 0;

		position: absolute;
		z-index: 1;
		top: 100%;
		left: 0;

		overflow: auto;
		background: #fff;
		box-shadow: 0px 15px 60px -40px rgba(45, 50, 90, 0.20), 0px 0px 3px 0px rgba(0, 0, 0, 0.15);
		border-radius: 8px;
	}
	&-option{
		padding: 2px 10px;
		cursor: pointer;
		&:hover{
			background-color: #F5F8FC;
		}
	}
	&-loading{
		padding: 2px 10px;
		background-color: #F5F8FC;
	}
	&-noResults{
		padding: 2px 10px;
		color: #777;
		background-color: #F5F8FC;
	}
	// &-name{}
	&-text{
		font-size: 0.8em;
		color: #777;
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
	}
	&-fake{
		max-width: calc(100% - 31px);
		padding: 2px;

		position: absolute;
		z-index: 2;
		top: 9px;
		left: 21px;

		font-size: 14px;
		color: 495057;

		background-color: #F7FAFC;
		pointer-events: none;

		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
	}
}
</style>
