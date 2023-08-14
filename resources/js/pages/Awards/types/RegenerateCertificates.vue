<template>
	<div class="regenerate">
		<div
			v-if="loading"
			class="spinner-container"
		>
			<div class="throbber-loader" />
		</div>
		<b-row class="align-items-end">
			<b-col
				cols="12"
				md="6"
			>
				<b-form-group
					label="выберите курс"
					class="m-0"
				>
					<b-form-select
						v-model="course"
						:options="courses"
						value-field="id"
						text-field="name"
						:disabled="start"
						@change="selectCourse"
					/>
				</b-form-group>
			</b-col>
			<b-col
				cols="12"
				md="6"
			>
				<b-button
					v-if="list && list.length > 0"
					variant="danger"
					style="height: 40px; width: 100%;"
					:disabled="start"
					@click="startClick"
				>
					Сгенерировать все сертификаты
				</b-button>
			</b-col>
		</b-row>
		<template v-if="start">
			<hr class="my-4">
			<div class="info-block">
				Внимание! Начался процесс генерации. Не закрывайте данное окно, не перезагружайте и не закрывайте эту страницу браузера.
			</div>
		</template>
		<template v-if="list">
			<div class="total-result">
				<div>
					<p class="mb-2">
						Результатов: {{ list.length }}
					</p>
					<p v-if="list.length > 0">
						Выполнено: {{ indexGen }} из {{ list.length }}
					</p>
				</div>
				<div
					v-if="showFilesGen"
					class="text-right"
				>
					<p
						class="mb-2 text-files-count"
						:class="[endGenerate && files.length > 0 ? 'finished' : '', endGenerate && files.length === 0 ? 'abort' : '']"
					>
						Файлов сгенерировано: {{ files.length }}
					</p>
					<p
						v-if="secondWait > 0"
						class="time-wait"
					>
						Примерное время генерации: менее
						<template v-if="secondWait > 45">
							{{ timeWait }}
							<template v-if="timeWait === 1">
								минуты
							</template>
							<template v-else>
								минут
							</template>
						</template>
						<template v-else-if="secondWait > 30">
							<span class="green-span-text">45 секунд</span>
						</template>
						<template v-else-if="secondWait > 15">
							<span class="green-span-text">30 секунд</span>
						</template>
						<template v-else-if="secondWait > 5">
							<span class="green-span-text">15 секунд</span>
						</template>
					</p>
				</div>
			</div>
			<template v-if="list.length > 0">
				<div
					id="logs"
					ref="logs"
					class="logs"
				>
					<div
						v-for="(log, index) in logs"
						:key="index"
						class="log-item"
					>
						<span class="index">{{ index }}</span>
						<div>
							<span class="msg">Message: {{ log.title }}</span>
							<span
								v-if="log.fileName"
								class="file-name"
							>File: {{ log.fileName }}</span>
							<span
								v-else
								class="file-name danger"
							>Файл не сгенерирован. Нет шаблона</span>
						</div>
					</div>
					<div
						v-if="start"
						class="loading-dots"
					>
						<div class="loading-dots--dot" />
						<div class="loading-dots--dot" />
						<div class="loading-dots--dot" />
					</div>
					<div
						v-if="endGenerate"
						class="finished"
					>
						<div
							v-if="files.length > 0"
							class="finish-text"
						>
							Генерация завершена!
						</div>
						<div
							v-else
							class="finish-text danger"
						>
							Ничего не сгенерировано
						</div>
					</div>
					<div style="height: 100px;" />
				</div>
				<template v-for="(item, index) in list">
					<saveCertificateReg
						v-if="index === indexGen && start"
						:key="item.id"
						:course_id="course"
						:item="item"
						@generated="generated"
					/>
				</template>
			</template>
		</template>
		<div
			v-if="endGenerate && files.length > 0"
			class="custom-modal-footer"
		>
			<b-button
				variant="success"
				@click="requestFiles"
			>
				Отправить файлы на замену
			</b-button>
		</div>
	</div>
</template>

<script>
import saveCertificateReg from './saveCertificateReg';

export default {
	name: 'RegenerateCertificates',
	components: {
		saveCertificateReg
	},
	data() {
		return {
			course: null,
			courses: null,
			list: null,
			files: [],
			start: false,
			indexGen: 0,
			logs: [],
			loading: false,
			showFilesGen: false,
			endGenerate: false
		}
	},
	computed: {
		timeWait() {
			return Math.ceil((this.list.length - this.indexGen) * 5 / 60);
		},
		secondWait() {
			return (this.list.length - this.indexGen) * 5;
		}
	},
	mounted() {
		this.axios
			.get('/admin/courses/get')
			.then(response => {
				this.courses = response.data.courses;
			}).catch(e => {
				console.error(e)
			});
	},
	methods: {
		requestFiles() {
			this.loading = true;
			this.axios
				.get('/awards/courses?course_ids[]=' + this.course)
				.then(() => {
					this.course = null;
					this.list = null;
					this.files = [];
					this.start = false;
					this.indexGen = 0;
					this.logs = [];
					this.loading = false;
					this.showFilesGen = false;
					this.endGenerate = false;
					this.$toast.success('Вы успешно очистили все данные, которые так долго генерировали  =) =) ;-). Потому что  я забыл, на какое апи нужно отправлять запрос', {
						timeout: 8000
					});
					this.loading = false;
				}).catch(e => {
					console.error(e);
					this.loading = false;

				});
		},
		startClick() {
			this.start = true;
			this.showFilesGen = true;
		},
		selectCourse() {
			this.loading = true;
			this.files = [];
			this.indexGen = 0;
			this.start = false;
			this.showFilesGen = false;
			this.endGenerate = false;
			this.logs = [];
			this.axios
				.get('/awards/courses?course_ids[]=' + this.course)
				.then(response => {
					this.list = response.data.data;
					this.loading = false;
				}).catch(e => {
					console.error(e);
					this.loading = false;

				});
		},
		generated(file, log) {
			if (file) {
				this.files.push(file);
			}
			this.logs.push(log);
			this.indexGen = this.indexGen + 1;
			if (this.list.length <= this.indexGen) {
				this.start = false;
				this.endGenerate = true;
			}
			this.$refs.logs.scrollTo(0, this.$refs.logs.scrollHeight);
		}
	}
}
</script>

<style lang="scss">
    .regenerate {
        .time-wait{
            font-size: 12px;
        }
        .info-block{
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-top: 1px solid #ddd;
            background-color: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            font-weight: 600;
            line-height: 1.5;
            text-align: center;
        }
        .custom-modal-footer{
            margin-top: 10px;
            display: flex;
            justify-content: flex-end;
        }
        .finish-text {
            color: #0bbd0b;
            margin-right: 10px;
            margin-left: 40px;
            &.danger{
                color: #ff5a5a;
            }
        }

        .green-span-text{
            color: #0bbd0b;
        }

        .total-result {
            margin: 20px 0;
            border: 1px solid #ddd;
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            .text-files-count{
                color: #fff;
                &.finished {
                    color: #0bbd0b;
                }
                &.abort{
                    color: #ff5a5a !important;
                }
            }
        }

        .custom-select {
            height: 40px;
        }

        .logs {
            height: calc(100vh - 500px);
            border: 1px solid #ddd;
            overflow: auto;
            background-color: #333;
            padding: 20px;

            .log-item {
                display: flex;
                align-items: flex-start;
                font-size: 14px;
                margin-bottom: 10px;
                border-bottom: 1px solid #666;
                padding-bottom: 10px;

                .index {
                    color: #0bbd0b;
                    margin-right: 10px;
                    text-align: right;
                    width: 30px;
                }

                .msg {
                    width: 100%;
                    display: block;
                    color: orange;
                    margin-bottom: 5px;
                }

                .file-name {
                    width: 100%;
                    display: block;
                    color: #21adff;

                    &.danger {
                        color: #ff5a5a;
                    }
                }
            }
        }

        @keyframes dot-keyframes {
            0% {
                opacity: .4;
                transform: scale(1, 1);
            }

            50% {
                opacity: 1;
                transform: scale(1.2, 1.2);
            }

            100% {
                opacity: .4;
                transform: scale(1, 1);
            }
        }

        .loading-dots {
            width: 100%;

            &--dot {
                animation: dot-keyframes 1.5s infinite ease-in-out;
                background-color: #fff;
                border-radius: 10px;
                display: inline-block;
                height: 5px;
                width: 5px;

                &:nth-child(2) {
                    animation-delay: .5s;
                }

                &:nth-child(3) {
                    animation-delay: 1s;
                }
            }
        }

        .spinner-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .throbber-loader {
            animation: throbber-loader 2000ms 300ms infinite ease-out;
            background: #dde2e7;
            display: inline-block;
            position: relative;
            text-indent: -9999px;
            width: 0.9em;
            height: 1.5em;
            margin: 0 1.6em;
            z-index: 22;
        }

        .throbber-loader:before, .throbber-loader:after {
            background: #dde2e7;
            content: '\x200B';
            display: inline-block;
            width: 0.9em;
            height: 1.5em;
            position: absolute;
            top: 0;
        }

        .throbber-loader:before {
            -moz-animation: throbber-loader 2000ms 150ms infinite ease-out;
            -webkit-animation: throbber-loader 2000ms 150ms infinite ease-out;
            animation: throbber-loader 2000ms 150ms infinite ease-out;
            left: -1.6em;
        }

        .throbber-loader:after {
            -moz-animation: throbber-loader 2000ms 450ms infinite ease-out;
            -webkit-animation: throbber-loader 2000ms 450ms infinite ease-out;
            animation: throbber-loader 2000ms 450ms infinite ease-out;
            right: -1.6em;
        }
    }
</style>
