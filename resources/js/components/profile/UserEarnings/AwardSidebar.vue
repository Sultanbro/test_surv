<template>

    <sidebar
        id="award-sidebar"
        title="Награды"
        :open="open"
        @close="$emit('update:open', false)"
        width="40%"
    >
        <div id="left-panel" class="rounded-left">
            <b-card class="rounded-0 py-2" bg-variant="light">
                <b-button block v-b-toggle.accordion-1 variant="info"
                    v-for="(award, index) in awardsLocal"
                    :key="index"
                    :pressed="award.pressed"
                    @click="awardClickHandler(index)">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="mr-3">{{ award.name }}</span>
                        </div>
                </b-button>
            </b-card>
        </div>

        <div id="body">
            <b-card class="awards-card" header="Мои" header-class="p-2 font-weight-bold">
                <div v-for="award in processedFetchedAwards.my">{{ award.imgSrc }}</div>
            </b-card>
            <b-card class="awards-card" header="Других сотрудников" header-class="p-2 font-weight-bold">
                <div v-for="award in processedFetchedAwards.notMy">{{ award.imgSrc }}</div>
            </b-card>
        </div>

    </sidebar>

</template>

<script>
    export default {
        name: 'AwardSidebar',
        props: {
            open: Boolean,
            awards: Array
        },
        data() {
            return {
                awardsLocal: this.createAwardsLocal(this.awards),
                processedFetchedAwards: {
                    my: [],
                    notMy: []
                }
            }
        },
        methods: {
            // EVENT HANDLERS

            async awardClickHandler (index) {
                try {
                    this.selectAwardByIndex(index)
                    const fetchedAwards = await this.fetchAwardsById(this.awardsLocal[index].id)
                    this.processedFetchedAwards = this.processFetchedAwards(fetchedAwards)
                } catch (error) {
                    console.error(error)
                }
            },
            
            // HELPERS

            selectAwardByIndex (index) {
                this.awardsLocal.forEach(item => { item.pressed = false })
                this.awardsLocal[index].pressed = true
            },
            createAwardsLocal (awards) {
                return awards.map((item) => ({ ...item, pressed: false }))
            },
            async fetchAwardsById (id) {
                return new Promise(resolve => {
                    const out = {
                        my: [{ imgSrc: 'myAward1.png' }],
                        notMy: [{ imgSrc: 'notMyAward1.png' }]
                    }
                    const loader = this.$loading.show()
                    setTimeout(() => {
                        loader.hide()
                        resolve(out)
                    }, 500);
                })

            },
            processFetchedAwards (awards) {
                return awards
            }
        },
        mounted () {
            this.awardClickHandler(0)
        }
    }
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
    .awards-card {

    }
}
</style>