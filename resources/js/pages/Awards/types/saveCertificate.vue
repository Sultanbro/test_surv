<template>
  <div v-if="availables.length > 0">
      <b-button variant="success" style="position: fixed; top: 100px; left: 100px; z-index: 1241241421421" @click="donwload">download</b-button>
      <vue-html2pdf
              :show-layout="true"
              :float-layout="true"
              :pdf-quality="2"
              :enable-download="true"
              :pdf-format="[6.5, 10]"
              :preview-modal="true"
              pdf-content-width="600px"
              :manual-pagination="true"
              ref="html2Pdf"
              @beforeDownload="lorem($event)"
      >
          <!-- 1 = 24.4мм -->
          <section slot="pdf-content">
              <div class="html2pdf__page-break">
                  <vue-pdf-embed :source="availables[0].path"/>
              </div>
          </section>
      </vue-html2pdf>
  </div>
</template>

<script>
    import VuePdfEmbed from "vue-pdf-embed/dist/vue2-pdf-embed";
    import VueHtml2pdf from "vue-html2pdf";

    export default{
        name: 'save-certificate',
        components: {
            VuePdfEmbed,
            'vue-html2pdf': VueHtml2pdf,
        },
        data(){
            return{
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
                availables: [],
            }
        },
        methods:{
            async lorem(event) {
                console.log(event);
                await event.html2pdf().get(event.options).from(event.pdfContent).toPdf().get('pdf').then((pdf) => {
                    console.log(pdf);
                    console.log(pdf.internal.collections.addImage_images[0]);
                    console.log(pdf.internal.getVerticalCoordinateString());
                    console.log(pdf.internal.pageSize.getWidth());
                    console.log(pdf.internal.pageSize.getHeight());
                }).save();
            },
            donwload(){
                this.$refs.html2Pdf.generatePdf()
            }
        },
        mounted(){
            this.axios
            .get('/awards/type?award_type_id=2')
            .then(response => {
                console.log(response);
                this.availables = response.data.data.available;
                this.styles = JSON.parse(JSON.parse(this.availables[0].styles).replace(/\\"/g, '\''));
                this.transformFullName = `translate(${this.styles.fullName.screenX}px, ${this.styles.fullName.screenY}px)`;
                this.transformCourseName = `translate(${this.styles.courseName.screenX}px, ${this.styles.courseName.screenY}px)`;
                this.transformHoursName = `translate(${this.styles.hours.screenX}px, ${this.styles.hours.screenY}px)`;
                this.transformDateName = `translate(${this.styles.date.screenX}px, ${this.styles.date.screenY}px)`;
            })
            .catch(error => {
                console.log(error);
            })
        }
    }
</script>