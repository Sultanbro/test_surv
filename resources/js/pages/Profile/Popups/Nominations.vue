<template>
	<div class="popup__content awards-profile mt-5">
		<div
			v-if="loading"
			class="spinner-container"
		>
			<div class="throbber-loader" />
		</div>
		<b-tabs
			v-if="nominations.length > 0 || certificates.length > 0 || accrual.length > 0"
			ref="tabis"
			v-model="tabIndex"
		>
			<div class="prev-next">
				<span
					class="prev"
					@click="tabIndex--"
				><i class="fa fa-chevron-left" /></span>
				<span
					class="next"
					@click="tabIndex++"
				><i class="fa fa-chevron-right" /></span>
			</div>
			<template v-if="nominations.length > 0">
				<b-tab
					v-for="award in nominations"
					:key="award.id"
					no-body
					:title="award.name"
					@click="activeteTab(award.description)"
				>
					<b-tabs class="inside-tabs">
						<b-tab
							no-body
							title="Мои номинации"
							active
						>
							<div class="certificates__title">
								Сертификатов:
								<span
									v-if="award.hasOwnProperty('my')"
									class="current"
								>{{ award.my.length }}</span>
								<span v-else>0</span>
							</div>

							<BRow v-if="award.hasOwnProperty('my')">
								<BCol
									v-for="item in award.my"
									:key="item.id"
									cols="12"
									md="4"
									lg="3"
									class="mb-5"
								>
									<div
										class="certificates__item"
										@click="modalShow(item, award.name, true)"
									>
										<img
											v-if="getFileName(item.previewPath)"
											:src="item.previewPath"
											alt="certificate image"
										>
										<img
											v-else-if="item.format !== 'pdf'"
											:src="item.tempPath"
											alt="certificate image"
										>
										<vue-pdf-embed
											v-else
											:source="item.tempPath"
										/>
									</div>
								</BCol>
							</BRow>
						</b-tab>

						<b-tab
							no-body
							title="Доступные номинации"
						>
							<div class="certificates__title">
								Сертификатов:
								<span
									v-if="award.hasOwnProperty('available')"
									class="current"
								>{{ nominationPublic(award.available).length }}</span>
								<span v-else>0</span>
							</div>

							<BRow v-if="award.hasOwnProperty('available')">
								<BCol
									v-for="item in nominationPublic(award.available)"
									:key="item.id"
									cols="12"
									md="4"
									lg="3"
									class="mb-5"
								>
									<div
										class="certificates__item"
										@click="modalShow(item, award.name, false)"
									>
										<img
											v-if="getFileName(item.previewPath)"
											:src="item.previewPath"
											alt="certificate image"
										>
										<img
											v-else-if="item.format !== 'pdf'"
											:src="item.tempPath"
											alt="certificate image"
										>
										<vue-pdf-embed
											v-else
											:source="item.tempPath"
										/>
									</div>
								</BCol>
							</BRow>
						</b-tab>
						<b-tab
							no-body
							title="Номинации других участников"
						>
							<BRow v-if="award.hasOwnProperty('other')">
								<BCol
									v-for="item in award.other"
									:key="item.id"
									cols="12"
									md="4"
									lg="3"
									class="mb-5"
								>
									<div
										class="certificates__item"
										@click="modalShow(item, award.name, false)"
									>
										<img
											v-if="getFileName(item.previewPath)"
											:src="item.previewPath"
											alt="certificate image"
										>
										<img
											v-else-if="item.format !== 'pdf'"
											:src="item.tempPath"
											alt="certificate image"
										>
										<vue-pdf-embed
											v-else
											:source="item.tempPath"
										/>
									</div>
								</BCol>
							</BRow>
							<div
								v-else
								class="certificates__title"
							>
								Ни один из участников еще не получил награды
							</div>
						</b-tab>
					</b-tabs>
				</b-tab>
			</template>

			<template v-if="certificates.length > 0">
				<b-tab
					v-for="award in certificates"
					:key="award.id"
					no-body
					:title="award.name"
					@click="activeteTab(award.description)"
				>
					<b-tabs class="inside-tabs">
						<b-tab
							no-body
							title="Мои сертификаты"
							active
						>
							<div class="certificates__title">
								Сертификатов:
								<span
									v-if="award.hasOwnProperty('my')"
									class="current"
								>{{ award.my.length }}</span>
								<span v-else>0</span>
								из
								<span
									v-if="award.hasOwnProperty('my')"
									class="all"
								>{{ award.available.length + award.my.length }}</span>
								<span
									v-else-if="award.hasOwnProperty('available')"
									class="all"
								>{{ award.available.length }}</span>
								<span
									v-else
									class="all"
								>0</span>
							</div>

							<BRow v-if="award.hasOwnProperty('my')">
								<BCol
									v-for="item in award.my"
									:key="item.id"
									cols="12"
									md="4"
									lg="3"
									class="mb-5"
								>
									<div
										class="certificates__item"
										@click="modalShow(item, award.name, true)"
									>
										<img
											v-if="getFileName(item.previewPath)"
											:src="item.previewPath"
											alt="certificate image"
										>
										<img
											v-else-if="item.format !== 'pdf'"
											:src="item.tempPath"
											alt="certificate image"
										>
										<vue-pdf-embed
											v-else
											:source="item.tempPath"
										/>
									</div>
								</BCol>
							</BRow>
						</b-tab>

						<b-tab
							no-body
							title="Доступные сертификаты за курсы"
						>
							<div class="certificates__title">
								Сертификатов:
								<span
									v-if="award.hasOwnProperty('available')"
									class="current"
								>{{ award.available.length }}</span>
								<span v-else>0</span>
								из
								<span
									v-if="award.hasOwnProperty('my')"
									class="all"
								>{{ award.available.length + award.my.length }}</span>
								<span
									v-else-if="award.hasOwnProperty('available')"
									class="all"
								>{{ award.available.length }}</span>
								<span
									v-else
									class="all"
								>0</span>
							</div>


							<BRow v-if="award.hasOwnProperty('available')">
								<BCol
									v-for="item in award.available"
									:key="item.id"
									cols="12"
									md="4"
									lg="3"
									class="mb-5"
								>
									<div
										class="certificate-available"
										@click="modalShow(item, award.name, false)"
									>
										<div class="certificates__item">
											<img
												v-if="getFileName(item.previewPath)"
												:src="item.previewPath"
												alt="certificate image"
											>
											<img
												v-else-if="item.format !== 'pdf'"
												:src="item.tempPath"
												alt="certificate image"
											>
											<vue-pdf-embed
												v-else
												:source="item.tempPath"
											/>
										</div>
										<div class="available-name">
											{{ item.course_name }}
										</div>
									</div>
								</BCol>
							</BRow>
						</b-tab>
						<b-tab
							no-body
							title="Сертификаты за курсы других участников"
						>
							<BRow v-if="award.hasOwnProperty('other')">
								<BCol
									v-for="item in award.other"
									:key="item.id"
									cols="12"
									md="4"
									lg="3"
									class="mb-5"
								>
									<div
										class="certificates__item"
										@click="modalShow(item, award.name, false)"
									>
										<img
											v-if="getFileName(item.previewPath)"
											:src="item.previewPath"
											alt="certificate image"
										>
										<img
											v-else-if="item.format !== 'pdf'"
											:src="item.tempPath"
											alt="certificate image"
										>
										<vue-pdf-embed
											v-else
											:source="item.tempPath"
										/>
									</div>
								</BCol>
							</BRow>
							<div
								v-else
								class="certificates__title"
							>
								Ни один из участников еще не получил награды
							</div>
						</b-tab>
					</b-tabs>
				</b-tab>
			</template>

			<template v-if="accrual.length > 0">
				<b-tab
					v-for="(award, index) in accrual"
					:key="index"
					class="accrual-tab"
					no-body
					:title="award.name"
					active
					@click="activeteTab(award.description)"
				>
					<BRow>
						<BCol
							v-for="(item, inx) in award.top"
							:key="item.id"
							cols="12"
							md="4"
						>
							<div
								class="nominations__item"
								:class="{green: inx === 1}"
							>
								<div class="nominations__item-title">
									{{ item.group }}
								</div>
								<div
									class="nominations__item-avatar"
									:class="'gift-' + (inx + 1)"
								>
									<img
										v-if="item.path.length > 10"
										:src="item.path"
										alt="profile avatar"
									>
									<img
										v-else
										src="images/avatar.png"
										alt="profile avatar"
									>
								</div>
								<div class="nominations__item-name">
									{{ item.name }} {{ item.last_name }}
								</div>
								<div class="nominations__item-subtext">
									{{ item.position }}
								</div>
								<div class="nominations__item-value">
									{{ item.total | splitNumber(item.total) }} ₸
								</div>
								<div class="nominations__item-wrapper">
									<div class="nominations__item-row">
										<p>KPI</p>
										<p> {{ item.kpi | splitNumber(item.kpi) }} ₸</p>
									</div>
									<div class="nominations__item-row">
										<p>БОНУСЫ</p>
										<p>{{ item.bonuses | splitNumber(item.bonuses) }} ₸</p>
									</div>
									<div class="nominations__item-row">
										<p>ОКЛАД</p>
										<p>{{ item.earnings | splitNumber(item.earnings) }} ₸</p>
									</div>
								</div>
							</div>
						</BCol>
					</BRow>
				</b-tab>
			</template>
		</b-tabs>
		<div v-else>
			<h4 class="not-awards">
				У Вас пока нет ни одной награды
			</h4>
		</div>

		<b-modal
			v-if="itemModal"
			v-model="modal"
			modal-class="awards-profile-modal-preview"
			centered
			size="lg"
			:title="itemModal.awardName"
			@show="onModalShow"
		>
			<img
				v-if="itemModal.format !== 'pdf'"
				:src="itemModal.tempPath"
				class="img-fluid"
				alt="certificate image"
			>
			<vue-pdf-embed
				v-else
				:source="itemModal.tempPath"
			/>
			<template #modal-footer>
				<BButton
					variant="primary"
					@click="modal = !modal"
				>
					Закрыть
				</BButton>
				<BButton
					v-if="itemModal.isMy"
					variant="success"
					@click="downloadImage(itemModal)"
				>
					Скачать
				</BButton>
			</template>
		</b-modal>
	</div>
</template>


<script>
import { mapActions } from 'pinia'
import { useProfileSalaryStore } from '@/stores/ProfileSalary'
import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed'
export default {
	name: 'PopupNominations',
	components: {
		VuePdfEmbed,
	},
	filters: {
		splitNumber: function (val) {
			return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ')
		}
	},
	props: {},
	data: function () {
		return {
			tabIndex: 0,
			loading: true,
			modal: false,
			itemModal: null,
			fields: [],
			awardTypes: null,
			nominations: [],
			certificates: [],
			accrual: [],
			format2type: {
				png: 'image/png',
				jpg: 'image/jpeg',
				pdf: 'application/pdf',
			}
		}
	},
	watch: {
		tabIndex(val) {
			const buttons = this.$refs.tabis.$refs.buttons
			buttons[val].$refs.link.$el.scrollIntoView({inline: 'end', behavior: 'smooth'})
		}
	},
	async mounted() {
		try {
			const {data} = await this.axios.get('/awards/type?key=nomination')
			if (data.data.data) this.nominations = data.data.data
		}
		catch (error) {
			console.error(error)
		}

		try {
			const {data} = await this.axios.get('/awards/type?key=certificate')
			this.certificates = data.data.data
		}
		catch (error) {
			console.error(error)
		}

		try {
			const {data} = await this.axios.get('/awards/type?key=accrual')
			this.accrual = data.data.data
		}
		catch (error) {
			console.error(error)
		}

		// установить награды как виденные
		this.setReadedAwards()

		this.loading = false
		if (this.nominations.length) {
			this.$emit('get-desc', this.nominations[0].description)
		}
		else if (this.certificates.length) {
			this.$emit('get-desc', this.certificates[0].description)
		}
		else if (this.accrual.length) {
			this.$emit('get-desc', this.accrual[0].description)
		}
		else{
			this.$emit('get-desc', 'Нет наград')
		}
	},
	methods: {
		...mapActions(useProfileSalaryStore, ['setReadedAwards']),
		nominationPublic(array){
			return array.filter(a => a.type === 'public')
		},
		activeteTab(text){
			this.$emit('get-desc', text)
		},
		downloadImage(data) {
			const xhr = new XMLHttpRequest()
			xhr.open('GET', data.tempPath, true)

			xhr.responseType = 'arraybuffer'

			xhr.onload = function () {
				const arrayBufferView = new Uint8Array(this.response)
				const options = {
					type: this.format2type[data.format]
				}
				const blob = new Blob([arrayBufferView], options)
				const imageUrl = window.URL.createObjectURL(blob)
				const a = document.createElement('a')
				a.href = imageUrl
				a.download = `${data.awardName}-${data.award_id | data.id}.${data.format}`
				document.body.appendChild(a)
				a.click()
				document.body.removeChild(a)
			}

			xhr.send()
		},
		modalShow(item, name, isMy) {
			this.itemModal = item
			this.itemModal.awardName = item.hasOwnProperty('course_name') ? item.course_name : name
			this.itemModal.isMy = isMy
			this.modal = !this.modal
		},
		onModalShow(){
			// костыль для модалки номинаций
			this.$nextTick(() => {
				const modal = document.querySelector('.awards-profile-modal-preview')
				if(!modal) return this.onModalShow()
				const outer = modal.parentNode
				outer.style.zIndex = 1040000
			})
		},
		getFileName(href){
			const url = new URL(href)
			return (url.pathname || '').split('/').reverse()[0]
		}
	}
}
</script>


<style lang="scss">
    .awards-profile-modal-preview{
        .img-fluid{
            width: 100% !important;
        }
        canvas{
            width: 100%!important;
            height: auto!important;
        }
    }
    .awards-profile {
        position: relative;
        .prev-next {
            position: absolute;
            top: 0;
            right: 0;
            height: 63px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-top: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
            background-color: #fff;
            width: 120px;
            span {
                width: 40px;
                height: 40px;
                border-radius: 50px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: 1px solid #ED2353;
                cursor: pointer;

                i {
                    font-size: 18px;
                    color: #ED2353;
                }

                &:hover {
                    background-color: #ED2353;

                    i {
                        color: #fff;
                    }
                }
            }

            .next {
                margin-left: 10px;
            }
        }

        .not-awards {
            font-size: 3.1rem;
            text-transform: uppercase;
            color: rgba(64, 64, 64, 0.4);
        }

        .nominations__item-avatar {
            img {
                width: 150px;
                height: 150px;
                border-radius: 50%;
            }
        }

        .tabs {
            .accrual-tab {
                margin-top: 30px;
                overflow-x: hidden;
                padding-bottom: 30px;
            }

            .nav-tabs {
                border-top: 1px solid #dee2e6;
                flex-wrap: nowrap;
                white-space: nowrap;
                overflow: hidden;
                margin-right: 120px;
                .nav-item {
                    .nav-link {
                        font-size: 2.1rem;
                        border-bottom: none;
                        margin-top: 0.1rem;
                        line-height: 2em;
                        color: #8D8D8D;
                        font-family: "Open Sans", sans-serif;
                        font-weight: 600;
                        transition: color 0.3s;
                        padding: 1.5rem 0 0 0;
                        cursor: pointer;
                        margin-right: 40px;
                        background-color: transparent;
                        border-top: 4px solid transparent;

                        &:hover {
                            border-color: transparent;
                            color: #ED2353;
                        }

                        &.active {
                            border-top: 4px solid #ED2353;
                            color: #ED2353;
                        }
                    }
                }
            }
        }

        .inside-tabs {
            .tab-pane {
                margin-top: 30px;
                overflow-x: hidden;
                padding-bottom: 30px;
            }

            .nav-tabs {
                border: none;
                margin-top: 2rem;

                .nav-item {
                    border-radius: 0 !important;
                }

                .nav-link {
                    margin-top: 0 !important;
                    line-height: 1.3 !important;
                    border: none !important;
                    color: #FFFFFF !important;
                    font-size: 1.4rem !important;
                    padding: 2rem !important;
                    background: #AEBEE0 !important;
                    transition: 0.3s !important;
                    border-radius: 0 !important;
                    margin-right: 30px !important;

                    &:hover {
                        background: #ED2353 !important;
                    }

                    &.active {
                        background: #ED2353 !important;
                    }
                }
            }
        }
        .img-fluid{
            width: 100% !important;
        }

        .nominations__wrapper {
            width: 100%;
            display: block;
        }

        .certificate-available{
            box-shadow: rgb(50 50 93 / 25%) 0px 13px 27px -5px, rgb(0 0 0 / 30%) 0px 8px 16px -8px;
            overflow: hidden;
            border: 1px solid #ddd;
            border-radius: 10px;
            cursor: pointer;
            .certificates__item{
                box-shadow: none;
                border-radius: 0;
                border: none;
            }
            .available-name{
                font-size: 14px;
                padding: 15px 10px;
                background-color: #AEBEE0;
                text-align: center;
                border-top: 1px solid #ddd;
                color: #fff;
                transition: 0.15s all ease;
            }
            &:hover{
                .available-name{
                    background-color: #ED2353;
                }
            }
        }

        .certificates__item {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            overflow: hidden;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: rgb(50 50 93 / 25%) 0px 13px 27px -5px, rgb(0 0 0 / 30%) 0px 8px 16px -8px;
            img {
                width: 100%;
                height: 200px;
                object-fit: cover;
            }
            canvas{
                width: 100%!important;
                height: 200px !important;
                object-fit: cover;
            }
        }

        .certificates__title {
            margin-top: 0;
        }

        @keyframes throbber-loader {
            0% {
                background: #dde2e7;
            }
            10% {
                background: #6b9dc8;
            }
            40% {
                background: #dde2e7;
            }
        }

        .spinner-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #fff;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .throbber-loader {
            animation: throbber-loader 2000ms 300ms infinite ease-out;
            background: #dde2e7;
            display: inline-block;
            position: relative;
            text-indent: -9999px;
            width: 0.9em;
            height: 1.5em;
            margin: 0 1.6em;
            z-index: 22;
        }

        .throbber-loader:before, .throbber-loader:after {
            background: #dde2e7;
            content: '\x200B';
            display: inline-block;
            width: 0.9em;
            height: 1.5em;
            position: absolute;
            top: 0;
        }

        .throbber-loader:before {
            -moz-animation: throbber-loader 2000ms 150ms infinite ease-out;
            -webkit-animation: throbber-loader 2000ms 150ms infinite ease-out;
            animation: throbber-loader 2000ms 150ms infinite ease-out;
            left: -1.6em;
        }

        .throbber-loader:after {
            -moz-animation: throbber-loader 2000ms 450ms infinite ease-out;
            -webkit-animation: throbber-loader 2000ms 450ms infinite ease-out;
            animation: throbber-loader 2000ms 450ms infinite ease-out;
            right: -1.6em;
        }
    }

</style>
