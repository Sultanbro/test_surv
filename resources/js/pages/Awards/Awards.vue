<template>
    <div id="awards-page">
        <b-table id="awards-table" class="mb-3" bordered small hover
            :items="tableItems"
            :fields="tableFields"
            @row-clicked="rowClickedHandler">
                <template slot="cell(files)" slot-scope="row">
                    <span class="mx-1">{{ row.value }} шт.</span>
                </template>
        </b-table>

        <b-button variant="primary" @click="addAwardButtonClickHandler">Добавить награду</b-button>

        <edit-award-sidebar v-if="showEditAwardSidebar" :open.sync="showEditAwardSidebar"/>
    </div>
</template>

<script>
import EditAwardSidebar from './EditAwardSidebar'

export default {
    name: 'Awards',
    components: { EditAwardSidebar },
    data() {
        return {
            showEditAwardSidebar: false,
            tableItems: [
                { name: 'Сертификаты', files: 0 },
                { name: 'Номинации', files: 0 }
            ],
            tableFields: [
                { key: 'name', label: 'Название' },
                { key: 'files', label: 'Шаблоны' }
            ]
        }
    },
    methods: {
        rowClickedHandler (data) {
            this.showEditAwardSidebar = true
        },
        addAwardButtonClickHandler () {
            this.showEditAwardSidebar = true
        }
    }
}
</script>

<style lang="scss">
#awards-page {
    #awards-table {
        tr {
            cursor: pointer;
        }
    }
}
</style>
