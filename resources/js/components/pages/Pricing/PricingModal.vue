<template>
	<div class="pricing-modal">
		<div class="pricing-modal-content">
			<button
				class="pricing-buttonCLose"
				@click="closeModal"
			>
				<svg
					width="24"
					height="24"
					viewBox="0 0 24 24"
					fill="none"
					xmlns="http://www.w3.org/2000/svg"
				>
					<g clip-path="url(#clip0_88_583)">
						<path
							d="M18.75 5.25L5.25 18.75"
							stroke="black"
							stroke-width="1.5"
							stroke-linecap="round"
							stroke-linejoin="round"
						/>
						<path
							d="M18.75 18.75L5.25 5.25"
							stroke="black"
							stroke-width="1.5"
							stroke-linecap="round"
							stroke-linejoin="round"
						/>
					</g>
					<defs>
						<clipPath id="clip0_88_583">
							<rect
								width="24"
								height="24"
								fill="white"
							/>
						</clipPath>
					</defs>
				</svg>
			</button>
			<slot />
		</div>
	</div>
</template>

<script>
import {mapActions} from 'pinia';
import {useModalStore} from '../../../stores/Modal';

export default {
	name: 'PricingModal',

	methods:{
		...mapActions(useModalStore, ['removeModalActive']),

		closeModal(){
			this.removeModalActive()
		}
	}
}
</script>

<style lang="scss" scoped>


@media (min-width: 1600px) {
  .pricing-buttonCLose{
	position: relative;
	right: 10%;
	width: 40px !important;
	height: 40px !important;
	padding: 8px !important;
	border-radius: 8px !important;
  }

  .pricing-modal-content{
	position: absolute;
	right: 1%;
	max-width: 564px !important;
	width: 100%;
	display: flex;
	flex-direction: column;
	padding: 8px !important;
	background-color: #FFFFFF;
	box-shadow: 4px 0px 32px 0px #C3C3C340;
	border-radius: 40px;
	height: 100vh !important;
  }
}

.pricing-buttonCLose{
  position: relative;
  right: 10%;
  width: 40px;
  height: 40px;
  padding: 8px;
  border-radius: 8px;
}

.pricing-modal-content{
  position: absolute;
  right: 1%;
  max-width: 464px;
  width: 100%;
  padding: 5px;
  display: flex;
  flex-direction: column;
  background-color: #FFFFFF;
  box-shadow: 4px 0px 32px 0px #C3C3C340;
  border-radius: 40px;
  height: 100vh;
}


</style>
