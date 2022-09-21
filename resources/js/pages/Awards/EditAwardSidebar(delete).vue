<template>

    <sidebar
        id="edit-award-sidebar"
        title="Награды"
        :open="open"
        @close="$emit('update:open', false)"
        width="40%"
    >

        <b-form @submit.prevent="onSubmit">
            <b-form-group
                id="input-group-1"
                label="Название награды"
                label-for="input-1"
                description="Например, сертификаты, грамоты и т.п."
            >
                <b-form-input
                    id="input-1"
                    v-model="form.name"
                    type="text"
                    placeholder="Название"
                    required
                ></b-form-input>
            </b-form-group>

            <b-form-group id="input-group-4" v-slot="{ ariaDescribedby }">
                <b-form-checkbox v-model="form.visibleToOthers" :ariaDescribedby="ariaDescribedby">
                    Показывать награду другим пользователям
                </b-form-checkbox>
            </b-form-group>

            <b-button type="submit" variant="primary">Сохранить</b-button>
        </b-form>

    </sidebar>

</template>

<script>
    export default {
        name: 'EditAwardSidebar',
        props: {
            open: Boolean
        },
        data() {
            return {
                form: {
                    visibleToOthers: false,
                    name: ''
                }
            }
        },
        methods: {
            onSubmit () {
                //
            }
        }
    }
</script>

<style lang="scss">
#edit-award-sidebar {
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
}
</style>