<template>
	<div class="container header__container">
		<profile-sidebar v-show="isProfileVisible" />

		<div class="header">
			<left-sidebar :class="{ closed: !isLeft }" />
			<right-sidebar
				:class="{ closed: !isRight }"
				@pop="pop"
			/>

			<div
				class="header__arrow"
				:class="{ show: isRight }"
			>
				<a href="javascript:">
					<img
						src="/images/dist/header-arrow.svg"
						alt="arrow icon"
					>
				</a>
			</div>
		</div>

		<!-- popup -->
		<Popup
			v-if="popNotifications"
			title="Уведомления"
			desc=""
			:open="popNotifications"
			width="50%"
			@close="popNotifications = false"
		>
			<popup-notifications />
		</Popup>

		<!-- popup -->
		<Popup
			v-if="popChecklist"
			title="Чек лист"
			desc="Важно в течении дня выполнить все пункты чек листа"
			:open="popChecklist"
			width="75%"
			@close="popChecklist = false"
		>
			<popup-checklist :data="checklistData" />
		</Popup>

		<!-- popup -->
		<Popup
			v-if="popFAQ"
			title="Вопросы и ответы"
			desc="Часто задаваемые вопросы и ГИД по веб-сервису jobtron.org"
			:open="popFAQ"
			width="80%"
			@close="popFAQ = false"
		>
			<FaqPopup />
		</Popup>

		<!-- popup -->
		<Popup
			v-if="popSearch"
			title="Поиск"
			desc="Дополнительное поле с описанием функционала данного окна"
			:open="popSearch"
			width="50%"
			@close="popSearch = false"
		>
			<popup-search />
		</Popup>

		<!-- popup -->
		<Popup
			v-if="popMail"
			title="Почта или что это?"
			desc="Дополнительное поле с описанием функционала данного окна"
			:open="popMail"
			width="50%"
			@close="popMail = false"
		>
			<popup-mail />
		</Popup>
	</div>
</template>

<script>
import Popup from '@/pages/Layouts/Popup.vue';
import FaqPopup from '@/pages/Layouts/FaqPopup.vue';

export default {
	name: 'LayoutSidebars',
	components: {
		Popup,
		FaqPopup,
	},
	props: {
		isLeft: Boolean,
		isRight: Boolean,
	},

	data() {
		return {
			popNotifications: false,
			popChecklist: false,
			popMessenger: false,
			popFAQ: false,
			popSearch: false,
			popMail: false,

			checklistData: {},
			checklistSettings: null,
			checklistTimer: null,
		};
	},
	computed: {
		isProfileVisible() {
			return this.$viewportSize.width >= 1360;
		},
	},
	created() {
		this.fetchChecklist();
	},
	mounted() {
		setTimeout(() => {
			this.checkCheckList();
			this.checklistTimer = setInterval(this.checkCheckList, 1000 * 60);
		}, 1000);
	},
	methods: {
		/**
		 * pop window
		 */
		pop(item) {
			if (item == 'faq') this.popFAQ = true;
			if (item == 'notifications') this.popNotifications = true;
			if (item == 'search') this.popSearch = true;
			if (item == 'messenger') this.popMessenger = true;
			if (item == 'mail') this.popMail = true;
			if (item == 'checklist') this.popChecklist = true;
		},

		fetchChecklist() {
			// axios.post('/checklist', {}).then((response) => {
			//   this.checklistData = response.data
			// })
		},

		checkCheckList() {
			if (!this.checklistData.show_count) return;
			if (!this.checklistData.work_start) return;
			if (!this.checklistData.work_end) return;

			if (!this.checklistSettings) this.prepareCheckList();

			const now = this.$moment(Date.now());
			let isTrigger = false;
			const dateInfo =
				this.checklistSettings.showed[this.checklistSettings.date];
			this.checklistSettings.showTimes.forEach((time) => {
				const fTime = time.format('HH:mm:ss');
				if (time.diff(now) < 0 && !dateInfo[fTime]) {
					isTrigger = true;
					this.checklistSettings.showed[this.checklistSettings.date][
						fTime
					] = true;
				}
			});

			if (isTrigger) {
				this.pop('checklist');
				localStorage.setItem(
					'bpCheckListShowed',
					JSON.stringify(this.checklistSettings.showed)
				);
			}
		},

		prepareCheckList() {
			if (!this.checklistData.show_count) return;
			if (!this.checklistData.work_start) return;
			if (!this.checklistData.work_end) return;

			this.checklistSettings = {
				showed: JSON.parse(localStorage.getItem('bpCheckListShowed')),
				date: this.$moment(Date.now()).format('DD.MM.YYYY'),
				startTime: this.checklistData.work_start.split(':'),
				endTime: this.checklistData.work_end.split(':'),
			};
			this.checklistSettings.start = this.$moment(Date.now()).set({
				hour: this.checklistSettings.startTime[0],
				minute: this.checklistSettings.startTime[1],
				second: this.checklistSettings.startTime[2],
			});
			this.checklistSettings.end = this.$moment(Date.now()).set({
				hour: this.checklistSettings.endTime[0],
				minute: this.checklistSettings.endTime[1],
				second: this.checklistSettings.endTime[2],
			});
			this.checklistSettings.diff = this.checklistSettings.end.diff(
				this.checklistSettings.start
			);
			this.checklistSettings.part =
				this.checklistSettings.diff / this.checklistData.show_count;

			this.checklistSettings.showTimes = [];
			for (let i = 0; i < this.checklistData.show_count; ++i) {
				if (this.checklistSettings.showTimes.length) {
					this.checklistSettings.showTimes.push(
						this.checklistSettings.start
							.clone()
							.add(parseInt(this.checklistSettings.part) * i, 'milliseconds')
					);
				} else {
					this.checklistSettings.showTimes.push(
						this.checklistSettings.start
							.clone()
							.add(parseInt(this.checklistSettings.part / 2), 'milliseconds')
					);
				}
			}

			if (!this.checklistSettings.showed) this.checklistSettings.showed = {};
			Object.keys(this.checklistSettings.showed).forEach((key) => {
				if (key !== this.checklistSettings.date)
					delete this.checklistSettings.showed[key];
			});
		},
	},
};
</script>

<style lang="scss" scoped>
.header__container {
	position: relative;
	z-index: 3;
}
</style>
