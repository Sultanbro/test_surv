<template>
  <footer id="jFooter">
    <div class="section-content">
      <div class="jFooter-cols">
        <div class="jFooter-col">
          <a class="jNav-logo" href="">
            <img alt="" class="jNav-logo-img" :src="require('../../assets/img/footer-logo.svg').default">
          </a>
        </div>
        <div class="jFooter-col">
          <ul class="jFooter-menu-items">
            <li class="jFooter-menu-item">
              <FooterLink :lang="lang" href="#prices" text="prices"/>
            </li>
            <li class="jFooter-menu-item">
              <FooterLink :lang="lang" href="#reviews" text="reviews"/>
            </li>
            <li class="jFooter-menu-item">
              <FooterLink :lang="lang" href="#features" text="features"/>
            </li>
          </ul>
          <p class="jFooter-sub">{{ $lang(lang, 'footer-subscribe') }}</p>
          <div class="jFooter-social">
            <a class="jFooter-social-link jFooter-social-tg" href="#"></a>
            <a class="jFooter-social-link jFooter-social-ig" href="#"></a>
            <a class="jFooter-social-link jFooter-social-yt" href="#"></a>
          </div>
        </div>
        <div class="jFooter-col">
          <ul class="jFooter-menu-items">
            <li class="jFooter-menu-item">
              <FooterLink :lang="lang" href="#" text="footer-docs"/>
            </li>
            <li class="jFooter-menu-item">
              <FooterLink :lang="lang" href="#" text="footer-info"/>
            </li>
            <li class="jFooter-menu-item">
              <FooterLink :lang="lang" href="#" text="footer-lic"/>
            </li>
          </ul>
<!--          <p>{{ $lang(lang, 'footer-docs') }}</p>-->
<!--          <p>{{ $lang(lang, 'footer-info') }}</p>-->
<!--          <p>{{ $lang(lang, 'footer-lic') }}</p>-->
          <p class="jFooter-sub">{{ $lang(lang, 'footer-pay') }}</p>
          <div class="jFooter-pay">
            <a class="jFooter-pay-link jFooter-pay-mir" href="#"></a>
            <a class="jFooter-pay-link jFooter-pay-visa" href="#"></a>
            <a class="jFooter-pay-link jFooter-pay-ym" href="#"></a>
          </div>
        </div>
        <div class="jFooter-col">
          <p>{{ $lang(lang, 'footer-sellcenter') }}</p>
          <p class="jFooter-contact">8 777 788 08 00</p>
          <p class="jFooter-contact">8 495 136 42 82</p>
          <p class="jFooter-contact">sales@jobtron.com</p>
          <p>{{ $lang(lang, 'footer-sellcenter') }}</p>
          <p class="jFooter-contact">sales@jobtron.com</p>
        </div>
      </div>
    </div>
    <div class="jFooter-separator"></div>
    <div class="section-content">
      <div class="jFooter-bottom">
        <div class="jFooter-rules">
          <a href="#">{{ $lang(lang, 'footer-conf') }}</a>
        </div>
        <div class="jFooter-copy">
          <p>{{ '© ' + year + ' ' + $lang(lang, 'footer-copy') }}</p>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
import FooterLink from '../footer/FooterLink'
export default {
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

  a {
    color: #fff;
    text-decoration: none;

    &:hover {
      text-decoration: underline;
    }
  }

  li {
    list-style: none;
  }
}

.jFooter-separator {
  border-top: 0.0625rem solid white;
}

.jFooter-cols {
  padding-bottom: 2rem;
}

.jFooter-menu-items {
  padding: 0;
}

.jFooter-sub {
  margin: 2rem 0 0.5rem;
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
  background-image: url("../../assets/img/footer-tg.svg");
}

.jFooter-social-ig {
  width: 1.25rem;
  height: 1.25rem;
  background-image: url("../../assets/img/footer-ig.svg");
}

.jFooter-social-yt {
  width: 2rem;
  height: 2rem;
  background-image: url("../../assets/img/footer-yt.svg");
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
  display: flex;
  order: 2;
  gap: 1rem;

  a {
    display: block;
  }
}

@media screen and (min-width $small) {
  .jFooter-cols {
    display: flex;
    flex-flow: row wrap;
    align-items: flex-start;
  }
}

.jFooter-col {
  //flex: 0 0 50%;
  flex: 0 0 25%;
}

.jFooter-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  white-space: nowrap;
}

.jFooter-copy {
  order: 1;
}

@media screen and (min-width $medium) {
  .jFooter-cols {
    flex-flow: row nowrap;
  }
}
</style>
