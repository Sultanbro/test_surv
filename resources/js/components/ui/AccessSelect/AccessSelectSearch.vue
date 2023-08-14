<template>
	<div class="AccessSelectSearch">
		<img
			class="AccessSelect-icon"
			src="/icon/news/filter/search.svg"
		>
		<input
			type="text"
			:value="value"
			class="AccessSelectSearch-input"
			placeholder="Быстрый поиск"
			@input="$emit('input', $event.target.value)"
		>
		<div
			v-if="value"
			class="AccessSelectSearch-btn ChatIcon-parent"
			@click="$emit('input', '')"
		>
			<ChatIconSearchClose />
		</div>
	</div>
</template>

<script>
import { ChatIconSearchClose } from '@icons'
export default {
	name: 'AccessSelectSearch',
	components: {
		ChatIconSearchClose,
	},
	props: {
		value: {
			type: String,
			default: '',
		}
	}
}
</script>

<style>
.AccessSelectSearch {
	display: flex;
	flex-flow: row nowrap;
	align-items: center;
	gap: 16px;

	padding-left: 21px;
	margin-bottom: 10px;

	background: #F7FAFC;
	border-radius: 8px;
}
.AccessSelectSearch-input {
	width: 80%;
	padding: 16px 10px;
	border: 0;
	font-weight: 500;
	font-size: 14px;
	line-height: 14px;
	color: #4A5568;
	background-color: #F7FAFC;
}
.AccessSelectSearch-input:focus {
	outline: none;
}
.AccessSelectSearch-input::-moz-placeholder {
	font-family: "Inter", sans-serif;
	font-style: normal;
	font-weight: 400;
	font-size: 16px;
	line-height: 20px;
	letter-spacing: -0.01em;
	color: #A6B7D4;
}
.AccessSelectSearch-input::placeholder {
	font-family: "Inter", sans-serif;
	font-style: normal;
	font-weight: 400;
	font-size: 16px;
	line-height: 20px;
	letter-spacing: -0.01em;
	color: #A6B7D4;
}
.AccessSelectSearch-btn{
	padding: 0.5rem;
}
</style>
