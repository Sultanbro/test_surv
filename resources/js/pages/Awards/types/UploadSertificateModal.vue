<template>
  <BRow>
    <BCol cols="10">
      <BImg
        v-b-modal.modal-1-xl
        :src="img"
        class="mb-3 modal-sertificate"
        fluid
        block
        rounded
      ></BImg>
      <canvas width="100%" height="100%" ref="canvas"> </canvas>

      <v-stage
        ref="stage"
        :src="img"
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
    <BCol cols="2">
      <BCardGroup class="canvas-blocks" deck columns>
        <BCard
          border-variant="primary"
          header="Primary"
          header-bg-variant="primary"
          header-text-variant="white"
          align="center"
        >
        </BCard>

        <BCard
          border-variant="secondary"
          header="Secondary"
          header-border-variant="secondary"
          align="center"
        >
        </BCard>

        <BCard border-variant="success" header="Success" align="center">
        </BCard>
      </BCardGroup>
    </BCol>
    <div class="" id="container"></div>
  </BRow>
</template>

<script>
const width = window.innerWidth;
const height = window.innerHeight;
export default {
  name: "UploadSertificateModal",
  props: {
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
    };
  },
  mounted() {
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
    console.log(this.img);
    const image = new window.Image();
    image.src = `https://konvajs.org/assets/yoda.jpg`;

    // another solution is to use rectangle shape
    var background = new Konva.Rect({
      x: 0,
      y: 0,
      width: stage.width(),
      height: stage.height(),
      fillLinearGradientStartPoint: { x: 0, y: 0 },
      fillLinearGradientEndPoint: { x: stage.width(), y: stage.height() },
      // gradient into transparent color, so we can see CSS styles
      fillPatternImage: img,
      fillLinearGradientColorStops: [
        0,
        "yellow",
        0.5,
        "blue",
        0.6,
        "rgba(0, 0, 0, 0)",
      ],
      // remove background from hit graph for better perf
      // because we don't need any events on the background
      listening: false,
    });
    layer.add(background);
    // the stage is draggable
    // that means absolute position of background may change
    // so we need to reset it back to {0, 0}

    stage.on("dragmove", () => {
      background.absolutePosition({ x: 0, y: 0 });
    });

    // add demo shape
    var circle = new Konva.Circle({
      x: stage.width() / 2,
      y: stage.height() / 2,
      radius: 100,
      fill: "red",
    });
    layer.add(circle);
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
