<template>
    <BRow class="m-0 cestificates-constructor">
        <BCol cols="3">
            <div class="settings">
                <BFormGroup>
                    <b-form-checkbox v-model="border">Убрать вспомогательные элементы</b-form-checkbox>
                </BFormGroup>
                <div v-if="selectedEdit === 1">
                    <BFormGroup>
                        <h4>Имя и Фамилия</h4>
                    </BFormGroup>
                    <BFormGroup
                            label="Имя и Фамилия (Впишите любое)"
                            description="Это поле будет брать имя и фамилию того сотрудника, который пройдет курс"
                    >
                        <BFormInput v-model="textFullName"></BFormInput>
                    </BFormGroup>
                    <BFormGroup label="Жирность текста">
                        <BFormSelect v-model="fullName.fontWeight" :options="fontWeightList"></BFormSelect>
                    </BFormGroup>
                    <BFormGroup label="Размер текста">
                        <BFormInput v-model="fullName.size"></BFormInput>
                    </BFormGroup>
                    <BFormGroup
                            description="Это позволит выводить текст по центру"
                    >
                        <b-form-checkbox v-model="fullName.fullWidth">На всю ширину</b-form-checkbox>
                    </BFormGroup>
                    <BFormGroup label="Цвет текста"><input type="color" class="color-picker" v-model="fullName.color">
                    </BFormGroup>
                    <b-form-group label="Заглавность">
                        <b-form-radio v-model="fullName.uppercase" name="some-radios" value="lowercase">Первая заглавная
                        </b-form-radio>
                        <b-form-radio v-model="fullName.uppercase" name="some-radios" value="uppercase">Все заглавные
                        </b-form-radio>
                    </b-form-group>
                </div>

                <div v-if="selectedEdit === 2">
                    <BFormGroup>
                        <h4>Название курса</h4>
                    </BFormGroup>
                    <BFormGroup
                            label="Название курса"
                            description="Это поле будет брать название курса, который пройдет сотрудник"
                    >
                        <BFormInput v-model="textCourseName"></BFormInput>
                    </BFormGroup>
                    <BFormGroup label="Жирность текста">
                        <BFormSelect v-model="courseName.fontWeight" :options="fontWeightList"></BFormSelect>
                    </BFormGroup>
                    <BFormGroup label="Размер текста">
                        <BFormInput v-model="courseName.size"></BFormInput>
                    </BFormGroup>
                    <BFormGroup
                            description="Это позволит выводить текст по центру"
                    >
                        <b-form-checkbox v-model="courseName.fullWidth">На всю ширину</b-form-checkbox>
                    </BFormGroup>
                    <BFormGroup label="Цвет текста"><input type="color" class="color-picker" v-model="courseName.color">
                    </BFormGroup>
                    <b-form-group label="Заглавность">
                        <b-form-radio v-model="courseName.uppercase" name="some-radios" value="lowercase">Первая
                            заглавная
                        </b-form-radio>
                        <b-form-radio v-model="courseName.uppercase" name="some-radios" value="uppercase">Все заглавные
                        </b-form-radio>
                    </b-form-group>
                </div>
                <div v-if="selectedEdit === 3">
                    <BFormGroup>
                        <h4>Количетсво потраченных часов на курс</h4>
                    </BFormGroup>
                    <BFormGroup
                            label="Потраченное время на курс"
                            description="Здесь будет время, за которое пройден курс"
                    >
                        <BFormInput v-model="textHours"></BFormInput>
                    </BFormGroup>
                    <BFormGroup label="Жирность текста">
                        <BFormSelect v-model="hours.fontWeight" :options="fontWeightList"></BFormSelect>
                    </BFormGroup>
                    <BFormGroup label="Размер текста">
                        <BFormInput v-model="hours.size"></BFormInput>
                    </BFormGroup>
                    <BFormGroup
                            description="Это позволит выводить текст по центру"
                    >
                        <b-form-checkbox v-model="hours.fullWidth">На всю ширину</b-form-checkbox>
                    </BFormGroup>
                    <BFormGroup label="Цвет текста"><input type="color" class="color-picker" v-model="hours.color">
                    </BFormGroup>
                    <b-form-group label="Заглавность">
                        <b-form-radio v-model="hours.uppercase" name="some-radios" value="lowercase">Первая
                            заглавная
                        </b-form-radio>
                        <b-form-radio v-model="hours.uppercase" name="some-radios" value="uppercase">Все заглавные
                        </b-form-radio>
                    </b-form-group>
                </div>
                <div v-if="selectedEdit === 4">
                    <BFormGroup>
                        <h4>Дата завершения курса</h4>
                    </BFormGroup>
                    <BFormGroup
                            label="Дата"
                            description="Здесь будет дата окончания курса"
                    >
                        <BFormInput v-model="textDate"></BFormInput>
                    </BFormGroup>
                    <BFormGroup label="Жирность текста">
                        <BFormSelect v-model="date.fontWeight" :options="fontWeightList"></BFormSelect>
                    </BFormGroup>
                    <BFormGroup label="Размер текста">
                        <BFormInput v-model="date.size"></BFormInput>
                    </BFormGroup>
                    <BFormGroup
                            description="Это позволит выводить текст по центру"
                    >
                        <b-form-checkbox v-model="date.fullWidth">На всю ширину</b-form-checkbox>
                    </BFormGroup>
                    <BFormGroup label="Цвет текста"><input type="color" class="color-picker" v-model="date.color">
                    </BFormGroup>
                    <b-form-group label="Заглавность">
                        <b-form-radio v-model="date.uppercase" name="some-radios" value="lowercase">Первая
                            заглавная
                        </b-form-radio>
                        <b-form-radio v-model="date.uppercase" name="some-radios" value="uppercase">Все заглавные
                        </b-form-radio>
                    </b-form-group>
                </div>
            </div>
        </BCol>
        <BCol cols="9">
            <div class="draggable-container">
                <div class="draggable-edit">
                    <div ref="fullName" name="fullName" class="draggable" follow-text="Имя и фамилия"
                         :style="styleFullName" style="margin-top: 200px;"
                         :class="{'no-border': border, 'active': selectedEdit === 1}" @click="selectEdit(1)">
                        {{textFullName}}
                    </div>
                    <div ref="courseName" name="courseName" class="draggable" follow-text="Название курса"
                         :style="styleCourseName" style="margin-top: 290px;"
                         :class="{'no-border': border, 'active': selectedEdit === 2}" @click="selectEdit(2)">
                        {{textCourseName}}
                    </div>
                    <div ref="hours" name="hours" class="draggable" follow-text="Потраченное время на курсы"
                         :style="styleHours" style="margin-top: 380px;"
                         :class="{'no-border': border, 'active': selectedEdit === 3}" @click="selectEdit(3)">
                        {{textHours}}
                    </div>
                    <div ref="date" name="date" class="draggable" follow-text="Дата завершения курса"
                         :style="styleDate" style="margin-top: 470px;"
                         :class="{'no-border': border, 'active': selectedEdit === 4}" @click="selectEdit(4)">
                        {{textDate}}
                    </div>
                    <vue-pdf-embed v-if="img" :source="img"/>
                </div>
            </div>
        </BCol>
    </BRow>
</template>

<script>
    import interact from "interactjs";
    import VuePdfEmbed from "vue-pdf-embed/dist/vue2-pdf-embed";

    export default {
        name: "Draggable",
        props: {
            sertificate: File,
            img: String,
        },
        components: {
            VuePdfEmbed
        },
        data() {
            return {
                selectedUppercase: [],
                border: false,
                selectedEdit: 1,
                textFullName: 'Иван Иванович Иванов',
                textCourseName: 'Название курса',
                textHours: 'В объеме 100 часа(ов) с домашними заданиями',
                textDate: new Date().toLocaleDateString(),
                fullName: {
                    screenX: 0,
                    screenY: 0,
                    size: 32,
                    fontWeight: 700,
                    uppercase: 'lowercase',
                    fullWidth: false,
                    color: '#000000'
                },
                courseName: {
                    screenX: 0,
                    screenY: 0,
                    text: 'Название курса',
                    size: 20,
                    fontWeight: 400,
                    uppercase: 'lowercase',
                    fullWidth: false,
                    color: '#000000'
                },
                hours: {
                    screenX: 0,
                    screenY: 0,
                    text: 'Название курса',
                    size: 16,
                    fontWeight: 400,
                    uppercase: 'lowercase',
                    fullWidth: false,
                    color: '#000000'
                },
                date: {
                    screenX: 0,
                    screenY: 0,
                    text: 'Название курса',
                    size: 16,
                    fontWeight: 400,
                    uppercase: 'lowercase',
                    fullWidth: false,
                    color: '#000000'
                },
                fontWeightList: [200, 300, 400, 500, 600, 700, 800, 900]
            };
        },
        mounted: function () {
            let fullNameEdit = this.$refs.fullName;
            let courseNameEdit = this.$refs.courseName;
            let hoursEdit = this.$refs.hours;
            let dateEdit = this.$refs.date;
            this.initInteract(fullNameEdit);
            this.initInteract(courseNameEdit);
            this.initInteract(hoursEdit);
            this.initInteract(dateEdit);
        },
        watch: {
            fullName: {
                handler: function (newValue) {
                    console.log('x = ' + newValue.screenX);
                    console.log('y = ' + newValue.screenY);
                },
                deep: true
            },
            courseName: {
                handler: function (newValue) {
                    console.log('x = ' + newValue.screenX);
                    console.log('y = ' + newValue.screenY);

                },
                deep: true
            },
            hours: {
                handler: function (newValue) {
                    console.log('x = ' + newValue.screenX);
                    console.log('y = ' + newValue.screenY);

                },
                deep: true
            },
            date: {
                handler: function (newValue) {
                    console.log('x = ' + newValue.screenX);
                    console.log('y = ' + newValue.screenY);

                },
                deep: true
            },
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
                    width: width,
                    color: this.fullName.color
                };
            },
            styleCourseName() {
                let width = 'auto';
                if (this.courseName.fullWidth) {
                    width = '100%';
                }
                return {
                    fontWeight: this.courseName.fontWeight,
                    fontSize: `${this.courseName.size}px`,
                    textTransform: this.courseName.uppercase,
                    width: width,
                    color: this.courseName.color
                };
            },
            styleHours() {
                let width = 'auto';
                if (this.hours.fullWidth) {
                    width = '100%';
                }
                return {
                    fontWeight: this.hours.fontWeight,
                    fontSize: `${this.hours.size}px`,
                    textTransform: this.hours.uppercase,
                    width: width,
                    color: this.hours.color
                };
            },
            styleDate() {
                let width = 'auto';
                if (this.date.fullWidth) {
                    width = '100%';
                }
                return {
                    fontWeight: this.date.fontWeight,
                    fontSize: `${this.date.size}px`,
                    textTransform: this.date.uppercase,
                    width: width,
                    color: this.date.color
                };
            }
        },
        methods: {
            selectEdit(val) {
                this.selectedEdit = val;
            },
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
                let name = target.getAttribute("name");
                let x = null;
                let y = null;
                if (name === 'fullName') {
                    x = (parseFloat(target.getAttribute("data-x")) || this.fullName.screenX) + event.dx;
                }
                if (name === 'courseName') {
                    x = (parseFloat(target.getAttribute("data-x")) || this.courseName.screenX) + event.dx;
                }
                if (name === 'hours') {
                    x = (parseFloat(target.getAttribute("data-x")) || this.hours.screenX) + event.dx;
                }
                if (name === 'date') {
                    x = (parseFloat(target.getAttribute("data-x")) || this.date.screenX) + event.dx;
                }

                if (name === 'fullName') {
                    y = (parseFloat(target.getAttribute("data-y")) || this.fullName.screenY) + event.dy;
                }
                if (name === 'courseName') {
                    y = (parseFloat(target.getAttribute("data-y")) || this.courseName.screenY) + event.dy;
                }
                if (name === 'hours') {
                    y = (parseFloat(target.getAttribute("data-y")) || this.hours.screenY) + event.dy;
                }
                if (name === 'date') {
                    y = (parseFloat(target.getAttribute("data-y")) || this.date.screenY) + event.dy;
                }
                target.style.webkitTransform = target.style.transform = "translate(" + x + "px, " + y + "px)";

                target.setAttribute("data-x", x);
                target.setAttribute("data-y", y);
            },
            onDragEnd: function (event) {
                var target = event.target;
                let name = target.getAttribute("name");
                if (name === 'fullName') {
                    this.fullName.screenX = (parseFloat(target.getAttribute("data-x")) || this.fullName.screenX);
                    this.fullName.screenY = (parseFloat(target.getAttribute("data-y")) || this.fullName.screenY)
                }
                if (name === 'courseName') {
                    this.courseName.screenX = (parseFloat(target.getAttribute("data-x")) || this.courseName.screenX);
                    this.courseName.screenY = (parseFloat(target.getAttribute("data-y")) || this.courseName.screenY)
                }
                if (name === 'hours') {
                    this.hours.screenX = (parseFloat(target.getAttribute("data-x")) || this.hours.screenX);
                    this.hours.screenY = (parseFloat(target.getAttribute("data-y")) || this.hours.screenY)
                }
                if (name === 'date') {
                    this.date.screenX = (parseFloat(target.getAttribute("data-x")) || this.date.screenX);
                    this.date.screenY = (parseFloat(target.getAttribute("data-y")) || this.date.screenY)
                }
            }
        }
    };
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss">
    .cestificates-constructor{
        canvas{
            width: 100% !important;
            height: auto!important;
        }
        .draggable-container {
            padding: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-group {
            margin-top: 15px;
            margin-bottom: 0 !important;
            padding-bottom: 20px;
            position: relative;

            &:before {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 1px;
                background-color: #ddd;
            }

            &:last-child {
                &:before {
                    content: none;
                }
            }
        }

        .draggable-edit {
            position: relative;
            border: 2px solid #333;
            img {
                width: 100%;
                height: auto;
            }

            .draggable {
                position: absolute;
                top: 0;
                left: 0;
                z-index: 12;
                text-align: center;
                display: inline-block;
                padding: 5px;
                border-radius: 6px;
                border: 1px dashed #ddd;
                box-shadow: 0 0 5px 0 #333;
                background-color: #fff;
                &:before {
                    content: attr(follow-text);
                    position: absolute;
                    top: -22px;
                    left: 0;
                    font-size: 12px;
                    color: #000;
                    background-color: #fff;
                    padding: 3px 6px;
                    border-radius: 4px;
                    z-index: 2;
                    white-space: nowrap;
                }

                &.no-border {
                    border: none;
                    padding: 0;
                    border-radius: 0;
                    background: transparent;
                    box-shadow: none;
                    &:before {
                        content: none;
                    }
                    &.active {
                        border: 1px dashed #000;
                        background-color: rgba(255, 255, 255, 0.4);
                    }

                    &:hover {
                        border: 1px dashed #000;
                        background-color: rgba(255, 255, 255, 0.7);
                    }

                    &:active {
                        border: 1px dashed #000;
                        background-color: rgba(255, 255, 255, 0.9);
                    }
                }

                &:hover {
                    border: 1px dashed #000;
                }

                &:active {
                    border: 1px dashed #000;
                    background-color: #d4d4d4;
                }
            }
        }

        .settings {
            max-height: calc(100vh - 130px);
            min-height: calc(100vh - 130px);
            padding: 20px 10px;
            overflow: auto;
            border-right: 1px solid #ddd;
            position: sticky;
            top: 0;
        }

        .color-picker {
            width: 100%;
            height: 40px;
        }
    }
</style>
