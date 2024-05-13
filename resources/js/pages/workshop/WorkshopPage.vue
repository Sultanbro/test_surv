<template>
	<DefaultLayout>
		<div class="workshop">
			<div class="workshop-container">
				<div>
					<button>
						<a
							href="https://wl.walletone.com/checkout/form/119184"
							target="_blank"
							rel="noopener noreferrer"
						>Оплата картой Казахстана</a>
					</button>
				</div>
			</div>
		</div>
	</DefaultLayout>
</template>

<script>
import DefaultLayout from '@/layouts/DefaultLayout';

export default {
	name: 'WorkshopPage',
	components: {
		DefaultLayout,
	},
};
</script>

<style scoped lang="scss">
%center-content {
	min-height: 100vh;
	display: flex;
	align-items: center;
	justify-content: center;
}

.workshop {
	font-size: 25px;
	@extend %center-content;
	&-container {
		@extend %center-content;
		background-color: #ffffff;
		border-radius: 15px;
		padding: 1%;
		min-height: 500px;
		min-width: 500px;
		box-shadow: rgba(17, 17, 26, 0.1) 0px 4px 16px,
			rgba(17, 17, 26, 0.1) 0px 8px 24px, rgba(17, 17, 26, 0.1) 0px 16px 56px;
	}
	button {
		background-color: #33b4ff;
		padding: 2%;
		color: white;
		border-radius: 10px;
		transition: all ease 100ms;
		&:hover {
			background-color: #0193e8;
		}
		&:focus {
			outline: none;
		}
		a {
			color: white;
		}
	}
}
</style>
