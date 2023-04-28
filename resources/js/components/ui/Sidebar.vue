<template>
	<div
		class="ui-sidebar"
		:class="[{'is-open':open}]"
		@click.self="$emit('close');"
	>
		<div
			class="ui-sidebar__body"
			:style="`width:${width}`"
		>
			<div class="ui-sidebar__header d-flexy">
				<slot name="header">
					<span class="ui-sidebar__header-text">{{ title }}</span>
					<span
						class="ui-sidebar__header-link"
						v-html="link"
					/>
				</slot>
			</div>
			<div class="ui-sidebar__content">
				<slot />
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'UISidebar',
	props: {
		title: {},
		open: {},
		width: {},
		link: {
			default: ''
		},
	},
	data() {
		return {}
	},
}
</script>

<style lang="scss" scoped>
.ui-sidebar__header {
    padding: 13px 27px  !important;
		&-text{
			color:aliceblue;
			background: transparent;
			font-size: 14px;
		}
		&-link{
			color:aliceblue;
		}
}
.ui-sidebar__content {
    .custom-checkbox {
        margin-bottom: .7rem;
    }
}
.ui-sidebar {
  background: rgba(0, 0, 0, 0.45);
  width: 100%;
  height: 100vh;
  left: 150%;
  top: 0;
  position: fixed;
  z-index: 11;

  &__body {
    position: absolute;
    right: -100%;
    top: 0;
    height: 100vh;
    background: #fff;
    z-index: 9999;
    box-shadow: 1px 1px 4px rgba(0, 0, 0, 0.64);
    transition: 0.3s ease-in-out all;
    overflow: auto;
  }

  &.is-open {
    left:0;

    .ui-sidebar__body {
      right: 50px;
    }

  }

  &__header {
    padding: 15px;
    color: #fff;
    font-size: 1rem;
    line-height: 1.5rem;
    position: relative;
    background-color: #045e92;
    background: linear-gradient(322deg, #1076b0 0%, #045e92 100%);
    //background-image: linear-gradient(62deg, #8EC5FC 0%, #E0C3FC 100%);

    a {
      margin-left: -10px;
      margin-right: 10px;
      padding: 5px;
    }
  }

  &__content {
    padding: 10px;
    background: #fff;
  }

}

.sidebar_warp {
  transition: 1s ease all;
}
.d-flexy {
  display: flex;
  justify-content: space-between;
}
</style>
