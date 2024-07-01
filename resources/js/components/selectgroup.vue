<template>
	<div>
		<select
			v-model="selecttrees"
			class="form-control"
			@change="log(), select(selecttrees)"
		>
			<option
				v-if="perenos == 1"
				value="koren"
			>
				Перенести в корень
			</option>
			<option
				v-if="selecttree != null"
				:value="selecttree"
				selected
			>
				В текущий
			</option>
			<template v-for="tre in tree">
				<option
					v-if="tre.parent_cat_id == selecttree"
					:key="tre.id"
					:value="tre.id"
				>
					- {{ tre.name }}
				</option>
			</template>
		</select>

		<selectgroup
			v-if="arr != null && selecttree != selecttrees"
			:tree="tree"
			:selecttree="selecttrees"
			@select="select"
		/>
	</div>
</template>

<script>
export default {
	name: 'SelectGroup',
	props: {
		tree: {
			type: Array,
			default: null,
		},
		selecttree: {
			type: Number,
			default: null,
		},
		perenos: {
			type: Number,
			default: null,
		},
	},
	data() {
		return {
			selecttrees:null,
			arr:null
		}
	},
	methods: {
		log(){
			this.arr = this.tree.find(x => x.parent_cat_id == this.selecttrees)

		},
		select(sel){
			this.$emit('select', sel)
		}
	}

}
</script>

<style scoped>

</style>
