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
									{{ award.course_results[0].user.name }} {{ award.course_results[0].user.last_name }}
								</div>
								<div
									class="draggable"
									style="margin-top: 120px; position: absolute; top: 0; left: 0; z-index: 12; text-align: center; display: inline-block;"
									:style="{fontStyle: styles.courseName.fontStyle, color: styles.courseName.color, fontSize: styles.courseName.size + 'px', fontWeight: styles.courseName.fontWeight, textTransform: styles.courseName.uppercase, width: styles.courseName.fullWidth + 'px', transform: transformCourseName}"
								>
									{{ award.name }}
								</div>
								<div
									class="draggable"
									style="margin-top: 280px; position: absolute; top: 0; left: 0; z-index: 12; text-align: center; display: inline-block;"
									:style="{fontStyle: styles.date.fontStyle, color: styles.date.color, fontSize: styles.date.size + 'px', fontWeight: styles.date.fontWeight, textTransform: styles.date.uppercase, width: styles.date.fullWidth + 'px', transform: transformDateName}"
								>
									{{ $moment(award.course_results[0].ended_at).format('DD.MM.YYYY') }}
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
import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed';
import VueHtml2pdf from './Html2pdf';

export default {
	name: 'SaveCertificate',
	components: {
		VuePdfEmbed,
		VueHtml2pdf,
	},
	props: {
		course_id: {
			type: Number,
			default: null
		},
		title: {
			type: String,
			default: ''
		},
		user_id: {
			type: Number,
			default: null
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
		if (this.course_id) {
			await this.axios
				.get('/awards/course?course_id=' + this.course_id)
				.then(response => {
					const data = response.data.data;
					this.award = data;
					this.styles = JSON.parse(data.award.styles);
					this.transformFullName = `translate(${this.styles.fullName.screenX}px, ${this.styles.fullName.screenY}px)`;
					this.transformCourseName = `translate(${this.styles.courseName.screenX}px, ${this.styles.courseName.screenY}px)`;
					this.transformDateName = `translate(${this.styles.date.screenX}px, ${this.styles.date.screenY}px)`;

					if(this.award.course_results.length === 0 || Object.keys(this.award.course_results).length === 0){
						//
					}
					else {
						this.loading = false;
					}
				})
				.catch(error => {
					console.error(error);
				})
		}
	},
	methods: {
		onProgress(progress) {
			this.progress = progress;
		},
		beforeDownload(){

		},
		hasDownloaded(blobPdf) {
			this.pdfDownloaded = true;
			let file = new File([blobPdf], 'qwerty.pdf', {
				type: blobPdf.type,
			});
			const formData = new FormData();
			formData.append('course_id', this.course_id);
			formData.append('award_id', this.award.award_id);
			formData.append('user_id', this.user_id);
			formData.append('file', file);
			this.axios
				.post('/awards/reward', formData, {
					headers: {
						'Content-Type': 'multipart/form-data'
					},
				})
				.then(() => {
					this.$emit('generate-success');
				})
				.catch(function (error) {
					console.error(error);
				});
		},
		renderedEmbed() {
			const canvas = document.querySelector('.vue-pdf-embed canvas');
			let canvasHeight = canvas.offsetHeight;
			let canvasWidth = canvas.offsetWidth;
			let canvasHeightCalc = parseFloat((canvasHeight * 0.264583) + 2).toFixed(2);
			let canvasWidthCalc = parseFloat(canvasWidth * 0.264583).toFixed(2);
			this.options.jsPDF.format = [canvasWidthCalc, canvasHeightCalc];
			if (canvasWidthCalc > canvasHeightCalc) {
				this.options.jsPDF.orientation = 'landscape';
			} else {
				this.options.jsPDF.orientation = 'portrait';
			}
			this.$refs.html2Pdf.generatePdf();
		}
	}
}
</script>


<style lang="scss">
.certificate-creator {
	canvas {}

	.draggable-container {
		position: relative;
		width: 1000px;
	}
}
</style>
