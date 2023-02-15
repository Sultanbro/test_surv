<template>
	<div class="messenger__meta">
		<div
			v-if="countReaders > 1 && own"
			class="messenger__meta-views"
		>
			{{ countReaders }}
		</div>
		<div class="messenger__meta-date">
			{{ time }}
		</div>
		<div
			v-if="own"
			class="messenger__meta-ckeck"
			:class="{'messenger__meta-ckeck_checked': countReaders}"
		/>
		<div class="messenger__meta-inner">
			<div
				v-if="countReaders > 1 && own"
				class="messenger__meta-views messenger__meta-actual"
				:title="readersString"
			>
				{{ countReaders }}
			</div>
			<div class="messenger__meta-date messenger__meta-actual">
				{{ time }}
			</div>
			<div
				v-if="own"
				class="messenger__meta-ckeck messenger__meta-actual"
				:class="{'messenger__meta-ckeck_checked': countReaders}"
			/>
		</div>
	</div>
</template>

<script>

export default {
	name: 'ConversationMessageMeta',
	components: {},
	props: {
		readers: {
			type: Array,
			default: () => [],
		},
		time: {
			type: String,
			default: '',
		},
		own: {
			type: Boolean,
			default: false,
		},
	},
	computed: {
		countReaders(){
			return this.readers.length
		},
		readersString(){
			return this.readers.reduce((list, reader) => {
				list.push(`${ reader.name } ${ reader.last_name }`)
				return list
			}, []).join(', ')
		}
	},
}
</script>

<style lang="scss">
.messenger__meta{
	margin-left: 1rem;
	float: right;
	white-space: nowrap;
	user-select: none;
	font-size: 1rem;
	&-views,
	&-date,
	&-ckeck{
		display: inline-block;
		visibility: hidden;
	}
	&-ckeck{
		font-size: 1.5rem;
		&::before{
			content: '✓';
		}
	}
	&-views{
		&::before{
			content: '👁';
		}
	}
	&-ckeck_checked{
		&::after{
			content: '✓';
			margin-left: -0.6em;
		}
	}
	&-inner{
		display: flex;
		flex-flow: row nowrap;
		gap: .5rem;
		align-items: center;
		margin-bottom: .2rem;
		margin-right: .8rem;
		position: absolute;
		right: 0;
		bottom: 0;
	}
	&-actual{
		visibility: visible;
	}
}
</style>
