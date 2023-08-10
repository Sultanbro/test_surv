'<template>
	<div
		v-if="user_id"
		class="d-flex mycourse"
	>
		<div
			v-if="disable_course"
			class="disable_course"
		>
			<!-- <div v-if="activeCourse != null" class="d-left">
           <div class="gggggg">
            <h1 class="page-title">{{ activeCourse.name }}</h1>

              <div class="mb-3">
                <img class="course-img w-full mr-3 mb-2"
                :src="activeCourse.img"
                onerror="this.src = '/images/course.jpg';"
                />
                <div class="mt-3" v-html="activeCourse.text"></div>
              </div>



              <p><b>Блоки курса</b></p>
              <div class="course-item pass"
                v-for="(item, c_index) in items"
                :key="item.id"
              >
                <div class="title d-flex">
                  <i class="fa fa-database icon" v-if="item.item_model == 'App\\KnowBase'"></i>
                  <i class="fa fa-book icon" v-if="item.item_model == 'App\\Models\\Books\\Book'"></i>
                  <i class="fa fa-play icon" v-if="item.item_model == 'App\\Models\\Videos\\VideoPlaylist'"></i>
                  <span class="ml-2">{{ item.title }}</span>
                </div>
              </div>
          </div>
        </div> -->

			<div class="d-right aic jcc">
				<div class="d-flex aic flex-column">
					<p>Чтобы Вам был доступен этот курс, Вам необходимо пройти все курсы <b>по порядку</b></p>
					<button
						class="btn btn-primary"
						@click="getCourse(0)"
					>
						Вернуться к текущему курсу
					</button>
				</div>
			</div>
		</div>

		<!-- левый сайдбар -->
		<div class="lp">
			<!-- список курсов -->
			<div v-if="activeCourse == null">
				<div
					v-for="course in courses"
					:key="course.id"
					class="section d-flex aic jcsb my-2"
					@click="getCourse(course.id)"
				>
					<p class="mb-0">
						{{ course.name }}
					</p>
				</div>
			</div>

			<!-- выбранный курс -->
			<div v-else>
				<div class="gggggg">
					<h1 class="page-title">
						{{ activeCourse.name }}
					</h1>

					<div>
						<img
							class="course-img w-full"
							:src="activeCourse.img"
							onerror="this.src = '/images/course.jpg';"
						>
					</div>

					<div class="mt-3 course-progress">
						<small>Пройдено: <span>{{ progress }}%</span></small>
						<b-progress
							:value="progress"
							variant="success"
							max="100"
							class="course-progress-bar"
						/>
					</div>

					<!-- <div class="mt-3 description" v-html="activeCourse.text"></div> -->

					<p class="block-title">
						Блоки курса
					</p>
					<div class="course-items">
						<div
							v-for="(item, c_index) in items"
							:key="item.id"
							class="course-item"
							:class="{
								'active': activeCourseItem != null && item.id == activeCourseItem.id,
								'pass': item.status == 1
							}"
							@click="selectCourseItem(c_index)"
						>
							<div class="course-title d-flex align-items-start">
								<i
									v-if="item.status == 2"
									class="fa fa-check icon"
								/>
								<i
									v-else-if="item.status == 1"
									class="fa fa-check-double icon"
								/>
								<i
									v-else
									class="fa fa-lock icon"
								/>
								<span>{{ item.title }}</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- правая часть -->
		<div
			class="rp"
			style="flex: 1 1 0%; padding-bottom: 0px;"
		>
			<div
				class="content mt-3"
				:class="{'knowbase': activeCourseItem && activeCourseItem.item_model == 'App\\KnowBase'}"
			>
				<div
					v-if="activeCourse"
					class=""
				>
					<!-- поле курса -->

					<div class="mmmm-block">
						<div v-if="activeCourseItem">
							<div v-if="activeCourseItem.item_model == 'App\\Models\\Books\\Book'">
								<UpbooksRead
									ref="upbook"
									:key="activeCourseKey"
									:book_id="activeCourseItem.item_id"
									:mode="'read'"
									:course_page="true"
									:course_item_id="activeCourseItem.id"
									:enable_url_manipulation="false"
									:active_page="activeCourseItem.last_item"
									:all_stages="all_stages"
									:completed_stages="completed_stages"
									@nextElement="nextElement"
									@changeProgress="completed_stages++"
									@forGenerateCertificate="generateCertificateStart"
								/>
							</div>

							<PlaylistEdit
								v-if="activeCourseItem.item_model == 'App\\Models\\Videos\\VideoPlaylist' || activeCourseItem.item_model == 'App\\Models\\Videos\\Video'"
								:id="activeCourseItem.item_id"
								ref="playlist"
								:key="activeCourseKey"
								:course_item_id="activeCourseItem.id"
								:is_course="true"
								:myvideo="activeCourseItem.last_item"
								:enable_url_manipulation="false"
								:mode="'read'"
								:all_stages="all_stages"
								:completed_stages="completed_stages"
								@nextElement="nextElement"
								@changeProgress="completed_stages++"
								@forGenerateCertificate="generateCertificateStart"
							/>

							<div
								v-if="activeCourseItem.item_model == 'App\\KnowBase'"
								class="opopoppop"
							>
								<Booklist
									ref="knowbase"
									:key="activeCourseKey"
									:trees="trees"
									:parent_name="activeCourseItem.title"
									:course_item_id="activeCourseItem.id"
									:parent_id="activeCourseItem.item_id"
									:show_page_id="activeCourseItem.last_item"
									:mode="'read'"
									:course_page="true"
									:enable_url_manipulation="false"
									:auth_user_id="0"
									:all_stages="all_stages"
									:completed_stages="completed_stages"
									@changeProgress="completed_stages++"
									@forGenerateCertificate="generateCertificateStart"
									@nextElement="nextElement"
								/>
							</div>
						</div>

						<div
							v-if="congrats"
							class="p-4"
						>
							<h1>Поздравляем с завершением курса! 😁 😁 😆 </h1>
							<p>Спасибо, что прошли курс несмотря ни на что!</p>
						</div>
						<SaveCertificate
							v-if="generateCertificate"
							:course_id="activeCourseItem.course_id"
							:user_id="user_id"
							:title="activeCourseItem.title"
							@generate-success="generateSuccess"
						/>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import SaveCertificate from './Awards/types/saveCertificate';
const Booklist = () => import(/* webpackChunkName: "Booklist" */ '@/pages/booklist') // база знаний разде
const UpbooksRead = () => import(/* webpackChunkName: "UpbooksRead" */ '@/pages/UpbooksRead') // книга чтение
const PlaylistEdit = () => import(/* webpackChunkName: "PlaylistEdit" */ '@/pages/PlaylistEdit') // редактирование плейлиста

export default {
	name: 'MyCourse',
	components: {
		SaveCertificate,
		Booklist,
		UpbooksRead,
		PlaylistEdit,
	},
	props:{
		user_id: {
			type: Number,
			default: 0
		}
	},
	data() {
		return {
			test: 'dsa',
			items: [],
			courses: [],
			activeCourseItem: null,
			activeCourse: null,
			activeStep: null,
			trees: [],
			congrats: false,
			all_stages: 0,
			completed_stages: 0,
			disable_course: false,
			activeCourseKey: 1,
			generateCertificate: false
		};
	},

	computed: {
		progress: function(){
			return this.all_stages > 0 ? Number(Number((this.completed_stages / this.all_stages) * 100).toFixed(2)) : 0
		},
	},
	watch:{
		user_id(){
			this.init()
		}
	},
	created() {
		if(this.user_id){
			this.init()
		}
	},

	mounted() {},

	methods: {
		init(){
			this.fetchData();
			// бывор группы
			const urlParams = new URLSearchParams(window.location.search);
			let id = urlParams.get('id');
			if (id) {
				this.getCourse(id);
			}
		},
		generateCertificateStart(model){
			if(model === 0){
				this.generateCertificate = true;
			}
		},
		generateSuccess(){
			this.$toast.success('Ваш сертификат готов! Можете посмотреть и загрузить его в своем профиле во вкладке "Номинации"', {
				timeout: 5000
			});
			this.activeCourseItem.status = 1;
			this.activeCourseItem = null;
			this.congrats = true;
		},
		after_click_next_element() {
			let index = this.items.findIndex(el => el.id == this.activeCourseItem.id);


			if(index != -1 && this.items.length - 1 != index) {
				this.activeCourseItem.status = 1;
				this.activeCourseItem = this.items[index + 1];
				this.activeCourseItem.status = 2;
				this.generateCertificate = false;
			} else {
				this.$toast.success('Поздравляем с завершением курса!');
				this.$toast.warning('Подождите пару секунд, Ваш сертификат почти готов...', {
					timeout: 5000
				});
			}

		},

		nextElement() {

			if(this.activeCourseItem.item_model == 'App\\KnowBase') {
				this.$refs.knowbase.nextElement();
			}

			if(this.activeCourseItem.item_model == 'App\\Models\\Books\\Book') {
				this.$refs.upbook.nextElement();
			}

			if(this.activeCourseItem.item_model == 'App\\Models\\Videos\\Video') {
				this.$refs.playlist.nextElement();
			}


		},


		setStart() {
			if(this.items.length > 0) {

				let index = this.items.findIndex(el => el.status == 2);

				if(index != -1) {
					this.activeCourseItem = this.items[index];
					//this.activeStep = this.items[0].steps[0];
				} else {
					this.congrats = true;
				}

			}
		},
		selectCourse(i) {
			this.items = [];
			this.activeCourse = this.courses[i];

		},

		fetchData() {
			let loader = this.$loading.show();

			this.axios
				.get('/my-courses/get', {})
				.then((response) => {
					this.courses = response.data.courses;
					loader.hide();
				})
				.catch((error) => {
					loader.hide();
					alert(error);
				});
		},

		selectCourseItem(i) {

			if(this.canSelect(this.items[i].status)) {

				this.congrats = false;
				this.activeCourseItem = this.items[i];

			}

		},

		canSelect(status) {
			const COMPLETED = 1;
			const STARTED = 2;
			// const INITIAL = 0;

			return [STARTED, COMPLETED].includes(status);
		},

		setStepStatus() {
			let index = this.activeCourseItem.steps.findIndex(s => s.id == this.activeStep.id);
			this.activeCourseItem.steps[index].status = 1;
		},

		selectStep(step, item) {
			this.activeCourseItem = item;
			this.activeStep = step;
		},


		nextStep() {
			let index = this.activeCourseItem.steps.findIndex(s => s.id == this.activeStep.id);

			this.$toast.info(index);
			this.$toast.info(this.activeCourseItem.steps[index]);
			if(this.activeCourseItem.steps[index].status != 1) {
				this.$toast.info('Ответьте на вопросы правильно!');
				return ;
			}


			if(this.activeCourseItem.steps.length - 1  == index) {

				let c_index = this.items.findIndex(s => s.id == this.activeCourseItem.id);
				if(this.items.length - 1 == c_index) {
					this.$toast.info('Конец курса!');
					return;
				} else {
					this.activeCourseItem = this.items[c_index + 1 ];
					this.activeStep = this.activeCourseItem.steps[0];
				}

			} else {
				this.activeCourseItem.steps[index + 1];
			}


		},

		getCourse(id) {
			let loader = this.$loading.show();

			this.axios
				.get('/my-courses/get/' + id)
				.then((response) => {
					this.items = response.data.items;
					this.activeCourse = response.data.course;
					this.activeCourseKey++;

					this.disable_course = false;
					if(this.activeCourse != null && !this.activeCourse.is_active) {
						this.disable_course = true;
					}

					this.completed_stages = response.data.completed_stages;
					this.all_stages = response.data.all_stages;

					// change URL
					// const urlParams = new URLSearchParams(window.location.search);
					// let b = urlParams.get('id');
					let uri = '/my-courses?id=' + id;
					window.history.replaceState({}, 'Мои курсы', uri);
					this.setStart();
					loader.hide();
				})
				.catch((error) => {
					loader.hide();
					alert(error);
				});
		},

		back() {
			this.activeCourse = null;
			window.history.replaceState({ id: '100' }, 'Мои курсы', '/my-courses');
		},
	},
};
</script>
