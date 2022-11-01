<template>
  <div>
    <slot
      name="emoji-invoker"
      :events="{ click: (e) => toggle(e) }"
    ></slot>
    <div
      v-if="display.visible"
      v-click-outside="hide"
    >
      <slot
        name="emoji-picker"
        :emojis="emojis"
        :insert="insert"
        :display="display"
      ></slot>
    </div>
  </div>
</template>

<script lang="ts">
import emojis from './emojis.ts'

// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide/Regular_Expressions#escaping
// const escapeRegExp = (s: string) => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')

export default /*#__PURE__*/{
  name: 'EmojiPicker',
  props: {
    search: {
      type: String,
      required: false,
      default: '',
    },
    emojiTable: {
      type: Object,
      required: false,
      default() {
        return emojis
      },
    },
  },
  data() {
    return {
      display: {
        x: 0,
        y: 0,
        visible: false,
      },
    }
  },
  computed: {
    emojis() {
      if (this.search) {
        const obj = {}

        for (const category in this.emojiTable) {
          obj[category] = {}

          // for (const emoji in this.emojiTable[category]) {
            // if (new RegExp(`.*${escapeRegExp(this.search)}.*`).test(emoji)) {
            //   obj[category][emoji] = this.emojiTable[category][emoji]
            // }
          // }

          if (Object.keys(obj[category]).length === 0) {
            delete obj[category]
          }
        }

        return obj
      }

      return this.emojiTable
    },
  },
  methods: {
    insert(emoji) {
      this.$emit('emoji', emoji)
    },
    toggle(e) {
      this.display.visible = ! this.display.visible

      // const messengerWindowRect = document.getElementById('messengerInput').getBoundingClientRect()
      // const x = messengerWindowRect.left - 300;
      // const y = messengerWindowRect.top - 400;
      // console.log('x', messengerWindowRect.left, 'y', messengerWindowRect.top, 'x', x, 'y', y)
      // //
      // this.display.x = x
      // this.display.y = y
    },
    hide() {
      this.display.visible = false
    },
    escape(e) {
      if (this.display.visible === true && e.keyCode === 27) {
        this.display.visible = false
      }
    },
  },
  directives: {
    'click-outside': {
      bind(el, binding) {
        if (typeof binding.value !== 'function') {
          return
        }

        const bubble = binding.modifiers.bubble
        const handler = e => {
          if (bubble || (! el.contains(e.target) && el !== e.target)) {
            binding.value(e)
          }
        }
        el.__vueClickOutside__ = handler

        document.addEventListener('click', handler)
      },
      unbind(el) {
        if (el.__vueClickOutside__ !== null) {
          document.removeEventListener('click', el.__vueClickOutside__)

          el.__vueClickOutside__ = null
        }
      },
    },
  },
  mounted() {
    document.addEventListener('keyup', this.escape)
  },
  destroyed() {
    document.removeEventListener('keyup', this.escape)
  },
}
</script>
