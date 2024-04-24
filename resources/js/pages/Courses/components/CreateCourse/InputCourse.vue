<template>
	<div class="input-with-icon">
		<input
			v-if="small"
			v-model="inputValue"
			class="input-with"
			:placeholder="computedPlaceholder"
			:class="{
				'input-height-big': big,
				'input-height-small': small,
				'input-height-medium': medium
			}"
			@input="updateCount"
		>
		<textarea
			v-else
			v-model="inputValue"
			class="input-with"
			:placeholder="computedPlaceholder"
			:class="{
				'input-height-big': big,
				'input-height-small': small,
				'input-height-medium': medium
			}"
			@input="updateCount"
		/>
		<div class="char-count">
			{{ remainingChars }}
		</div>
	</div>
</template>

<script>
export default {
	name:'InputCourse',
	props: {
		placeholderText: {
			type: String,
			default: 'Введите текст',
		},
		maxChars: {
			type: Number,
			default: 0,
		},
		big: {
			type: Boolean
		},
		small: {
			type: Boolean
		},
		medium: {
			type: Boolean
		}
	},
	data() {
		return {
			inputValue: '',
		};
	},
	computed: {
		computedPlaceholder() {
			return `${this.placeholderText}`;
		},
		remainingChars() {
			return this.maxChars - this.inputValue.length;
		},
	},
	methods: {
		updateCount() {
			if (this.inputValue.length > this.maxChars) {
				this.inputValue = this.inputValue.slice(0, this.maxChars);
			}
		},
	},
};
</script>

<style scoped>
.input-with-icon {
	position: relative;

}
.input-with{
	background: #F7FAFC;
	border-radius: 8px;
	padding: 10px 20px;
	font-size: 14px;

}

.input-height-medium{
	width: 100%;
		height: 116px;
}

.input-height-big{
	width: 100%;
	height: 176px;
}


.input-height-small{
	width: 100%;
	height: 40px;
}

.input-with::placeholder{
		color: black;
}

.char-count {
	position: absolute;
	bottom: 5px;
	z-index: 100;
	right: 5px;
	font-size: 12px;
	color: #888;
}
</style>
