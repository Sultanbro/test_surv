<template>
	<sidebar
		id="award-sidebar"
		title="Награды"
		:open="open"
		width="60%"
		@close="$emit('update:open', false)"
	>
		<div id="body">
			<BTabs
				content-class="mt-3"
				fill
			>
				<BTab
					v-for="(awardCard, index) in awardCards"
					:key="index"
					:title="awardCard.header"
					active
				>
					<BCard
						bg-variant="primary"
						text-variant="white"
						header="Primary"
						class="text-center"
					/>
				</BTab>
			</BTabs>
			<BTabs
				content-class="mt-3"
				fill
			>
				<BTab
					v-for="(fakeAward, index) in fakeAwardsLocal"
					:key="index"
					:title="fakeAward.name"
					active
				>
					<BCard
						bg-variant="primary"
						text-variant="white"
						header="Primary"
						class="text-center"
					>
						<BCardImg
							src="https://placekitten.com/480/210"
							alt="Image"
							bottom
						/>
						<BCardText>
							{{ fakeAward.content }}
						</BCardText>
					</BCard>
				</BTab>
			</BTabs>
		</div>
	</sidebar>
</template>

<script>
// import AwardsCard from './AwardsCard';
const baseUrl = 'https://dummyjson.com/users';

export default {
	name: 'AwardSidebar',
	components: {
		// AwardsCard,
	},
	props: {
		open: Boolean,
		awards: {
			type: Array,
			default: () => []
		},
	},
	data() {
		return {
			id: 1,
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
				{ type: 'my', header: 'Мои', values: [] },
				{ type: 'all', header: 'Все', values: [] },
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

		async getSertificateById(id) {
			this.axios
				.get(`${baseUrl}/` + id)
				.then(() => {})
				.catch((error) => {
					console.error(error);
					this.errored = true;
				})
				.finally(() => (this.loading = false));
		},

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
		async fetchAwardsById(/* id */) {
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
