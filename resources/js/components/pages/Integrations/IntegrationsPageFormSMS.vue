<template>
	<SideBar
		width="50%"
		title="СМС интеграция"
		:open="open"
		@close="$emit('close', $event)"
	>
		<div class="IntegrationsPageForm IntegrationsPageFormSMS">
			<div class="IntegrationsPageFormSMS-header">
				<img
					src="https://util.1c-bitrix.kz/upload/resize_cache/bx24vendor/dca/170_170_1/app2v3.png"
					alt=""
					class="IntegrationsPageFormSMS-logo"
				>
				<div class="IntegrationsPageFormSMS-headerContent">
					<div class="IntegrationsPageFormSMS-title">
						U-Marketing.org СМС рассылка по России, Казахстану и по всему миру
					</div>
					<div class="IntegrationsPageFormSMS-subtitle">
						Интеграция SMS от u-marketing.org позволит Вам совершать отправку смс сообщений из созданных уведомлений своим сотрудникам, а также для подписания договоров со своими новыми сотрудниками.
					</div>
				</div>
			</div>

			<ProfileTabs
				v-model="tab"
				:tabs="tabs"
			>
				<template #tab(0)>
					<div class="py-4">
						<p>Установите приложение из каталога.</p>
						<ol>
							<li>Пройдите регистрацию на веб-сервисе https://cp.u-marketing.org/</li>
							<li>Создайте интеграцию</li>
							<li>Скопируйте код интеграции</li>
							<li>Скопируйте API ключ</li>
							<li>Вставьте полученный API ключ и код интеграции в форму</li>
						</ol>
						<p>Пользуйтесь СМС рассылкой для своих клиентов ;)</p>
					</div>
					<div
						id="blueimp-gallery"
						class="blueimp-gallery"
						aria-label="image gallery"
						aria-modal="true"
						role="dialog"
					>
						<div
							class="slides"
							aria-live="polite"
						/>
						<h3 class="title" />
						<a
							class="prev"
							aria-controls="blueimp-gallery"
							aria-label="previous slide"
							aria-keyshortcuts="ArrowLeft"
						/>
						<a
							class="next"
							aria-controls="blueimp-gallery"
							aria-label="next slide"
							aria-keyshortcuts="ArrowRight"
						/>
						<a
							class="close"
							aria-controls="blueimp-gallery"
							aria-label="close"
							aria-keyshortcuts="Escape"
						/>
						<a
							class="play-pause"
							aria-controls="blueimp-gallery"
							aria-label="play slideshow"
							aria-keyshortcuts="Space"
							aria-pressed="false"
							role="button"
						/>
						<ol class="indicator" />
					</div>
					<div
						id="links"
						ref="links"
						class="IntegrationsPageFormSMS-images"
						@click.prevent="onGallery"
					>
						<a
							href="https://util.1c-bitrix.kz/upload/bx24vendor/0c9/1.jpg"
							title="Banana"
						>
							<img
								src="https://util.1c-bitrix.kz/upload/bx24vendor/0c9/1.jpg"
								alt="Banana"
							>
						</a>
						<a
							href="https://util.1c-bitrix.kz/upload/bx24vendor/d60/2.jpg"
							title="Banana"
						>
							<img
								src="https://util.1c-bitrix.kz/upload/bx24vendor/d60/2.jpg"
								alt="Banana"
							>
						</a>
						<a
							href="https://util.1c-bitrix.kz/upload/bx24vendor/15f/3.jpg"
							title="Banana"
						>
							<img
								src="https://util.1c-bitrix.kz/upload/bx24vendor/15f/3.jpg"
								alt="Banana"
							>
						</a>
						<a
							href="/images/sms-4.png"
							title="Banana"
						>
							<img
								src="/images/sms-4.png"
								alt="Banana"
							>
						</a>
					</div>
				</template>
				<template #tab(1)>
					<div class="py-4">
						<!-- Адрес -->
						<label class="IntegrationsPageForm-row">
							<span class="IntegrationsPageForm-label">
								Код интеграции
							</span>
							<span class="IntegrationsPageForm-control">
								<b-form-input
									v-model="apiId"
									type="text"
								/>
							</span>
						</label>
						<!-- ключ -->
						<label class="IntegrationsPageForm-row">
							<span class="IntegrationsPageForm-label">
								API ключ
							</span>
							<span class="IntegrationsPageForm-control">
								<b-form-input
									v-model="apiKey"
									type="text"
								/>
							</span>
						</label>
					</div>
				</template>
			</ProfileTabs>
		</div>
		<template #footer>
			<b-button
				variant="primary"
				@click="onSave"
			>
				Сохранить
			</b-button>
		</template>
	</SideBar>
</template>

<script>
import 'blueimp-gallery/css/blueimp-gallery.min.css';
import blueimp from 'blueimp-gallery/js/blueimp-gallery.js';

import SideBar from '@ui/Sidebar'
import ProfileTabs from '@ui/ProfileTabs.vue'

export default {
	name: 'IntegrationsPageFormSMS',
	components: {
		SideBar,
		ProfileTabs,
	},
	props: {
		open: Boolean,
		data: {
			type: Object,
			required: true
		}
	},
	data(){
		return {
			apiId: this.data.apiId,
			apiKey: this.data.apiKey,
			tabs: ['Инструкция', 'Установка'],
			tab: 0,
		}
	},
	mounted(){},
	beforeDestroy(){},
	methods: {
		onSave(){
			this.$emit('save', {
				apiId: this.apiId,
				apiKey: this.apiKey,
			})
		},
		onGallery($event){
			const instance = typeof blueimp.Gallery !== 'undefined' ? blueimp.Gallery : blueimp;
			var target = $event.target
			var link = target.src ? target.parentNode : target
			var options = { index: link, event: $event }
			var links = this.$refs.links.getElementsByTagName('a')
			instance(links, options)
		}
	}
}
</script>

<style lang="scss">
.IntegrationsPageFormSMS{
	&-header{
		display: flex;
		gap: 20px;
		margin-bottom: 20px;
	}
	// &-logo{}
	&-headerContent{
		flex: 1;
	}
	&-title{
		margin-bottom: 10px;
		font-size: 20px;
		// text-wrap: balance;
	}
	&-subtitle{
		font-size: 16px;
	}
	#blueimp-gallery{
		left: 60px;
		right: 60px;
	}
}
#links{
	a{
		display: table-cell;
		width: 25%;
	}
	img{
		width: 100%;
	}
}

</style>
