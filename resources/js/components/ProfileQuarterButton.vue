<template>
	<div>
		<a
			id="clickQuarter"
			class="btn-block btn btn-primary text-white rounded"
			@click="toggle()"
		>
			Квартальная премия
		</a>

		<sidebar
			title="Квартальная премия"
			:open="showKpiSidebar"
			width="70%"
			@close="toggle()"
		>
			<t-quarter
				:activeuserid="activeuserid"
				group="Ежегодный  квартальный календарь"
				:user_id="user_id"
				:is_admin="true"
				type="individual"
			/>
		</sidebar>
	</div>
</template>

<script>
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */

export default {
	name: 'ProfileQuarterButton',
	props: {
		user_id: {
			type: Number,
			default: 0
		},
		activeuserid: {
			type: Number,
			default: 0
		},
	},
	data() {
		return {
			showKpiSidebar: false,
		};
	},
	created() {

	},
	methods: {
		toggle() {
			this.showKpiSidebar = !this.showKpiSidebar
			document.getElementById('list-example').classList.toggle('sticky');
		},
	},
};
</script>

<style lang="scss">

</style>
