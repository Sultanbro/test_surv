<template>
	<div class="TextClip">
		<div class="TextClip-short">
			<slot />
		</div>
		<div class="TextClip-full">
			<slot />
		</div>
	</div>
</template>

<script>
export default {
	name: 'TextClip',
	components: {},
}
</script>

<style lang="scss">
.TextClip{
	position: relative;
	&:hover{
		.TextClip-full{
			z-index: 11;
			top: 40px;

			visibility: visible;
			opacity: 1;
		}
	}
	&-short{
		max-height: 3em;
		max-height: 3lh;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	&-full{
		max-width: calc(100% - 20px);
    padding: 10px 20px;
    border: 1px solid #999;

		position: absolute;
    top: 20px;
    left: 10px;

    visibility: hidden;
    opacity: 0;

    font-size: 14px;
    line-height: 1.3;
    text-align: left;

    box-shadow: rgba(0, 0, 0, 0.1) 0px 10px 15px -3px, rgba(0, 0, 0, 0.05) 0px 4px 6px -2px;
    transition: 0.2s all ease;
    border-radius: 10px;
    background-color: #fff;
	}
}
</style>
