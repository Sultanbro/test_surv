<template>
	<div
		id="editor"
		@mousemove="onMouseMove"
	>
		<div class="listdrag">
			<input
				id="namesshema"
				v-model="namesshema"
				type="text"
				class="form-control"
				placeholder="Название схемы"
			>

			<div class="pdn">
				<div
					class="draggable"
					@mousedown="onMouseDown"
					@mousemove="onMouseMove"
					@mouseup="onMouseUp(this,'textsintez')"
				>
					<div class="image">
						<i
							class="fa fa-question"
							aria-hidden="true"
						/>
					</div>
					<div class="text">
						Вопрос
					</div>
					<div class="info" />
				</div>

				<div
					class="draggable"
					@mousedown="onMouseDown"
					@mousemove="onMouseMove"
					@mouseup="onMouseUp(this,'yesorno')"
				>
					<div class="image">
						<i
							class="fa fa-sitemap"
							aria-hidden="true"
						/>
					</div>
					<div class="text">
						Ответ
					</div>
					<div class="info" />
				</div>

				<div
					class="draggable"
					@mousedown="onMouseDown"
					@mousemove="onMouseMove"
					@mouseup="onMouseUp(this,'forwarding')"
				>
					<div class="image">
						<i
							class="fa fa-phone"
							aria-hidden="true"
						/>
					</div>
					<div class="text">
						Переадресация
					</div>
					<div class="info" />
				</div>

				<div
					class="draggable"
					@mousedown="onMouseDown"
					@mousemove="onMouseMove"
					@mouseup="onMouseUp(this,'smsforward')"
				>
					<div class="image">
						<i
							class="fa fa-commenting-o"
							aria-hidden="true"
						/>
					</div>
					<div class="text">
						Отправка смс
					</div>
					<div class="info" />
				</div>

				<div
					class="draggable"
					@mousedown="onMouseDown"
					@mousemove="onMouseMove"
					@mouseup="onMouseUp(this,'end')"
				>
					<div class="image">
						<i
							class="fa fa-flag"
							aria-hidden="true"
						/>
					</div>
					<div class="text">
						Конец
					</div>
					<div class="info" />
				</div>
			</div>
			<button
				id="saveButton"
				@click="schemaSave"
			>
				Сохранить сценарий
			</button>
		</div>
		<simple-flowchart
			:scene.sync="scene"
			:height="600"
			@nodeClick="nodeClick"
			@nodeDelete="nodeDelete"
			@linkBreak="linkBreak"
			@linkAdded="linkAdded"
			@canvasClick="canvasClick"
			@onLogin="oneLogin"
			@addNodes="addNode"
		/>


		<transition v-if="showModal">
			<template #header>
				<h3>
					custom header
				</h3>
			</template>


			<div
				class="modal-mask"
				@click="showModal=false"
			>
				<div class="modal-wrapper">
					<div
						class="modal-container"
						@click.stop=""
					>
						<div class="modal-header">
							<slot name="header">
								<template v-if="selectedNode.type==='start'">
									Начало
								</template>
								<template v-if="selectedNode.type==='yesorno'">
									Ответ
								</template>
								<template v-if="selectedNode.type==='textsintez'">
									Вопрос
								</template>
								<template v-if="selectedNode.type==='end'">
									Конец
								</template>
								<template v-if="selectedNode.type==='forwarding'">
									Переадресация
								</template>
								<template v-if="selectedNode.type==='smsforward'">
									Отправить смс
								</template>
							</slot>
						</div>

						<div class="modal-body">
							<slot name="body">
								<div class="tool-wrapper">
									<label>Описание</label>
									<input
										v-model="newNodeLabel"
										type="text"
									>

									<template v-if="selectedNode.type==='smsforward'">
										<label>Код интеграции</label>
										<input
											v-model="integrationsms"
											class="form-control"
											type="text"
										>

										<label>Текст для смс</label>
										<div class="blocksms">
											<textarea
												v-model="smsforward"
												class="form-control "
												@keyup="simvoli"
											/>
											<div class="foot">
												<em><span>{{ length }}</span>/<span>{{ length_sms }}</span> осталось символов <span class="count_sms">({{ count_sms }} смс)</span></em>
											</div>
										</div>
									</template>

									<template v-if="selectedNode.type==='forwarding'">
										<label>Номер для переадресации</label>
										<input
											v-model="forwardphone"
											class="form-control"
											type="text"
										>
									</template>

									<template v-if="selectedNode.type==='textsintez'">
										<select v-model="selectaudio">
											<option value="text">
												Текст
											</option>
											<option value="file">
												Аудио файл
											</option>
										</select>

										<template v-if="selectaudio=='text'">
											<label>Текст для синтеза речи</label>
											<textarea
												v-model="description"
												class="form-control"
											/>
											<template v-if="audiolisttwo!=null">
												<audio
													controls="controls"
													autobuffer="autobuffer"
												>
													<source :src="audiolisttwo">
												</audio>
											</template>

											<button
												class="modal-default-button"
												@click="sintez"
											>
												Синтезировать
											</button>
										</template>

										<template v-if="selectaudio=='file'">
											<label>Прикрепить файл (*.mp3)</label>
											<input
												ref="file"
												class="form-control"
												type="file"
												accept=".mp3"
												@change="uploadFile()"
											>

											<audio
												v-if="audiolist"
												controls="controls"
												autobuffer="autobuffer"
											>
												<source :src="audiolist">
											</audio>
										</template>
									</template>
									<template v-if="selectedNode.type==='yesorno'">
										<label>Для положительного ответа (через запятую)</label>
										<input
											v-model="otvetyes"
											placeholder="Введите слова для положительного ответа"
										>

										<label>Для отрицательного ответа (через запятую)</label>
										<input
											v-model="otvetno"
											placeholder="Введите слова для отрицательного ответа"
										>
									</template>
								</div>
							</slot>
						</div>

						<div class="modal-footer">
							<slot name="footer">
								<button
									class="modal-default-button"
									@click="editNode"
								>
									Сохранить
								</button>
							</slot>
						</div>
					</div>
				</div>
			</div>
		</transition>
	</div>
</template>

<script>
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */

import SimpleFlowchart from './SimpleFlowchart.vue'

export default {
	name: 'App',
	components: {
		SimpleFlowchart
	},
	props: {
		script_schema: {
			type: String,
			default: '{}'
		},
		script_id: {
			type: Number,
			default: null
		},
	},
	data() {

		this.modalVisibility = false,
		this.dragObject = {},
		this.files_upload = [];

		return {
			length:0,
			length_sms:0,
			integrationsms:'',
			count_sms:0,
			smsforward:'',
			forwardphone:'',
			namesshema:'Новая схема',
			audiolist:null,
			audiolisttwo:null,
			configuration: {
				alert:false,
				modal: false,
				lid:false,
			},
			otvetyes:'',
			otvetno:'',
			selectaudio:'text',
			description:'',
			temp:0,
			showModal:false,
			scene: {
				centerX: 1024,
				centerY: 140,
				scale: 1,
				nodes: [
					{
						id: 2,
						x: -650,
						y: -90,
						type: 'start',
						label: 'Начало сценарий',
						key_button: -1,
						action_button: null,
						audio_file: null,
						templateAudio:null,
						lidnoanswer:false,
					},
					{
						id: 7,
						x: -650,
						y: 240,
						type: 'end',
						label: 'Конец сценарий',
						key_button: -1,
						action_button: null,
						audio_file: null,
						templateAudio:null,
						lidnoanswer:false,
					}
				],
				links: []
			},

			newNodeConnection:0,
			newNodeNumber:'',
			newNodeLabel:'',
			newNodeKey: 0,
			newButtonAction: 0,
			nodeKey: [
				0, 1, 2, 3, 4, 5 ,6 ,7, 8, 9,
			],

			nodeAction: [
				// 'Позвонить оператору',
				// 'Связать с call-centre',
				'Перенести в отдел',
				'Связать с номером',
				// 'Отправить сообщение',
				'Повторить ролик',
				'Завершить звонок',
				'Следующий уровень'
			]
		}
	},
	mounted: function () {

		if (this.script_schema) {
			this.scene = JSON.parse(JSON.parse(this.script_schema));
		}


	},
	methods: {
		simvoli(){

			this.axios.post('/autocalls/length',{
				message: this.smsforward,
				latin: 1,
			}).then(response => {
				this.length = response.data.length
				this.length_sms = response.data.length_sms
				this.count_sms = response.data.count_sms
			}).catch(error => {
				console.error(error)
			});

		},
		sintez(){
			this.audiolisttwo = null

			this.axios.post('/schema/syntez',{
				message:this.description,
			}).then(response => {
				this.audiolisttwo=response.data

			}).catch(error => {
				console.error(error)
			});
		},
		onMouseDown: function (e) {
			if (e.which != 1) return;
			var elem = e.target.closest('.draggable');
			if (!elem) return;
			this.dragObject.elem = elem;
			// запомним, что элемент нажат на текущих координатах pageX/pageY
			this.dragObject.downX = e.pageX;
			this.dragObject.downY = e.pageY;
			return false;

		},
		onMouseMove: function (e) {

			if (!this.dragObject.elem) return; // элемент не зажат

			if (!this.dragObject.avatar) { // если перенос не начат...
				// var moveX = e.pageX - this.dragObject.downX;
				// var moveY = e.pageY - this.dragObject.downY;

				// если мышь передвинулась в нажатом состоянии недостаточно далеко
				// if (Math.abs(moveX) < 1 && Math.abs(moveY) < 1) {
				//     return;
				// }

				// начинаем перенос
				this.dragObject.avatar = this.createAvatar(e); // создать аватар
				if (!this.dragObject.avatar) { // отмена переноса, нельзя "захватить" за эту часть элемента
					this.dragObject = {};
					return;
				}

				// аватар создан успешно
				// создать вспомогательные свойства shiftX/shiftY
				var coords = this.getCoords(this.dragObject.avatar);
				this.dragObject.shiftX = this.dragObject.downX - coords.left;
				this.dragObject.shiftY = this.dragObject.downY - coords.top;

				this.startDrag(e); // отобразить начало переноса
			}

			// отобразить перенос объекта при каждом движении мыши
			this.dragObject.avatar.style.left = e.pageX - this.dragObject.shiftX + 'px';
			this.dragObject.avatar.style.top = e.pageY - this.dragObject.shiftY + 'px';

			return false;
		},
		onMouseUp: function (e,type) {

			if (this.dragObject.avatar) { // если перенос идет
				this.finishDrag(e,type);
			}

			// перенос либо не начинался, либо завершился
			// в любом случае очистим "состояние переноса" dragObject
			this.dragObject = {};
		},
		finishDrag: function (e,type) {
			var dropElem = this.findDroppable(e);

			if (!dropElem) {
				this.dragObject.avatar.rollback();
			}
			else {
				this.dragObject.elem.style.display = 'none';

				this.addNode(type);
				this.dragObject.elem.style.display = null;
				this.dragObject.avatar.rollback();
			}
		},

		createAvatar: function () {
			// запомнить старые свойства, чтобы вернуться к ним при отмене переноса
			var avatar = this.dragObject.elem;
			var old = {
				parent: avatar.parentNode,
				nextSibling: avatar.nextSibling,
				position: avatar.position || '',
				left: avatar.left || '',
				top: avatar.top || '',
				zIndex: avatar.zIndex || ''
			};

			// функция для отмены переноса
			avatar.rollback = function() {
				old.parent.insertBefore(avatar, old.nextSibling);
				avatar.style.position = old.position;
				avatar.style.left = old.left;
				avatar.style.top = old.top;
				avatar.style.zIndex = old.zIndex
			};

			return avatar;
		},

		startDrag: function () {
			var avatar = this.dragObject.avatar;

			// инициировать начало переноса


			document.body.appendChild(avatar);
			avatar.style.zIndex = 9999;
			avatar.style.position = 'absolute';
		},

		findDroppable: function () {
			// спрячем переносимый элемент
			this.dragObject.avatar.hidden = true;

			// получить самый вложенный элемент под курсором мыши
			var elem = document.elementFromPoint(event.clientX, event.clientY);

			// показать переносимый элемент обратно
			this.dragObject.avatar.hidden = false;

			if (elem == null) {
				// такое возможно, если курсор мыши "вылетел" за границу окна
				return null;
			}

			return elem.closest('.flowchart-container');
		},
		getCoords: function (elem) {
			var box = elem.getBoundingClientRect();

			return {
				top: box.top + pageYOffset,
				left: box.left + pageXOffset
			};

		},
		oneLogin (data) {
			this.selectedNode = data;
			this.newNodeLabel = data.label;
			this.newNodeKey = data.key_button;
			let findInput = this.nodeAction.findIndex(element => element === data.action_button);
			this.newButtonAction = findInput;
			this.newNodeConnection = data.connect_with;
			this.showModal = true;
			this.description = data.description,
			this.otvetyes = data.otvetyes,
			this.otvetno = data.otvetno,
			this.forwardphone = data.forwardphone,
			this.integrationsms = data.integrationsms,
			this.smsforward = data.smsforward,
			this.length = 0
			this.length_sms = 0
			this.count_sms = 0
			if (this.smsforward.length>0) {
				this.simvoli()
			}




			this.lidnoanswer = data.lidnoanswer;
		},
		canvasClick() {},
		uploadFile() {
			this.audiolist = null
			this.files = this.$refs.file.files[0];
			this.files_upload.push(this.files);

			var audio = this.$refs.file.files[0];

			this.readFile(audio, (e) => {
				var result = e.target.result;

				this.audiolist = result

			});

		},
		readFile: function(file, onLoadCallback){
			var reader = new FileReader();
			reader.onload = onLoadCallback;
			reader.readAsDataURL(file);
		},
		templateAudioChange: function(e){

			if (this.temp == 1) {

				e.templateAudio = '/wait.mp3'
			} else {
				e.templateAudio = null
			}

		},
		addNode(types){



			let maxID = Math.max(0, ...this.scene.nodes.map((link) => {
				return link.id
			}));

			this.scene.nodes.push({
				id: maxID + 1,
				x: -300 - maxID*25,
				y: 50 - maxID*10,
				type: types,
				label: 'Описание...',
				description:'',
				key_button: null,
				action_button: null,
				audio_file: 'Ваш ролик',
				connect_with: null,
				templateAudio:null,
			})




		},

		editNode() {
			let key;
			let action;
			let audio;
			let connection;
			let maxID = Math.max(0, ...this.scene.nodes.map((link) => {
				return link.id
			}));


			if(this.selectedNode.type === 'textsintez'){
				if(this.files != null) {
					audio = this.files.name;
				}
				else{
					audio = this.selectedNode.audio_file;
				}
				key = -1;
				action = null;
				connection=null;
			}
			else{
				key = -1;
				action = null;
				audio=null;
				connection=null;

			}
			let findNode = this.scene.nodes.find(o => o.id === this.selectedNode.id);
			findNode.label = this.newNodeLabel ? this.newNodeLabel : `test${maxID + 1}`,
			findNode.key_button = key,
			findNode.action_button = action,
			findNode.audio_file = audio,
			findNode.connect_with = connection;
			findNode.description = this.description;
			findNode.otvetyes = this.otvetyes;
			findNode.otvetno = this.otvetno;
			findNode.lidnoanswer = this.selectedNode.lidnoanswer;
			findNode.forwardphone = this.forwardphone;
			findNode.smsforward = this.smsforward;
			findNode.integrationsms = this.integrationsms;


			this.showModal = false;
			this.description = '';
			this.otvetyes = '';
			this.otvetno = '';
			this.forwardphone='';
			this.integrationsms='';
			this.smsforward=''
			this.audiolisttwo=null
		},
		nodeClick() {},
		nodeDelete() {},
		linkBreak() {},
		linkAdded() {},

		schemaSave: function () {
			/* global _ */
			// var buttonSave = document.getElementById('saveButton');
			// buttonSave.disabled = true;
			let formData = new FormData();
			// alert('Идет сохранение данных...');


			var nodes = [];
			var links = [];
			var file =  [];
			_.forEach(this.files_upload, function (item) {
				file.push(item);
			});
			_.forEach(this.scene.nodes, function (item) {
				nodes.push(item);
			});
			_.forEach(this.scene.links, function (item) {
				links.push(item)
			});
			var ins = this.files_upload.length;
			for (var x = 0; x < ins; x++) {
				formData.append('file[]', file[x]);
			}

			if (this.script_id) {
				formData.append('id', this.script_id);
			}
			formData.append('name', this.namesshema);
			formData.append('file[]', file);
			formData.append('schema', JSON.stringify(this.scene));
			formData.append('nodes', JSON.stringify(nodes));
			formData.append('links', JSON.stringify(this.scene.links));
			formData.append('config', JSON.stringify(this.configuration));


			this.axios.post('/schema/create',
				formData,
			).then(() => {
				alert('Сценарий успешно сохранен!');
			}).catch(error => {
				alert('Ошибка в графике или обратитесь в службу поддержки');
				console.error(error.response)
			});

		},

	}

}
</script>

<style scoped lang="scss">
    #app {
        font-family: 'Avenir', Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-align: center;
        color: #2c3e50;
        margin: 0;
        overflow: hidden;
        height: 600px;


    }

    #app .tool-wrapper {
        position: relative;
    }

    .modal-mask {
        position: fixed;
        z-index: 9998;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .5);
        display: table;
        transition: opacity .3s ease;
        font-size: 14px;
        input{
            font-size: 14px;
        }
    }

    .modal-wrapper {
        display: table-cell;
        vertical-align: middle;
    }
    .modal-mask input, .modal-mask select{
        margin-bottom: 20px;
    }
    .modal-container {
        width: 500px;
        margin: 0px auto;
        padding: 0;
        background-color: #fff;
        border-radius: 2px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
        transition: all .3s ease;
        font-family: Helvetica, Arial, sans-serif;
    }

    .modal-header h3 {
        margin-top: 0;
        color: #42b983;
    }

    .modal-body {
        margin: 0;
    }



    .modal-enter {
        opacity: 0;
    }

    .modal-leave-active {
        opacity: 0;
    }

    .modal-enter .modal-container,
    .modal-leave-active .modal-container {
        -webkit-transform: scale(1.1);
        transform: scale(1.1);
    }
    .cheklid{
        display: inline-block;
        width: auto;
        margin: 3px 5px 3px 0;
    }
    #config{
        background: #c3c2c2;
    color: white;
    border: 0;
    border-radius: 6px;
    padding: 6px 30px;
    display: table;
    margin: 50px auto 0;
    }
    .checkbox label:after,
    .radio label:after {
        content: '';
        display: table;
        clear: both;
    }
    .checkbox .cr,
    .radio .cr {
        position: relative;
        display: inline-block;
        border: 1px solid black;
        border-radius: .25em;
        width: 1.3em;
        height: 1.3em;
        float: left;
        margin-right: .5em;
        color: #e785f2;
    }

    .radio .cr {
        border-radius: 75%;
        border-color: green;
    }

    .checkbox .cr .cr-icon,
    .radio .cr .cr-icon {
        position: absolute;
        font-size: .8em;
        line-height: 0;
        top: 50%;
        left: 20%;
    }

    .radio .cr .cr-icon {
        margin-left: 0.04em;
    }

    .checkbox label input[type="checkbox"],
    .radio label input[type="radio"] {
        display: none;
    }

    .checkbox label input[type="checkbox"] + .cr > .cr-icon,
    .radio label input[type="radio"] + .cr > .cr-icon {
        transform: scale(3) rotateZ(-220deg);
        opacity: 0;
        transition: all .7s ease-in;
    }

    .checkbox label input[type="checkbox"]:checked + .cr > .cr-icon,
    .radio label input[type="radio"]:checked + .cr > .cr-icon {
        transform: scale(1) rotateZ(0deg);
        opacity: 1;
    }

    .checkbox label input[type="checkbox"]:disabled + .cr,
    .radio label input[type="radio"]:disabled + .cr {
        opacity: .5;
    }
    textarea{
        display: block;
        margin-bottom:20px;
        width:100%;
        max-height: 60px;
        min-height: 50px;
        resize: vertical;
    }

    .blocksms{
        background: #e2e2e2;
        textarea{
            display: block;
            margin: 0 0 5px;
            padding: 8px 16px;
            background: #e2e2e2;
            border: none;
            height: 54px;
            min-height: 54px;
            width: 100%;
            max-width: 100%;
            min-width: 100%;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            border-radius: 2px;
            font: 11px/20px 'Open Sans', Arial, Helvetica, sans-serif;
            color: #202226;
            max-height: 120px;
        }
        .foot{
            em{
                line-height: 16px;
                font-size: 12px;
                display: block;
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
                padding: 0 20px;
                color: #c0c0c0;
                text-align: right;
            }
        }
    }
</style>
