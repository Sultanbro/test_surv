<template>
	<div class="taking-dropdown">
		<button
			class="taking-dropdown-toggle"
			@click="toggleDropdown"
		>
			<div
				v-if="selectedOption && selectedOption.name"
				class="material-modal-select-icon"
			>
				<MaterialModalSelectIcon />
			</div>
			<p class="taking-dropdown-title">
				{{ selectedOption && selectedOption.name ? selectedOption.name : placeholder }}
			</p>
			<DropDownIcon />
		</button>
		<ul
			v-show="isOpen"
			class="taking-dropdown-menu"
		>
			<li
				v-for="option in options"
				:key="option"
				class="taking-dropdown-item"
				@click="selectOption(option)"
			>
				<p class="taking-dropdown-title">
					{{ option.name }}
				</p>
				<div class="material-modal-select-button">
					<div
						v-if="isActiveSelect(option)"
						class="material-modal-select-icon"
					>
						<MaterialModalSelectIcon />
					</div>
					<div
						v-else
						class="material-modal-select-default"
					/>
				</div>
			</li>
		</ul>
	</div>
</template>

<script>
import DropDownIcon from '../../../assets/icons/DropDownIcon.vue';
import MaterialModalSelectIcon from '../../../assets/icons/MaterialModalSelectIcon.vue';

export default {
	name: 'TakingDropDown',
	components: { MaterialModalSelectIcon, DropDownIcon },
	props: {
		placeholder: {
			type: String,
			default: 'Тип курса',
		},
		options: {
			type: Array,
			required: true,
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
		isActiveSelect(option) {
			return this.selectedOption === option;
		},
	},
};
</script>

<style lang="scss" scoped>
.taking-{
  &dropdown {
	position: relative;
	display: inline-block;
  }

  &dropdown-toggle {
	width: 474px;
	background-color: #f8f9fa;
	border: 1px solid #CDD1DB;
	border-radius: 8px;
	padding: 8px;
	cursor: pointer;
	display: flex;
	justify-content: space-between;
	align-items: center;
	height: 50px;
  }

  &dropdown-menu {
	position: absolute;
	background-color: #fff;
	border: 1px solid #ced4da;
	padding: 0;
	margin: 0;
	z-index: 444;
	width: 100%;
  }

  &dropdown-menu li {
	padding: 5px 10px;
	cursor: pointer;
  }
	&dropdown-menu li:hover {
	background-color: #f8f9fa;
  }

  &dropdown-menu li:hover {
	background-color: #f1f1f1;
  }

  &dropdown-title{
	font-weight: 600;
	font-size: 16px;
  }

  &dropdown-item{
	display: flex;
	align-items: center;
	gap: 16px;
	justify-content: space-between;
	height: 60px;
  }
}
.material-modal-select-default{
  width: 30px;
  height: 30px;
  background-color: #EEF6FF80;
  border-radius: 50%;
  margin-right: 10px;
}
.material-modal-select-icon{
  margin-top: 10px;
  min-width: 30px;
  min-height: 30px;
}

</style>
