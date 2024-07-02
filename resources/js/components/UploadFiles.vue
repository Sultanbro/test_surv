<template>
	<div class="upload-files">
		<div
			ref="filedropzone"
			class="file-dropzone"
		>
			<div class="dropzone-display">
				<div class="p-5">
					<h4>Нажмите или перекиньте файл чтобы его загрузить</h4>
				</div>
			</div>
		</div>

		<UploadingFile
			v-for="(file, index) in files"
			:key="file.file.uniqueIdentifier + index"
			:file="file.file"
			:status="file.status"
			:progress="file.progress"
			@cancel="cancelFile"
		/>
	</div>
</template>

<script>
import Resumable from 'resumablejs'
import UploadingFile from './UploadingFile'

export default {
	components: {
		UploadingFile
	},
	props: {
		token: {
			type: String,
			default: ''
		},
		type: {
			type: String,
			default: ''
		},
		/* eslint-disable-next-line */
		file_types: {
			type: Array,
			default: () => []
		},
		id: {
			type: Number,
			default: 0
		},
	},
	data(){
		return {
			files: [], // our local files array, we will pack in extra data to force reactivity
			r: false
		}
	},
	mounted(){
		// init resumablejs on mount
		this.r = new Resumable({
			target:'/file/upload',
			query:{
				_token:this.token,
				id: this.id,
				type: this.type
			},
			maxChunkRetries: 1,
			maxFiles: 1,
			fileType: this.file_types,
			testChunks: false,
		});

		// Resumable.js isn't supported, fall back on a different method
		if(!this.r.support) return alert('Your browser doesn\'t support chunked uploads. Get a better browser.');

		this.r.assignBrowse(this.$refs.filedropzone);
		this.r.assignDrop(this.$refs.filedropzone);

		// set up event listeners to feed into vues reactivity
		this.r.on('fileAdded', file => {
			file.hasUploaded = false
			// keep a list of files with some extra data that we can use as props
			this.files.push({
				file,
				status: 'uploading',
				progress: 0
			})
			this.r.upload()
		})
		this.r.on('fileSuccess', (file, event) => {
			this.findFile(file).status = 'success'
			let res = JSON.parse(event);
			file.name = res.filename;
			file.title = res.filename;
			file.path = res.path;
			file.model = res.model;
			this.$emit('onupload', file);
		})
		this.r.on('fileError', file => {
			this.findFile(file).status = 'error'
		})
		this.r.on('fileRetry', file => {
			this.findFile(file).status = 'retrying'
		})
		this.r.on('fileProgress', (file) => {
			// console.log('fileProgress', progress)
			var localFile = this.findFile(file)
			// if we are doing multiple chunks we may get a lower progress number if one chunk response comes back early
			var progress = file.progress()
			if( progress > localFile.progress)
				localFile.progress = progress

		})
	},
	methods: {
		// finds the file in the local files array
		findFile(file){
			let x = this.files.find(item => item.file.uniqueIdentifier === file.uniqueIdentifier && item.status !== 'canceled');
			return  x ? x : {}
		},
		// cancel an individual file
		cancelFile(file){
			this.findFile(file).status = 'canceled'
			file.cancel()
		}
	}
}
</script>

<style lang="scss">

.p-5 {
	padding: 3rem;
}

.upload-files {
	display: flex;
	flex-direction: column;
}

.file-dropzone {
    height: 168px;
    width: 100%;
    padding: 0px;
	display: flex;
	align-self: center;
	margin-bottom: 0;
    cursor: pointer;

    * {
        pointer-events: none;
    }

    .dropzone-display {
        height: 100%;
        width: 100%;
		display: flex;
		align-items: center;
		justify-content: center;
		flex-direction: column	;
        border: 2px dashed #ccc;
        border-radius: 8px;

        img {
            width: 64px;
        }

		small {
			font-size: 0.65em;
			display: block;
		}
    }
}
</style>
