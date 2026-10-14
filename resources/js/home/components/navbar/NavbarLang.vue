<template>
	<a
		v-click-outside="hidePopup"
		:class="{'jNav-menu-lang-active': active}"
		class="jNav-menu-lang"
		href="javascript:void(0)"
		v-bind="$attrs"
		@click="togglePopup"
		@mouseover="showPopup"
		@mouseleave="hidePopup"
	>
		{{ isShowCurrency ? showCurrency : lang.toUpperCase() }}
		<div
			class="jNav-menu-lang-popup"
		>
			<div
				class="jNav-menu-lang-wrapper"
				@click="$emit('change', 'ru')"
			>
				<div class="jNav-menu-lang-button">{{ options.russia }}</div>
				<img
					v-if="isShowFlag"
					:src="require('../../assets/img/rus.png').default"
					alt="ru"
					class="jNav-menu-lang-img"
				>
			</div>
			<div
				class="jNav-menu-lang-wrapper"
				@click="$emit('change', 'kz')"
			>
				<div class="jNav-menu-lang-button">{{ options.kazakh }}</div>
				<img
					v-if="isShowFlag"
					:src="require('../../assets/img/kz.png').default"
					alt="kz"
					class="jNav-menu-lang-img"
				>
			</div>
			<div
				class="jNav-menu-lang-wrapper"
				@click="$emit('change', 'en')"
			>
				<div class="jNav-menu-lang-button">{{ options.english }}</div>
				<img
					v-if="isShowFlag"
					:src="require('../../assets/img/eng.png').default"
					alt="en"
					class="jNav-menu-lang-img"
				>
			</div>
		</div>
	</a>
</template>

<script>
export default {
	props: {
		lang: {
			type: String,
			default: ''
		},
		isShowFlag: {
			type: Boolean,
			default: false
		},
		isShowCurrency: {
			type: Boolean,
			default: false
		},
		options: {
			type: Object,
			default() {
				return {
					'russia': 'Русский',
					'english': 'English',
					'kazakh': 'Қазақ',
				}
			}
		}
	},
	data() {
		return {
			active: false,
			timeout: null,
		}
	},
	computed: {
		showCurrency() {
			return this.lang.toUpperCase() === 'RU' ? '₽' : '₸'
		},
	},
	methods: {
		hidePopup() {
			this.timeout = setTimeout(() => {
				this.active = false
			}, 300)
		},
		showPopup(){
			clearTimeout(this.timeout)
			this.active = true
		},
		togglePopup(){
			clearTimeout(this.timeout)
			this.active = !this.active
		}
	}
}
</script>

<style lang="scss">
@import '../../assets/scss/app.variables.scss';

.jNav-menu-lang {
  display: inline-block;
  padding: 0.25rem 0;
  margin: 0.5rem 0;
  position: relative;
  font-size: 0.9rem;
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

.jNav-menu-lang-popup {
  display: none;
  width: auto;
	padding: 0.5rem;
  position: absolute;
  top: 115%;
  left: 0;
  background: #fff;
  box-shadow: 0 0.125rem 0.1875rem rgba(0, 0, 0, 0.5);
  border-radius: 0.8rem;
}

.jNav-menu-lang-active {
  .jNav-menu-lang-popup {
    display: block;
  }
}

.jNav-menu-lang-wrapper {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 13px;
  padding: 3px;
}

// .jNav-menu-lang-button {
//   width: 54px;
//   margin-right: 1rem;
// }

@media screen and (min-width: $large) {
  .jNav-menu-lang-button {
    width: 108px;
  }

  .jNav-menu-lang-popup {
    right: -80px;
  }
}

.jNav-menu-lang-img {
  width: 24px;
  height: 24px;
}

@media screen and (min-width: $large) {
  .jNav-menu-lang {
    &:after {
      transform: scale(2);
      width: 0.25rem;
      height: 0.2125rem;
    }
  }
  .jNav-menu-lang-img {
    width: 48px;
    height: 48px;
  }
}
</style>
