<template>
	<div class="price-dropdown">
		<button
			class="price-dropdown-toggle"
		>
			<div class="price-dropdown-option">
				<img
					v-if="selectedOption"
					class="price-dropdown-img"
					:src="selectedOption.logo || '/images/price/DefaultAvatar.png'"
				>
				<p class="price-dropdown-title">
					{{ selectedOption && selectedOption ? selectedOption.id || selectedOption : placeholder }}
				</p>
			</div>
		</button>
		<div
			v-if="isOpen"
			class="price-dropdown-menu"
		>
			<div
				v-if="options"
				class="price-dropdown-menu-item"
			>
				<div
					v-for="option in options"
					:key="option.name"
					class="price-dropdown-item"
					@click="selectOption(option)"
				>
					<img
						class="price-dropdown-img"
						:src="option.logo || '/images/price/DefaultAvatar.png'"
					>
					<p
						v-if="option"
						class="price-dropdown-title"
					>
						{{ option.id }}
					</p>
				</div>
			</div>
			<div
				v-else
				class="price-dropdown-skeleton"
			>
				<b-skeleton />
			</div>
		</div>
	</div>
</template>


<script>

export default {
	name: 'DropdownPrice',
	props: {
		options: {
			type: Array,
			required: true,
		},
		placeholder: {
			type: String,
			default: 'Select an option',
		},
		selectedOption: {
			type: [String, null],
			default: null
		}
	},
	data() {
		return {
			isOpen: false,
		};
	},

	methods: {
		toggleDropdown() {
			this.isOpen = !this.isOpen;
		},
		selectOption(option) {
			this.$emit('update', option)
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

		.price-dropdown-title{
				font-size: 16px !important;
		}
}
.price-dropdown {
	position: relative;
	display: inline-block;
}
.price-dropdown-menu-item{
	display: flex;
	flex-direction: column;
	gap: 5px;
}

.price-dropdown-skeleton{
		padding: 10px;
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
	height: 200px;
	overflow-y: auto;
	scrollbar-width: none; /* Firefox */
	-ms-overflow-style: none; /* Internet Explorer 10+ */
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
		font-size: 13px;
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
