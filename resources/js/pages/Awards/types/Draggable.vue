<template>
    <BRow class="m-0">
        <BCol cols="3">
            <div class="settings">
                <BFormGroup
                        label="Имя и Фамилия (Впишите любое)"
                >
                    <BFormInput
                            v-model="fullName.text"
                    >
                    </BFormInput>
                </BFormGroup>
                <BFormGroup
                        label="Жирность текста"
                >
                    <BFormSelect
                            v-model="fullName.fontWeight"
                            :options="fontWeightList"
                    >
                    </BFormSelect>
                </BFormGroup>
                <BFormGroup
                        label="Размер текста"
                >
                    <BFormInput
                            v-model="fullName.size"
                    >
                    </BFormInput>
                </BFormGroup>
                <BFormGroup>
                    <b-form-checkbox v-model="fullName.fullWidth">Remember my preference</b-form-checkbox>
                </BFormGroup>
                <b-form-group label="Заглавность">
                    <b-form-radio v-model="fullName.uppercase" name="some-radios" value="lowercase">Первая заглавная
                    </b-form-radio>
                    <b-form-radio v-model="fullName.uppercase" name="some-radios" value="uppercase">Все заглавные
                    </b-form-radio>
                </b-form-group>
            </div>
        </BCol>
        <BCol cols="9">
            <div class="draggable-container">
                <div class="draggable-edit">
                    <div ref="myDraggable" class="draggable" :style="styleFullName">{{fullName.text}}</div>
                    <img src="/upload/sertificates/SL_012519_18110_94.jpg" alt="">
                </div>
            </div>
        </BCol>
    </BRow>
</template>

<script>
    import interact from "interactjs";

    export default {
        name: "Draggable",
        data() {
            return {
                selectedUppercase: [],
                fullName: {
                    text: 'Иван Иванович Иванов',
                    size: 12,
                    fontWeight: 700,
                    uppercase: 'lowercase',
                    fullWidth: false
                },
                fontWeightList: [200, 300, 400, 500, 600, 700, 800, 900],
                screenX: 0,
                screenY: 0
            };
        },
        mounted: function () {
            let myDraggable = this.$refs.myDraggable;
            this.initInteract(myDraggable);
        },
        watch: {
            screenX() {
                console.log('x = ' + this.screenX);
            },
            screenY() {
                console.log('y = ' + this.screenY);
            }
        },
        computed: {
            styleFullName() {
                let width = 'auto';
                if (this.fullName.fullWidth) {
                    width = '100%';
                }
                return {
                    fontWeight: this.fullName.fontWeight,
                    fontSize: `${this.fullName.size}px`,
                    textTransform: this.fullName.uppercase,
                    width: width
                };
            }
        },
        methods: {
            initInteract: function (selector) {
                interact(selector).draggable({
                    // enable inertial throwing
                    inertia: true,
                    // keep the element within the area of it's parent
                    restrict: {
                        restriction: "parent",
                        endOnly: true,
                        elementRect: {top: 0, left: 0, bottom: 1, right: 1}
                    },
                    // enable autoScroll
                    autoScroll: true,

                    // call this function on every dragmove event
                    onmove: this.dragMoveListener,
                    // call this function on every dragend event
                    onend: this.onDragEnd
                });
            },
            dragMoveListener: function (event) {
                let target = event.target;
                let x = null;
                if (this.fullName.fullWidth) {
                    x = 0;
                } else {
                    x = (parseFloat(target.getAttribute("data-x")) || this.screenX) + event.dx;
                }
                let y = (parseFloat(target.getAttribute("data-y")) || this.screenY) + event.dy;
                target.style.webkitTransform = target.style.transform = "translate(" + x + "px, " + y + "px)";

                target.setAttribute("data-x", x);
                target.setAttribute("data-y", y);
            },
            onDragEnd: function (event) {
                var target = event.target;
                // update the state
                // this.screenX = target.getBoundingClientRect().left;
                this.screenX = (parseFloat(target.getAttribute("data-x")) || this.screenX);
                // this.screenY = target.getBoundingClientRect().top;
                this.screenY = (parseFloat(target.getAttribute("data-y")) || this.screenY);
            }
        }
    };
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped>
    .draggable-container{
        height: calc(100vh - 140px);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .draggable-edit {
        position: relative;
        img {
            width: auto;
            height: calc(100vh - 160px);
        }

        .draggable {
            position: absolute;
            top: 0;
            left: 0;
            text-align: center;
            display: inline-block;
            padding: 5px;
            border-radius: 6px;
            border: 1px dashed #ddd;
        }
    }
    .settings{
        max-height: calc(100vh - 130px);
        min-height: calc(100vh - 130px);
        padding: 20px 10px;
        overflow: auto;
        border-right: 1px solid #ddd;
    }
</style>
