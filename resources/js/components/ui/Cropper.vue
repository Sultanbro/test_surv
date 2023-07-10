<template>
	<JobtronOverlay @close="$emit('close')">
		<div class="JobtronCropper">
			<div class="JobtronCropper-cropper" />
			<JobtronButton @click="onSave">
				Сохранить
			</JobtronButton>
		</div>
	</JobtronOverlay>
</template>

<script>
import JobtronOverlay from '@ui/Overlay.vue'
import JobtronButton from '@ui/Button.vue'
export default {
	name: 'JobtronCropper',
	components: {
		JobtronOverlay,
		JobtronButton,
	},
	props: {
		image: {
			type: File,
			default: null
		},
		options: {
			type: Object,
			default: () => ({})
		}
	},
	data(){
		return {
			croppie: null,
			defaultOptions: {
				enableExif: true,
				viewport: {
					width: 200,
					height: 200,
					type: 'square'
				},
				boundary:{
					width: 300,
					height: 300
				}
			}
		}
	},
	watch: {
		image(){
			this.bindImage()
		}
	},
	mounted(){
		/* global Croppie */
		this.croppie = new Croppie(this.$el.querySelector('.JobtronCropper-cropper'), {
			...this.defaultOptions,
			...this.options,
		})
		this.bindImage()
	},
	beforeUnmount(){
		if(this.croppie) this.croppie.destroy()
	},
	methods: {
		async bindImage(){
			if(!this.image || !this.croppie) return
			console.log('bindImage')

			try {
				const result = await new Promise(resolve => {
					const reader = new FileReader()
					reader.addEventListener('load', () => {
						resolve(reader.result)
					}, false)
					reader.readAsDataURL(this.image)
				})
				this.croppie.bind({
					url: result
				})
			}
			catch (error) {
				alert(error)
			}

			console.log('weasfa')
		},
		async onSave(){
			const blob = await this.croppie.result({
				type: 'blob',
				format: 'jpeg',
				quality: 0.8
			})
			this.$emit('result', blob)
		}
	}
}
</script>

<style lang="scss">
.JobtronCropper{
	display: flex;
	flex-flow: column nowrap;
	align-items: center;
	padding-bottom: 10px;
	position: absolute;
	top: 50%;
	left: 50%;
	background-color: #fff;
	border-radius: 16px;
	transform: translate(-50%, -50%);
	box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.05), 0px 15px 60px -40px rgba(45, 50, 90, 0.2);
	// &-cropper{}
}
</style>
