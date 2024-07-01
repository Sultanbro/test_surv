<template functional>
	<svg
		width="19"
		height="19"
		viewBox="0 0 24 24"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
		v-bind="data.attrs"
		:class="['AuthIcon', data.staticClass]"
	>
		<g clip-path="url(#clip0_50_413)">
			<path
				d="M20.25 12H3.75"
				stroke="#333333"
				stroke-width="1.5"
				stroke-linecap="round"
				stroke-linejoin="round"
				class="AuthIcon-stroke"
			/>
			<path
				d="M10.5 5.25L3.75 12L10.5 18.75"
				stroke="#333333"
				stroke-width="1.5"
				stroke-linecap="round"
				stroke-linejoin="round"
				class="AuthIcon-stroke"
			/>
		</g>
		<defs>
			<clipPath id="clip0_50_413">
				<rect
					width="24"
					height="24"
					fill="white"
				/>
			</clipPath>
		</defs>
	</svg>
</template>

<script>
export default {
	name: 'IconBack',
}
</script>
