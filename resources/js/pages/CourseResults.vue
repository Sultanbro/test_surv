<template>
<div class="course-results">
    <div class="d-flex mb-2">
        <button class="btn btn-grey mr-2 rounded" :class="{'btn-primary': type == BY_USER}" @click="type = BY_USER">
            <span>По сотрудникам</span>
        </button>
        <button class="btn btn-grey mr-2 rounded" :class="{'btn-primary': type == BY_GROUP}" @click="type = BY_GROUP">
            <span>По отделам</span>
        </button>
    </div>

    <div v-if="type == BY_USER" class="by_user">

        <div class="table-responsive" v-if="users.items.length > 0">

            <table class="table b-table table-bordered table-sm">

                <tr>
                    <th v-for="(field, index) in users.fields" :key="index" :class="field.class">
                        <div>{{ field.name }}</div>
                    </th>
                </tr>


                <template v-for="(item, i) in users.items">
                    <tr class="pointer" :class="{
                        'expanded-title': item.expanded
                    }">
                        <td v-for="(field, f) in users.fields" :key="f" :class="field.class" @click="expandUser(item)">
                            <div v-if="field.key == 'progress'" class="d-flex jcc aic">
                                <p class="mb-0 mr-1">{{ item[field.key] }}</p>
                                <progress :value="item[field.key].slice(0, -1)" max="100"></progress>
                            </div>
                            <div v-else>{{ item[field.key] }}</div>
                        </td>
                    </tr>

                    <template v-for="(course, c) in item.courses">
                        <tr v-if="item.expanded" class="expanded">
                            <td v-for="(field, f) in users.fields" :key="f" :class="[field.class, {pointer: course.items && course.items.length > 1}]" @click="expandCourse(course, item)">
                                <div v-if="field.key == 'progress'" class="d-flex jcc aic">
                                    <p class="mb-0 mr-1">{{ course[field.key] }}</p>
                                    <progress :value="course[field.key].slice(0, -1)" max="100"></progress>
                                </div>
                                <div v-else-if="field.key == 'name'" class="nullify-wrap relative">

                                    {{ course[field.key] }}

                                    <i class="absolute nullify fa fa-broom" title="Обнулить прогресс" @click="nullify(i, c)"></i>

                                </div>
                                <div v-else>{{ course[field.key] }}</div>
                            </td>
                        </tr>

                        <template v-if="course.items && course.items.length > 1 && course.expanded">
                            <tr v-for="(coureItem, ci) in course.items" :key="ci" class="expanded-course-item">
                                <td v-for="(field, f) in users.fields" :key="f" :class="field.class">
                                    <div v-if="field.key == 'name'" class="nullify-wrap relative">
                                        {{ coureItem[course2item[field.key]] || field.key }}
                                        <i
                                            class="absolute nullify fa fa-broom"
                                            title="Обнулить прогресс"
                                            @click="regress(item.user_id, course.course_id, coureItem)"
                                        />
                                    </div>
                                    <template v-else>
                                        {{ courseItems[coureItem.id] ? coureItem[course2item[field.key]] : field.key }}
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </template>

                </template>


            </table>

        </div>
    </div>

    <div v-else class="by_group">
        <div class="table-responsive" v-if="groups.items.length > 0">

            <table class="table b-table table-bordered table-sm">

                <tr>
                    <th v-for="(field, index) in groups.fields" :key="index" :class="field.class">
                        <div>{{ field.name }}</div>
                    </th>
                </tr>


                <template v-for="(item, i) in groups.items">
                    <tr>
                        <td v-for="(field, f) in groups.fields" :key="f" :class="field.class" @click="expandUser(item)">
                            <div>{{ item[field.key] }}</div>
                        </td>
                    </tr>
                </template>


            </table>

        </div>
    </div>



</div>
</template>

<script>
const BY_USER = 1;
const BY_GROUP = 2;

export default {
    name: "CourseResults",
    watch: {
        monthInfo(val) {
            this.first = true;
            if(this.type == this.BY_GROUP) {
                this.fetchData('groups');
                this.first = false;
            } else {
                this.fetchData('users');
            }
        },
        currentGroup() {
            this.first = true;
            if(this.type == this.BY_GROUP) {
                this.fetchData('groups');
                this.first = false;
            } else {
                this.fetchData('users');
            }
        },
        type(val) {
            if(val == this.BY_GROUP && this.first) {
                this.fetchData('groups');
                this.first = false;
            }
        },
    },
    props: {
        monthInfo: {
            required: false
        },
        currentGroup: {
            required: false
        }
    },
    data() {
        return {
            data: [],
            type: BY_USER,
            first: true,
            BY_USER: BY_USER,
            BY_GROUP: BY_GROUP,
            users: {
                items: [],
                fields: [],
            },
            groups: {
                items: [],
                fields: [],
            },
            course2item: {
                name: 'title',
                status: 'status',
                points: 'points',
                progress: 'progress',
                progress_on_week: 'progress_on_week',
                started_at: 'started_at',
                ended_at: 'ended_at'
            },
            courses: {},
            testResults: {}
        }
    },
    computed: {
        courseItems(){
            const result = {}
            for(let [userId, userResult] of Object.entries(this.testResults)){
                for(let [courseId, courseResult] of Object.entries(userResult)){
                    const course = this.courses[courseId]
                    if(!course) continue
                    const points = course.points / course.stages

                    if(!result[userId]) result[userId] = {}
                    courseResult.forEach(testResult => {
                        const courseItemId = testResult.course_item_model_id
                        if(!result[userId][courseItemId]) result[userId][courseItemId] = {
                            status: 1,
                            points: 0,
                            progress: 0,
                            progress_on_week: 0,
                            started_at: new Date(),
                            ended_at: new Date(0)
                        }
                        result[userId][courseItemId].points += points
                    })
                }
            }
            console.log('courseItems', result)
            return result
        }
    },
    created() {
        this.fetchData();
    },
    methods: {
        fetchData(type = 'users') {
            let loader = this.$loading.show();

            axios
                .post("/course-results/get", {
                    type: type,
                    month: this.monthInfo.month,
                    year: this.monthInfo.currentYear,
                    group_id: this.currentGroup !== undefined ? this.currentGroup :  null,
                })
                .then((response) => {

                    if(type == 'users') {
                        this.users = response.data.items;
                    }
                    if(type == 'groups') {
                        this.groups = response.data.items;
                    }


                    loader.hide();
                });
        },

        fetchTestResults(userId, courseId) {
            axios.get('/course/progress', {
                params: { userId, courseId }
            }).then(({ data }) => {
                if(!this.testResults[userId]) this.$set(this.testResults, userId, {})
                this.$set(this.testResults[userId], courseId, data.data.testResults)
                this.courses[data.data.course.id] = data.data.course
            })
        },

        expandUser(item) {
            let ex = item.expanded;
            this.users.items.forEach(i => {
                i.expanded = false
                if(i.courses) i.courses.forEach(c => this.$set(c, 'expanded', false))
            });
            item.expanded = !ex;
        },

        expandCourse(course, item) {
            if(course.items && course.items.length > 1){
                if(!(this.testResults[item.user_id] && this.testResults[item.user_id][course.course_id])){
                    this.fetchTestResults(item.user_id, course.course_id)
                }
                this.users.items.every(el => {
                    // console.log('el.user_id', el.user_id, item.user_id)
                    if(el.user_id !== item.user_id) return true
                    item.courses.forEach(c => {
                        // console.log('c.course_id', c.course_id, course.course_id)
                        if(c.course_id === course.course_id){
                            this.$set(c, 'expanded', !c.expanded)
                        }
                        else{
                            this.$set(c, 'expanded', false)
                        }
                    })
                })
            }
        },

        nullify(i, c) {

            if(!confirm('Вы уверены? Потом прогресс не восстановить')) {
                return;
            }

            let course = this.users.items[i].courses[c];
            // course
            // ended_at:""
            // name:"Знакомство с нашей компанией"
            // points:"185 / 762 / 24.3%"
            // progress:"30%"
            // progress_on_week:"0%"
            // started_at:"28.07.2022"
            // status:"Запланирован"
            // user_id: 5

            this.nullifyRequest({
                user_id: course.user_id,
                course_id: course.course_id,
            }, (res) => {
                this.$toast.success('Прогресс по курсу Обнулен');

                course.progress = '0%';
                course.started_at = '';
                course.ended_at = '';
                course.status = 'Запланирован';
                course.points = '0 / 0 / 0%';
                course.progress_on_week = '0%';
            });

        },

        nullifyRequest(obj, callback) {
            let loader = this.$loading.show();

            axios
                .post("/course-results/nullify", obj)
                .then((response) => {
                    callback(response);
                })
                .catch(e => console.log(e));

            loader.hide();
        },

        regress(user_id, course_id, coureItem){
            if(!confirm('Вы уверены? Потом прогресс не восстановить')) return

            const loader = this.$loading.show()

            axios.post('/course/regress', {
                type: 'item',
                user_id,
                course_id,
                course_item_id: coureItem.id
            }).then((response) => {
                // coureItem.progress = '0%'
                // coureItem.started_at = ''
                // coureItem.ended_at = ''
                // coureItem.status = 'Запланирован'
                // coureItem.points = '0 / 0 / 0%'
                // coureItem.progress_on_week = '0%'
                this.$toast.success('Прогресс по разделу курса обнулен')
            }).catch(e => console.log(e))

            loader.hide()
        },
    }
}
</script>


<style scoped lang="scss">
.nullify {
    right: -6px;
    top: -2px;
    z-index: 2;
    background: aliceblue;
    padding: 6px 4px;
    border-radius: 50px;
    display: none;
    cursor: pointer;
    position: absolute;
}
.nullify-wrap{
    padding: 5px 10px;
    margin: -5px -10px;
}
.nullify-wrap:hover .nullify {
    display: block;
}

.expanded-course-item{
    background: lighten(#c0def2, 5%);
}
</style>