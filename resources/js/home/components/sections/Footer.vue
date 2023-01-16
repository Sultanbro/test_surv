<template>
  <footer id="jFooter">
    <div class="section-content">
      <div class="jFooter-cols">
        <div class="jFooter-col">
          <a
              class="jNav-logo"
              href="javascript:void(0)"
          >
            <img
                :src="require('../../assets/img/footer-logo.svg').default"
                alt=""
                class="jNav-logo-img"
            >
          </a>
        </div>
        <div class="jFooter-col">
          <ul class="jFooter-menu-items">
            <li class="jFooter-menu-item">
              <FooterLink
                  :lang="lang"
                  href="#prices"
                  text="prices"
              />
            </li>
            <li class="jFooter-menu-item">
              <FooterLink
                  :lang="lang"
                  href="#reviews"
                  text="reviews"
              />
            </li>
            <li class="jFooter-menu-item">
              <FooterLink
                  :lang="lang"
                  href="#features"
                  text="features"
              />
            </li>
          </ul>
          <p class="jFooter-sub-social">{{ $lang(lang, 'footer-subscribe') }}</p>
          <div class="jFooter-social">
            <a
                class="jFooter-social-link jFooter-social-yt"
                href="https://www.youtube.com/@adilkarimov8362"
                target="_blank"
            />
            <a
                class="jFooter-social-link jFooter-social-ig"
                href="javascript:void(0)"
            />
            <a
                class="jFooter-social-link jFooter-social-tg"
                href="javascript:void(0)"
            />
          </div>
        </div>
        <div class="jFooter-col">
          <p>{{ $lang(lang, 'footer-sellcenter') }}</p>
          <p class="jFooter-contact">
            <a
                class="jFooter-link"
                href="tel:87777880800"
            >8 777 788 08 00</a>
          </p>
          <p class="jFooter-contact">
            <a
                class="jFooter-link"
                href="tel:84951364282"
            >8 495 136 42 82</a>
          </p>
          <p class="jFooter-contact">
            <a
                class="jFooter-link"
                href="mailto:sales@jobtron.com"
            >sales@jobtron.com</a>
          </p>
          <p class="jFooter-support">{{ $lang(lang, 'footer-support') }}</p>
          <p class="jFooter-contact">
            <a
                class="jFooter-link"
                href="mailto:support@jobtron.com"
            >support@jobtron.com</a>
          </p>
        </div>
        <div class="jFooter-col">
          <p class="jFooter-sub-pay">{{ $lang(lang, 'footer-pay') }}</p>
          <div class="jFooter-pay">
            <a
                class="jFooter-pay-link jFooter-pay-mir"
                href="javascript:void(0)"
            />
            <a
                class="jFooter-pay-link jFooter-pay-visa"
                href="javascript:void(0)"
            />
            <a
                class="jFooter-pay-link jFooter-pay-ym"
                href="javascript:void(0)"
            />
          </div>
        </div>
      </div>
    </div>
    <div class="jFooter-separator"></div>
    <div class="section-content">
      <div class="jFooter-bottom">
        <div class="jFooter-rules">
          <a
              class="jFooter-rules-link jFooter-link"
              :href="require('../../assets/docs/Договор оферты.pdf').default"
              target="_blank"
          >{{ $lang(lang, 'contract-offer') }}</a>
          <a
              class="jFooter-rules-link jFooter-link"
              :href="require('../../assets/docs/Соглашение о пользовании сайтом.pdf').default"
              target="_blank"
          >{{ $lang(lang, 'site-agreement') }}</a>
          <a
              class="jFooter-rules-link jFooter-link"
              :href="require('../../assets/docs/Политика обработки персональных данных.pdf').default"
          >{{ $lang(lang, 'personal-data') }}</a>
          <a
              class="jFooter-rules-link jFooter-link"
              :href="require('../../assets/docs/Условия конфиденциальности.pdf').default"
          >{{ $lang(lang, 'privacy-policy') }}</a>

        </div>
        <div class="jFooter-copy">
          <p class="jFooter-copy-right">{{ '© ' + year + ' ' + $lang(lang, 'footer-copy') }}</p>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
import FooterLink from '../footer/FooterLink'

export default {
	name: 'SectionFooter',
	components: {
		FooterLink
	},
	computed: {
		lang() {
			return this.$root.$data.lang
		}
	},
	data() {
		return {
			year: new Date().getFullYear()
		}
	}
}
</script>

<style lang="scss">
@import '../../assets/scss/app.variables.scss';

#jFooter {
  width: 100%;
  padding-top: 2rem;
  background: #424d53;
  color: #fff;
}

.jFooter-link {
  color: #fff;
  text-decoration: none;

  &:hover {
    text-decoration: underline;
  }
}

.jFooter-separator {
  border-top: 0.0625rem solid white;
}

@media (max-width: $small) {
  .jFooter-cols {
    display: flex;
    padding-bottom: 2rem;
    flex-flow: column;
  }

  .jFooter-col .jFooter-menu-items,
  .jFooter-support,
  .jFooter-contact:nth-child(4) {
    display: none;
  }

  .jFooter-bottom {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 1rem;
  }

  .jFooter-copy-right {
    text-align: center;
  }
}

@media (max-width: $medium) {
  .jFooter-col {
    display: flex;
    flex-flow: column;
    align-items: center;
  }
}

.jFooter-menu-items {
  padding: 0;
}

.jFooter-menu-item {
  list-style: none;
}

.jFooter-sub-social {
  margin: 2rem 0 0.5rem;
}

.jFooter-sub-pay {
  margin-bottom: 0.3rem;
}

.jFooter-contact {
  margin: -0.25rem 0 0.75rem;
}

.jFooter-social {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.jFooter-social-link {
  display: inline-block;
  background-size: cover;
}

.jFooter-social-tg {
  width: 1.5rem;
  height: 1.5rem;
  background-image: url("../../assets/img/footer-tg.png");
}

.jFooter-social-tg:hover {
  background-image: url("../../assets/img/footer-tg-hover.png");
}

.jFooter-social-ig {
  width: 1.25rem;
  height: 1.25rem;
  background-image: url("../../assets/img/footer-ig.png");
}

.jFooter-social-ig:hover {
  background-image: url("../../assets/img/footer-ig-hover.png");
}

.jFooter-social-yt {
  width: 2rem;
  height: 2rem;
  background-image: url("../../assets/img/footer-yt.png");
}

.jFooter-social-yt:hover {
  background-image: url("../../assets/img/footer-yt-hover.png");
}

.jFooter-support {
  margin-top: 2rem;
}

.jFooter-pay {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding-top: 0.5rem;
}

.jFooter-pay-link {
  display: inline-block;
  background-size: cover;
}

.jFooter-pay-mir {
  width: 3.4375rem;
  height: 0.9375rem;
  background-image: url("../../assets/img/footer-mir.svg");
}

.jFooter-pay-visa {
  width: 2.9375rem;
  height: 0.9375rem;
  background-image: url("../../assets/img/footer-visa.svg");
}

.jFooter-pay-ym {
  width: 4.75rem;
  height: 1rem;
  background-image: url("../../assets/img/footer-ym.svg");
}

.jFooter-rules {
  font-size: 0.875rem;
}

@media screen and (min-width: $small) {
  .jFooter-cols {
    display: flex;
    //flex-flow: row;
    justify-content: space-around;
    padding-bottom: 2rem
  }
  //.jFooter-col {
  //  flex: 0 0 50%;
  //}

  .jFooter-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    white-space: nowrap;
    //flex-flow: column;
  }


  .jFooter-cols > .jFooter-col:nth-child(1) {
    display: flex;
    align-self: center;
  }

  .jFooter-rules {
    display: flex;
    order: 2;
    gap: 1rem;
  }

  .jFooter-copy {
    order: 1;
  }
}


@media screen and (min-width: $medium) {
  .jFooter-cols {
    flex-flow: row nowrap;
  }
  .jFooter-col {
    flex: 0 0 25%;
  }
  .jFooter-bottom {
    flex-flow: row nowrap;
  }
}
</style>
