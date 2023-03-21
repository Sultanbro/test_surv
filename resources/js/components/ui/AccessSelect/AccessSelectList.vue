<template>
	<div class="AccessSelectList">
		<div
			v-for="item in items"
			:key="item.id"
			v-show="item.name ? item.name.toLowerCase().includes(lowerSearch) : false"
			class="AccessSelectList-item"
			@click="$emit('change', {
				id: item.id,
				name: item.name,
				type,
				image: item.avatar || null
			})"
		>
			<img
				v-if="avatar"
				:src="item.avatar || null"
				class="AccessSelectList-avatar"
			>
			<div class="AccessSelectList-info">
				<div
					v-if="position"
					class="AccessSelectList-sub"
				>
					{{ item.position }}
				</div>
				<div class="AccessSelectList-name">
					{{ item.name }}
				</div>
			</div>
			<label class="AccessSelectList-checkbox">
				<input
					type="checkbox"
					:checked="checked(item, type) ? 'checked' : ''"
					class="AccessSelectList-input"
					@click="$emit('change', {
						id: item.id,
						name: item.name,
						type,
						image: item.avatar || null
					})"
				>
				<span class="AccessSelectList-checkmark" />
			</label>
		</div>
	</div>
</template>

<script>
export default {
	name: 'AccessSelectList',
	components: {},
	props: {
		search: {
			type: String,
			default: ''
		},
		selected: {
			type: Array,
			default: () => []
		},
		items: {
			type: Array,
			default: () => []
		},
		avatar: {
			type: Boolean,
			default: false
		},
		position: {
			type: Boolean,
			default: false
		},
		type: {
			type: Number,
			default: 2
		},
	},
	computed: {
		lowerSearch(){
			return this.search.toLowerCase()
		}
	},
	methods: {
		checked(item, type) {
			return this.selected.some(el => {
				return el.id === item.id && el.type === type
			});
		},
	}
}
</script>

<style>
.AccessSelectList {
	display: flex;
	flex-direction: column;
	gap: 15px;
	max-height: 300px;
	overflow-y: scroll;
}

.AccessSelectList-item {
	display: flex;
	flex-direction: row;
	align-items: center;
	gap: 20px;
	padding: 5px 30px 5px 0;
}
.AccessSelectList-item:hover {
	cursor: pointer;
	background: rgba(21, 106, 232, 0.031372549);
	box-shadow: 0px 0px 1px 2px rgba(21, 106, 232, 0.1);
}
.AccessSelectList-avatar {
	width: 40px;
	height: 40px;
	border-radius: 50%;
	margin-left: 20px;
}
.AccessSelectList-info {
	display: flex;
	flex-direction: column;
	flex-grow: 1;
}
.AccessSelectList-sub {
	font-family: "Inter", sans-serif;
	font-style: normal;
	font-weight: 500;
	font-size: 9px;
	line-height: 15px;
	color: #8DA0C1;
}
.AccessSelectList-name {
	font-family: "Inter", sans-serif;
	font-style: normal;
	font-weight: 600;
	font-size: 15px;
	line-height: 20px;
	letter-spacing: -0.02em;
	color: #0A1323;
}

.AccessSelectList-checkbox {
	width: 30px;
	height: 30px;
	cursor: pointer;
	position: relative;
}
.AccessSelectList-input {
	height: 0;
	width: 0;
	position: absolute;
	opacity: 0;
	cursor: pointer;
}
.AccessSelectList-checkmark {
	height: 30px;
	width: 30px;
	border: 0.5px solid #EFF5FC;
	border-radius: 50%;

	position: absolute;
	z-index: 11;
	top: 10%;

	background: rgba(238, 246, 255, 0.5);
}
.AccessSelectList-checkmark:after {
	content: "";
	display: none;
	position: absolute;
}
.AccessSelectList-input:checked ~ .AccessSelectList-checkmark {
	transition: all 0.3s ease;
	background-color: #3781EF;
}
.AccessSelectList-input:checked ~ .AccessSelectList-checkmark:after {
	content: url("/icon/news/access-modal/checked.svg");
	display: block;
	position: absolute;
	left: 50%;
	top: 50%;
	transform: translate(-50%, -50%);
	transition: all 0.3s ease;
}
</style>
