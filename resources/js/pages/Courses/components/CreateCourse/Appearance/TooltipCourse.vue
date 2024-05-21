<template>
	<div class="tooltipCourse">
		<slot />
	</div>
</template>

<script>
export default {
	name: 'TooltipCourse'
};
</script>

<style scoped>
.tooltipCourse {
	position: relative;
	z-index: 99999999;
}

.tooltipCourse::before {
	content: '';
	position: absolute;
	top: -6px;
	left: 50%;
	transform: translateX(-50%);
	border-width: 0 8px 8px 8px;
	border-style: solid;
	border-color: transparent transparent #333 transparent;
}

.tooltipCourse {
	width: 285px;
	position: absolute;
	bottom: -100px;
	left: 50%;
	transform: translateX(-50%);
	background-color: #333;
	color: white;
	padding: 10px 25px;
	border-radius: 5px;
}
</style>
