<script>
export default {
    name: 'Section1Tab',
    props: {
        activeTab: String,
        tabId: String,
    },
    computed: {
        lang () {
            return this.$root.$data.lang
        }
    },
}
</script>

<template>
    <li class="jSec1-tabs-button">
        <a
            href="javascript:void(0)"
            class="jSec1-tabs-link"
            :class="{'jSec1-tabs-link--active': activeTab === tabId}"
            @click="$emit('content')"
        >{{ $lang(lang, 's1-' + tabId) }}</a>
        <a
            href="javascript:void(0)"
            class="jSec1-tabs-qm"
            @click="$emit('popup')"
        />
    </li>
</template>

<style lang="scss"></style>