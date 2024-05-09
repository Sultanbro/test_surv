<template>
	<div
		class="WellcomeNewCabinet"
		@click.self="onClose"
	>
		<div class="WellcomeNewCabinet-dialog">
			<div class="WellcomeNewCabinet-header">
				Приветствуем в Jobtron.org
			</div>
			<div class="WellcomeNewCabinet-body">
				<p>Мы уже создали для вас рабочее пространство, вы можете начать им пользоваться прямо сейчас!</p>
				<p>Пароль для входа в систему выслали вам на почту, указанную при регистрации.</p>
			</div>
			<div class="WellcomeNewCabinet-footer">
				<JobtronButton
					@click="onClose"
				>
					Хорошо
				</JobtronButton>
			</div>
		</div>
	</div>
</template>

<script>
import JobtronButton from '@ui/Button.vue'

export default {
	name: 'WellcomeNewCabinet',
	components: {
		JobtronButton,
	},
	props: {},
	data(){
		return {}
	},
	computed: {},
	watch: {},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {
		onClose(){
			this.$emit('close')
		}
	},
}
</script>

<style lang="scss">
.WellcomeNewCabinet{
	display: flex;
	justify-content: center;
	align-items: center;

	position: fixed;
	z-index: 999999999;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background-color: #0003;

	&-dialog{
		width: 500px;
		max-width: calc(100% - 20px);
		padding: 40px;
		border-radius: 24px;
		background-color: #fff;
	}
	&-header{
		margin-bottom: 12px;

		font-size: 28px;
		font-weight: 600;
		line-height: 36px;
		text-align: left;

		color: #333;
	}
	&-body{
		margin-bottom: 32px;

		font-size: 16px;
		font-weight: 400;
		line-height: 24px;
		text-align: left;

		color: #737B8A;
		p{
			margin-bottom: 4px;
		}
	}
	&-footer{
		.JobtronButton{
			display: flex;
			justify-content: center;
			width: 100%;

			font-size: 16px;
			font-weight: 500;
		}
	}
}
</style>
