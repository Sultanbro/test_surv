<template>
	<ul class="dragArea">
		<li
			v-for="el in tasks"
			:id="el.id"
			:key="el.id"
			class="chapter opened"
			:class="{
				'pass' : el.item_model !== null,
				'active': active == el.id,
				'disabled' : el.item_model === null && firstActive != el.id
			}"
		>
			<div class="d-flex titles">
				<div class="handles d-flex aic">
					<div>
						<i
							v-if="el.item_model != null"
							class="fa fa-check-double pointer"
						/>
						<i
							v-else-if="firstActive == el.id"
							class="fa fa-unlock pointer"
						/>
						<i
							v-else
							class="fa fa-lock pointer"
						/>
					</div>
				</div>
				<p
					class="mb-0"
					@click="showPage(el.id)"
				>
					{{ el.title }}
				</p>
			</div>
			<NestedCourse
				:tasks="el.children"
				:active="active"
				@showPage="showPage"
			/>
		</li>
	</ul>
</template>
<script>
const NestedCourse = {
	name: 'NestedCourse',
	props: {
		tasks: {
			required: true,
			type: Array
		},
		active: {
			default: 0
		},
	},
	watch: {
		active: function() {
			if(this.firstActive == 0) this.firstActive = this.active
		}
	},
	data() {
		return {
			firstActive: 0
		}
	},
	methods: {
		showPage(id) {

			let item = null;
			let i = this.tasks.findIndex(el => el.id == id);
			if(i != -1) {
				item = this.tasks[i]
			}

			if(id != this.firstActive) {
				if(item != null && item.item_model == null) return;
			}
			this.$emit('showPage', id);
		},
	},
};
NestedCourse.components = {
	NestedCourse
}
export default NestedCourse
</script>
