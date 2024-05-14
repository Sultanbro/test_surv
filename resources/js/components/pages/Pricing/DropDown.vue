<template>
	<div class="price-dropdown">
		<button
			class="price-dropdown-toggle"
			@click="toggleDropdown"
		>
			<div class="price-dropdown-option">
				<img
					v-if="selectedOption && selectedOption.logo"
					class="price-dropdown-img"
					:src="selectedOption.logo"
				>
				<p class="price-dropdown-title">
					{{ selectedOption && selectedOption.name ? selectedOption.name : placeholder }}
				</p>
			</div>
			<DropDownIcon />
		</button>
		<div
			v-if="isOpen"
			class="price-dropdown-menu"
		>
			<div
				v-for="option in options"
				:key="option.name"
				class="price-dropdown-item"
				@click="selectOption(option)"
			>
				<img
					class="price-dropdown-img"
					:src="option.logo"
				>
				<p class="price-dropdown-title">
					{{ option.name }}
				</p>
			</div>
		</div>
	</div>
</template>


<script>
import DropDownIcon from './DropDownIcon.vue';

export default {
	name: 'DropdownPrice',
	components: {DropDownIcon},
	props: {
		options: {
			type: Array,
			required: true,
		},
		placeholder: {
			type: String,
			default: 'Select an option',
		},
	},
	data() {
		return {
			isOpen: false,
			selectedOption: null,
		};
	},
	methods: {
		toggleDropdown() {
			this.isOpen = !this.isOpen;
		},
		selectOption(option) {
			this.selectedOption = option;
			this.isOpen = false;
		},
	},
};
</script>

<style scoped>
@media (min-width: 1600px) {

	.price-dropdown-toggle {
		max-width: 574px !important;

		padding: 8px !important;

	}
}
.price-dropdown {
	position: relative;
	display: inline-block;
}

.price-dropdown-toggle {
	max-width: 404px;
	width: 100%;
	background-color: #f8f9fa;
	border: 1px solid #CDD1DB;
		border-radius: 8px;
	padding: 6px;
	cursor: pointer;
	display: flex;
		justify-content: space-between;
		align-items: center;
}

.price-dropdown-menu {
	position: absolute;
	background-color: #fff;
	border: 1px solid #ced4da;
	padding: 0;
	margin: 0;
	z-index: 444;
	width: 100%;
}

.price-dropdown-menu li {
	padding: 5px 10px;
	cursor: pointer;
}

.price-dropdown-menu li:hover {
	background-color: #f8f9fa;
}
.price-dropdown-img{
	width: 48px;
	height: 48px;
	border-radius: 8px;
}

.price-dropdown-title{
font-weight: 600;
		font-size: 16px;
}

.price-dropdown-option{
	display: flex;
		align-items: center;
		justify-content: center;
		gap: 12px;
}

.price-dropdown-item{
	display: flex;
	align-items: center;
		gap: 16px;
}
</style>
