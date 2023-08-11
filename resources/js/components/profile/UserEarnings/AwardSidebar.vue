<template>
	<sidebar
		id="award-sidebar"
		title="Награды"
		:open="open"
		width="60%"
		@close="$emit('update:open', false)"
	>
		<div
			id="award-headers"
			class="rounded-left"
		>
			<b-card
				class="rounded-0 py-2"
				bg-variant="light"
			>
				<!-- Variant with tabs and bootstrap -->
				<b-tab
					v-for="(award, index) in awardsLocal"
					:key="index"
					:title="award"
				>
					<b-card-text>Tab contents {{ award }}</b-card-text>
				</b-tab>
				<!-- Same but with a fake data -->
				<b-tab
					v-for="(fakeAward, index) in fakeAwardsLocal"
					:key="index"
					:title="fakeAward.name"
				>
					<b-card-text>Tab contents {{ fakeAward.content }}</b-card-text>
				</b-tab>

				<!-- Award headers with fetch data -->
				<b-button
					v-for="(award, index) in awardsLocal"
					:key="index"
					block
					variant="info"
					:pressed="award.pressed"
					@click="awardsClickHandler(index)"
				>
					<div class="d-flex justify-content-between align-items-center">
						<span class="mr-3">{{ award.name }}</span>
					</div>
				</b-button>
			</b-card>
		</div>

		<!-- Award headers with fetch data -->
		<!-- Award headers removed from the left side to inner block -->
		<div
			id="left-panel"
			class="rounded-left"
		>
			<b-card
				class="rounded-0 py-2"
				bg-variant="light"
			>
				<b-button
					v-for="(award, index) in awardsLocal"
					:key="index"
					block
					variant="info"
					:pressed="award.pressed"
					@click="awardsClickHandler(index)"
				>
					<div class="d-flex justify-content-between align-items-center">
						<span class="mr-3">{{ award.name }}</span>
					</div>
				</b-button>

				<b-button
					v-for="(fakeAward, index) in fakeAwardsLocal"
					:key="index"
					block
					variant="info"
					:pressed="fakeAward.pressed"
					@click="awardsClickHandler(index)"
				>
					<div class="d-flex justify-content-between align-items-center">
						<span class="mr-3">{{ fakeAward.name }}</span>
					</div>
				</b-button>
			</b-card>
		</div>

		<div id="body">
			<template v-if="nominationsSelected">
				<b-card
					class="nominations-card"
					body-class="text-center px-2"
					header-class="p-2 bg-secondary text-white"
				>
					<template #header>
						<b>Заработали больше всех</b>
					</template>
					<b-card-group>
						<b-card
							v-for="(item, index) in featuredUsers"
							:key="index"
							:class="index === 1 && 'mx-sm-2'"
							header-class="p-2 text-center"
						>
							<template #header>
								<b>{{ index + 1 }} место</b>
							</template>

							<img
								class="user-image"
								src="/images/avatar.png"
							>
							<b-card-text>{{ item.name }}</b-card-text>

							<template #footer>
								<em>{{ item.value }}</em>
							</template>
						</b-card>
					</b-card-group>
				</b-card>
			</template>

			<template v-else>
				<awards-card
					v-for="(card, index) in awardCards"
					:key="index"
					v-bind="card"
				/>
			</template>
		</div>
	</sidebar>
</template>

<script>
import AwardsCard from './AwardsCard';

export default {
	name: 'AwardSidebar',
	components: { AwardsCard },
	props: {
		open: Boolean,
		awards: {
			type: Array,
			default: () => []
		},
	},
	data() {
		return {
			data: 'data',
			fakeAwardsLocal: [
				{ name: 'Сертификаты', content: 'fake content sertificate' },
				{ name: 'Награды', content: 'fake content awards' },
				{ name: 'Красавчики', content: 'fake content hansome' },
				{ name: 'Поездки', content: 'fake content trip' },
			],
			//   awardsLocal: this.createAwardsLocal(this.awards),
			featuredUsers: [
				{ name: 'Фамилия Имя Отчество', value: '100 000 тнг.' },
				{ name: 'Фамилия Имя Отчество', value: '80 000 тнг.' },
				{ name: 'Фамилия Имя Отчество', value: '69 000 тнг.' },
			],
			awardCards: [
				{ type: 'all', header: 'Все', values: [] },
				{ type: 'my', header: 'Мои', values: [] },
				{ type: 'notMy', header: 'Других сотрудников', values: [] },
			],
			nominationsSelected: false,
		};
	},
	mounted() {
		if (this.awards.length) {
			this.awardsClickHandler(0);
		}

	},
	methods: {
		// EVENT HANDLERS

		async awardsClickHandler(index) {
			try {
				this.nominationsSelected = false;
				this.clearAwardCards();
				this.selectAwardByIndex(index);
				const fetchedAwards = await this.fetchAwardsById(
					this.awardsLocal[index].id
				);
				this.updateAwardCards(fetchedAwards);
			} catch (error) {
				console.error(error);
			}
		},
		async nominationsClickHandler() {
			try {
				this.nominationsSelected = true;
				this.unpressAwards();
			} catch (error) {
				console.error(error);
			}
		},

		// HELPERS

		selectAwardByIndex(index) {
			this.unpressAwards();
			this.awardsLocal[index].pressed = true;
		},
		unpressAwards() {
			this.awardsLocal.forEach((item) => {
				item.pressed = false;
			});
		},
		createAwardsLocal(awards) {
			return awards.map((item) => ({ ...item, pressed: false }));
		},
		async fetchAwardsById() {
			// ЗДЕСЬ БУДЕТ ЗАПРОС К API
			return new Promise((resolve) => {
				const out = {
					all: [
						{ imgSrc: 'all1.png' },
						{ imgSrc: 'all1.png' },
						{ imgSrc: 'all1.png' },
						{ imgSrc: 'all1.png' },
						{ imgSrc: 'all1.png' },
					],
					my: [
						{ imgSrc: 'myAward1.png' },
						{ imgSrc: 'myAward1.png' },
						{ imgSrc: 'myAward1.png' },
						{ imgSrc: 'myAward1.png' },
						{ imgSrc: 'myAward1.png' },
					],
					notMy: [],
				};
				const loader = this.$loading.show();
				setTimeout(() => {
					loader.hide();
					resolve(out);
				}, 500);
			});
		},
		updateAwardCards(awards) {
			this.awardCards[0].values = awards.all;
			this.awardCards[1].values = awards.my;
			this.awardCards[2].values = awards.notMy;
		},
		clearAwardCards() {
			this.updateAwardCards({ all: [], my: [], notMy: [] });
		},
	},
};
</script>

<style lang="scss">
#award-sidebar {
  .ui-sidebar__body {
    overflow: visible;
    display: flex;
    flex-direction: column;
  }
  .ui-sidebar__content {
    flex: 1;
    max-height: 100%;
    overflow: auto;
  }
  #left-panel {
    position: absolute;
    left: 0;
    transform: translateX(-100%);
    overflow: hidden;
  }
  .nominations-card {
    .user-image {
      width: 64px;
      height: 64px;
    }
  }
  .card {
    border: 1px solid #e9ecef;
  }
}
</style>
