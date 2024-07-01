<template>
	<div class="input-with-icon">
		<div class="input-course-group">
			<div v-if="remainingChars <= 0">
				<AcceptedIcon />
			</div>
			<input
				v-if="small"
				:value="value"
				class="input-with"
				:placeholder="computedPlaceholder"
				:class="{
					'input-height-big': big,
					'input-height-small': small,
					'input-height-medium': medium
				}"
				@input="handleInput"
			>
			<textarea
				v-else
				:value="value"
				class="input-with"
				:placeholder="computedPlaceholder"
				:class="{
					'input-height-big': big,
					'input-height-small': small,
					'input-height-medium': medium
				}"
				@input="handleInput"
			/>
		</div>

		<div
			v-if="showCharCount"
			class="char-count"
		>
			{{ remainingChars }}
		</div>
	</div>
</template>

<script>
import AcceptedIcon from '../../../assets/icons/AcceptedIcon.vue';

export default {
	name:'InputCourse',
	components: {AcceptedIcon},
	props: {
		placeholderText: {
			type: String,
			default: 'Введите текст',
		},
		minChars: {
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
		},
		value: {
			type: String,
			default: ''
		}
	},
	data() {
		return {
		};
	},
	computed: {
		computedPlaceholder() {
			return `${this.placeholderText}`;
		},
		remainingChars() {
			return this.minChars - this.value.length;
		},
		showCharCount() {
			return this.remainingChars > 0;
		}
	},
	methods: {
		handleInput(event) {
			let newValue = event.target.value;
			this.$emit('input', newValue);
		},
	},
};
</script>

<style scoped>

.input-course-group{
	display: flex;
		gap: 5px;
}

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
