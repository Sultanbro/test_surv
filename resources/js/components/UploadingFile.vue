<template>
	<div class="uploading-video mt-2">
		<span
			:class="{
				'status-canceled': status == 'canceled'
			}"
		> {{ file.fileName }} </span>
		<small v-if="status == 'success'">Загружено!</small>
		<small v-else-if="status == 'retrying'">Ошибка, повторная попытка...</small>
		<small v-else-if="status == 'error'">Ошибка! Не получилось загрузить</small>
		<small v-else-if="status == 'canceled'">Отменено</small>
		<small v-else>Загружается {{ uploadedAmount }}%</small>

		<span v-if="isUploading">
			<button
				class="btn mr-1"
				@click="isPaused ? resume() : pause()"
			>{{ isPaused ? "продолжить" : "пауза" }}</button>
			<button
				class="btn"
				@click="cancel()"
			>отмена</button>
		</span>
	</div>
</template>

<script>
export default {
	props: {
		file: {
			type: Object,
			default: null
		},
		status: {
			type: String,
			default: ''
		},
		progress: {
			type: Number,
			default: 0
		},
	},
	data(){
		return {
			isPaused: false // we upload straight away by default
		}
	},
	computed: {
		isUploading(){
			return (this.status !=='success' && this.status !== 'canceled')
		},
		uploadedAmount(){
			return (this.progress * 100).toFixed(2)
		},
	},
	methods: {
		upload(){
			this.file.resumableObj.upload()
			this.isPaused = false
		},
		pause(){
			this.file.pause()
			this.isPaused = true
		},
		resume(){
			this.pause() // not sure why, but we have to call pause again before upload will resume
			this.upload()
		},
		cancel(){
			this.$emit('cancel', this.file)
		}
	}
}
</script>

<style lang="scss">
.uploading-video {
    .status-canceled {
        text-decoration: line-through;
    }
}
</style>
