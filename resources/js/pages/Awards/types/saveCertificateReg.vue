<template>
	<div class="certificate-creator">
		<VueHtml2pdf
			v-if="!loading"
			ref="html2Pdf"
			:show-layout="false"
			:float-layout="true"
			:pdf-quality="2"
			:preview-modal="false"
			:enable-download="pdfDownloaded"
			pdf-content-width="1000px"
			:manual-pagination="true"
			:html-to-pdf-options="options"
			@progress="onProgress($event)"
			@beforeDownload="beforeDownload($event)"
			@hasDownloaded="hasDownloaded($event)"
		>
			<!-- 1 = 24.4мм -->
			<template #pdf-content>
				<section>
					<div class="html2pdf__page-break">
						<div
							v-if="Object.keys(styles).length > 0"
							class="draggable-container"
						>
							<div
								id="draggable-block"
								class="draggable-block"
							>
								<div
									class="draggable"
									style="margin-top: 40px; position: absolute; top: 0; left: 0; z-index: 12; text-align: center; display: inline-block;"
									:style="{fontStyle: styles.fullName.fontStyle, color: styles.fullName.color, fontSize: styles.fullName.size + 'px', fontWeight: styles.fullName.fontWeight, textTransform: styles.fullName.uppercase, width: styles.fullName.fullWidth + 'px', transform: transformFullName}"
								>
									{{ item.user.name }} {{ item.user.last_name }}
								</div>
								<div
									class="draggable"
									style="margin-top: 120px; position: absolute; top: 0; left: 0; z-index: 12; text-align: center; display: inline-block;"
									:style="{fontStyle: styles.courseName.fontStyle, color: styles.courseName.color, fontSize: styles.courseName.size + 'px', fontWeight: styles.courseName.fontWeight, textTransform: styles.courseName.uppercase, width: styles.courseName.fullWidth + 'px', transform: transformCourseName}"
								>
									{{ item.course.name }}
								</div>
								<div
									class="draggable"
									style="margin-top: 280px; position: absolute; top: 0; left: 0; z-index: 12; text-align: center; display: inline-block;"
									:style="{fontStyle: styles.date.fontStyle, color: styles.date.color, fontSize: styles.date.size + 'px', fontWeight: styles.date.fontWeight, textTransform: styles.date.uppercase, width: styles.date.fullWidth + 'px', transform: transformDateName}"
								>
									{{ $moment(item.ended_at).format('DD.MM.YYYY') }}
								</div>
							</div>
							<vue-pdf-embed
								:source="award.award.tempPath"
								@rendered="renderedEmbed"
							/>
						</div>
					</div>
				</section>
			</template>
		</VueHtml2pdf>
	</div>
</template>

<script>
import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed'
import VueHtml2pdf from './Html2pdf'

export default {
	name: 'SaveCertificate',
	components: {
		VuePdfEmbed,
		VueHtml2pdf,
	},
	props: {
		/* eslint-disable camelcase, vue/prop-name-casing */
		course_id: {
			type: Number,
			default: null
		},
		item: {
			type: Object,
			default: () => ({})
		}
	},
	data() {
		return {
			loading: true,
			award: {},
			transformFullName: {},
			transformCourseName: {},
			transformDateName: {},
			styles: {},
			canvasHeight: null,
			canvasWidth: 1000,
			pdfDownloaded: false,
			options: {
				jsPDF: {
					unit: 'mm',
					format: [],
					orientation: 'portrait'
				}
			}
		}
	},
	async mounted() {
		if(!this.course_id) return
		if(!Object.keys(this.item).length) return
		try {
			const {data} = await this.axios.get('/awards/course?course_id=' + this.course_id)
			this.award = data.data
			if(this.award.award){
				this.styles = JSON.parse(this.award.award.styles)
				this.transformFullName = `translate(${this.styles.fullName.screenX}px, ${this.styles.fullName.screenY}px)`
				this.transformCourseName = `translate(${this.styles.courseName.screenX}px, ${this.styles.courseName.screenY}px)`
				this.transformDateName = `translate(${this.styles.date.screenX}px, ${this.styles.date.screenY}px)`
				this.loading = false
			}
			else {
				const log = {
					title: `user ID - ${this.item.id}. Курс - ${this.item.course.name}. ФИО - ${this.item.user.name} ${this.item.user.last_name}`,
					fileName: null
				}
				this.$emit('generated', null, log)
			}
		}
		catch (error) {
			console.error(error)
		}
	},
	methods: {
		onProgress(progress) {
			this.progress = progress
		},
		beforeDownload() {},
		hasDownloaded(blobPdf) {
			this.pdfDownloaded = true
			const file = new File([blobPdf], `${this.course_id}_${this.item.user.id}_${this.item.course.name}-${this.item.user.name}-${this.item.user.last_name}.pdf`, {
				type: blobPdf.type,
			})
			const log = {
				title: `User ID - ${this.item.id}. Курс - ${this.item.course.name}. ФИО - ${this.item.user.name} ${this.item.user.last_name}`,
				fileName: `${this.course_id}_${this.item.user.id}_${this.item.course.name}-${this.item.user.name}-${this.item.user.last_name}.pdf`
			}
			this.$emit('generated', file, log)
		},
		renderedEmbed() {
			const canvas = document.querySelector('.vue-pdf-embed canvas')
			const canvasHeight = canvas.offsetHeight
			const canvasWidth = canvas.offsetWidth
			const canvasHeightCalc = parseFloat((canvasHeight * 0.264583) + 2).toFixed(2)
			const canvasWidthCalc = parseFloat(canvasWidth * 0.264583).toFixed(2)
			this.options.jsPDF.format = [canvasWidthCalc, canvasHeightCalc]
			this.options.jsPDF.orientation = canvasWidthCalc > canvasHeightCalc ? 'landscape' : 'portrait'
			this.$refs.html2Pdf.generatePdf()
		}
	}
}
</script>


<style lang="scss">
.certificate-creator {
	.layout-container{
		display: none !important;
	}

	.draggable-container {
		position: relative;
		width: 1000px;
	}
}
</style>
