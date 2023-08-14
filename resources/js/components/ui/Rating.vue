<template>
	<div
		class="rating"
		:class="{
			'red': Number(grade) < 7,
			'yellow': Number(grade) < 8 && Number(grade) >= 7,
			'green': Number(grade) >= 8,
		}"
	>
		<ul class="list">
			<li
				v-for="star in maxStars"
				:key="star.stars"
				:class="{ 'active': star <= stars }"
				class="star"
				@click="rate(star)"
			>
				<i :class="star <= stars ? 'fa fa-star' : 'fa fa-star-o'" />
			</li>
		</ul>
		<div
			v-if="hasCounter"
			class="info counter"
		>
			<span class="score-rating">{{ stars }}</span>
			<span class="divider">/</span>
			<span class="score-max">{{ maxStars }}</span>
		</div>
	</div>
</template>
<script>
export default {
	name: 'UIRating',
	props: {
		grade: {
			type: Number,
			required: true
		},
		maxStars: {
			type: Array,
			required: true
		},
		hasCounter: {
			type: Boolean,
		},
	},
	data() {
		return {
			stars: this.grade
		}
	},
	methods: {
		rate(star) {
			if (typeof star === 'number' && star <= this.maxStars && star >= 0) {
				this.stars = this.stars === star ? star - 1 : star
			}
		}
	},
}
</script>

<style scoped lang="scss">
.rating {
  &.green i.fa {
    color: #28a745
  }
  &.red i.fa {
    color: #dc3545
  }
  &.yellow i.fa {
    color: #ffe100
  }
}

</style>
