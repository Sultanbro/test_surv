<template>
	<footer id="jFooter">
		<div class="section-content">
			<div class="jFooter-cols">
				<div class="jFooter-col">
					<a
						class="jFooter-logo"
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
						<!--            <li class="jFooter-menu-item">-->
						<!--              <FooterLink-->
						<!--                  :lang="lang"-->
						<!--                  href="#reviews"-->
						<!--                  text="reviews"-->
						<!--              />-->
						<!--            </li>-->
						<li class="jFooter-menu-item">
							<FooterLink
								:lang="lang"
								href="#features"
								text="features"
							/>
						</li>
					</ul>
					<p class="jFooter-sub-social">
						{{ $lang(lang, 'footer-subscribe') }}
					</p>
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
					<p class="jFooter-support">
						{{ $lang(lang, 'footer-support') }}
					</p>
					<p class="jFooter-contact">
						<a
							class="jFooter-link"
							href="mailto:support@jobtron.com"
						>support@jobtron.com</a>
					</p>
				</div>
				<div class="jFooter-col">
					<p class="jFooter-sub-pay">
						<RouterLink
							class="jFooter-link"
							to="/payments"
						>
							{{ $lang(lang, 'footer-pay') }}
						</RouterLink>
					</p>
					<div class="jFooter-pay">
						<a
							class="jFooter-pay-link jFooter-pay-visa"
							href="javascript:void(0)"
						/>
						<a
							class="jFooter-pay-link jFooter-pay-mc"
							href="javascript:void(0)"
						/>
						<a
							class="jFooter-pay-link jFooter-pay-mir"
							href="javascript:void(0)"
						/>
						<a
							class="jFooter-pay-link jFooter-pay-uniseller"
							href="javascript:void(0)"
						/>
						<a
							class="jFooter-pay-link jFooter-pay-ym"
							href="javascript:void(0)"
						/>
					</div>
					<div class="jFooter-docs">
						<RouterLink
							class="jFooter-rules-link jFooter-link"
							to="/contract-offer"
						>
							{{ $lang(lang, 'contract-offer') }}
						</RouterLink>
						<RouterLink
							class="jFooter-rules-link jFooter-link"
							to="/site-use-agreement"
						>
							{{ $lang(lang, 'site-agreement') }}
						</RouterLink>
						<RouterLink
							class="jFooter-rules-link jFooter-link"
							to="/personal-data"
						>
							{{ $lang(lang, 'personal-data') }}
						</RouterLink>
						<RouterLink
							class="jFooter-rules-link jFooter-link"
							to="/privacy-policy"
						>
							{{ $lang(lang, 'privacy-policy') }}
						</RouterLink>
						<RouterLink
							class="jFooter-rules-link jFooter-link"
							to="/contacts"
						>
							{{ $lang(lang, 'contacts') }}
						</RouterLink>
					</div>
				</div>
			</div>
		</div>
		<div class="jFooter-separator" />
		<div class="section-content final-footer">
			<div class="jFooter-bottom">
				<div class="jFooter-copy">
					<p class="jFooter-copy-right">
						{{ '© ' + year + ' ' + $lang(lang, 'footer-copy') }}
					</p>
				</div>
			</div>
		</div>
		<div
			v-if="!cookieViewed"
			class="cookie-alert"
		>
			<span>Мы используем файлы cookie на нашем веб-сайте для поддержки технических функций, которые улучшают ваш
				пользовательский опыт. Мы также собираем анонимные аналитические данные, как описано в нашей
				<a
					href="/docs/privacy-policy.pdf"
					target="_blank"
				>Политике конфиденциальности</a></span>
			<button
				class="jButton"
				@click="saveCookieView"
			>
				Понятно
			</button>
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
	data() {
		return {
			year: new Date().getFullYear(),
			cookieViewed: false
		}
	},
	computed: {
		lang() {
			return this.$root.$data.lang
		}
	},
	mounted(){
		this.cookieViewed = localStorage.getItem('cookieViewed');
	},
	methods:{
		saveCookieView(){
			if(!this.cookieViewed){
				this.cookieViewed = !this.cookieViewed;
				localStorage.setItem('cookieViewed', true);
			}
		}
	}
}
</script>

<style lang="scss">
@import '../../assets/scss/app.variables.scss';
.docs-content{
	max-width: 800px;
	margin: 0 auto 50px auto;
	width: 100%;
	h4{
		text-align: center;
	}
	ul{
		margin-bottom: 2rem;

		&.no-margin{
			margin: 0 !important;
			marign-block-end: 0;
			marign-block-start: 0;
		}
	}
	.mt-0{
		margin-top: 0!important;
	}
	.mb-0{
		margin-bottom: 0!important;
	}
}
.cookie-alert{
	position: fixed;
	bottom: 20px;
	left: 20px;
	max-width: 700px;
	padding: 20px;
	background-color: #43a9fe;
	color: #fff;
	border-radius: 10px;
	z-index: 1001;
	display: flex;
	align-items: center;
	a{
		color: #fff;
	}
	.jButton{
		margin-left: 30px;
		background-color: #fff;
		color: #43a9fe;
		font-size: 14px;
		&:hover{
			background-color: #fafafa;
			color: #337fc0;
		}
	}
}

#jFooter {
  width: 100%;
  padding-top: 2rem;
  background: #424d53;
  color: #fff;
}

.jFooter-docs{
	a{
		display: block;
		margin-bottom: 5px;
	}
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

.jFooter-copy-right{
	margin: 10px 0;
}

@media (max-width: 850px) {
	.jFooter-docs{
		a{
			text-align: center;
		}
	}
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

  .jFooter-sub-social {
    margin: 2rem 0 0.5rem;
  }

  .jFooter-bottom {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .jFooter-copy-right {
    text-align: center;
  }

  .jFooter-rules {
    font-size: 0.875rem;
    display: flex;
    flex-direction: column;
    text-align: center;
  }

  .jFooter-rules-link {
    margin-bottom: 0.7rem;
  }
}

@media screen and (min-width: 850px) {
  .jFooter-rules {
    display: flex;
    order: 2;
    gap: 1rem;
    padding-left: 1rem;
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
	margin-bottom: 1.5rem;
}

.jFooter-pay-link {
  display: inline-block;
  background-size: cover;
  opacity: 0.8;
  &:hover{
	opacity: 1;
  }
}

.jFooter-pay-mir {
  width: 3.4375rem;
  height: 1.375rem;
  background-image: url("../../assets/img/footer-mir.png");
}

.jFooter-pay-visa {
  width: 3.0625rem;
  height: 1.375rem;
  background-image: url("../../assets/img/footer-visa.png");
}

.jFooter-pay-mc {
  width: 2.4375rem;
  height: 1.375rem;
  background-image: url("../../assets/img/footer-mc.png");
}

.jFooter-pay-uniseller {
  width: 2.5rem;
  height: 1.375rem;
  background-image: url("../../assets/img/footer-uniseller.png");
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
	width: 100%;
    justify-content: space-around;
    padding-bottom: 2rem
  }

  .jFooter-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    white-space: wrap;
    text-align: center;
  }


  .jFooter-cols > .jFooter-col:nth-child(1) {
    display: flex;
    align-self: center;
  }

  .jFooter-copy {
    order: 1;
  }

  .jFooter-logo {
    padding-right: 1rem;
  }

  .jFooter-sub-social {
    margin: 4rem 0 0.5rem;
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
