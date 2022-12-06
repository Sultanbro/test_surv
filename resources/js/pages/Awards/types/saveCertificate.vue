<template>
    <div class="certificate-creator">
        <vue-html2pdf
                :show-layout="true"
                :float-layout="true"
                :pdf-quality="2"
                :preview-modal="true"
                :enable-download="false"
                pdf-content-width="1000px"
                :manual-pagination="true"
                ref="html2Pdf"
                :html-to-pdf-options="options"
                @progress="onProgress($event)"
                @beforeDownload="beforeDownload($event)"
                @hasDownloaded="hasDownloaded($event)"
        >
            <!-- 1 = 24.4мм -->
            <section slot="pdf-content">
                <div class="html2pdf__page-break">
                    <div class="draggable-container" v-if="Object.keys(styles).length > 0">
                        <div class="draggable-block" id="draggable-block">
                            <div class="draggable"
                                 style="margin-top: 40px; position: absolute; top: 0; left: 0; z-index: 12; text-align: center; display: inline-block;"
                                 :style="{color: styles.fullName.color, fontSize: styles.fullName.size + 'px', fontWeight: styles.fullName.fontWeight, textTransform: styles.fullName.uppercase, display: styles.fullName.fullWidth ? 'block' : 'inline-block', width: styles.fullName.fullWidth ? '100%' : 'auto', transform: transformFullName}"
                            >
                                {{award.course_results.user.name}} {{award.course_results.user.last_name}}
                            </div>
                            <div class="draggable"
                                 style="margin-top: 120px; position: absolute; top: 0; left: 0; z-index: 12; text-align: center; display: inline-block;"
                                 :style="{color: styles.courseName.color, fontSize: styles.courseName.size + 'px', fontWeight: styles.courseName.fontWeight, textTransform: styles.courseName.uppercase, display: styles.courseName.fullWidth ? 'block' : 'inline-block', width: styles.courseName.fullWidth ? '100%' : 'auto', transform: transformCourseName}"
                            >
                                {{award.name}}
                            </div>
<!--                            <div class="draggable"-->
<!--                                 style="margin-top: 200px; position: absolute; top: 0; left: 0; z-index: 12; text-align: center; display: inline-block;"-->
<!--                                 :style="{color: styles.hours.color, fontSize: styles.hours.size + 'px', fontWeight: styles.hours.fontWeight, textTransform: styles.hours.uppercase, display: styles.hours.fullWidth ? 'block' : 'inline-block', width: styles.hours.fullWidth ? '100%' : 'auto', transform: transformHoursName}"-->
<!--                            >{{img.time}}-->
<!--                            </div>-->
                            <div class="draggable"
                                 style="margin-top: 280px; position: absolute; top: 0; left: 0; z-index: 12; text-align: center; display: inline-block;"
                                 :style="{color: styles.date.color, fontSize: styles.date.size + 'px', fontWeight: styles.date.fontWeight, textTransform: styles.date.uppercase, display: styles.date.fullWidth ? 'block' : 'inline-block', width: styles.date.fullWidth ? '100%' : 'auto', transform: transformDateName}"
                            >
                                {{award.course_results.ended_at}}
                            </div>
                        </div>
                        <vue-pdf-embed :source="award.award.tempPath" @rendered="renderedEmbed"/>
                    </div>
                </div>
            </section>
        </vue-html2pdf>
    </div>
</template>

<script>
    import VuePdfEmbed from "vue-pdf-embed/dist/vue2-pdf-embed";
    import VueHtml2pdf from "./Html2pdf";

    export default {
        name: 'save-certificate',
        components: {
            VuePdfEmbed,
            'vue-html2pdf': VueHtml2pdf,
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
        methods: {
            onProgress(progress) {
                this.progress = progress;
                console.log(`PDF generation progress: ${progress}%`)
            },
            beforeDownload(){

            },
            hasDownloaded(blobPdf) {
                this.pdfDownloaded = true;
                let file = new File([blobPdf], this.title);
                const formData = new FormData();
                formData.append('course_id', this.course_id);
                formData.append('award_id', this.award.id);
                formData.append('user_id', this.user_id);
                formData.append('file', file);
                // this.axios
                //     .post("/awards/reward", formData, {
                //         headers: {
                //             'Content-Type': 'multipart/form-data'
                //         },
                //     })
                //     .then(response => {
                //         console.log(response);
                //         this.$emit('generate-success');
                //     })
                //     .catch(function (error) {
                //         console.log(error);
                //     });
            },
            renderedEmbed() {
                const canvas = document.querySelector('.vue-pdf-embed canvas');
                console.log(canvas);
                let canvasHeight = canvas.offsetHeight;
                let canvasWidth = canvas.offsetWidth;
                let canvasHeightCalc = parseFloat((canvasHeight * 0.264583) + 2).toFixed(2);
                let canvasWidthCalc = parseFloat(canvasWidth * 0.264583).toFixed(2);
                console.log(canvasHeightCalc);
                console.log(canvasWidthCalc);
                this.options.jsPDF.format = [canvasWidthCalc, canvasHeightCalc];
                if (canvasWidthCalc > canvasHeightCalc) {
                    this.options.jsPDF.orientation = 'landscape';
                } else {
                    this.options.jsPDF.orientation = 'portrait';
                }
                this.$refs.html2Pdf.generatePdf();
            }
        },
        async mounted() {
            if (this.course_id) {
                await this.axios
                    .get('/awards/course?course_id=' + this.course_id)
                    .then(response => {
                        const data = response.data.data;
                        this.award = data;
                        console.log(this.award);
                        this.styles = JSON.parse(data.award.styles);
                        this.transformFullName = `translate(${this.styles.fullName.screenX}px, ${this.styles.fullName.screenY}px)`;
                        this.transformCourseName = `translate(${this.styles.courseName.screenX}px, ${this.styles.courseName.screenY}px)`;
                        this.transformDateName = `translate(${this.styles.date.screenX}px, ${this.styles.date.screenY}px)`;
                        this.loading = false;
                    })
                    .catch(error => {
                        console.log(error);
                    })
            }
        }
    }
</script>


<style lang="scss">
    .certificate-creator {
        canvas {
        }

        .draggable-container {
            position: relative;
            width: 1000px;
        }
    }
</style>