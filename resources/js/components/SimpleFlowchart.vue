<template>
	<!-- eslint-disable vue/no-mutating-props -->
	<div
		class="flowchart-container"
		@mousemove="handleMove"
		@mouseup="handleUp"
		@mousedown="handleDown"
	>
		<svg
			width="100%"
			:height="`${height}px`"
		>
			<!-- eslint-disable-next-line vue/valid-v-bind-sync -->
			<flowchart-link
				v-for="(link, index) in lines"
				:key="`link${index}`"
				@deleteLink="linkDelete(link.id)"
			/>
		</svg>
		<!-- eslint-disable-next-line vue/valid-v-bind-sync -->
		<flowchart-node
			v-for="(node, index) in scene.nodes"
			:key="`node${index}`"
			:options="nodeOptions"
			@linkingStart="linkingStart(node.id)"
			@linkingStartyes="linkingStartyes(node.id)"
			@linkingStartno="linkingStartno(node.id)"
			@login="onLogin"
			@linkingStop="linkingStop(node.id)"
			@nodeSelected="nodeSelected(node, $event)"
		/>
	</div>
</template>

<script>
/* eslint-disable camelcase */
/* eslint-disable vue/no-mutating-props */

import FlowchartLink from './FlowchartLink.vue';
import FlowchartNode from './FlowchartNode.vue';
import { getMousePosition } from '../position';

export default {
	name: 'VueFlowchart',
	components: {
		FlowchartLink,
		FlowchartNode,
	},
	props: {
		scene: {
			type: Object,
			default() {
				return {
					centerX: 500,
					scale: 1,
					centerY: 140,
					nodes: [],
					links: [],
				}
			}
		},
		height: {
			type: Number,
			default: 400,
		},
	},
	data() {
		return {
			buttons:[
				{
					id: 2,
					x: -250,
					y: -90,
					type: 'Старт',
					label: 'Начало сценарий',
					key_button: -1,
					action_button: null,
					audio_file: null,
					templateAudio:null,
					lidnoanswer:false,
				},
				{
					id: 7,
					x: -250,
					y: 360,
					type: 'Конец',
					label: 'Конец сценарий',
					key_button: -1,
					action_button: null,
					audio_file: null,
					templateAudio:null,
					lidnoanswer:false,
				}
			],
			action: {
				linking: false,
				dragging: false,
				scrolling: false,
				selected: 0,
			},
			mouse: {
				x: 0,
				y: 0,
				lastX: 0,
				lastY: 0,
			},
			draggingLink: null,
			rootDivOffset: {
				top: 0,
				left: 0
			},
		};
	},
	computed: {
		nodeOptions() {
			return {
				centerY: this.scene.centerY,
				centerX: this.scene.centerX,
				scale: this.scene.scale,
				offsetTop: this.rootDivOffset.top,
				offsetLeft: this.rootDivOffset.left,
				selected: this.action.selected,
			}
		},
		lines() {
			const lines = this.scene.links.map((link) => {
				const fromNode = this.findNodeWithID(link.from)
				const toNode = this.findNodeWithID(link.to)
				let x, y, cy, cx, ex, ey;
				if (fromNode.type == 'yesorno' && link.action =='yes') {
					x = this.scene.centerX + fromNode.x-38;
					y = this.scene.centerY + fromNode.y;
					[cx, cy] = this.getPortPosition('bottom', x, y);
				} else if (fromNode.type == 'yesorno' && link.action =='no') {
					x = this.scene.centerX + fromNode.x+38;
					y = this.scene.centerY + fromNode.y;
					[cx, cy] = this.getPortPosition('bottom', x, y);
				} else {
					x = this.scene.centerX + fromNode.x;
					y = this.scene.centerY + fromNode.y;
					[cx, cy] = this.getPortPosition('bottom', x, y);

				}

				x = this.scene.centerX + toNode.x;
				y = this.scene.centerY + toNode.y;
				[ex, ey] = this.getPortPosition('top', x, y);
				return {
					start: [cx, cy],
					end: [ex, ey],
					id: link.id,
				};
			})
			if (this.draggingLink) {
				let x, y, cy, cx;
				const fromNode = this.findNodeWithID(this.draggingLink.from)


				if (fromNode.type == 'yesorno' && this.draggingLink.action == 'yes') {
					x = this.scene.centerX + fromNode.x-38;
					y = this.scene.centerY + fromNode.y;
					[cx, cy] = this.getPortPosition('bottom', x, y);
				} else if (fromNode.type == 'yesorno' && this.draggingLink.action == 'no') {
					x = this.scene.centerX + fromNode.x+38;
					y = this.scene.centerY + fromNode.y;
					[cx, cy] = this.getPortPosition('bottom', x, y);
				} else {

					x = this.scene.centerX + fromNode.x;
					y = this.scene.centerY + fromNode.y;
					[cx, cy] = this.getPortPosition('bottom', x, y);

				}
				// push temp dragging link, mouse cursor postion = link end postion
				lines.push({
					action:this.draggingLink.action,
					start: [cx, cy],
					end: [this.draggingLink.mx, this.draggingLink.my],
				})
			}
			return lines;
		}
	},
	mounted() {
		this.rootDivOffset.top = this.$el ? this.$el.offsetTop : 0;
		this.rootDivOffset.left = this.$el ? this.$el.offsetLeft : 0;
	},
	methods: {
		addNodes () {
			this.$emit('addNodes', {

			})
		},
		findNodeWithID(id) {
			return this.scene.nodes.find((item) => {
				return id === item.id
			})
		},
		getPortPosition(type, x, y) {
			if (type === 'top') {
				return [x + 75, y];
			}
			else if (type === 'bottom') {
				return [x + 75, y + 100];
			}
		},
		linkingStart(index) {
			this.action.linking = true;
			this.draggingLink = {
				from: index,
				mx: 0,
				my: 0,
			};
		},
		linkingStartyes(index) {
			this.action.linking = true;
			this.draggingLink = {
				from: index,
				action:'yes',
				mx: 0,
				my: 0,
			};
		},
		linkingStartno(index) {
			this.action.linking = true;
			this.draggingLink = {
				from: index,
				action:'no',
				mx: 0,
				my: 0,
			};
		},
		onLogin (data) {
			this.$emit('onLogin', data)
		},
		linkingStop(index) {
			// add new Link
			if (this.draggingLink && this.draggingLink.from !== index) {
				// check link existence
				const existed = this.scene.links.find((link) => {
					return link.from === this.draggingLink.from && link.to === index;
				})
				if (!existed) {
					let maxID = Math.max(0, ...this.scene.links.map((link) => {
						return link.id
					}))
					const newLink = {
						id: maxID + 1,
						from: this.draggingLink.from,
						to: index,
						action: this.draggingLink.action,
					};
					this.scene.links.push(newLink)
					this.$emit('linkAdded', newLink)
				}
			}
			this.draggingLink = null
		},
		linkDelete(id) {
			const deletedLink = this.scene.links.find((item) => {
				return item.id === id;
			});
			if (deletedLink) {
				this.scene.links = this.scene.links.filter((item) => {
					return item.id !== id;
				});
				this.$emit('linkBreak', deletedLink);
			}
		},
		nodeSelected(node, e) {

			let id=node.id
			this.action.dragging = id;
			this.action.selected = id;
			this.$emit('nodeClick', id);
			this.mouse.lastX = e.pageX || e.clientX + document.documentElement.scrollLeft
			this.mouse.lastY = e.pageY || e.clientY + document.documentElement.scrollTop
		},
		handleMove(e) {
			if (this.action.linking) {
				[this.mouse.x, this.mouse.y] = getMousePosition(this.$el, e);
				[this.draggingLink.mx, this.draggingLink.my] = [this.mouse.x, this.mouse.y];
			}
			if (this.action.dragging) {
				this.mouse.x = e.pageX || e.clientX + document.documentElement.scrollLeft
				this.mouse.y = e.pageY || e.clientY + document.documentElement.scrollTop
				let diffX = this.mouse.x - this.mouse.lastX;
				let diffY = this.mouse.y - this.mouse.lastY;

				this.mouse.lastX = this.mouse.x;
				this.mouse.lastY = this.mouse.y;
				this.moveSelectedNode(diffX, diffY);
			}
			if (this.action.scrolling) {
				[this.mouse.x, this.mouse.y] = getMousePosition(this.$el, e);
				let diffX = this.mouse.x - this.mouse.lastX;
				let diffY = this.mouse.y - this.mouse.lastY;

				this.mouse.lastX = this.mouse.x;
				this.mouse.lastY = this.mouse.y;

				this.scene.centerX += diffX;
				this.scene.centerY += diffY;

				// this.hasDragged = true
			}
		},
		handleUp(e) {
			const target = e.target || e.srcElement;
			if (this.$el.contains(target)) {
				if (typeof target.className !== 'string' || target.className.indexOf('node-input') < 0) {
					this.draggingLink = null;
				}
				if (typeof target.className === 'string' && target.className.indexOf('node-delete') > -1) {
					this.nodeDelete(this.action.dragging);
				}
			}
			this.action.linking = false;
			this.action.dragging = null;
			this.action.scrolling = false;
		},
		handleDown(e) {
			const target = e.target || e.srcElement;
			if ((target === this.$el || target.matches('svg, svg *')) && e.which === 1) {
				this.action.scrolling = true;
				[this.mouse.lastX, this.mouse.lastY] = getMousePosition(this.$el, e);
				this.action.selected = null; // deselectAll
			}
			this.$emit('canvasClick', e);
		},
		moveSelectedNode(dx, dy) {
			let index = this.scene.nodes.findIndex((item) => {
				return item.id === this.action.dragging
			})
			let left = this.scene.nodes[index].x + dx / this.scene.scale;
			let top = this.scene.nodes[index].y + dy / this.scene.scale;
			this.$set(this.scene.nodes, index, Object.assign(this.scene.nodes[index], {
				x: left,
				y: top,
			}));
		},
		nodeDelete(id) {
			this.scene.nodes = this.scene.nodes.filter((node) => {
				return node.id !== id;
			})
			this.scene.links = this.scene.links.filter((link) => {
				return link.from !== id && link.to !== id
			})
			this.$emit('nodeDelete', id)
		}
	},
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->

