<template>
	<div
		class="flowchart-node"
		:style="nodeStyle"
		:class="{selected: options.selected === id}"
		@mousedown="handleMousedown"
		@mouseover="handleMouseOver"
		@mouseleave="handleMouseLeave"
	>
		<template v-if="type!='start'">
			<div
				class="node-port node-input"
				@mousedown="inputMouseDown"
				@mouseup="inputMouseUp"
			/>
		</template>
		<div class="node-main">
			<div
				v-if="type==='yesorno'"
				class="node-type"
			>
				Ответ
			</div>
			<div
				v-else-if="type==='start'"
				class="node-type"
			>
				Начало
			</div>
			<div
				v-else-if="type==='textsintez'"
				class="node-type"
			>
				Вопрос
			</div>
			<div
				v-else-if="type==='end'"
				class="node-type"
			>
				Конец
			</div>
			<div
				v-else-if="type==='forwarding'"
				class="node-type"
			>
				Переадресация
			</div>
			<div
				v-else-if="type==='smsforward'"
				class="node-type"
			>
				Отправить смс
			</div>

			<div
				v-else
				class="node-type"
				v-text="type"
			/>

			<div
				class="node-label"
				v-text="label"
			/>
			<div
				v-if="type==='Музыка'"
				class="audio_file"
				v-text="audio_file"
			/>
			<div
				v-if="type==='Кнопка'"
				class="node-key"
				v-text="key_button"
			/>
			<div
				v-if="type==='Кнопка'"
				class="node-action"
				v-text="action_button"
			/>
			<div
				v-if="action_button==='Позвонить оператору' || action_button==='Связать с номером' ||
					action_button==='Перенести в отдел'"
				class="node-connection"
				v-text="connect_with"
			/>
		</div>

		<template v-if="type=='yesorno'">
			<div
				class="node-port node-output portyes"
				@mousedown="outputMouseDownyes"
			/>

			<div
				class="node-port node-output portno"
				@mousedown="outputMouseDownno"
			/>
		</template>

		<template v-if="type!='yesorno' && type!='end'">
			<div
				class="node-port node-output"
				@mousedown="outputMouseDown"
			/>
		</template>
		<template v-if="type!='start'">
			<div
				v-show="show.delete"
				class="node-delete"
			>
				&times;
			</div>
		</template>

		<button @click="login">
			Изменить
		</button>
	</div>
</template>



<script>
export default {
	name: 'FlowchartNode',
	props: {
		id: {
			type: Number,
			default: 1000,
			validator(val) {
				return typeof val === 'number'
			}
		},
		x: {
			type: Number,
			default: 0,
			validator(val) {
				return typeof val === 'number'
			}
		},    
		y: {
			type: Number,
			default: 0,
			validator(val) {
				return typeof val === 'number'
			}
		},
		type: {
			type: String,
			default: 'Default'
		},
		label: {
			type: String,
			default: 'input name'
		},
		description: {
			type: String,
			default: ''
		},
		otvetyes: {
			type: String,
			default: ''
		},
		smsforward: {
			type: String,
			default: ''
		},
		integrationsms: {
			type: String,
			default: ''
		},
		otvetno: {
			type: String,
			default: ''
		},
		forwardphone: {
			type: String,
			default: ''
		},
		key_button: {
			type: Number,
			default: -1,
		},
		action_button: {
			type: String,
			default: null,
		},
		audio_file: {
			type: String,
			default:null,
		},
		templateAudio: {
			type: String,
			default:null,
		},
		lidnoanswer: {
			type: Boolean,
			default:false,
		},
		connect_with: {
			type: String,
			default:null,
		},
		options: {
			type: Object,
			default() {
				return {
					centerX: 500,
					scale: 1,
					centerY: 140,
				}
			}
		}
	},
	data() {
		return {
			show: {
				delete: false,

			}
		}
	},
	computed: {
		nodeStyle() {
			return {
				top: this.options.centerY + this.y * this.options.scale + 'px', // remove: this.options.offsetTop + 
				left: this.options.centerX + this.x * this.options.scale + 'px', // remove: this.options.offsetLeft +
				transform: `scale(${this.options.scale})`,
			}
		}
	},
	mounted() {
	},
	methods: {
		handleMousedown(e) {
			const target = e.target || e.srcElement;
			// console.log(target);
			if (target.className.indexOf('node-input') < 0 && target.className.indexOf('node-output') < 0) {
				this.$emit('nodeSelected', e);
			}
			e.preventDefault();
		},
		handleMouseOver() {
			this.show.delete = true;

		},
		handleMouseLeave() {
			this.show.delete = false;

		},
		outputMouseDownyes(e) {
			this.$emit('linkingStartyes')
			e.preventDefault();
		},
		outputMouseDownno(e) {
			this.$emit('linkingStartno')
			e.preventDefault();
		},
		outputMouseDown(e) {
			this.$emit('linkingStart')
			e.preventDefault();
		},
		inputMouseDown(e) {
			e.preventDefault();
		},
		inputMouseUp(e) {
			this.$emit('linkingStop')
			e.preventDefault();
		},

		login () {
			this.$emit('login', {
				id: this.id,
				type: this.type,
				label: this.label,
				action_button: this.action_button,
				key_button: this.key_button,
				audio_file: this.audio_file,
				connect_with: this.connect_with,
				templateAudio: this.templateAudio,
				description: this.description,
				otvetyes: this.otvetyes,
				otvetno: this.otvetno,
				lidnoanswer: this.lidnoanswer,
				forwardphone: this.forwardphone,
				smsforward:this.smsforward,
				integrationsms:this.integrationsms,
			})
		},
	},
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
$themeColor: #d632e9;
$portSize: 12;

.flowchart-node {
  margin: 0;
  width: 150px;
  height: 100px;
  position: absolute;
  box-sizing: border-box;
  border: none;
  background: white;
  z-index: 1;
  opacity: .9;
  cursor: move;
  transform-origin: top left;
  box-shadow: 0 0 10px #ccc;
  .node-main {
    text-align: center;
    .node-type {
      background: $themeColor;
      color: white;
      font-size: 13px;
      padding: 6px;
    }
    .node-label {
      font-size: 13px;
    }
    .node-key {
      font-size: 13px;
    }
  .audio_file {
    font-size: 13px;
  }
  .node-action {
      font-size: 13px;
    }
    .node-connect {
      font-size: 13px;
    }
  }
  .node-port {
    position: absolute;
    width: #{$portSize}px;
    height: #{$portSize}px;
    left: 50%;
    transform: translate(-50%);
    border: 1px solid #ccc;
    border-radius: 100px;
    background: white;
    &:hover {
      background: $themeColor;
      border: 1px solid $themeColor;
    }
  }
  .node-input {
    top: #{-2+$portSize/-2}px;
  }
  .node-output {
    bottom: #{-2+$portSize/-2}px;
  }
  .node-delete {
    position: absolute;
    right: -6px;
    top: -6px;
    font-size: 12px;
    width: 12px;
    height: 12px;
    line-height: 9px;
    color: $themeColor;
    cursor: pointer;
    background: white;
    border: 1px solid $themeColor;
    border-radius: 100px;
    text-align: center;
    &:hover{
      background: $themeColor;
      color: white;
    }
  }
}
.selected {
  box-shadow: 0 0 0 2px $themeColor;
}

.portyes {
  left: 25%!important;
  background: green!important;
  border-color: green!important;
}

.portno {
  left: 75%!important;
  background: red!important;
  border-color: red!important;
}
</style>
