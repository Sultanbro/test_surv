<template>
	<div
		class="NotificationsItem"
		:class="{
			'NotificationsItem_active': active
		}"
	>
		<div class="NotificationsItem-date">
			{{ $moment(notification.created_at).format(dateFormat) }}
		</div>
		<div class="NotificationsItem-title">
			{{ notification.title }}
		</div>
		<div class="NotificationsItem-text">
			<!-- eslint-disable-next-line -->
			<div class="NotificationsItem-html" v-html="linkedMessage" />
			<div
				v-if="active && isOvertime"
				class="Notification-actions"
			>
				<JobtronButton
					small
					@click="$emit('acceptOvertime', notification.id)"
				>
					Принять
				</JobtronButton>
				<JobtronButton
					small
					@click="$emit('rejectOvertime', notification.id)"
				>
					Отклонить
				</JobtronButton>
			</div>
		</div>
		<div
			v-if="active"
			class="NotificationsItem-read"
			@click="$emit('read')"
		/>
		<div
			v-else
			class="NotificationsItem-date NotificationsItem-date_absolute"
		>
			{{ $moment(notification.read_at).format(dateFormat) }}
		</div>
	</div>
</template>

<script>
import JobtronButton from '@ui/Button'
const urlRegex = /(https?:\/\/[^\s]+)/g

export default {
	name: 'NotificationsItem',
	components: {
		JobtronButton,
	},
	props: {
		active: Boolean,
		notification: {
			type: Object,
			required: true
		},
	},
	data(){
		return {
			dateFormat: 'DD.MM.YYYY',
		}
	},
	computed: {
		isOvertime(){
			return this.notification.title === 'Заявка на сверхурочную работу' && this.$moment(Date.now()).diff(this.notification.created_at, 'hours') < 2
		},
		linkedMessage(){
			const div = document.createElement('div')
			div.innerHTML = this.notification.message
			const matces = div.innerText.match(urlRegex)
			if(!matces) return this.notification.message
			let text = this.notification.message
			matces.forEach(match => {
				text = text.replace(match, `<a href="${match}" target="_blank">${match}</a>`)
			})
			return text
		}
	}
}
</script>

<style lang="scss">
$buttonBG: #b7e100;

.NotificationsItem{
	padding: 2rem 4rem;
	margin-top: 1rem;

	position: relative;

	font-family: "Inter",sans-serif;
	color: #62788b;

	background: #ebedf5;
	&_active{
		padding-right: 7rem;
	}
	&-date{
		margin-bottom: 1rem;
		font-size: 1.2rem;
		&_absolute{
			position:absolute;
			top: 2rem;
			right: 4rem;
		}
	}
	&-title{
		margin-bottom: 1rem;
		font-size: 1.4rem;
		font-weight: 700;
	}
	&-text{
		font-size: 1.4rem;
		p{
			margin-bottom: .7rem;
		}
		.red{
			color:#ED2353;
		}
	}
	&-read{
		width: 6.4rem;
		height: 100%;

		position: absolute;
		z-index: 1;
		top: 0;
		right: 0;

		transition: background .3s 0s;
		cursor: pointer;
		background: $buttonBG;

		&:after{
			content: '';
			height: 2.4rem;
			width: 2.4rem;

			position: absolute;
			z-index: 1;
			top: 50%;
			right: .8rem;

			transform: translate(-50%,-50%);
			background: url(/images/done.png?73ab6c3…) 50% no-repeat;
		}

		&:hover{
			background: darken($buttonBG, 10%);
		}
	}
}

@media(max-width:370px){
	.NotificationsItem{
		&-date{
			&_absolute{
				display: none;
			}
		}
		&-html{
			white-space: pre-line;
		}
	}
}
</style>
