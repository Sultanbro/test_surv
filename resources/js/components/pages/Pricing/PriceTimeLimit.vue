<template>
	<div
		class="price-trial"
		:class="{'small-content' : small, 'default-content' : isDefault}"
	>
		<div
			class="price-trial-content"
			:class="{'small-content-price-content' : small, 'default-content-price-content' : isDefault}"
		>
			<h1
				class="price-trial-title"
				:class="{'small-content-title' : small, 'default-content-title' : isDefault}"
			>
				Действие вашего тарифа скоро закончится
			</h1>
			<div
				class="price-trial-description"
				:class="{'small-content-description' : small, 'default-content-description' : isDefault}"
			>
				Осталось 5 дней до конца действия тарифа. Оплатите его сейчас, чтобы сохранить все преимущества
			</div>
			<div class="price-trial-button-group">
				<button
					v-if="!small"
					class="price-trial-button"
				>
					Продлить или сменить тариф
				</button>
			</div>
		</div>
		<div class="price-trial-icons-content">
			<div
				class="price-trial-eclipse"
				:class="{'small-content-eclipse' : small, 'default-content-eclipse' : isDefault}"
			/>
			<img
				src="/images/price/fire.png"
				class="price-trial-fire"
				:class="{'small-content-fire' : small, 'default-content-fire' : isDefault}"
			>
			<img
				src="/images/price/clock.png"
				class="price-trial-icon-clock"
				:class="{'small-content-clock' : small, 'default-content-clock' : isDefault}"
			>
		</div>
	</div>
</template>
<script>
export default {
	name: 'PriceTimeLimit',
	props:{
		small: {
			type: Boolean,
		},
		isDefault: {
			type: Boolean,
		}
	}
}
</script>

<style lang="scss" scoped>
@media (min-width: 1600px) {
  .default-content{
	width: 1195px !important;

	&-price-content{
		max-width: 610px !important;
		width: 100% !important;
		padding: 40px !important;
	}

	&-title{
		font-size: 28px  !important;
	}
	&-description{
		font-size: 18px  !important;
	}

	&-fire{
		width: 200px !important;
		height: 200px !important;
		top: 30% !important;
		left: 60% !important;
	}

	&-clock{
		width: 312px !important;
		height: 254px !important;
		left: 75% !important;
		bottom: 0% !important;
	}
	&-eclipse{
	box-shadow: 0 0 230px 20px rgba(237, 161, 12, 0.8) !important;
	width: 441px !important;
	height: 221px !important;
	border-radius: 50% !important;
	top: 60% !important;
	left: 90% !important;
	}
	}
	.price-trial-button:focus{
	outline: none;
	}
  .price-trial-button{
	padding: 16px 32px !important;
	font-size: 16px !important;

  }
  .price-trial-content{
	gap: 12px !important;
  }
}

.price-trial{
	background-color: #EDEDED;
	border-radius: 24px;
	display: flex;
	position: relative;
	overflow: hidden;
}
.small-content{
  width: 100%;
  max-height: 106px;
  height: 100%;
  z-index: 10001;


	&-price-content{
	max-width: 932px;
	width: 100%;
	padding: 24px;
	}
  &-title{
	font-size: 20px ;
  }
  &-description{
	font-size: 14px ;
  }

  &-fire{
	width: 74px;
	height: 74px;
	top: 29%;
	left: 85%;
  }

  &-clock{
	width: 121px;
	height: 91px;
	left: 90%;
	bottom: 0%;
  }
  &-eclipse{
	box-shadow: 0 0 230px 20px rgba(237, 161, 12, 0.8);
	width: 401px;
	height: 401px;
	border-radius: 50%;
	top: 60%;
	left: 85%;
  }

}
.default-content {
  width: 900px;
  &-price-content{
	max-width: 610px;
	width: 100%;
	padding: 40px;
  }

  &-title{
	font-size: 20px ;
  }
  &-description{
	font-size: 16px ;
  }

  &-fire{
	width: 108px;
	height: 108px;
	top: 54%;
	left: 67%;
  }

  &-clock{
	width: 212px;
	height: 154px;
	left: 75%;
	bottom: 0%;
  }
  &-eclipse{
	box-shadow: 0 0 180px 20px rgba(237, 161, 12, 0.8);
	width: 251px;
	height: 121px;
	border-radius: 50%;
	top: 60%;
	left: 90%;
  }
}



.price-trial-content{

	color: black;
	display: flex;
	flex-direction: column;
	gap: 6px;

}
.price-trial-title{
	font-weight: 600;
	line-height:24px;
}
.price-trial-description{
	line-height: 27px;
}

.price-trial-button-group{
	display: flex;
	gap: 24px;
	align-items: center;
	margin-top: 12px;
}

.price-trial-button{
	background: linear-gradient(92deg, #15368C 0%, #215DF8 100%);
	border-radius: 8px;
	padding: 13px 28px;
	font-size: 14px;
	color: white;

}
.price-trial-button:focus{
  outline: none !important;
}


.price-trial-icons-content{
}

.price-trial-eclipse{
	background-color: #EDA10C;

	position: absolute;

	transform: translate(-50%, -50%);
	z-index: 2;
	filter: blur(40px);
}

.price-trial-icon-clock{
	position: absolute;
	z-index: 7;

}

.price-trial-fire{
	position: absolute;
	z-index: 7;

}

</style>
