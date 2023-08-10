<template>
	<div class="upbook-read-page">
		<!-- left side bar -->
		<div class="controls">
			<div
				v-if="showBackBtn"
				class="btn w-full btn-success"
				@click="$emit('back')"
			>
				<i class="fa fa-arrow-left" />
				Назад
			</div>

			<div class="first-block">
				<div class="d-flex pg-pages">
					<i
						class="fa fa-chevron-left"
						@click="prevPage"
					/>
					<span>{{ page }} из {{ pageCount }}</span>
					<i
						class="fa fa-chevron-right"
						@click="nextPage"
					/>
				</div>
				<div v-if="activeBook != null">
					<img
						:src="activeBook.img == '' ? '/images/book_cover.jpg' : activeBook.img"
						class="w-full pr-3"
					>
					<div v-if="isLoading">
						<p class="text-center mt-3">
							<b>Загружается...</b>
						</p>
					</div>
				</div>
				<div class="zoom-items">
					<i
						class="fa fa-search-plus"
						@click="zoomIn"
					/>
					<i
						class="fa fa-bars zero"
						@click="zoom = 0"
					/>
					<i
						class="fa fa-search-minus"
						@click="zoomOut"
					/>
				</div>
			</div>

			<!-- Page numbers -->
			<div class="chapters">
				<p class="upbook-items-title">
					Вопросы на странице:
				</p>

				<div class="upbook-items">
					<div
						v-for="(segment, t) in segments"
						:key="t"
						class="item d-flex"
						:class="{
							'pass': segment.item_model !== null,
							'active': page == segment.page
						}"
					>
						<i
							v-if="segment.item_model !== null"
							class="fa fa-check pointer mr-4"
						/>
						<i
							v-else
							class="fa fa-lock pointer mr-4"
						/>
						<p
							class="mb-0 item-text"
							@click="moveTo(segment.page, segment.item_model)"
						>
							<span>Стр. {{ segment.page }}</span>
							<small>{{ segment.questions.length }} вопрос (-ов)</small>
						</p>
					</div>
				</div>
			</div>
		</div>

		<!-- PDF viewer -->
		<div
			class="pdf show"
			:class="{
				w600: zoom == 600,
				w700: zoom == 700,
				w800: zoom == 800,
				w900: zoom == 900,
				w1000: zoom == 1000,
				w1100: zoom == 1100,
				w1200: zoom == 1200,
				w1300: zoom == 1300,
				w1400: zoom == 1400,
				w1500: zoom == 1500,
				w1600: zoom == 1600,
				full: zoom == 0,
			}"
		>
			<vue-pdf-embed
				v-if="activeBook !== null"
				ref="pdfRef"
				:source="activeBook.link"
				:page="page"
				class="plugin"
				loading-failed="sad"
				@rendered="loaded"
			/>
		</div>

		<!-- Test viewer -->
		<div
			v-if="activeSegment !== null"
			class="test"
		>
			<questions
				:id="0"
				:key="segment_key"
				:course_item_id="course_item_id"
				:questions="activeSegment.questions"
				:pass="activeSegment.item_model !== null"
				:pass_grade="activeSegment.pass_grade"
				type="book"
				:mode="mode"
				@continueRead="nextPage"
				@passed="nextElement"
				@nextElement="nextElement"
			/>
		</div>

		<!-- <template v-if="(activeSegment != null && course_page && activeSegment.item_model !== null) || (activeSegment == null && pageCount == page)">
      <button class="next-btn btn btn-primary"
        @click="nextElement()">
        Продолжить курс
        <i class="fa fa-angle-double-right ml-2"></i>
      </button>
    </template> -->
	</div>
</template>

<script>
import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed'
export default {
	name: 'UpbooksRead',

	components: {
		VuePdfEmbed
	},

	props: {
		book_id: Number,
		mode: {
			default: 'read'
		},
		showBackBtn: {
			default: false
		},
		course_page: {
			default: false,
		},
		active_page: {
			default: 0
		},
		course_item_id: {
			default: 0
		},
		all_stages: {
			default: 0
		},
		completed_stages: {
			default: 0
		},
	},

	data() {
		return {
			page: 1,
			activeCategory: null,
			activeSegment: null,
			activeBook: null,
			pageCount: 0,
			zoom: 800,
			isLoading:true,
			segments: [],
			segment_key: 1,
			checkpoint: 1, // last page
			page_map: [],
			map_index: 0,
			pdf_loaded: false,
		};
	},

	created() {
		this.checkpoint = this.pageCount
		this.getSegments()
	},

	mounted() {
		document.addEventListener('keyup', this.keyup);
	},

	methods: {
		moveTo(page, pass) {
			if(pass) {
				this.page = page;

				let m = this.page_map.findIndex(el => el.page == page);
				if(m != -1) {
					this.map_index = m;
				}


				let i = this.segments.findIndex(el => el.page == page);
				if(i != -1) {
					this.activeSegment = this.segments[i]
					this.segment_key++;
				} else {
					this.activeSegment = null;
				}

			}
		},

		loaded() {
			this.isLoading = false
			this.pageCount = this.$refs.pdfRef.pageCount
			this.formPageMap()
			this.pdf_loaded = true;
		},

		nextElement() {

			if(this.activeSegment != null && this.activeSegment.item_model == null) {
				this.setSegmentPassed();
			}

			if(this.page == this.pageCount && this.course_page) {
				this.$parent.after_click_next_element();
				return;
			}

			this.nextPage()

		},

		setSegmentPassed() {

			if(this.activeSegment.item_model != null) return;

			this.axios
				.post('/my-courses/pass', {
					id: this.activeSegment.id,
					type: 1,
					course_item_id: this.course_item_id,
					questions: this.activeSegment.questions,
					all_stages: this.all_stages,
					completed_stages: this.completed_stages + 1,
				})
				.then((response) => {
					this.$emit('changeProgress');
					this.$emit('forGenerateCertificate', response.data.item_model);
					this.activeSegment.item_model = {status: 1};
					// this.activeVideo.item_models.push(response.data.item_model);
				})
				.catch((error) => {
					alert(error);
				});
		},

		getSegments() {
			let loader = this.$loading.show();

			this.axios
				.post('/admin/upbooks/segments/get', {
					id: this.book_id,
					course_item_id: this.course_item_id
				})
				.then((response) => {
					this.segments = response.data.segments;
					this.activeBook = response.data.activeBook;


					loader.hide();
				})
				.catch((error) => {
					loader.hide();
					alert(error);
				});
		},

		keyup(e) {
			if (e.keyCode == 37) {
				this.prevPage();
			}
			if (e.keyCode == 39) {
				this.nextPage();
			}
		},

		formPageMap() {
			let arr = [];
			let page = 1;


			while (page <= this.pageCount) {

				arr.push({
					page: page,
					has_test: false,
				});

				let i = this.segments.findIndex(el => el.page == page);
				if(i != -1) {
					arr.push({
						page: page,
						has_test: true,
					});
				}

				page++;
			}

			this.page_map = arr;
		},

		nextPage() {


			if (this.map_index == this.page_map.length - 1 || !this.pdf_loaded) return 0;

			// check current test
			if(this.activeSegment != null && this.activeSegment.item_model == null) {
				this.$toast.info('Ответьте на вопросы, чтобы пройти дальше');
				return 0;
			}

			this.map_index++;

			let next_page = this.page_map[this.map_index];

			this.page = next_page.page;
			// next page has test ?
			if(next_page.has_test) {

				let i = this.segments.findIndex(el => el.page == next_page.page);
				this.activeSegment = this.segments[i]
				this.segment_key++;

			} else {

				this.activeSegment = null;
			}


		},

		prevPage() {
			if(this.map_index == 0  || !this.pdf_loaded) return 0;

			this.map_index--;

			let prev_page = this.page_map[this.map_index];

			this.page = prev_page.page;


			// prev_page has test ?
			if(prev_page.has_test) {

				let i = this.segments.findIndex(el => el.page == prev_page.page);
				this.activeSegment = this.segments[i]
				this.segment_key++;

			} else {
				this.activeSegment = null;
			}


		},

		zoomIn() {
			if (this.zoom == 0) this.zoom = 1600;
			if (this.zoom < 1600) {
				this.zoom += 100;
			}
		},

		zoomOut() {
			if (this.zoom == 0) this.zoom = 1500;
			if (this.zoom > 600) {
				this.zoom -= 100;
			}
		},

	},
};
</script>

<style>

</style>
