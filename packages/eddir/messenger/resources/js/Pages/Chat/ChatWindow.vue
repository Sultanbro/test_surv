<template>
  <div>
    <h1>Feature chat interface</h1>
    <h2>{{ phpVersion }}</h2>
    <SocialAction />
    <ChatComponent />
  </div>
</template>

<script>
import SocialAction from '@/Components/Chat/SocialAction.vue';
import ChatComponent from '@/Components/Chat/ChatComponent.vue';

export default {
  name: 'ChatWindow',
  components: {
    SocialAction,
    ChatComponent,
  },

  props: {
    phpVersion: { type: String, default: undefined },
  },
};
</script>
