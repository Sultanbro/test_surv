<template>
    <div>

        <select class="form-control" v-model="selecttrees" @change="log(), select(selecttrees)">
            <option v-if="perenos == 1" value="koren">Перенести в корень</option>
            <option :value="selecttree" selected v-if="selecttree != null">В текущий</option>
            <template v-for="tre in tree">
				<option :key="tre.id" :value="tre.id" v-if="tre.parent_cat_id == selecttree">- {{tre.name}}</option>
			</template>
        </select>

        <selectgroup v-if="arr != null && selecttree != selecttrees" @select="select" :tree="tree" :selecttree="selecttrees"/>


    </div>
</template>

<script>
export default {
	name: 'SelectGroup',
	props: ['tree','selecttree','perenos'],
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