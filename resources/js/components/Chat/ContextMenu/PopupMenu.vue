<template>
	<div
		class="PopupMenu"
		@mousedown.stop
		@contextmenu.prevent
	>
		<slot />
	</div>
</template>

<script>
export default {
	name: 'PopupMenu',
}
</script>

<style lang="scss">
.PopupMenu{
	display: block;
	min-width: 100px;
	border-radius: 8px;
	padding: 10px 0;

	position: absolute;
	z-index: 1000;
	top: 100%;
	right: 0;

	background-color: #fff;
	box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.05), 0px 15px 60px -40px rgba(45, 50, 90, 0.2);

	&-item{
		display: flex;
		align-items: center;
		justify-content: flex-start;
		gap: 10px;

		width: 100%;
		padding: 10px 20px;

		text-decoration: none;
		color: #0a0a0a;
		cursor: pointer;
		&:hover{
			color: #3361FF;
			background: #F6F8FF;
		}
		&_red,
		&_red:hover{
			color: #F8254B;
		}
	}
}
</style>
