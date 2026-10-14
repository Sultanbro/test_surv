<template functional>
	<svg
		width="48"
		height="48"
		viewBox="0 0 48 48"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
		v-bind="data.attrs"
		:class="['AuthIcon', data.staticClass]"
	>
		<rect
			width="48"
			height="48"
			rx="8"
			fill="#E8EEFD"
		/>
		<g clip-path="url(#clip0_50_812)">
			<path
				d="M15.0656 19.2112L24 24.1012L32.9344 19.2112"
				stroke="#0C50FF"
				stroke-width="1.5"
				stroke-linecap="round"
				stroke-linejoin="round"
			/>
			<path
				d="M24.36 14.3438L32.61 18.8606C32.7278 18.9251 32.8261 19.02 32.8947 19.1354C32.9633 19.2509 32.9997 19.3826 33 19.5169V28.4831C32.9997 28.6174 32.9633 28.7492 32.8947 28.8646C32.8261 28.98 32.7278 29.0749 32.61 29.1394L24.36 33.6563C24.2496 33.7167 24.1258 33.7483 24 33.7483C23.8742 33.7483 23.7504 33.7167 23.64 33.6563L15.39 29.1394C15.2722 29.0749 15.1739 28.98 15.1053 28.8646C15.0367 28.7492 15.0003 28.6174 15 28.4831V19.5169C15.0003 19.3826 15.0367 19.2509 15.1053 19.1354C15.1739 19.02 15.2722 18.9251 15.39 18.8606L23.64 14.3438C23.7504 14.2834 23.8742 14.2517 24 14.2517C24.1258 14.2517 24.2496 14.2834 24.36 14.3438Z"
				stroke="#0C50FF"
				stroke-width="1.5"
				stroke-linecap="round"
				stroke-linejoin="round"
			/>
			<path
				d="M24 24.1022V33.75"
				stroke="#0C50FF"
				stroke-width="1.5"
				stroke-linecap="round"
				stroke-linejoin="round"
			/>
		</g>
		<defs>
			<clipPath id="clip0_50_812">
				<rect
					width="24"
					height="24"
					fill="white"
					transform="translate(12 12)"
				/>
			</clipPath>
		</defs>
	</svg>
</template>

<script>
export default {
	name: 'IconCabinet',
}
</script>
