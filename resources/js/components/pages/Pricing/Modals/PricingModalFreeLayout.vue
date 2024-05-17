<template>
	<div class="pricing-modal">
		<div class="pricing-modal-content">
			<slot />
		</div>
	</div>
</template>

<script>

import {mapActions} from 'pinia';
import {useModalStore} from '../../../../stores/Modal';

export default {
	name: 'PricingModalFreeLayout',

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

  .pricing-modal-content{
	max-width: 564px !important;
  }
}


.pricing-modal-content{
	justify-content: center;
  animation: fadeInUp 0.3s ease;
  max-width: 464px;
  width: 100%;
  padding: 5px;
  display: flex;
  flex-direction: column;
  background-color: #ffffff;
  box-shadow: 4px 0px 32px 0px #c3c3c340;
  border-radius: 40px;
}
.pricing-modal{
  display: flex;
  justify-content: center;
  left: 35%;
  top: 35%;
  position: absolute;
  align-items: center;
}


@keyframes fadeInUp {
  0% {
	opacity: 0;
	transform: translateY(400px);
  }
  100% {
	opacity: 1;
	transform: translateY(0);
  }
}

</style>
