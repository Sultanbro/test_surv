<script>
import Sidebars from '@/pages/Layouts/Sidebars'
import ChatApp from '@/components/Chat/ChatApp'

export default {
	components: {
		Sidebars,
		ChatApp,
	},
	props: {
		hasBg: Boolean
	},
	data(){
		return {
			isLeft: false,
			isRight: false,
		}
	}
}
</script>

<template>
	<div
		class="DefaultLayout"
		:class="{'has-bg': hasBg}"
	>
		<Sidebars
			:is-left="isLeft"
			:is-right="isRight"
		/>
		<ChatApp />
		<div class="wrapper">
			<main class="main">
				<div class="container">
					<!-- Mobile menu buttons -->
					<div class="header__top">
						<a
							href="javascript:void(0)"
							class="header__top-button burger-left"
							:class="{opened: isLeft}"
							@click.prevent="isLeft = !isLeft"
						>Раскрыть меню</a>
						<div class="header__top-wrapper">
							<a
								href="javascript:void(0)"
								class="header__top-button burger-right"
								:class="{opened: isRight}"
								@click.prevent="isRight = !isRight"
							>Раскрыть меню</a>
							<!-- <a href="#" class="header__right-icon"><img src="images/dist/header-right-3.svg" alt="nav icon" class="header__icon-img"></a><a href="#" class="header__right-icon red"><img src="images/dist/header-right-2.svg" alt="nav icon" class="header__icon-img"></a>-->
						</div>
					</div>
					<slot />
				</div>
			</main>
			<footer class="footer" />
		</div>
	</div>
</template>
