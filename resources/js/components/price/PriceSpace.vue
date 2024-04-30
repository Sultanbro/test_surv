<template>
	<div class="price-space">
		<h2 class="price-space-title">
			Ваше пространство
		</h2>
		<div class="price-space-info-blocks">
			<div class="price-space-content">
				<div class="price-space-image">
					<img
						class="price-space-image"
						src="/images/price/DefaultAvatar.png"
					>
				</div>
				<div class="price-space-info">
					<div class="price-space-title-block">
						1suol9rbcn
					</div>
					<div class="price-space-text-content">
						<p class="price-space-text-name">
							Тариф:
						</p>
						<p class="price-space-name-data">
							Бесплатный
						</p>
					</div>
					<div class="price-space-text-content">
						<p class="price-space-text-name">
							Пользователей:
						</p>
						<p class="price-space-name-data">
							4 из 5
						</p>
					</div>
				</div>
			</div>
			<div class="price-space-content">
				<div class="price-space-image">
					<img
						class="price-space-image"
						src="/images/price/AvatarManager.png"
					>
				</div>
				<div class="price-space-info">
					<div class="price-space-info">
						Ваш персональный менеджер
					</div>
					<div class="price-space-title-block">
						Миркаримова Лейла
					</div>
					<div class="price-space-description">
						Я здесь и готова помочь по любому вопросу 😊
					</div>
					<div class="price-space-contact">
						<p>+7 778 548-67-59</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'PriceSpace'
}
</script>

<style scoped>
.price-space{
	width: 1195px;
}

.price-space-title{
	font-size: 44px;
		font-weight: 600;
		line-height: 54px;
}

.price-space-content{
	display: flex;
	gap: 24px;

}

.price-space-image{
	max-width: 164px;
	width: 100%;
	border-radius: 16px;
}

.price-space-title-block{
		font-size: 20px;
		font-weight: 600;
		line-height: 30px;
		margin-bottom: 8px;
}

.price-space-text-content{
		margin-top: 8px;
	display: flex;
		gap: 4px;
}

.price-space-text-name{
		color: #737B8A;
		font-size: 16px;
		font-weight: 400;
}

.price-space-name-data{
	font-size: 16px;

}
.price-space-info-blocks{
	display: flex;
	justify-content: space-between;
		margin-top: 24px;
}

.price-space-info{
	display: flex;
		flex-direction: column;
	max-width: 258px;
	width: 100%;
}

.price-space-contact{
		margin-top: auto;
}
</style>
