<template>
	<BContainer
		class="mt-3"
		fluid
	>
		<BFormGroup>
			<div
				v-if="options"
				class="dropdown"
			>
				<!-- Dropdown Input -->
				<input
					v-model="searchFilter"
					class="dropdown-input"
					:name="name"
					:disabled="disabled"
					:placeholder="placeholder"
					@focus="showOptions()"
					@blur="exit()"
					@keyup="keyMonitor"
				>

				<!-- Dropdown Menu -->
				<div
					v-show="optionsShown"
					class="dropdown-content"
				>
					<div
						v-for="(option, index) in filteredOptions"
						:key="index"
						class="dropdown-item"
						@mousedown="selectOption(option)"
					>
						{{ option.name || option.id || "-" }}
					</div>
				</div>
			</div>
		</BFormGroup>
	</BContainer>
</template>

<script>
export default {
	name: 'FormUsers',
	props: {
		name: {
			type: String,
			required: false,
			default: 'dropdown',
			note: 'Input name',
		},
		options: {
			type: Array,
			required: true,
			default: () => [],
			note: 'Options of dropdown. An array of options with id and name',
		},
		placeholder: {
			type: String,
			required: false,
			default: 'Please select an option',
			note: 'Placeholder of dropdown',
		},
		disabled: {
			type: Boolean,
			required: false,
			default: false,
			note: 'Disable the dropdown',
		},
		maxItem: {
			type: Number,
			required: false,
			default: 6,
			note: 'Max items showing',
		},
	},
	data() {
		return {
			selected: {},
			optionsShown: false,
			searchFilter: '',
		}
	},
	computed: {
		filteredOptions() {
			const filtered = []
			const regOption = new RegExp(this.searchFilter, 'ig')
			for (const option of this.options) {
				if (this.searchFilter.length < 1 || option.name.match(regOption)) {
					if (filtered.length < this.maxItem) filtered.push(option)
				}
			}
			return filtered
		},
	},
	watch: {
		searchFilter() {
			if (this.filteredOptions.length) {
				this.selected = this.filteredOptions[0]
			}
			else {
				this.selected = {}
			}
			this.$emit('filter', this.searchFilter)
		},
	},
	created() {
		this.$emit('selected', this.selected)
	},
	methods: {
		selectOption(option) {
			this.selected = option
			this.optionsShown = false
			this.searchFilter = this.selected.name
			this.$emit('selected', this.selected)
		},
		showOptions() {
			if(this.disabled) return
			this.searchFilter = ''
			this.optionsShown = true
		},
		exit() {
			if (this.selected.id) {
				this.searchFilter = this.selected.name
			}
			else {
				this.selected = {}
				this.searchFilter = ''
			}
			this.$emit('selected', this.selected)
			this.optionsShown = false
		},
		// Selecting when pressing Enter
		keyMonitor(event) {
			if (event.key === 'Enter' && this.filteredOptions[0])
				this.selectOption(this.filteredOptions[0])
		},
	},
	template: 'Dropdown',
};
</script>

<style lang="scss" scoped>
.dropdown {
	position: relative;
	display: block;
	.dropdown-input {
		background: #fff;
		cursor: pointer;
		border: 1px solid #e7ecf5;
		border-radius: 3px;
		color: #333;
		display: block;
		font-size: 0.8em;
		padding: 6px;
		min-width: 250px;
		max-width: 250px;
		&:hover {
			background: #f8f8fa;
		}
	}
	.dropdown-content {
		position: absolute;
		background-color: #fff;
		min-width: 248px;
		max-width: 248px;
		max-height: 248px;
		border: 1px solid #e7ecf5;
		box-shadow: 0px -8px 34px 0px rgba(0, 0, 0, 0.05);
		overflow: auto;
		z-index: 1;
	}
	.dropdown-item {
		color: black;
		font-size: 0.7em;
		line-height: 1em;
		padding: 8px;
		text-decoration: none;
		display: block;
		cursor: pointer;
		&:hover {
			background-color: #e7ecf5;
		}
	}
	.dropdown:hover .dropdowncontent {
		display: block;
	}
}
</style>
