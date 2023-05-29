<template>
	<div
		v-if="item"
		v-show="show"
		class="AccessSelectListItem"
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
			class="AccessSelectListItem-avatar"
		>
		<template v-else>
			<i
				v-if="type === 2"
				class="fa fa-users"
			/>
			<i
				v-if="type === 3"
				class="fa fa-briefcase"
			/>
		</template>
		<div class="AccessSelectListItem-info">
			<div
				v-if="position"
				class="AccessSelectListItem-sub"
			>
				{{ item.position }}
			</div>
			<div class="AccessSelectListItem-name">
				{{ item.name }}
			</div>
		</div>
		<label class="AccessSelectListItem-checkbox">
			<input
				type="checkbox"
				:checked="checked"
				class="AccessSelectListItem-input"
				@click="$emit('change', {
					id: item.id,
					name: item.name,
					type,
					image: item.avatar || null
				})"
			>
			<span class="AccessSelectListItem-checkmark" />
		</label>
	</div>
</template>

<script>
export default {
	name: 'AccessSelectListItem',
	components: {},
	props: {
		item: {
			type: Object,
			default: null
		},
		type: {
			type: Number,
			default: 2
		},
		position: {
			type: Boolean,
			default: false
		},
		avatar: {
			type: Boolean,
			default: false
		},
		search: {
			type: String,
			default: ''
		},
		checked: {
			type: Boolean,
			default: false
		}
	},
	computed: {
		show(){
			if(!this.search) return true
			const inName = this.item?.name?.toLowerCase().includes(this.search)
			const inPosition = this.position && this.item?.position?.toLowerCase().includes(this.search)
			return inName || inPosition
		}
	}
}
</script>

<style lang="scss">
.AccessSelectListItem {
	display: flex;
	flex-direction: row;
	align-items: center;
	gap: 20px;

	width: 100%;
	padding: 3px 0;
	&:hover {
		cursor: pointer;
		background: rgba(21, 106, 232, 0.031372549);
		box-shadow: 0px 0px 1px 2px rgba(21, 106, 232, 0.1);
	}

	&-avatar {
		width: 40px;
		height: 40px;
		border-radius: 50%;
	}

	&-info {
		display: flex;
		flex-direction: column;
		flex: 1;
		overflow: hidden;
	}

	&-sub {
		font-family: "Inter", sans-serif;
		font-style: normal;
		font-weight: 500;
		font-size: 9px;
		line-height: 15px;
		color: #8DA0C1;
	}

	&-name {
		font-family: "Inter", sans-serif;
		font-style: normal;
		font-weight: 600;
		font-size: 15px;
		line-height: 20px;
		letter-spacing: -0.02em;
		color: #0A1323;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	&-checkbox {
		width: 30px;
		height: 30px;
		cursor: pointer;
		position: relative;
	}

	&-input {
		height: 0;
		width: 0;
		position: absolute;
		opacity: 0;
		cursor: pointer;

		&:checked ~ .AccessSelectListItem-checkmark {
			transition: all 0.3s ease;
			background-color: #3781EF;
		}
		&:checked ~ .AccessSelectListItem-checkmark:after {
			content: url("/icon/news/access-modal/checked.svg");
			display: block;
			position: absolute;
			left: 50%;
			top: 50%;
			transform: translate(-50%, -50%);
			transition: all 0.3s ease;
		}
	}

	&-checkmark {
		height: 30px;
		width: 30px;
		border: 0.5px solid #EFF5FC;
		border-radius: 50%;

		position: absolute;
		z-index: 11;
		top: 10%;

		background: rgba(238, 246, 255, 0.5);

		&:after {
			content: "";
			display: none;
			position: absolute;
		}
	}
}
</style>
