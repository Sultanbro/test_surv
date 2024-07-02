<template>
	<a
		class="PromotionalMaterialFile"
		:href="href"
	>
		<i
			class="fa PromotionalMaterialFile-icon"
			:class="[
				`fa-file${type ? '-' + type : ''}`
			]"
		/>
		<div class="PromotionalMaterialFile-name">
			{{ name }}
		</div>
		<div class="PromotionalMaterialFile-download">
			скачать <i class="fa fa-arrow-down" />
		</div>
	</a>
</template>

<script>
const fileTypes = [
	'',
	'alt',
	'image',
	'audio',
	'video',
	'archive',
	'pdf',
	'word',
	'excel',
	'powerpoint',
	'code',
]

export default {
	name: 'PromotionalMaterialFile',
	props: {
		type: {
			type: String,
			default: '',
			validator(value) {
				return fileTypes.includes(value)
			}
		},
		href: {
			type: String,
			default: '',
		},
		name: {
			type: String,
			default: '',
		},
	},
}
</script>

<style lang="scss">
.PromotionalMaterialFile{
	display: flex;
	flex-flow: column nowrap;

	width: 200px;
	padding: 2rem;

	font-size: 1.4rem;
	text-align: center;
	color: #333;

	background-color: #EBEBEB;

	&:hover{
		color: #333;
		.PromotionalMaterialFile-download{
			color: #77e;
		}
	}

	&-icon{
		display: block;
		margin: 0 auto 1rem;
		font-size: 5rem;
	}
	&-name{
		height: 3em;
		margin-bottom: 1rem;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	&-download{
		font-weight: 700;
	}
}
</style>
