<template>
	<div class="support">
		<IconChatVue />
		<span> Поддержка </span>
	</div>
</template>

<script>
import IconChatVue from './IconChat.vue'

export default {
	name: 'SupportView',
	components: {
		IconChatVue
	}
}
</script>

<style lang="scss" scoped>
.support {
  background: white;
  border-radius: 8px;
  display: flex;
  align-items: center;
  padding: 4px 18px;
  gap: 8px;
  cursor: pointer;
}
</style>