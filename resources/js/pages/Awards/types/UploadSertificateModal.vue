<template>
  <BRow>
    <BCol cols="10">
      <v-stage ref="stage" :config="stageSize">
        <v-layer ref="layer">
          <v-image
            :config="{
              image: image,
            }"
          />
        </v-layer>
      </v-stage>
      <v-stage
        ref="stage"
        type="file"
        :config="stageSize"
        @mousemove="handleMouseMove"
        @mouseDown="handleMouseDown"
        @mouseUp="handleMouseUp"
      >
        <v-layer ref="layer">
          <v-text
            ref="text"
            :config="{
              x: 10,
              y: 10,
              fontSize: 20,
              text: text,
              fill: 'black',
            }"
          />
          <v-rect
            v-for="(rec, index) in recs"
            :key="index"
            :config="{
              x: Math.min(rec.startPointX, rec.startPointX + rec.width),
              y: Math.min(rec.startPointY, rec.startPointY + rec.height),
              width: Math.abs(rec.width),
              height: Math.abs(rec.height),
              fill: 'rgb(0,0,0,0)',
              stroke: 'black',
              strokeWidth: 3,
            }"
          />
        </v-layer>
      </v-stage>
      <div id="container"></div>
    </BCol>
  </BRow>
</template>

<script>
const width = window.innerWidth;
const height = window.innerHeight;
export default {
  name: "UploadSertificateModal",
  props: {
    sertificate: File,
    img: String,
  },
  data() {
    return {
      stageSize: {
        width: width,
        height: height,
      },
      text: "Try to draw a rectangle",
      lines: [],
      isDrawing: false,
      recs: [],
      image: null,
    };
  },
  mounted() {
    console.log(this.sertificate, 'sert');
    const image = new window.Image();
    image.src = `http://bp.localhost.com/upload/sertificates/${this.sertificate.name}`;
    image.onload = () => {
      // set image only when it is loaded
      this.image = image;
    };

    var width = window.innerWidth;
    var height = window.innerHeight;

    var stage = new Konva.Stage({
      container: "container",
      width: width,
      height: height,
      draggable: true,
    });

    var layer = new Konva.Layer();
    stage.add(layer);
  },
  methods: {
    handleMouseDown(event) {
      this.isDrawing = true;
      const pos = this.$refs.stage.getNode().getPointerPosition();
      this.setRecs([
        ...this.recs,
        { startPointX: pos.x, startPointY: pos.y, width: 0, height: 0 },
      ]);
    },
    handleMouseUp() {
      this.isDrawing = false;
    },
    setRecs(element) {
      this.recs = element;
    },
    handleMouseMove(event) {
      // no drawing - skipping
      if (!this.isDrawing) {
        return;
      }
      // console.log(event);
      const point = this.$refs.stage.getNode().getPointerPosition();
      // handle  rectangle part
      let curRec = this.recs[this.recs.length - 1];
      curRec.width = point.x - curRec.startPointX;
      curRec.height = point.y - curRec.startPointY;
    },
  },
};
</script>

<style>
.modal-sertificate {
  width: 100%;
}
.canvas-blocks {
  display: flex;
  flex-direction: column;
}
.box {
  width: 200px;
  height: 100px;
  border: 1px solid #0515f0;
}
.modal-dialog {
  max-width: 80%;
}
</style>
