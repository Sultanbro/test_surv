<template>
	<div
		v-if="file"
		class="ConversationSearchFile"
	>
		<div class="ConversationSearchFile-preview">
			<img
				v-if="file.thumbnail_path"
				:src="file.thumbnail_path"
				alt=""
				class="ConversationSearchFile-image"
			>
		</div>
		<div class="ConversationSearchFile-info">
			<div class="ConversationSearchFile-name">
				{{ file.name }}
			</div>
			<!-- <div class="ConversationSearchFile-size"></div> -->
			<div class="ConversationSearchFile-date">
				{{ date }}
			</div>
		</div>
		<!-- <div class="ConversationSearchFile-actions"></div> -->
	</div>
</template>

<script>
export default {
	name: 'ConversationSearchFile',
	components: {},
	props: {
		file: {
			type: Object,
			required: true
		}
	},
	computed: {
		date(){
			if(!this.file) return ''
			return this.$moment(this.file.created_at).format('DD MMMM YYYY в HH:mm')
		}
	}
}
</script>

<style lang="scss">
.ConversationSearchFile{
	display: flex;
	flex-flow: row nowrap;
	align-items: flex-start;
	justify-content: flex-start;
	gap: 20px;

	padding: 10px;

	&:hover{
		background-color: #F9FBFF;
	}

	&-preview{
		flex: 0 0 75px;
		width: 75px;
		height: 75px;
		border-radius: 3px;
		background-color: #E9F0FB;
	}
	&-image{
		width: 75px;
		height: 75px;
		border-radius: 3px;
		object-fit: cover;
	}
	&-info{
		flex: 1;
		overflow: hidden;
	}
	&-name,
	&-date{
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}

	&-name{
		font-weight: 600;
		font-size: 13px;
		line-height: 16px;
		letter-spacing: -0.02em;

		color: #3361FF;
	}
	&-size{
		margin-top: 4px;

		font-weight: 400;
		font-size: 11px;
		line-height: 16px;
		letter-spacing: -0.02em;

		color: #A6B7D4;
	}
	&-date{
		margin-top: 8px;

		font-weight: 500;
		font-size: 12px;
		line-height: 16px;
		letter-spacing: -0.03em;
		color: #A6B7D4;
	}
	&-actions{
		align-self: center;
	}
}
</style>
