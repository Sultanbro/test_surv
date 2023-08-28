<template>
	<div class="header__menu">
		<template v-for="item in items">
			<template v-if="!item.hide">
				<router-link
					v-if="item.to"
					:key="item.name"
					:to="item.to"
					class="menu__item"
				>
					<span
						v-if="item.icon"
						:class="item.icon"
						class="menu__item-icon"
					/>
					<img
						v-if="item.img"
						:src="item.img.src"
						:style="'max-width: 1em;' + item.img.style"
						:alt="item.alt"
					>
					<span class="menu__item-title">{{ item.name }}</span>
				</router-link>
				<a
					v-else
					:key="item.name + 'else'"
					:href="item.href"
					class="menu__item"
					target="_blank"
				>
					<span
						v-if="item.icon"
						:class="item.icon"
						class="menu__item-icon"
					/>
					<img
						v-if="item.img"
						:src="item.img.src"
						:style="'max-width: 1em;' + item.img.style"
						:alt="item.alt"
					>
					<span class="menu__item-title">{{ item.name }}</span>
				</a>
			</template>
		</template>
	</div>
</template>

<script>
export default {
	name: 'LeftSidebarMenu',
	props: {
		items: {
			type: Array,
			default: () => {}
		}
	}
}
</script>

<style lang="scss">
</style>
