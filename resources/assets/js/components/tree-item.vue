<template>
    <li>
        <div :class="{bold: isFolder}"
                @click="toggle"
                @dblclick="makeFolder">
            {{ item.name }}

            <span v-if="isFolder">[{{ isOpen ? '-' : '+' }}]</span>
        </div>
        <ul v-show="isOpen" v-if="isFolder">
            <tree-item
                    class="item"
                    v-for="(child, index) in item.categoryes"
                    :key="index"
                    :item="child"
                    @make-folder="$emit('make-folder', $event)"
                    @add-item="$emit('add-item', $event)"
            ></tree-item>

        </ul>
    </li>
</template>

<script>
    export default {
        name: "tree-item",
        props: {
            item: Object
        },
        data() {
            return {
                isOpen: false
            }
        },
        computed: {
            isFolder: function () {
                return this.item.categoryes &&
                    this.item.categoryes.length
            }
        },
        methods: {
            toggle: function () {
                if (this.isFolder) {
                    this.isOpen = !this.isOpen
                }
            },
            makeFolder: function () {
                if (!this.isFolder) {
                    this.$emit('make-folder', this.item)
                    this.isOpen = true
                }
            }
        }
    }
</script>

<style scoped>

</style>