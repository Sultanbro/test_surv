<template>
	<div class="material-test-content">
		<div class="material-test-header">
			<MaterialTestModalIcon />
			<div>
				<svg
					width="14"
					height="14"
					viewBox="0 0 14 14"
					fill="none"
					xmlns="http://www.w3.org/2000/svg"
				>
					<path
						d="M13 1L1 13M1 1L13 13"
						stroke="#667085"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					/>
				</svg>
			</div>
		</div>
		<div class="material-test-body">
			<p class="material-test-title">
				В разделе нет тестовых вопросов
			</p>
			<p class="material-test-description">
				Хотите добавить?
			</p>
		</div>
		<div class="material-test-buttons">
			<button
				class="material-test-cancel"
				@click="cancel"
			>
				Отмена
			</button>
			<button class="material-test-add">
				Добавить
			</button>
		</div>
	</div>
</template>

<script>
import MaterialTestModalIcon from '../../../assets/icons/MaterialTestModalIcon.vue';

export default {
	name: 'MaterialTestModal',
	components: {MaterialTestModalIcon},
	methods:{
		cancel(){
			this.$emit('close')
		}
	}
}
</script>

<style lang="scss" scoped>
.material-test-{
		&content{
		display: flex;
		flex-direction: column;
		width: 420px;
		height: 240px;
		max-height: 80vh;
		padding: 24px;
		border-radius: 15px;
		position: fixed;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		background-color: #fff;
		}
	&header{
	display: flex;
		justify-content: space-between;
	}
	&body{
		margin-top: 16px;
	}
	&title{
		font-size: 18px;
		font-weight: 600;
		line-height:28px;
		color: #101828;
	}
	&description{
		color: #475467;
	}
	&buttons{
		margin-top: auto;
	display: flex;
	justify-content: space-between;
	}
	&cancel{
		border: 1px solid #156AE8;
		color: #156AE8;
		padding: 10px 54px;
	border-radius: 8px;


  }
	&add{
	background-color:  #156AE8;
	color: white;
		border-radius: 8px;
	padding: 10px 54px;
	}
}
</style>
