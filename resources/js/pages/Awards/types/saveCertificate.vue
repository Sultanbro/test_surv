<template>
    <div class="certificate-creator">
        <b-button variant="success" style="position: fixed; top: 100px; left: 100px; z-index: 1241241421421"
                  @click="donwload">download
        </b-button>
        <vue-html2pdf
                :show-layout="true"
                :float-layout="true"
                :pdf-quality="2"
                :preview-modal="true"
                :enable-download="pdfDownloaded"
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
                            >{{img.name}}
                            </div>
                            <div class="draggable"
                                 style="margin-top: 120px; position: absolute; top: 0; left: 0; z-index: 12; text-align: center; display: inline-block;"
                                 :style="{color: styles.courseName.color, fontSize: styles.courseName.size + 'px', fontWeight: styles.courseName.fontWeight, textTransform: styles.courseName.uppercase, display: styles.courseName.fullWidth ? 'block' : 'inline-block', width: styles.courseName.fullWidth ? '100%' : 'auto', transform: transformCourseName}"
                            >{{img.certificate}}
                            </div>
                            <div class="draggable"
                                 style="margin-top: 200px; position: absolute; top: 0; left: 0; z-index: 12; text-align: center; display: inline-block;"
                                 :style="{color: styles.hours.color, fontSize: styles.hours.size + 'px', fontWeight: styles.hours.fontWeight, textTransform: styles.hours.uppercase, display: styles.hours.fullWidth ? 'block' : 'inline-block', width: styles.hours.fullWidth ? '100%' : 'auto', transform: transformHoursName}"
                            >{{img.time}}
                            </div>
                            <div class="draggable"
                                 style="margin-top: 280px; position: absolute; top: 0; left: 0; z-index: 12; text-align: center; display: inline-block;"
                                 :style="{color: styles.date.color, fontSize: styles.date.size + 'px', fontWeight: styles.date.fontWeight, textTransform: styles.date.uppercase, display: styles.date.fullWidth ? 'block' : 'inline-block', width: styles.date.fullWidth ? '100%' : 'auto', transform: transformDateName}"
                            >{{img.date}}
                            </div>
                        </div>
                        <vue-pdf-embed :source="this.availables[5].path" @rendered="renderedEmbed" ref="lorem"/>
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
        data() {
            return {
                loading: true,
                img: {
                    name: 'Хайруллин Тимур',
                    certificate: 'За лучшие заслуги лучших',
                    date: '24.11.2022',
                    time: 'Пройдено за 50 часа(ов) вместе с домашними заданиями'
                },
                transformFullName: {},
                transformCourseName: {},
                transformHoursName: {},
                transformDateName: {},
                styles: {},
                canvasHeight: null,
                canvasWidth: 1000,
                availables: [],
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
            async beforeDownload(event) {
                console.log('before Download');
                // console.log(event.html2pdf());
                // event.html2pdf().get(event.options).from(event.pdfContent).toPdf().export().then((pdf) => {
                //     // console.log(pdf);
                // });
                const result = event.html2pdf().get(event.options).from(event.pdfContent).toPdf().get('pdf').then((pdf) => {
                    // console.log(pdf.internal.collections.addImage_images[0]);
                    // console.log(pdf.internal.getVerticalCoordinateString());
                    // console.log(pdf.internal.pageSize.getWidth());
                    // console.log(pdf.internal.pageSize.getHeight());
                });

            },
            hasDownloaded(blobPdf) {
                console.log(`PDF has downloaded yehey`);
                this.pdfDownloaded = true;
                console.log(blobPdf);
                let file = new File([blobPdf], "my name");
                console.log(file);
                const formData = new FormData();

                formData.append('targetable_type', 1);
                formData.append('targetable_id', 2);
                formData.append('award_type_id', 2);
                formData.append('name', 'test from js');
                formData.append('description', 'test from js');
                formData.append('hide', 1);
                formData.append('file', file);
                formData.append('styles', 'ss');
                formData.append('course_ids[]', 1);
                formData.append('course_ids[]', 2);
                // this.axios
                //     .post("/awards/store", formData, {
                //         headers: {
                //             'Content-Type': 'multipart/form-data'
                //         },
                //     })
                //     .then(response => {
                //         console.log(response);
                //     })
                //     .catch(function (error) {
                //         console.log(error);
                //     });
            },
            renderedEmbed(){
                 const canvas = document.querySelector('.vue-pdf-embed canvas');
                 let canvasHeight = canvas.offsetHeight;
                 let canvasWidth = canvas.offsetWidth;
                 let canvasHeightCalc = parseFloat((canvasHeight * 0.264583) + 2).toFixed(2);
                 let canvasWidthCalc = parseFloat(canvasWidth * 0.264583).toFixed(2);
                console.log(canvasHeightCalc);
                console.log(canvasWidthCalc);
                 this.options.jsPDF.format = [canvasWidthCalc, canvasHeightCalc];
                 if(canvasWidthCalc > canvasHeightCalc){
                     this.options.jsPDF.orientation = 'landscape';
                 } else{
                     this.options.jsPDF.orientation = 'portrait';
                 }
                this.$refs.html2Pdf.generatePdf();
            },
            donwload(){
                this.$refs.html2Pdf.generatePdf();
            }
        },
         mounted() {
            this.axios
                .get('/awards/type?award_type_id=2')
                .then(response => {
                    this.availables = response.data.data.available;
                    this.styles = JSON.parse(JSON.parse(this.availables[5].styles).replace(/\\"/g, '\''));
                    this.transformFullName = `translate(${this.styles.fullName.screenX}px, ${this.styles.fullName.screenY}px)`;
                    this.transformCourseName = `translate(${this.styles.courseName.screenX}px, ${this.styles.courseName.screenY}px)`;
                    this.transformHoursName = `translate(${this.styles.hours.screenX}px, ${this.styles.hours.screenY}px)`;
                    this.transformDateName = `translate(${this.styles.date.screenX}px, ${this.styles.date.screenY}px)`;
                    this.loading = false;
                })
                .catch(error => {
                    console.log(error);
                })
        }
    }
</script>


<style lang="scss">
    .certificate-creator{
        canvas{
        }
        .draggable-container{
            position: relative;
            width: 1000px;
        }
    }
</style>