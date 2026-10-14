<template>
	<div class="GroupDateFilter">
		<div class="row">
			<slot name="before" />
			<slot
				name="groups"
				:groups="groups"
			>
				<div
					v-if="groups"
					class="col-3"
				>
					<select
						v-model="currentGroup"
						class="form-control"
						@change="onChange()"
					>
						<option
							v-for="group in groups"
							:key="group.id"
							:value="group.id"
						>
							{{ group.name }}
						</option>
					</select>
				</div>
			</slot>
			<div class="col-3">
				<select
					v-model="currentMonth"
					class="form-control"
					@change="onChange()"
				>
					<option
						v-for="month, index in $moment.months()"
						:key="index"
						:value="index"
					>
						{{ month }}
					</option>
				</select>
			</div>
			<div class="col-2">
				<select
					v-model="currentYear"
					class="form-control"
					@change="onChange()"
				>
					<option
						v-for="year in years"
						:key="year"
						:value="year"
					>
						{{ year }}
					</option>
				</select>
			</div>
			<div class="col-1">
				<div
					class="btn btn-primary"
					@click="onRefresh()"
				>
					<i class="fa fa-redo-alt" />
				</div>
			</div>
			<slot name="after" />
		</div>
	</div>
</template>

<script>
export default {
	name: 'GroupDateFilter',
	components: {},
	props: {
		groups: {
			validator(value){
				return value ? Array.isArray(value) : true
			},
			default: null
		},
		years: {
			type: Array,
			default: () => [new Date().getFullYear()]
		},
		refreshOnChange: {
			type: Boolean
		},
	},
	data(){
		const now = new Date()
		return {
			currentGroup: this.groups ? this.groups[0] : null,
			currentYear: now.getFullYear(),
			currentMonth: now.getMonth(),
		}
	},
	computed: {},
	watch: {},
	created(){},
	mounted(){},
	methods: {
		onRefresh(){
			this.$emit('refresh', {
				group: this.currentGroup,
				year: this.currentYear,
				month: this.currentMonth,
			})
		},
		onChange(){
			if(!this.refreshOnChange) return
			this.onRefresh()
		},
	},
}
</script>

<style lang="scss">
//.GroupDateFilter{}
</style>
