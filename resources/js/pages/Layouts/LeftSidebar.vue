<template>
	<div class="header__left LeftSidebar">
		<!-- avatar  -->
		<div class="header__avatar">
			<PulseCard
				v-if="profileUnfilled"
				class="LeftSidebar-pulseAvatar"
				style="--card-pulse-interval: 5s"
			>
				<img
					:src="avatar"
					alt="avatar image"
					class="LeftSidebar-avatar"
				>
			</PulseCard>
			<img
				v-else
				:src="avatar"
				alt="avatar image"
				class="LeftSidebar-avatar"
			>
			<template v-if="['activist', 'ambassador'].includes(status)">
				<img
					class="LeftSidebar-refIcon"
					:src="status === 'activist' ? '/images/dist/second-place.png' : '/images/dist/first-place.png'"
					alt=""
				>
			</template>
			<template v-else-if="isDefaultAvatar">
				<i class="fas fa-upload LeftSidebar-upload" />
			</template>
			<!-- hover menu -->
			<div class="hover-avatar-area">
				<div class="header_menu_avatar">
					<div class="header__menu">
						<div
							v-scroll-lock="isCreatingProject"
							class="header__menu-project"
						>
							<img
								src="/images/dist/icon-settings.svg"
								alt="settings icon"
							>
							Проект: {{ project }}
							<div class="header__submenu">
								<a
									v-for="cabinet in cabinets"
									:key="cabinet.tenant_id"
									:href="cabinet.tenant_id === project ? 'javascript:void(0)' : `/login/${cabinet.tenant_id}`"
									class="header__submenu-item"
									:class="{'header__submenu-item_active': cabinet.tenant_id === project}"
								>
									{{ cabinet.tenant_id }} <i
										v-if="cabinet.owner === 1"
										aria-hidden="true"
										class="fa fa-star"
									/>
								</a>
								<div class="header__submenu-divider" />
								<div
									v-if="isOwner"
									class="header__submenu-item"
									@click="onNewProject"
								>
									Добавить проект
								</div>
							</div>
						</div>
						<div class="header__menu-title">
							Пользователь <a
								class="header__menu-userid"
								href="javascript:void(0)"
							>#{{ $laravel.userId }}</a>
							<p class="header__menu-email">
								{{ $laravel.email }}
							</p>
						</div>
						<router-link
							v-if="isOwner"
							to="/pricing"
							class="menu__item"
						>
							<img
								src="/images/dist/icon-settings.svg"
								alt="settings icon"
							>
							<span class="menu__item-title">Оплата</span>
						</router-link>
						<PulseCard v-if="profileUnfilled">
							<router-link
								to="/cabinet"
								class="menu__item"
							>
								<img
									src="/images/dist/icon-settings.svg"
									alt="settings icon"
								>
								<span class="menu__item-title">Настройки</span>
							</router-link>
						</PulseCard>
						<router-link
							v-else
							to="/cabinet"
							class="menu__item"
						>
							<img
								src="/images/dist/icon-settings.svg"
								alt="settings icon"
							>
							<span class="menu__item-title">Настройки</span>
						</router-link>
						<form @click.prevent="logout">
							<button
								class="menu__item w-full"
								type="submit"
							>
								<img
									src="/images/dist/icon-exit.svg"
									alt="settings icon"
								>
								<span class="menu__item-title">Выход</span>
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>

		<nav
			ref="nav"
			class="header__nav"
			:class="{'header__nav_even': height - filteredItems.totalHeight < 50}"
		>
			<template v-for="item in filteredItems.visible">
				<LeftSidebarItem
					v-if="!item.hide"
					:key="item.name"
					:name="item.name"
					:class="item.className"
					:to="item.to"
					:href="item.href"
					:icon="item.icon"
					:img="item.img"
					:menu="item.menu"
					:popover="item.popover"
					:highlight="item.route === routeMenuItem"
					@calcsize="item.height = $event.offsetHeight"
				/>
			</template>
			<template v-if="filteredItems.more.length === 1">
				<LeftSidebarItem
					v-if="!filteredItems.more[0].hide"
					:key="filteredItems.more[0].name"
					:name="filteredItems.more[0].name"
					:class="filteredItems.more[0].className"
					:href="filteredItems.more[0].href"
					:to="filteredItems.more[0].to"
					:icon="filteredItems.more[0].icon"
					:img="filteredItems.more[0].img"
					:menu="filteredItems.more[0].menu"
					:highlight="filteredItems.more[0].route === routeMenuItem"
				/>
			</template>
			<LeftSidebarItem
				v-if="filteredItems.more.length > 1"
				name="Еще"
				class="header__nav-link-more"
				icon="icon-nd-more"
				:menu="filteredItems.more"
			/>
		</nav>
		<LeftSidebarItem
			v-if="showSettings"
			name="Настройка"
			class="last header__nav-link-settings"
			icon="icon-nd-settings"
			to="/timetracking/settings"
			:menu="settingsSubmenu"
			:highlight="routeMenuItem === 'settings'"
		/>
	</div>
</template>

<script>
/* global Laravel */
import { bus } from '../../bus'
import { useUnviewedNewsStore } from '@/stores/UnviewedNewsCount'
import { mapActions } from 'pinia'
import { mapGetters } from 'vuex'
import { settingsSubmenu } from './helper.js'

import LeftSidebarItem from './LeftSidebarItem'
import PulseCard from '@ui/PulseCard.vue'

export default {
	name: 'LeftSidebar',
	components: {
		LeftSidebarItem,
		PulseCard,
	},
	props: {},
	data: function () {
		return {
			height: 300,
			fields: [],
			avatar: Laravel.avatar,
			token: Laravel.csrfToken,
			isAdmin: this.$laravel.is_admin,
			project: window.location.hostname.split('.')[0],
			cabinets: Laravel.cabinets,
			tenants: (Laravel.tenants || []).map(tenant => tenant.toLowerCase()),
			isCreatingProject: false,
			resizeObserver: null,
		};
	},
	computed: {
		...mapGetters(['user']),
		isDefaultAvatar(){
			return this.avatar === 'https://cp.callibro.org/files/img/8.png'
		},
		status(){
			if(!this.user) return ''
			return this.user.referrer_status
		},
		isMainProject(){
			return this.project === 'bp' || this.project === 'test'
		},
		routeMenuItem(){
			return this.$route.meta?.menuItem
		},
		showSettings(){
			return this.$can('settings_view')
				|| this.$can('users_view')
				|| this.$can('positions_view')
				|| this.$can('groups_view')
				|| this.$can('fines_view')
				|| this.$can('notifications_view')
				|| this.$can('permissions_view')
				|| this.$can('checklists_view')
				|| this.$can('awards_view')
		},
		showReports(){
			return (this.$can('top_view') && this.isMainProject)
				|| this.$can('tabel_view')
				|| this.$can('entertime_view')
				|| (this.$can('hr_view') && this.isMainProject)
				|| this.$can('analytics_view')
				|| this.$can('salaries_view')
				|| this.$can('quality_view')
		},
		showEducation(){
			return this.$can('books_view')
				|| this.$can('videos_view')
				|| this.$can('courses_view')
		},
		items(){
			return [
				{
					name: 'Профиль',
					to: '/',
					icon: 'icon-nd-profile',
					height: 0,
					route: 'profile',
				},
				{
					name: 'Новости',
					to: '/news',
					icon: 'icon-nd-news',
					height: 0,
					route: 'news',
					// hide: !this.$can('news_edit')
				},
				{
					name: 'Структура',
					to: '/structure',
					icon: 'icon-nd-struct',
					height: 0,
					route: 'structure',
				},
				{
					name: 'База знаний',
					to: '/kb',
					icon: 'icon-nd-kdb',
					height: 0,
					route: 'kb',
				},
				{
					name: 'Обучение',
					icon: 'icon-nd-education',
					height: 0,
					route: 'courses',
					menu: [
						{
							name: 'Читать книги',
							icon: 'icon-nd-books',
							to: '/admin/upbooks'
						},
						{
							name: 'Смотреть видео',
							icon: 'icon-nd-video',
							to: '/video_playlists'
						},
						{
							name: 'Курсы',
							icon: 'icon-nd-courses',
							to: '/courses',
							hide: !this.$can('courses_view')
						}
					]
				},
				{
					hide: !this.showReports,
					name: 'Отчеты',
					to: '/timetracking/reports',
					icon: 'icon-nd-reports',
					height: 0,
					route: 'reports',
					menu: [
						{
							name: 'ТОП',
							icon: 'icon-nd-dashboard',
							to: '/timetracking/top',
							hide: !(this.$can('top_view') && this.isMainProject)
						},
						{
							name: 'Табель',
							icon: 'icon-nd-tabel',
							to: '/timetracking/reports',
							hide: !this.$can('tabel_view')
						},
						{
							name: 'Время прихода',
							icon: 'icon-nd-enter-time',
							to: '/timetracking/reports/enter-report',
							hide: !this.$can('entertime_view')
						},
						{
							name: 'HR',
							icon: 'icon-nd-hr',
							to: '/timetracking/analytics',
							hide: !(this.$can('hr_view') && this.isMainProject)
						},
						{
							name: 'Аналитика',
							icon: 'icon-nd-analytics',
							to: '/timetracking/an',
							hide: !this.$can('analytics_view')
						},
						{
							name: 'Начисления',
							icon: 'icon-nd-salary',
							to: '/timetracking/salaries',
							hide: !this.$can('salaries_view')
						},
						{
							name: 'Контроль качества',
							icon: 'icon-nd-quality',
							to: '/timetracking/quality-control',
							hide: !this.$can('quality_view')
						},
					]
				},
				{
					name: 'Карта',
					to: '/maps',
					icon: 'icon-nd-map',
					height: 0,
					route: 'maps',
				},
				{
					name: 'KPI',
					to: '/kpi',
					icon: 'icon-nd-kpi',
					height: 0,
					route: 'kpi',
					hide: !this.$can('kpi_view')
				},
				{
					name: 'KK',
					to: '/',
					icon: 'icon-nd-kk',
					height: 0,
					route: 'kk',
					hide: true
				},
				{
					name: 'Частые вопросы',
					to: '/timetracking/info',
					icon: 'icon-nd-questions',
					height: 0,
					route: 'faq',
					hide: !(this.$can('faq_view') && this.isMainProject)
				},
				{
					name: 'Депреми рование',
					to: '/timetracking/fines',
					icon: 'icon-nd-deduction',
					height: 0,
					route: 'fines',
					hide: !this.isMainProject
				},
				{
					name: 'U-calls',
					href: '/callibro/login',
					icon: 'icon-nd-u-calls',
					height: 0,
					hide: !(this.$can('ucalls_view') && this.isMainProject)
				},
			]
		},
		filteredItems(){
			return this.items.reduce((res, item) => {
				if(item.hide) return res;
				res.totalHeight += item.height + 4
				if(this.height - res.totalHeight > 0){
					res.visible.push(item)
				}
				else{
					res.more.push(item)
				}
				return res;
			}, {
				visible: [],
				more: [],
				totalHeight: this.items[0].height
			})
		},
		isOwner(){
			return this.tenants && this.tenants.includes(this.project)
		},
		settingsSubmenu(){
			return settingsSubmenu.reduce((result, item) => {
				if(item.access && !this.can(item.access)) return result
				result.push(item)
				return result
			}, [])
		},
		profileUnfilled(){
			return !this.user.phone
				|| !this.user.name
				|| !this.user.last_name
				|| !this.user.email
				|| !this.user.birthday
				|| !this.user.working_country
		},
	},
	mounted(){
		this.onResize()
		this.resizeObserver = new ResizeObserver(this.onResize).observe(this.$refs.nav)
		this.getUnviewedNewsCount();
		this.startAutoCheck();
		bus.$on('user-avatar-update', this.updateAvatar)
	},

	beforeDestroy(){
		if(this.resizeObserver) this.resizeObserver.disconnect()
		bus.$off('user-avatar-update', this.updateAvatar)
	},

	methods: {
		...mapActions(useUnviewedNewsStore, ['getUnviewedNewsCount', 'startAutoCheck']),
		onResize(){
			if(!this.$refs.nav) return
			this.height = this.$refs.nav.offsetHeight
		},
		onNewProject(){
			if(!confirm('Вы уверены? Создатся еще один кабинет под другим субдоменом. Вам это нужно ?')) return
			this.isCreatingProject = true
			const loader = this.$loading.show({
				zIndex: 99999
			})
			this.$toast.info('Ваш кабинет создается', {
				timeout: 20000,
				closeOnClick: false,
				pauseOnFocusLoss: true,
				pauseOnHover: true,
				draggable: false,
				showCloseButtonOnHover: false,
				hideProgressBar: true,
				closeButton: false,
				icon: true
			})
			this.axios.post('/projects/create', {}).then(response => {
				if(response.data) location.assign(response.data.link)
			}).catch(error => {
				loader.hide()
				this.isCreatingProject = false
				this.$toast.error(error.error || error.message || 'Ошибка при создании кабинета')
				console.error(error)
			})
		},
		updateAvatar(avatar){
			this.avatar = avatar
		},
		logout(){
			const formData = new FormData();
			formData.append('_token', this.$laravel.csrfToken);

			const protocol = window.location.protocol
			const redirectHost = window.location.hostname.split('.')
			if (protocol === 'https:') {
				redirectHost.splice(0, 1)
			}

			this.axios.post('/logout', formData).then(() => {
				window.location.assign(`${protocol}//${redirectHost.join('.')}/?logout=1`)
			})
		},
		can(access){
			if(!access) return true
			if(access === 'is_admin') return this.isAdmin
			if(typeof access === 'string') return this.$can(access)
			return access.some(item => this.$can(item))
		},
	},
};
</script>

<style lang="scss">
	.header__left{
		display: flex;
		flex-direction: column;
		align-items: center;
		width:7rem;
		min-height: inherit;
		max-height: inherit;
		padding-top: 0.5rem;
		padding-bottom: 1rem;
		background-color: darken(#F6F7FC, 3%);
		transform:translateX(0);
		opacity:1;
		visibility: visible;
		transition: all 0.5s;
		// box-shadow: -0.1rem 0px 0.5rem rgba(0, 0, 0, 0.25);
	}

	.header__avatar{
		display: block;
		width: 100%;
		max-width: 100%;
		margin-bottom: 0.5rem;
		position:relative;
		border-radius: 10px;
		padding: 0 5px;
		z-index: 1003;
		.hover-avatar-area{
			position: absolute;
			top: 0;
			z-index: 2;
			left: 0;
			width: 50px;
			height: 60px;
			cursor: pointer;
		}
		.hover-avatar-area:hover .header_menu_avatar{
			display: block;
		}
		.header_menu_avatar{
			display: none;
			position: fixed;
			z-index: 1005;
			width: 26rem;
			padding-left: 2rem;
			left: 5rem;
			.header__menu{
				visibility: visible!important;
				opacity: 1!important;
			}
		}
		.header__menu{
			max-width: 24rem;
			top: 0.5rem;
			left: 0;
			position: static;
		}

		> img {
			display: block;
			height: auto;
			border-radius: 10px;
			width: 100%;
			object-fit: cover;
		}

	}

	.header__menu-project{
		padding: 1.2rem 1.3rem;
		text-align: left;
		cursor: pointer;
		position: relative;
		&:hover{
			background: #FAFCFD;
			.header__submenu{
				opacity: 1;
				visibility: visible;
			}
		}
	}
	.header__submenu{
		display: flex;
		width: auto;
		flex-direction: column;
		padding-top: 0;

		position: absolute;
		z-index: 1010;
		top: 100%;
		right: 0;

		background: #fff;
		color: #657A9F;
		font-size: 1.3rem;
		box-shadow: 1rem 0 2rem rgba(0, 0, 0, 0.25);
		opacity: 0;
		visibility: hidden;
	}
	.header__submenu-item{
		display: flex;
		gap:1rem;
		align-items: center;

		height: 3.4rem;
		padding: 1rem 2rem;

		background: #fff;
		cursor:pointer;
		color:#657A9F;

		&:first-of-type{
			border-radius: 0 1rem 0 0;
		}

		&:last-of-type{
			border-radius: 0 0 1rem 0;
		}

		&:hover{
			background: #FAFCFD;
			color: #156AE8;
			.menu__item-title,
			.menu__item-icon{
				color: #156AE8;
			}
		}
	}
	.header__submenu-item_active{
		cursor: default;
		font-weight: 700;
		&:hover{
			background: #fff;
			color: #657A9F;
			.menu__item-title,
			.menu__item-icon{
				color: #657A9F;
			}
		}
	}
	.header__submenu-divider{
		margin: 0.5rem 0;
		border-top: 1px solid lighten(#657A9F, 25%);
	}

	.header__nav{
		display: flex;
		flex-direction: column;
		flex: 0 1 100%;
		gap:.3rem;
		overflow-y: visible;
		&::-webkit-scrollbar {
			width: 0; /* высота для горизонтального скролла */
			height: 0;
		}
		&_even{
			justify-content: space-evenly;
		}
	}

	.header__nav-link{
		width: 100%;
		display: flex;
		flex-direction: column;
		align-items: center;
		text-align: center;
		font-size: 1rem;
		gap: 1rem;
		font-weight: 400;
		color: #8D9CA9;
		position:relative;
		z-index: auto;
		transition:.3s;

		&:hover,
		&.opened{
			.header__nav-link-a{
				background: #608EE9;
				color: #fff;
			}
			.header__nav-name{
				color: #fff;
			}
			.header__menu{
				opacity:1;
				visibility: visible;
			}
			.header__nav-icon::before{
				color:#fff;
			}
		}

		&.last{
			margin-top: auto;
		}
	}
	.header__nav-link-a{
		display: flex;
		flex-direction: column;
		align-items: center;
		gap:.5rem;

		width: 100%;
		height: 100%;
		padding:  0.9rem 0.5rem;

		text-align: center;
		font-size: 1.2rem;
		font-weight: 400;
		color:#8DA0C1;

		transition:.3s;
		cursor:pointer;
		&:visited{
			color:#8DA0C1;
		}
	}
	.header__nav-icon {
		font-size:2rem;
		&::before{
			transition:.2s;
			width: auto;
		}
	}

	.header__menu{
		display: flex;
		flex-direction: column;
		width: 25rem;
		padding-top: 0;
		border-radius: 0 1rem 1rem 0;

		position: fixed;
		z-index: 1005;
		left: 7rem;

		background: #fff;
		color: #657A9F;
		font-size: 1.3rem;
		box-shadow: 1rem 0 2rem rgba(0, 0, 0, 0.15);
		opacity: 0;
		visibility: hidden;
	}

	.header__menu-title{
		padding: 2rem 1.3rem 1.2rem 2rem;
		text-align: left;
		text-transform: uppercase;
	}

	.header__menu-email{
		border-bottom: 1px solid #EDEFF3;
		font-size:1.1rem;
		padding-top: 1rem;
		padding-bottom: 1rem;
		text-transform: none;
	}

	.header__menu-userid{
		color:#608EE9;
	}

	.menu__item{
		display: flex;
		gap:1rem;
		align-items: center;

		height: 3.4rem;
		padding-right: 3rem;
		padding-left: 2rem;

		background: #fff;
		cursor:pointer;

		&:first-of-type{
			border-radius: 0 1rem 0 0;
		}

		&:last-of-type{
			border-radius: 0 0 1rem 0;
		}

		&:hover{
			background: #FAFCFD;
			.menu__item-title,
			.menu__item-icon{
				color: #156AE8;
			}
		}
		&-title{
			color:#657A9F;
			padding: 1rem 0;
		}
	}

	.menu__item-icon{
		color: #A6B7D4;
	}

	.header__nav-link-settings,
	.header__nav-link-more{
		.header__menu{
			transform: translateY(calc(-100% + 5rem));
		}
	}

	@media(max-width:900px){
		.header__left{
			&.closed{
				transform:translateX(-30px);
				opacity:0;
				visibility: hidden;
			}
		}
	}

	.LeftSidebar{
		&-refIcon{
			width: auto !important;

			position: absolute;
			right: 5px;
			bottom: 0;
		}
		&-upload{
			position: absolute;
			right: 5px;
			bottom: 0;
			font-size: 24px;
			color: #555;
			text-shadow: 0 0 2px #fff;
		}
		&-avatar{
			display: block;
			height: auto;
			border-radius: 10px;
			width: 100%;
			-o-object-fit: cover;
			object-fit: cover;
		}
		&-pulseAvatar{
			border-radius: 10px;
		}
	}
</style>
