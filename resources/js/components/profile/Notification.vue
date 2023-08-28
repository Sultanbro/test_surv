<template>
	<div class="kolokolchik">
		<div class="inf">
			<a
				href="#"
				title="Ваши уведомления"
				class="tooglenotifi "
				@click="showPanel = !showPanel"
			>
				<i
					class="fa fa-bell"
					aria-hidden="true"
				/>
				<div
					:class="{ 'blink-notification' : unread }"
					style="border-color: #00bef6;position: absolute;top: 0;left: 0;bottom: 0;right: 0;border-radius: 50%;"
				/>
				<span
					class="numb"
					style="font-weight:800"
				>{{ unread }}</span>
			</a>
		</div>
		<div class="bgpanel" />
		<div
			v-if="showPanel"
			class="panel"
		>
			<div class="tail" />
			<div class="panel_head">
				<div
					class="panel_in active"
					data-tab="1"
				>
					Уведомления
				</div>
				<div
					class="panel_in "
					data-tab="2"
				>
					Уведомления прочитанные
				</div>
			</div>
			<div class="panel_body">
				<div
					class="panel_out active"
					data-id="1"
				>
					<div class="notification_list">
						<div
							v-for="noti in unreads"
							:key="noti.id"
							class="notification_item"
						>
							<div class="notifi_top">
								<div
									class="label-wrapper"
									:class="{'hidden' : noti.type != 'important'}"
								>
									<span
										v-if="noti.type == 'important'"
										class="label-wrapper_text"
									>ВАЖНОЕ</span>
									<span
										v-else
										class="label-wrapper_text"
									/>
								</div>
								<span class="notification-date">{{ noti.created_at }}</span>
								<span class="notification-projectId">Jobtron.org</span>
							</div>
							<div class="notification-title">
								{{ noti.title }}
							</div>
							<!-- eslint-disable -->
							<div
								class="notification-text"
								v-html="noti.message"
							/>
							<!-- eslint-enable -->
							<a @click="markRead(noti.id)">
								<div class="notification-change"><i class="fa fa-check" /></div>
							</a>
						</div>
					</div>
				</div>
				<div
					class="panel_out "
					data-id="2"
				>
					<div class="notification_list" />
				</div>
			</div>
			<div class="panel_foot">
				<button><i class="fa fa-check" />Отметить все, как прочитанные</button>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'ProfileNotification',
	props: {
		unread: {
			type: String,
			default: ''
		},
		/* eslint-disable-next-line */
		read_notifications: {
			type: Array,
			default: () => []
		},
		/* eslint-disable-next-line */
		unread_notifications: {
			type: Array,
			default: () => []
		}
	},
	data() {
		return {
			password: '',
			showPanel: false
		}
	},
	created() {
		this.$options.reads = this.read_notifications
		this.$options.unreads = this.unread_notifications
		// this.u = JSON.parse(this.user)
	},
	methods: {
		markRead(){

		},

		test() {


			//    $(document).ready(function () {
			//         $('.tooglenotifi').click(function() {
			//             $('.kolokolchik .panel').toggle();
			//             $('.bgpanel').toggleClass('active');
			//         })
			//     });
			//     $(".bgpanel").on("click", function(e) {
			//         e.preventDefault();
			//         $('.kolokolchik .panel').toggle();
			//         $('.bgpanel').toggleClass('active');
			//     });
			//     $(".panel_head .panel_in").on("click", function(e) {
			//         e.preventDefault();
			//         $('.panel_head .panel_in, .panel_body .panel_out').removeClass('active');
			//         $(this).addClass('active');
			//         var numb = $(this).data('tab');
			//         $('.panel_out[data-id=' + numb + ']').addClass('active');
			//     });




		},

		// sendPost() {
		//     this.message = ''
		//     axios.post('/timetracking/change-password', {
		//         password: this.password,
		//         repassword: this.repassword,
		//     })
		//     .then(response => {
		//         if(response.data.code == 200) {
		//             this.message = 'Пароль успешно изменен!';
		//             this.messageColor = 'green';
		//             setTimeout(function() {
		//                 window.location.reload();
		//             },1500)

		//         }
		//         if(response.data.code == 500) {
		//             this.message = 'Пароли не совпадают!';
		//             this.messageColor = 'red';
		//         }
		//     })
		// },
	}
}
</script>

<style lang="scss" scoped>
.kolokolchik {
  float: right;
  display: table;
  position: relative;
  width: 65px;}

.kolokolchik div.inf {
  padding: 14px 0;
  display: table-cell;
  vertical-align: middle;
}

.kolokolchik div.inf > a {
  display: block;
  background: #202226;
  color: white;
  padding: 5px 8px;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  margin-right: 0;
  text-decoration: none;
  position: relative;
}

.kolokolchik div.inf > a span {
  display: block;
  width: 30px;
  height: 22px;
  background: white;
  position: absolute;
  top: 5px;
  left: 23px;
  border-radius: 10px;
  text-align: center;
  color: #5ebee9;
  font-size: 13px;
  line-height: 22px;
}

.kolokolchik div.inf > a:focus, .kolokolchik div.inf > a:hover {
  background: #ffc400;
}

.kolokolchik div.inf .yvemod {
  margin: 0 10px 0 0;
  font-size: 13px;
  display: inline-block;
  vertical-align: top;
  color: red;
}

.kolokolchik div.inf img {
  width: 77px;
  height: auto;
  max-height: 31px;
}

.kolokolchik .bgpanel {
  display: none;
}

.kolokolchik .bgpanel.active {
  display: block;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: none;
  z-index: 8888;
}

.kolokolchik .panel {
  width: 480px;
  position: absolute;
  left: 50%;
  margin-left: -450px;
  top: 100%;
  background: #fff;
  -webkit-box-shadow: 0 8px 12px rgba(27, 42, 57, 0.2);
          box-shadow: 0 8px 12px rgba(27, 42, 57, 0.2);
  border: 1px solid rgba(27, 42, 48, 0.1);
  border-radius: 2px;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
      -ms-flex-direction: column;
          flex-direction: column;
  -webkit-box-align: stretch;
      -ms-flex-align: stretch;
          align-items: stretch;
  z-index: 8889;
}

.kolokolchik .panel .tail {
  position: absolute;
  border: 10px solid transparent;
  border-top-color: #ffffff;
  top: -19px;
  left: 50%;
  margin-left: -10px;
  -webkit-transform: rotate(180deg);
  transform: rotate(180deg);
}

.kolokolchik .panel .panel_head {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: stretch;
      -ms-flex-align: stretch;
          align-items: stretch;
  -webkit-box-flex: 0;
      -ms-flex: none;
          flex: none;
  height: 40px;
  padding: 0 10px;
  background: #fff;
  -webkit-box-shadow: inset 0 -1px 0 rgba(27, 42, 48, 0.1);
          box-shadow: inset 0 -1px 0 rgba(27, 42, 48, 0.1);
}

.kolokolchik .panel .panel_head .panel_in {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
      -ms-flex-align: center;
          align-items: center;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
  padding: 0 10px;
}

.kolokolchik .panel .panel_head .panel_in.active {
  -webkit-box-shadow: inset 0 -2px 0 #5ebee9;
          box-shadow: inset 0 -2px 0 #5ebee9;
  color: #5ebee9;
  text-decoration: none;
}

.kolokolchik .panel .panel_body {
  padding: 8px 9px;
  overflow-y: scroll;
  height: 400px;
}

/* width */
.kolokolchik .panel .panel_body::-webkit-scrollbar {
  width: 7px;
}

/* Track */
.kolokolchik .panel .panel_body::-webkit-scrollbar-track {
  background: #f1f1f1;
}

/* Handle */
.kolokolchik .panel .panel_body::-webkit-scrollbar-thumb {
  background: rgba(33,169,229,.58);
}

/* Handle on hover */
.kolokolchik .panel .panel_body::-webkit-scrollbar-thumb:hover {
  background: #21a9e5;
}

.kolokolchik .panel .panel_body .panel_out {
  display: none;
}

.kolokolchik .panel .panel_body .panel_out.active {
  display: block;
}

.kolokolchik .panel .panel_body .notification_list .notification_item {
      position: relative;
    margin-bottom: 10px;
    width: 100%;
    padding: 16px 20px;
    border-radius: 0;
    margin-right: 0;
    background-color: rgba(118, 221, 20, 0.2);
    overflow: hidden;
}

.kolokolchik .panel .panel_body .notification_list .notification_item .notification-title {
  margin-bottom: 8px;
  font-size: 14px;
  line-height: 14px;
  font-weight: 700;
  color: rgba(27, 42, 48, 0.8);
  width: 90%;
}

.kolokolchik .panel .panel_body .notification_list .notification_item .notification-text {
  font-size: 13px;
  line-height: 18px;
  color: rgba(27, 42, 48, 0.8);
  white-space: pre-line;
  width: 90%;
}

.kolokolchik .panel .panel_body .notification_list .notification_item .notification-change {
  position: absolute;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
      -ms-flex-align: center;
          align-items: center;
  -webkit-box-pack: center;
      -ms-flex-pack: center;
          justify-content: center;
  top: 0;
  right: 0;
  width: 50px;
  height: 100%;
  background-color: #76dd14;
  color: #ffffff;
  cursor: pointer;
  opacity: 0.7;
  -webkit-transition: all 0.1s linear;
  transition: all 0.1s linear;
}

.kolokolchik .panel .panel_body .notification_list .notification_item .notifi_top {
  height: 16px;
  margin-bottom: 8px;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  font-size: 10px;
  color: rgba(27, 42, 48, 0.6);
}

.kolokolchik .panel .panel_body .notification_list .notification_item .notifi_top .label-wrapper {
  display: -webkit-inline-box;
  display: -ms-inline-flexbox;
  display: inline-flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
      -ms-flex-direction: column;
          flex-direction: column;
  -webkit-box-align: center;
      -ms-flex-align: center;
          align-items: center;
  -webkit-box-pack: center;
      -ms-flex-pack: center;
          justify-content: center;
  width: -webkit-fit-content;
  width: -moz-fit-content;
  width: fit-content;
  padding: 3px 8px 1px;
  border-radius: 4px;
}

.kolokolchik .panel .panel_body .notification_list .notification_item .notifi_top .label-wrapper span {
  color: #fff;
  font-style: normal;
  font-weight: 700;
  font-size: 10px;
  line-height: 12px;
  letter-spacing: 1px;
  background: #ffc400;
  padding: 5px 10px;
  border-radius: 4px;
}

.kolokolchik .panel .panel_body .notification_list .notification_item .notifi_top .notification-date,
.kolokolchik .panel .panel_body .notification_list .notification_item .notifi_top .notification-projectId {
  margin-left: 12px;
  line-height: 17px;
}

.kolokolchik .panel .panel_foot {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: end;
      -ms-flex-pack: end;
          justify-content: flex-end;
  -webkit-box-align: center;
      -ms-flex-align: center;
          align-items: center;
  position: -webkit-sticky;
  position: sticky;
  -webkit-box-shadow: 0 1px 12px rgba(27, 42, 57, 0.2);
          box-shadow: 0 1px 12px rgba(27, 42, 57, 0.2);
  background-color: #fff;
  width: 480px;
  padding: 7px 20px;
}

.kolokolchik .panel .panel_foot button {
  display: -webkit-inline-box;
  display: -ms-inline-flexbox;
  display: inline-flex;
  -webkit-box-align: center;
      -ms-flex-align: center;
          align-items: center;
  -webkit-box-pack: center;
      -ms-flex-pack: center;
          justify-content: center;
  border: none;
  outline: none;
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 14px;
  line-height: 18px;
  cursor: pointer;
  -webkit-box-shadow: inset 0 -1px 0 0 rgba(27, 42, 48, 0.35);
          box-shadow: inset 0 -1px 0 0 rgba(27, 42, 48, 0.35);
  padding: 2px 8px;
  height: 28px;
  min-width: 28px;
  color: rgba(27, 42, 48, 0.8);
  background: -webkit-gradient(linear, left top, left bottom, from(#fff), to(#f1f2f3));
  background: linear-gradient(180deg, #fff, #f1f2f3);
  -webkit-box-shadow: inset 0 1px 0 rgba(27, 42, 48, 0.1), inset 0 -1px 0 rgba(27, 42, 48, 0.2), inset -1px 0 0 rgba(27, 42, 48, 0.1), inset 1px 0 0 rgba(27, 42, 48, 0.1);
          box-shadow: inset 0 1px 0 rgba(27, 42, 48, 0.1), inset 0 -1px 0 rgba(27, 42, 48, 0.2), inset -1px 0 0 rgba(27, 42, 48, 0.1), inset 1px 0 0 rgba(27, 42, 48, 0.1);
}
.linksa {
    color: #1890ff !important;
}
.navbar .navbar-brand img {
    max-width: 166px;
}
.blink-notification {
  -webkit-box-shadow: 0 0 4px #61bfe9;
          box-shadow: 0 0 4px #61bfe9;
  border: 1px solid #61bfe9;
  -webkit-animation: blinktwo infinite 1.5s;
  animation: blinktwo infinite 1.5s;
}
@-webkit-keyframes blinktwo {
  50% {
    -webkit-transform: scale(1, 1);
    transform: scale(1, 1);
    opacity: 1;
  }
  100% {
    -webkit-transform: scale(2, 2);
    transform: scale(2, 2);
    opacity: 0;
  }
}
@keyframes blinktwo {
  50% {
    -webkit-transform: scale(1, 1);
    transform: scale(1, 1);
    opacity: 1;
  }
  100% {
    -webkit-transform: scale(2, 2);
    transform: scale(2, 2);
    opacity: 0;
  }
}

</style>
