<template>
	<div>
		<CourseFilter />
		<CourseTable />
	</div>
</template>

<script>
import CourseFilter from './components/CourseFilter.vue';
import CourseTable from './components/CourseTable.vue';
export default {
	components: {
		CourseFilter,
		CourseTable
	},
};
</script>

<style lang="scss" scoped></style>
