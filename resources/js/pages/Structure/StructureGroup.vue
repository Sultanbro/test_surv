<!--<template>-->
<!--	<div class="structure-item">-->
<!--		<div class="structure-card">-->
<!--			<div class="structure-card-header">-->
<!--				<p class="department">{{department.department}}</p>-->
<!--				<p class="count">{{department.employeesCount}} сотрудников</p>-->
<!--			</div>-->
<!--			<div class="structure-card-body">-->
<!--				<template v-if="department.director">-->
<!--					<img :src="department.director.photo" alt="photo">-->
<!--					<p class="position">{{department.director.position}}</p>-->
<!--					<p class="full-name">{{department.director.fullName}}</p>-->
<!--				</template>-->
<!--			</div>-->
<!--		</div>-->
<!--		<div class="structure-group" v-if="department.hasOwnProperty('departmentChildren')">-->
<!--			<div class="structure-card" v-for="dep in department.departmentChildren">-->
<!--				<div class="structure-card-header">-->
<!--					<p class="department">{{dep.department}}</p>-->
<!--					<p class="count">{{dep.employeesCount}} сотрудников</p>-->
<!--				</div>-->
<!--				<div class="structure-card-body">-->
<!--					<template v-if="dep.director">-->
<!--						<img :src="dep.director.photo" alt="photo">-->
<!--						<p class="position">{{dep.director.position}}</p>-->
<!--						<p class="full-name">{{dep.director.fullName}}</p>-->
<!--					</template>-->
<!--				</div>-->
<!--			</div>-->
<!--		</div>-->
<!--		<template v-for="dep in department.departmentChildren">-->
<!--			<StructureGroup :department="dep.departmentChildren"/>-->
<!--		</template>-->
<!--	</div>-->
<!--</template>-->

<!--<script>-->
<!--	export default {-->
<!--		name: 'StructureGroup',-->
<!--		props: {-->
<!--			department: Object-->
<!--		}-->
<!--	}-->
<!--</script>-->
