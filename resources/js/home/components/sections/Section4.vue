<template>
	<section id="jSec4">
		<div class="section-content">
			<h2 class="jSec4-header jHeader">
				{{ $lang(lang, 's4-header') }}
			</h2>
			<p class="jSec4-subheader">
				{{ $lang(lang, 's4-subheader') }}
			</p>
			<ul
				v-show="isMedium"
				ref="items"
				class="jSec4-items"
			>
				<li
					:class="{'jSec4-highlight': isBlock1Highlight}"
					class="jSec4-item jSec4-item-1"
				>
					<span class="jSec4-item-title">{{ $lang(lang, 's4-b1-title') }}</span>
					<span class="jSec4-item-text">{{ $lang(lang, 's4-b1-text') }}</span>
				</li>
				<li
					:class="{'jSec4-highlight': isBlock2Highlight}"
					class="jSec4-item jSec4-item-2"
				>
					<span class="jSec4-item-title">{{ $lang(lang, 's4-b2-title') }}</span>
					<span class="jSec4-item-text">{{ $lang(lang, 's4-b2-text') }}</span>
				</li>
				<li
					:class="{'jSec4-highlight': isBlock3Highlight}"
					class="jSec4-item jSec4-item-3"
				>
					<span class="jSec4-item-title">{{ $lang(lang, 's4-b3-title') }}</span>
					<span class="jSec4-item-text">{{ $lang(lang, 's4-b3-text') }}</span>
				</li>
			</ul>
			<Hooper
				v-if="!isMedium"
				:auto-play="true"
				:infinite-scroll="true"
				:play-speed="3000"
			>
				<Slide>
					<div class="jSec4-item jSec4-item-1">
						<span class="jSec4-item-title">{{ $lang(lang, 's4-b1-title') }}</span>
						<span class="jSec4-item-text">{{ $lang(lang, 's4-b1-text') }}</span>
					</div>
				</Slide>
				<Slide>
					<div class="jSec4-item jSec4-item-2">
						<span class="jSec4-item-title">{{ $lang(lang, 's4-b2-title') }}</span>
						<span class="jSec4-item-text">{{ $lang(lang, 's4-b2-text') }}</span>
					</div>
				</Slide>
				<Slide>
					<div class="jSec4-item jSec4-item-3">
						<span class="jSec4-item-title">{{ $lang(lang, 's4-b3-title') }}</span>
						<span class="jSec4-item-text">{{ $lang(lang, 's4-b3-text') }}</span>
					</div>
				</Slide>
			</Hooper>
			<form
				action=""
				class="jSec4-form"
				@submit="onSubmit"
			>
				<p class="jSec4-footer">
					{{ $lang(lang, 's4-footer') }}
				</p>
				<div class="jSec4-form-inputs">
					<input
						v-model="name"
						class="input-text-input jSec4-form-inputs-field"
						:placeholder="$lang(lang, 's4-name')"
					>
					<input
						v-model="phone"
						class="input-text-input jSec4-form-inputs-field"
						:placeholder="$lang(lang, 's4-phone')"
					>
				</div>
				<button
					:disabled="isButtonDisabled"
					class="jButton"
					type="submit"
				>
					{{ callMeButtonContent }}
				</button>
			</form>
		</div>
	</section>
</template>

<script>
import {Hooper, Slide} from 'hooper'
import 'hooper/dist/hooper.css'
import axios from 'axios';

export default {
	name: 'SectionSection4',
	components: {
		Hooper,
		Slide,
	},
	data() {
		return {
			name: '',
			phone: '',
			isBlock1Highlight: false,
			isBlock2Highlight: false,
			isBlock3Highlight: false,
			isButtonDisabled: false,
			observer: null,
		}
	},
	computed: {
		lang() {
			return this.$root.$data.lang
		},
		isMedium() {
			return this.$viewportSize.width >= 1260
		},
		callMeButtonContent() {
			if (!this.isButtonDisabled) {
				if (this.lang === 'ru') {
					return 'Перезвоните мне'
				}
				if (this.lang === 'en') {
					return 'Call me back'
				}
				if (this.lang === 'kz') {
					return 'маған қайта қоңырау шал'
				}
			} else {
				if (this.lang === 'ru') {
					return 'Ожидайте звонка'
				}
				if (this.lang === 'en') {
					return 'Expect a call'
				}
				if (this.lang === 'kz') {
					return 'Қоңырау күтіңіз'
				}
			}
			return ''
		}
	},
	mounted() {
		this.observer = new IntersectionObserver(this.animate, {
			rootMargin: '30px',
			threshold: 1
		})
		this.observer.observe(this.$refs.items)
	},
	methods: {
		async onSubmit(e) {
			e.preventDefault()
			if (this.name && this.phone) {
				const formData = new FormData();
				formData.append('name', this.name);
				formData.append('phone', this.phone);
				const response = await axios.post('/create_lead', formData);
				if(response.data.data.result){
					this.isButtonDisabled = true
				}
				alert(`${this.name}, мы Вам перезвоним в ближайшее время.`)
				this.isButtonDisabled = false

			} else {
				alert('Заполните пожалуйста все поля.')
				this.isButtonDisabled = false
			}

			this.name = ''
			this.phone = ''
		},
		wait(ms) {
			return new Promise(resolve => {
				setTimeout(resolve, ms)
			})
		},
		async animate(entries/* , observer */) {
			if (!entries.some(entry => entry.isIntersecting)) return
			this.isBlock1Highlight = true
			await this.wait(350)
			this.isBlock1Highlight = false
			this.isBlock2Highlight = true
			await this.wait(350)
			this.isBlock2Highlight = false
			this.isBlock3Highlight = true
			await this.wait(350)
			this.isBlock3Highlight = false
			this.observer.disconnect()
		}
	}
}
</script>

<style lang="scss">
@import '../../assets/scss/app.variables.scss';

#jSec4 {
  width: 100%;
  padding-bottom: 2rem;

  .hooper {
    height: auto;
  }
}

.jSec4-header {
  width: fit-content;
  margin-left: auto;
  margin-right: auto;
  margin-bottom: 3rem;
  position: relative;

  &:before {
    content: '';
    display: block;
    width: 10.625rem;
    height: 6.625rem;
    position: absolute;
    z-index: -1;
    top: -2.5rem;
    left: -3rem;
    background-image: url("../../assets/img/s2-bg.svg");
  }
}

.jSec4-subheader {
  text-align: center;
  font-size: 1.125rem;
}

.jSec4-items {
  display: flex;
  flex-flow: row wrap;
  justify-content: stretch;
  padding: 0;
  margin: 0;
  list-style: none;
}

.jSec4-item {
  display: flex;
  flex-flow: column nowrap;
  align-items: center;
  flex: 0 0 100%;
  padding: 2rem;
  margin-bottom: 1.25rem;
  border-radius: 0.9375rem;

  position: relative;
  overflow: hidden;

  &:before {
    content: '';
    width: 0;
    height: 0;
    border-radius: 100vw;
    position: absolute;
    z-index: 1;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: rgba(#000, 0.25);
    transition: all 0.35s;
  }
}

.jSec4-item-title {
  font-weight: 700;
  font-size: 3.125rem;
  line-height: 2;
  position: relative;
  z-index: 5;

  transition: text-shadow 0.35s;
}

.jSec4-item-text {
  font-size: 1.125rem;
  line-height: 1.39;
  text-align: center;
  width: 16rem;
}

.jSec4-item-1 {
  background: #edf8ff;

  .jSec4-item-title {
    color: #72c6f9;
  }
}

.jSec4-item-2 {
  background: #fff9ea;

  .jSec4-item-title {
    color: #ffd86b;
  }
}

.jSec4-item-3 {
  background: #f3f0fc;

  .jSec4-item-title {
    color: #9082bb;
  }
}

.jSec4-highlight {
  .jSec4-item-title {
    text-shadow:
        0 0 1px #aaa,
        0 0 1rem #fff,
        0 0 1rem #fff,
        0 0 1rem grey,
        0 0 1rem grey;
  }
}

.jSec4-form,
.jSec4-form-inputs {
  display: flex;
  flex-flow: column;
  align-items: center;
  gap: 1rem;
  &-field{
	padding: 0.875rem 1.1875rem;
    outline: none;
    border: none;
    border-radius: 3.125rem;
	border:1px solid gray;
  }
}

@media screen and (min-width: $medium) {
  #jSec4 {
    padding-bottom: 5rem;
  }
  .jSec4-items {
    flex-flow: row nowrap;
    justify-content: stretch;
    align-items: stretch;
    gap: 1.25rem;
  }
  .jSec4-item {
    flex: 0 1 33%;
    padding: 2rem 3rem;
  }
  .jSec4-form {
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    gap: 1.25rem;
    padding-top: 1rem;

    .jButton {
      white-space: nowrap;
    }
  }
  .jSec4-form-inputs {
    display: flex;
    flex-flow: row nowrap;
    gap: 1.25rem;
  }
  .jSec4-footer {
    flex: 1 1 30%;
    font-size: 1.125rem;
    line-height: 1.39;
  }
}

@media (max-width: $small) {
  .jSec4-subheader {
    font-size: 16px;
  }

  .jSec4-footer {
    text-align: center;
  }
}
</style>
