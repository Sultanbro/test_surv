<template>
	<span
		class="JobtronAvatar"
		:style="style"
	>
		<img
			v-if="image && !imageError"
			:src="image"
			:alt="/* shotrTitle */ ''"
			:style="sizeStyle"
			@error="imageError = true"
			class="JobtronAvatar-img"
		>
		<span
			v-else
			class="JobtronAvatar-text"
		>
			{{ shotrTitle }}
		</span>
		<span
			v-if="tooltip"
			class="JobtronAvatar-tooltip"
		>{{ title }}</span>
		<span
			v-if="status"
			class="JobtronAvatar-status"
			:class="statusClass"
		/>
	</span>
</template>

<script>
import { stringToColor, shade } from '@/composables/stringToColor'
export default {
	name: 'JobtronAvatar',
	components: {},
	props: {
		image: {
			type: String,
			required: false,
			default: null
		},
		title: {
			type: String,
			required: true,
		},
		size: {
			type: Number,
			default: 50
		},
		status: {
			type: String,
			default: ''
		},
		tooltip: {
			type: Boolean,
			default: false
		},
	},
	data() {
		return {
			imageError: false,
			// size: 50,
			// status: 'online',
		}
	},
	computed: {
		shotrTitle(){
			return this.title.split(' ').reduce((short, item) => {
				if(item[0]) short += item[0]
				return short
			}, '').slice(0, 2)
		},
		background(){
			return stringToColor(this.title)
		},
		sizeStyle(){
			return [
				`width: ${this.size}px`,
				`height: ${this.size}px`,
			].join(';')
		},
		style(){
			return [
				this.sizeStyle,
				`background: linear-gradient(220.73deg, #${this.background} 12.66%, #${shade(this.background, -20)} 83.08%) `,
			].join(';')
		},
		statusClass(){
			if(!this.status) return ''
			return `JobtronAvatar-status_${this.status}`
		}
	}
}
</script>

<style lang="scss">
.JobtronAvatar{
	container: avatar / size;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	border-radius: 50%;
	position: relative;
	&-text,
	&-img{
		display: flex;
		align-items: center;
		justify-content: center;

		border-radius: 50%;
		color: #fff;
		font-size: 16px;
	}
	&-text{
		color: #fff;
		font-size: 16px;
		font-weight: 700;
	}
	&-tooltip{
		width: 120px;
		padding: 5px;

		background-color: black;
		color: #fff;

		text-align: center;

		border-radius: 6px;
		visibility: hidden;

		position: absolute;
		z-index: 1;
	}
	&-status{
		width: 10px;
		height: 10px;
		border: 2px solid #fff;
		border-radius: 50em;

		position: absolute;
		top: 77%;
		left: 77%;

		&_online{
			background-color: #27AE60;
		}
		&_offline{
			background-color: #8BABD8;
		}
	}
	&:hover{
		.JobtronAvatar-tooltip{
			visibility: visible;
		}
	}
}

@container (min-width: 50px) {
  .JobtronAvatar{
		&-text {
			font-size: 22px;
		}
	}
}
@container (min-width: 100px) {
  .JobtronAvatar{
		&-text {
			font-size: 44px;
		}
		&-status{
			width: 16px;
			height: 16px;
		}
	}
}
</style>
