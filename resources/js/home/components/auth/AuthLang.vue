<template>
	<div
		v-click-outside="hidePopup"
		class="AuthLang"
		:class="{ 'AuthLang_active': active }"
		@click="togglePopup"
		@mouseover="showPopup"
		@mouseleave="hidePopup"
	>
		<div class="AuthLang-selected">
			<AuthLangItem
				:item="selected"
			>
				<template #icon>
					<IconCaret />
				</template>
			</AuthLangItem>
		</div>
		<div class="AuthLang-popup">
			<div class="AuthLang-popupContent">
				<AuthLangItem
					v-for="opt, index in options"
					:key="index"
					:item="opt"
					@click.native="$emit('input', opt.value)"
				>
					<template
						v-if="opt.value === value"
						#icon
					>
						<IconCheck />
					</template>
				</AuthLangItem>
			</div>
		</div>
	</div>
</template>

<script>
import AuthLangItem from './AuthLangItem.vue'
import IconCaret from './IconCaret.vue'
import IconCheck from './IconCheck.vue'

import flagEn from '../../assets/img/eng.png'
import flagKz from '../../assets/img/kz.png'
import flagRu from '../../assets/img/rus.png'

export default {
	name: 'AuthLang',
	components: {
		AuthLangItem,
		IconCaret,
		IconCheck,
	},
	props: {
		value: {
			type: String,
			default: 'ru',
		},
	},
	data() {
		return {
			active: false,
			timeout: null,
			options: [
				{
					flag: flagRu,
					label: 'Ru',
					value: 'ru',
				},
				{
					flag: flagKz,
					label: 'Kz',
					value: 'kz',
				},
				{
					flag: flagEn,
					label: 'En',
					value: 'en',
				},
			]
		};
	},
	computed: {
		selected(){
			return this.options.find(opt => opt.value === this.value)
		},
	},
	methods: {
		hidePopup() {
			this.timeout = setTimeout(() => {
				this.active = false;
			}, 300);
		},
		showPopup() {
			clearTimeout(this.timeout);
			this.active = true;
		},
		togglePopup() {
			clearTimeout(this.timeout);
			this.active = !this.active;
		},
	},
};
</script>

<style lang="scss">
@import "../../assets/scss/app.variables.scss";

.AuthLang{
	position: relative;

	&-selected{
		padding: 4px 0;
		position: relative;
		z-index: 2;

		border-radius: 8px;
		background-color: #fff;
	}
	&-popup{
		padding-top: 4px;

		position: absolute;
		z-index: 1;
		top: 100%;
		left: 0;

		transition: 0.3s;
		opacity: 0;
		visibility: hidden;
		transform: translateY(-20px);
	}
	&-popupContent{
		padding: 8px 0;
		border-radius: 8px;
		background-color: #fff;
	}
	&_active{
		.AuthLang{
			&-selected{
				.AuthLangItem-icon{
					transform: rotate(180deg);
				}
			}
			&-popup{
				opacity: 1;
				visibility: visible;
				transform: none;
			}
		}
	}

	&:hover{
		.AuthLang{
			&-selected{
				color: #0C50FF;
				.AuthIcon-fill{
					fill: #0C50FF;
				}
			}
		}
	}
}
</style>
