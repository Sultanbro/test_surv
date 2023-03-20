<template>
	<div class="messenger__message-reactions">
		<div
			v-for="(reaction, index) in reactions"
			:key="index"
			class="messenger__message-reaction"
			@click="$emit('reaction-click', reaction.type)"
		>
			<div class="messenger__message-reaction-icon">
				<span v-if="reaction.type === 1">&#128077;</span>
				<span v-else-if="reaction.type === 2">&#128078;</span>
				<span v-else-if="reaction.type === 3">&#10004;</span>
				<span v-else-if="reaction.type === 4">&#10006;</span>
				<span v-else-if="reaction.type === 5">&#10067;</span>
			</div>
			<div
				v-if="reaction.count > 1"
				class="messenger__message-reaction-count"
			>
				<span>{{ reaction.count }}</span>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'MyComp',
	components: {},
	props: {
		reactions: {
			type: Array,
			default: () => []
		}
	}
}
</script>

<style>
.messenger__message-reactions {
	display: inline-flex;
	flex-direction: row;
	align-items: center;
	justify-content: flex-end;
}

.messenger__message-reactions .messenger__message-reaction {
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: 40%;
	margin-left: 5px;
	font-size: 12px;
	cursor: pointer;
	padding: 1px 3px;
	/* background-color: #f5f5f5; */
}

.messenger__message-reactions .messenger__message-reaction:hover {
	background-color: #e0e0e0;
}

.messenger__message-reactions .messenger__message-reaction .messenger__message-reaction-count {
	margin-left: 5px;
}

.messenger__message-reactions .messenger__message-reaction .messenger__message-reaction-count:hover {
	background-color: transparent;
}
</style>
