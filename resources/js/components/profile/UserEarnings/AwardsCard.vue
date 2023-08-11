<template>
	<b-card
		class="awards-card mb-2"
		:header="header"
		body-class="p-1"
		header-class="p-2 bg-secondary text-white font-weight-bold"
	>
		<div
			v-for="(award, index) in values"
			:key="index"
			class="temp-placeholder m-1"
		>
			<span>{{ award.name }}</span>
			<img
				:src="award.path"
				alt=""
			>
		</div>
		<div
			v-if="values.length === 0"
			class="text-center w-100"
		>
			Ничего нет
		</div>
	</b-card>
</template>

<script>
export default {
	name: 'AwardsCard',
	props: {
		header: {
			type: String,
			default: ''
		},
		values: {
			type: Array,
			default: () => []
		}
		/*
			values: [{
				imgSrc: string
			}]
		*/
	}
}
</script>

<style lang="scss">
    .awards-card {
        .card-body {
            display: flex;
            flex-wrap: wrap;
            padding: 0;
        }
        .temp-placeholder {
            border: 1px solid #e9ecef;
            width: 150px;
            height: 150px;
            flex-grow: 0;
            background-color: aliceblue;
        }
		.card {
			border: 1px solid #e9ecef;
		}
    }
</style>
