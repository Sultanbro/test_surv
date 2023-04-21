<template>
	<div class="messenger__chat-item-container messenger__text-ellipsis">
		<JobtronAvatar
			:image="item.second_user ? `/users_img/${item.second_user.img_url}` : item.image"
			:title="item.title"
			:size="fullscreen ? 50 : 40"
			:status="item.sOnline ? 'online' : ''"
		/>

		<!-- status on sidebar -->
		<div
			v-if="!fullscreen"
			class="messenger__chat-name-label"
		>
			<div
				v-if="item.muted"
				class="messenger__chat-muted hidden"
			/>
			<div
				v-if="item.unread_messages_count"
				class="messenger__chat-unread"
			>
				{{ item.unread_messages_count }}
			</div>
		</div>

		<!-- Title -->
		<div
			v-if="fullscreen"
			class="messenger__name-container messenger__text-ellipsis"
		>
			<div class="messenger__title_container">
				<div
					class="ContactItem-name messenger__text-ellipsis"
					v-text="item.title"
				/>
				<div class="ContactItem-status">
					<template v-if="item.last_message && item.last_message.sender_id === currentUserId">
						<ChatIconStatusSended v-if="item.unread_messages_count > 0" />
						<ChatIconStatusReaded v-else />
					</template>
					{{ time }}
				</div>
			</div>
			<div class="messenger__chat-info">
				<div
					class="messenger__last-message"
					v-text="item.last_message ? item.last_message.body : ''"
				/>
				<div class="messenger__chat-indicators ml-a">
					<div
						v-if="item.pinned"
						class="messenger__chat-pinned"
					>
						<ChatIconPinChat />
					</div>
					<div
						v-if="item.muted"
						class="messenger__chat-muted"
					>
						<ChatIconMuteChat />
					</div>
					<div
						v-if="item.unread_messages_count"
						class="messenger__chat-unread"
					>
						{{ item.unread_messages_count }}
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import {mapActions} from 'vuex';
import JobtronAvatar from '@ui/Avatar'
import {
	ChatIconStatusReaded,
	ChatIconStatusSended,
	ChatIconPinChat,
	ChatIconMuteChat,
} from '@icons'

export default {
	name: 'ContactItem',
	components: {
		JobtronAvatar,
		ChatIconStatusReaded,
		ChatIconStatusSended,
		ChatIconPinChat,
		ChatIconMuteChat,
	},
	props: {
		item: {
			type: Object,
			required: true
		},
		fullscreen: {
			type: Boolean,
			default: false
		},
		currentUserId: {
			type: Number,
			default: 0
		},
	},
	data() {
		return {
			imageError: false
		}
	},
	computed: {
		time(){
			if(!this.item.last_message) return ''
			const $time = this.$moment(this.item.last_message.updated_at)
			const $now = this.$moment()
			const diff = $now.diff($time, 'days')
			if(diff < 1) return $time.format('HH:mm')
			if(diff < 7) return $time.format('ddd')
			return $time.format('DD.MM.YYYY')
		}
	},
	methods: {
		...mapActions(['toggleInfoPanel']),
	}
}
</script>

<style scoped>

.ContactItem-name{
	font-weight: 500;
	font-size: 14px;
	line-height: 16px;
	letter-spacing: -0.01em;
	color: #152136;
}

.ContactItem-status{
	font-weight: 400;
	font-size: 11px;
	line-height: 14px;

	letter-spacing: -0.02em;

	color: #A6B7D4;
}

.messenger__chat-item-container {
  display: flex;
  flex: 1;
  align-items: flex-start;
	gap: 15px;

  width: 100%;
}

.messenger__avatar-list {
  background-image: url("data:image/svg+xml,%3C%3Fxml version='1.0' encoding='iso-8859-1'%3F%3E%3C!-- Generator: Adobe Illustrator 18.0.0  SVG Export Plug-In . SVG Version: 6.00 Build 0) --%3E%3C!DOCTYPE svg PUBLIC '-//W3C//DTD SVG 1.1//EN' 'http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd'%3E%3Csvg version='1.1' id='Capa_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 453.818 453.818' style='enable-background:new 0 0 453.818 453.818%3B' xml:space='preserve'%3E%3Cpath d='M438.818 96.071H15c-8.284 0-15 6.716-15 15v231.676c0 8.284 6.716 15 15 15h423.818c8.284 0 15-6.716 15-15V111.071C453.818 102.787 447.103 96.071 438.818 96.071z M30 133.089l140.736 93.819L30 320.733V133.089z M55.533 327.747l133.231-88.821l32.598 21.731c1.68 1.12 3.613 1.679 5.547 1.679s3.867-0.56 5.547-1.679l32.601-21.733l133.233 88.822H55.533z M226.909 240.319L55.53 126.071h342.759L226.909 240.319z M283.085 226.907l140.734-93.818v187.64L283.085 226.907z'/%3E%3C/svg%3E");
  background-size: cover;
  background-position: top center;
  background-repeat: no-repeat;
  filter: invert(0.8);
  height: 18px;
  width: 18px;
  min-height: 18px;
  min-width: 18px;
  margin-right: 15px;
}

.messenger__filter-blue {
  filter: invert(78%) sepia(92%) saturate(2055%) hue-rotate(168deg) brightness(95%) contrast(96%);
}

.messenger__name-container {
  flex: 1;
	display: flex;
	flex-flow: column nowrap;
}

.messenger__short-name-container {
  border-radius: 50%;
  height: 40px;
  width: 40px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 20px;
  font-weight: 500;
  background-color: #7ccaed;
  color: #fff;
}

.messenger__card-window .messenger__short-name-container {
  margin-right: 10px;
}

.messenger__chat-name-label {
  position: absolute;
  top: 0;
  right: 0;
  padding: 5px;
}

.messenger__chat-name-label img {
  width: 17px;
  vertical-align: top;
}

.messenger__text-center {
  text-align: center;
}

.messenger__text-ellipsis {
  width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
}

.messenger__title_container {
  display: flex;
  align-items: center;

  line-height: 25px;
  text-overflow: ellipsis;
  white-space: nowrap;
  font-weight: 500;
}

.messenger__chat-name_short {
  font-size: 17px;
  font-weight: 700;
  line-height: 22px;
}

.messenger__chat-info{
	display: flex;
	align-items: center;
	justify-content: flex-start;
	gap: 10px;

	min-height: 22px;
}

.messenger__last-message {
  align-items: center;
  font-size: 12px;
  line-height: 18px;
  color: #8b8b8b;
  /* display: -webkit-box; */
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
	overflow: hidden;
	white-space: nowrap;
	text-overflow: ellipsis;
}

.messenger__chat-indicators{
	display: flex;
	flex-flow: row nowrap;
}

.messenger__chat-unread{
	display: flex;
	align-items: center;
	justify-content: center;

	min-width: 22px;
	height: 22px;
	padding: 8px;

	border-radius: 22px;
	font-weight: 700;

	font-size: 11px;
	line-height: 12px;
	letter-spacing: -0.05em;

	color: #FFFFFF;
	background-color: #3361FF;
}
.messenger__chat-muted ~ .messenger__chat-unread{
	background-color: #8BABD8;
}

.messenger__chat-name-label .messenger__chat-unread{
	position: absolute;
	top: 8px;
	right: 2px;
}

/* .messenger__chat-selected .messenger__last-message {
  color: #fff;
} */

.messenger__avatar {
   background-size: cover;
   background-position: center center;
   background-repeat: no-repeat;
   background-color: #ddd;
   height: 42px;
   width: 42px;
   min-height: 42px;
   min-width: 42px;
   margin-right: 10px;
   border-radius: 50%;
 }

.messenger__last-message__marked {
  color: #1883b2;
  font-weight: 600;
}

.messenger__avatar img {
  height: 100%;
  width: 100%;
  border-radius: 50%;
}

</style>
