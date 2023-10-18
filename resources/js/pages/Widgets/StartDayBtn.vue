<template>
	<div>
		<!-- Start day btn -->
		<a
			href="javascript:void(0)"
			class="profile__button"
			:class="{
				'profile__button_error': status === 'error',
				'profile__button_started': workdayStatus === 'started',
				'profile__button_loading': status === 'loading',
				'profile__button_dayoff': workdayStatus === 'workdone'
			}"
			@click="onClick"
		>
			<svg
				id="loader-1"
				:class="{'visible': status === 'loading'}"
				class="profile__loader"
				version="1.1"
				xmlns="http://www.w3.org/2000/svg"
				xmlns:xlink="http://www.w3.org/1999/xlink"
				x="0px"
				y="0px"
				width="20px"
				height="20px"
				viewBox="0 0 40 40"
				enable-background="new 0 0 40 40"
				xml:space="preserve"
			>
				<path
					opacity="0.2"
					fill="#000"
					d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946
						s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634
						c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"
				/>
				<path
					fill="#000"
					d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0
						C22.32,8.481,24.301,9.057,26.013,10.047z"
				>
					<animateTransform
						attributeType="xml"
						attributeName="transform"
						type="rotate"
						from="0 20 20"
						to="360 20 20"
						dur="0.5s"
						repeatCount="indefinite"
					/>
				</path>
			</svg>

			<template v-if="status === 'error'">
				<p class="profile__button-text">Ошибка сети</p>
			</template>

			<template v-else>
				<p
					v-if="workdayStatus === 'stopped'"
					class="profile__button-text"
				>Начать рабочий день</p>
				<p
					v-if="workdayStatus === 'started'"
					class="profile__button-text"
				>Завершить рабочий день</p>
				<p
					v-if="workdayStatus === 'workdone'"
					class="StartDayBtn-overtime"
				>Оставить заявку на&nbsp;сверхурочную</p>
			</template>
		</a>
	</div>
</template>

<script>
/* global Laravel */
import { mapActions } from 'pinia'
import { useProfileStatusStore } from '@/stores/ProfileStatus'
export default {
	name: 'StartDayBtn',
	components: {},
	props: {
		workdayStatus: {
			type: String,
			default: ''
		},
		status: {
			type: String,
			default: ''
		}
	},
	data() {
		return {
			data: {}
		}
	},
	created() {},
	methods: {
		...mapActions(useProfileStatusStore, ['pushOvertime']),
		async clickOvertime(){
			if(confirm('Хотите запросить у руководителя работу в выходной?')){
				try {
					/* eslint-disable camelcase */
					await this.pushOvertime({
						user_id: Laravel.userId,
						date: this.$moment(Date.now()).format('YYYY-MM-DD'),
						start_time: this.$moment(Date.now()).format('HH:mm:ss'),
					})
					alert('Запрос на работу в выходной отправлен')
					/* eslint-enable camelcase */
				}
				catch (error) {
					console.error(error)
					window.onerror && window.onerror(error)

					alert(error?.response?.data?.message || '!Запрос на работу в выходной не отправлен!')
				}
			}
		},
		onClick(){
			if(this.workdayStatus === 'workdone') return this.clickOvertime()
			this.$emit('clickStart')
		},
	}
}
</script>

<style lang="scss" scoped>
.profile__button{
  display: none;
  background: #8FAF00;
  color:#fff;
  text-align: center;
  padding: 2rem 1.5rem;
  width: 100%;
  max-width: 28rem;
  border-radius:1rem;
  margin-bottom: 1.5rem;
  align-items:center;
  text-transform: uppercase;
  transition: background .3s 0s;
  opacity:0;
  visibility: hidden;

  &:hover{
    background: #88a402;
  }
}
.profile__button_started,
.profile__button_error{
  background: #fc5757;
  &:hover{
    background: darken(#fc5757, 10%);
  }
}
.profile__button_loading{
  background-color: #555;
  cursor: default;

  .profile__button-text::before {
    display: none;
  }
}

.profile__button_dayoff{
	background-color: #555;
	cursor: default;
	justify-content: center;
	p{
		margin: 0 !important;
		padding: 0 !important;
	}
	svg{
		display: none!important;
	}
	&:hover{
		background-color: #555 !important;
		cursor: default !important;
	}
	.profile__button-text::before {
		display: none;
	}
}

.profile__button-text{
  padding-left: 3rem;
  font-size:1.4rem;
  font-weight: 600;
  position:relative;
  white-space: nowrap;

  &::before{
    content:"";
    position:absolute;
    top: 0;
    left:0;
    width: 2rem;
    height: 2rem;
    background: url("../../../../public/images/dist/start-icon.svg") center no-repeat;
    background-size: cover;
  }
}

.profile__loader {
  opacity: 0;
  &.visible {
    opacity: 1;
  }
}
.aet {
  background: #608EE9 !important;
}
.corpbook {
  font-size: 14px;
}

@media(min-width: 900px){
  .profile__button{
    display: flex;
    opacity:1;
    visibility: visible;
  }
}

@media(min-width:1360px){
  .header__profile._active{
    .profile__button{
      opacity:1;
      visibility: visible;
      transform: translateY(0);
    }
  }
  .profile__button{
    transition: all 1s .2s, background .3s 0s;
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
  }
}
</style>

<style lang="scss">
.StartDayBtn{
	&-overtime{
		display: flex;
		justify-content: center;

		position: relative;

		font-size: 1.3rem;
		font-weight: 600;
		white-space: nowrap;

		border-radius: 1rem;
	}
}
</style>
