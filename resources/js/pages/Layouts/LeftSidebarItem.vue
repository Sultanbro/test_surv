<template>
	<div
		class="header__nav-link"
		:class="{'header__nav-link_highlight': highlight}"
	>
		<router-link
			v-if="to"
			:to="to || ''"
			class="header__nav-link-a"
		>
			<span
				v-if="icon"
				:class="icon"
				class="header__nav-icon"
			/>
			<img
				v-if="img"
				:style="img.style"
				:src="img.src"
				:class="img.className"
			>
			<span class="header__nav-name">{{ name }}</span>
		</router-link>
		<a
			v-else
			:href="href || 'javascript:void(0)'"
			class="header__nav-link-a"
		>
			<span
				v-if="icon"
				:class="icon"
				class="header__nav-icon"
			/>
			<img
				v-if="img"
				:style="img.style"
				:src="img.src"
				:class="img.className"
			>
			<span class="header__nav-name">{{ name }}</span>
		</a>
		<div
			v-if="popover"
			class="header__nav-popover"
		>
			{{ popover }}
		</div>
		<LeftSidebarMenu
			v-if="menu"
			:items="menu"
		/>
		<span
			v-if="to === '/news' && unviewedNewsCount > 0"
			class="news-counter"
		>
			{{ unviewedNewsCount }}
		</span>
	</div>
</template>

<script>
import LeftSidebarMenu from './LeftSidebarMenu'
import { useUnviewedNewsStore } from '@/stores/UnviewedNewsCount'
import { mapState } from 'pinia'
export default {
	name: 'LeftSidebarItem',
	components: {
		LeftSidebarMenu
	},
	props: [
		'href',
		'to',
		'name',
		'icon',
		'img',
		'menu',
		'popover',
		'highlight'
	],
	mounted(){
		this.$emit('calcsize', this.$el)
	},
	computed: {
		...mapState(useUnviewedNewsStore, ['unviewedNewsCount']),
	}
}
</script>

<style lang="scss">
	.news-counter{
		position: absolute;
		top: 5px;
		right: 5px;
		z-index: 12;
		width: 17px;
		height: 17px;
		font-size: 9px;
		font-weight: 600;
		border-radius: 50%;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		background-color: red;
		color: #fff;
	}
.header__nav-link{
  &:hover{
    .header__nav-popover{
      opacity: 1;
      visibility: visible;
    }
  }
  &_highlight{
	.header__nav-link-a{
		background: #608EE9;
		color: #fff;
	}
	.header__nav-icon,
	.header__nav-name{
		color: #fff;
	}
  }
}

.header__nav-popover{
  display: block;
  width: 25rem;
  padding: 1rem;
  border-radius: 1rem 1rem;

  position: fixed;
  z-index: 1005;
  left: 8rem;

  background: #fff;
  color: #657A9F;
  font-size: 1.3rem;
  box-shadow: 1rem 0 2rem rgba(0, 0, 0, 0.15);
  opacity: 0;
  visibility: hidden;
}
</style>
