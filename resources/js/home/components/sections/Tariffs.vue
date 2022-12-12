<template>
  <section id="jTariffs">
    <a name="prices" id="prices" class="ancor"/>
    <div class="section-content">
      <h2 class="jTariffs-header jHeader">{{ $lang(lang, 'prices-header') }}</h2>
      <div class="jTariffs-content">
        <table
          class="jTariffs-table"
          :data-col="activeCol"
          @mouseout="activeCol = -1"
        >
          <tbody class="jTariffs-tbody">
            <tr
              v-for="(tr, rkey) in table"
              :key="'r' + rkey"
              class="jTariffs-tr"
            >
              <template v-if="rkey === 0 || rkey >= table.length - 3">
                <th
                  v-for="(td, dkey) in tr"
                  :key="'r' + rkey + 'd' + dkey"
                  class="jTariffs-th jTariffs-cell"
                  :data-col="dkey"
                  @mouseover="activeCol = dkey"
                >{{ td }}</th>
              </template>
              <template v-else>
                <td
                  v-for="(td, dkey) in tr"
                  :key="'r' + rkey + 'd' + dkey"
                  class="jTariffs-td jTariffs-cell"
                  :data-col="dkey"
                  @mouseover="activeCol = dkey"
                >{{ td }}</td>
              </template>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  computed: {
    lang () {
      return this.$root.$data.lang
    },
    table () {
      return this.$lang(this.lang, 'prices-table')
    }
  },
  data () {
    return {
      activeCol: -1
    }
  }
}
</script>

<style lang="stylus">
@import '../../themes/app.variables.styl'
#jTariffs
  width 100%
.jTariffs-header
  width fit-content
  margin-left auto
  margin-right auto
  margin-bottom 5rem
  position relative
  &:before
    content ''
    display block
    width 10.625rem
    height 6.625rem
    position absolute
    z-index -1
    top -2.5rem
    left -5rem
    background-image url(../../assets/s2-bg.svg)
.jTariffs-content
  overflow-x auto
.jTariffs-table
  width 100%
  border-collapse collapse
  // &[data-col="0"]
  //   [data-col="0"]
  //     background #F0F9FF
  &[data-col="1"]
    [data-col="1"]
      background #F0F9FF
  &[data-col="2"]
    [data-col="2"]
      background #F0F9FF
  &[data-col="3"]
    [data-col="3"]
      background #F0F9FF
  &[data-col="4"]
    [data-col="4"]
      background #F0F9FF
.jTariffs-tr
  border-radius 1rem
  &:nth-of-type(odd)
  &:hover
    .jTariffs-cell
      background #F0F9FF
.jTariffs-cell
  min-width 8rem
  padding 0.5rem
  text-align center
  white-space nowrap
  &[data-col="0"]
    text-align left
    white-space normal
    border-radius 1rem 0 0 1rem
  &[data-col="4"]
    border-radius 0 1rem 1rem 0
.jTariffs-th
  font-weight 500
  padding 0.6875rem
@media screen and (min-width: $medium)
  #jTariffs
    padding-bottom 5rem
  .jTariffs-cell
    min-width 14.375rem
    padding 0.6875rem
    font-size 1.125rem
  .jTariffs-th
    padding 1.125rem
</style>