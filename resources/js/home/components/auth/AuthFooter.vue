<template>
	<div class="section__footer">
		<div class="section__footer__text">
			Jobtron.org - Корпоративный портал: полезный для бизнеса, удобный для
			сотрудников
		</div>
		<div class="section__footer__text">
			©2024 Jobtron.org
		</div>
	</div>
</template>

<style lang="scss" scoped>
$md2: 991.98px;
$md3: 767.98px;
$md4: 479.98px;
.section__footer {
	margin-top: 30px;
	display: flex;
	justify-content: space-between;
	@media (max-width: $md2) {
		flex-direction: column;
		text-align: center;
	}
	&__text {
		font-size: 14px;
		color: #737b8a;
		@media (max-width: $md2) {
			margin-bottom: 10px;
		}
	}
}
</style>
