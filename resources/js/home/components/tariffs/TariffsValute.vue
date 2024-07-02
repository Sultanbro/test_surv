<template>
	<a
		v-click-outside="hidePopup"
		:class="{'jTariffs-valute-active': active}"
		class="jTariffs-valute"
		href="javascript:void(0)"
		@click="active = !active"
	>
		{{ valute || '₽' }}
		<div
			class="jTariffs-valute-popup"
		>
			<div
				class="jTariffs-valute-wrapper"
				@click="onSelectValute('₽')"
			>
				<div class="jTariffs-valute-button">₽</div>
			</div>
			<div
				class="jTariffs-valute-wrapper"
				@click="onSelectValute('₸')"
			>
				<div class="jTariffs-valute-button">₸</div>
			</div>
			<div
				class="jTariffs-valute-wrapper"
				@click="onSelectValute('$')"
			>
				<div class="jTariffs-valute-button">$</div>
			</div>
		</div>
	</a>
</template>

<script>
export default {
	props: {
		lang: {
			type: String,
			default: 'ru'
		},

	},
	data() {
		return {
			active: false,
			valute: '₽'
		}
	},
	computed: {
	},
	methods: {
		hidePopup() {
			if (this.active) this.active = false
		},
		onSelectValute(valute) {
			this.valute = valute
			this.$emit('selected', valute)
		}
	}
}
</script>

<style lang="scss">
@import '../../assets/scss/app.variables.scss';

.jTariffs-valute {
  display: inline-block;
  padding: 0.25rem 0;
  margin: 0.5rem 0;
  position: relative;
  font-size: 1.4rem;
  color: #000;
  text-decoration: none;

  &:after {
    content: '';
    display: inline-block;
    width: 0.5rem;
    height: 0.3125rem;
    vertical-align: middle;
    background-image: url("../../assets/img/select-arrow.svg");
    background-repeat: no-repeat;
  }
}

.jTariffs-valute-popup {
  display: none;
  width: auto;
  position: absolute;
  top: 100%;
  right: -8px;
  background: #fff;
  box-shadow: 0 0.125rem 0.1875rem rgba(0, 0, 0, 0.5);
  border-radius: 0.8rem;
}

.jTariffs-valute-active {
  .jTariffs-valute-popup {
    display: block;
  }
}

.jTariffs-valute-wrapper {
  display: flex;
  align-items: center;
  padding: 0.5rem 0;
}

.jTariffs-valute-button {
  width: 54px;
  text-align: center;
  font-size: 1.125rem;
}

@media screen and (min-width: $large) {
  .jTariffs-valute {
    &:after {
      transform: scale(2);
      width: 0.25rem;
      height: 0.2125rem;
    }
  }

  .jTariffs-valute-button {
    width: 108px;
  }

  .jTariffs-valute-popup {
    right: -16px;
  }
}
</style>
