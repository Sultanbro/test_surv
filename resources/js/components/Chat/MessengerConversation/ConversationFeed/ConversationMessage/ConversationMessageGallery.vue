<template>
	<div class="ConversationMessageGallery">
		<div
			v-for="(file, key) in files.slice(0, 2)"
			:key="key"
			class="ConversationMessageGallery-item"
			@click="openImage(file)"
		>
			<img
				@load="$emit('loadImage')"
				:src="file.thumbnail_path ? file.thumbnail_path : file.file_path"
				:alt="file.name"
				class="ConversationMessageGallery-image"
			>
			<div
				v-if="files.length > 2 && key === 1"
				class="ConversationMessageGallery-more"
			>
				<span>+{{ files.length - 2 }}</span>
			</div>
			<div
				v-else
				class="ConversationMessageGallery-zoom"
			>
				<ChatIconSearch />
			</div>
		</div>
	</div>
</template>

<script>
import { mapActions } from 'vuex'
import {
	ChatIconSearch,
} from '@icons'

export default {
	name: 'ConversationMessageGallery',
	components: {
		ChatIconSearch,
	},
	props: {
		files: {
			type: Array,
			default: () => []
		}
	},
	methods: {
		...mapActions(['showGallery']),
		isImage(file) {
			const ext = file.name.split('.').pop();
			return ['jpg', 'jpeg', 'png', 'gif'].includes(ext);//todo: другой способ определения. Хранить тип файла в БД.
		},
		getImages() {
			return this.files.filter(file => this.isImage(file)).map(file => file.file_path);
		},
		openImage(image){
			this.showGallery({
				images: this.getImages(),
				index: this.files.findIndex(f => f.id === image.id),
			})
		}
	}
}
</script>

<style lang="scss">
.ConversationMessageGallery{
	display: flex;
	flex-flow: row wrap;
	align-items: flex-start;
	justify-content: center;
	gap: 10px;

	width: 510px;
	max-width: 100%;
	margin-top: 15px;

	&-item{
		width: 250px;
    height: 150px;
		position: relative;
		overflow: hidden;
	}
	&-image{
		width: 250px;
		height: 150px;
		border-radius: 8px;
		object-fit: cover;
	}
	&-more,
	&-zoom{
		display: flex;
		align-items: center;
		justify-content: center;

		width: 50px;
		height: 50px;

		position: absolute;
		z-index: 1;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);

		background: rgba(40, 60, 87, 0.58);
		border-radius: 100px;
	}
	&-more{
		font-weight: 500;
		font-size: 18px;
		line-height: 18px;

		text-align: center;
		letter-spacing: -0.01em;
		color: #fff;
	}
	&-zoom{
		.ChatIcon-line{
			stroke: #fff;
		}
	}
}
@media only screen and (max-width: 670px) {
	.ConversationMessageGallery{
		flex-flow: column nowrap;
		width: auto;
	}
}
</style>
