<template>
  <BRow>
    <BCol cols="10">
      <div class="konva-stage" id="container" ref="container"></div>
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
    </BCol>
  </BRow>
</template>

<script>
var width = window.innerWidth;
var height = window.innerHeight;

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
      stageWidth: null,
      stageHeight: null,
    };
  },
  mounted() {
    console.log(this.sertificate, "sert");
    const image = new window.Image();
    image.src = `http://bp.localhost.com/upload/sertificates/${this.sertificate.name}`;
    image.onload = () => {
      console.log(image.width, "11111111111111111111111;");
      this.stageWidth = image.width;
      console.log(this.stageWidth, "0000000000000000;");
      this.stageHeight = image.height;
      // set image only when it is loaded
      this.image = image;
      var width = window.innerWidth;

      console.log(image.width, "22222222222222222222;");
      var stage = new Konva.Stage({
        container: "container",
        width: this.stageWidth,
        draggable: true,
      });

      var layer = new Konva.Layer();
      stage.add(layer);

      console.log(this.stageWidth, "3333333333333333333333333;");
      // another solution is to use rectangle shape
      var background = new Konva.Rect({
        x: 0,
        y: 0,
        width: stage.width(),
        height: stage.height(),
        listening: false,
      });
      var imageObj = new Image();
      imageObj.onload = function () {
        background.fillPatternImage(imageObj);
      };
      imageObj.src = `http://bp.localhost.com/upload/sertificates/${this.sertificate.name}`;
      layer.add(background);
      // the stage is draggable
      // that means absolute position of background may change
      // so we need to reset it back to {0, 0}

      stage.on("dragmove", () => {
        background.absolutePosition({ x: 0, y: 0 });
      });

      // add demo shape
      var rec = new Konva.Rect({
        x: stage.width() / 2,
        y: stage.height() / 2,
        width: stage.width() / 2,
        height: stage.height() / 2,
        fill: "rgb(0,0,0,0)",
        stroke: "black",
        strokeWidth: 3,
      });
      layer.add(rec);
    };
  },
  methods: {
    getWidth() {
      console.log(this.stageWidth, "stageWidth");
      let res =
        this.stageWidth <= container.width ? this.stageWidth : container;
      return res;
    },
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
body {
  margin: 0;
  padding: 0;
}
.konva-stage {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
}
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
