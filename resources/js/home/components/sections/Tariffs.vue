<template>
  <section id="jTariffs">
    <a id="prices" class="ancor" name="prices"/>
    <div class="section-content">
      <h2 class="jTariffs-header jHeader">{{ $lang(lang, 'prices-header') }}</h2>
      <div class="jTariffs-content">
        <table
            :data-col="activeCol"
            class="jTariffs-table"
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
                  :data-col="dkey"
                  class="jTariffs-th jTariffs-cell"
                  @mouseover="activeCol = dkey"
              >{{ td }}
              </th>
            </template>
            <template v-else>
              <td
                  v-for="(td, dkey) in tr"
                  :key="'r' + rkey + 'd' + dkey"
                  :data-col="dkey"
                  class="jTariffs-td jTariffs-cell"
                  @mouseover="activeCol = dkey"
              >{{ td }}
              </td>
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
    lang() {
      return this.$root.$data.lang
    },
    table() {
      return this.$lang(this.lang, 'prices-table')
    }
  },
  data() {
    return {
      activeCol: -1
    }
  }
}
</script>

<style lang="scss">
@import '../../assets/scss/app.variables.scss';

#jTariffs {
  width: 100%;
}

.jTariffs-header {
  width: fit-content;
  margin-left: auto;
  margin-right: auto;
  margin-bottom: 5rem;
  position: relative;

  &:before {
    content: '';
    display: block;
    width: 10.625rem;
    height: 6.625rem;
    position: absolute;
    z-index: -1;
    top: -2.5rem;
    left: -5rem;
    background-image: url("../../assets/img/s2-bg.svg");
  }
}

.jTariffs-content {
  overflow-x: auto;
}

.jTariffs-table {
  width: 100%;
  border-collapse: collapse;
}

.jTariffs-table[data-col="1"] {
  [data-col="1"] {
    background: #f0f9ff;
  }
}

.jTariffs-table[data-col="2"] {
  [data-col="2"] {
    background: #f0f9ff;
  }
}

.jTariffs-table[data-col="3"] {
  [data-col="3"] {
    background: #f0f9ff;
  }
}

.jTariffs-table[data-col="4"] {
  [data-col="4"] {
    background: #f0f9ff;
  }
}

.jTariffs-tr {
  border-radius: 1rem;
}

.jTariffs-tr:nth-of-type(odd) .jTariffs-cell,
.jTariffs-tr:hover .jTariffs-cell {
  background: #f0f9ff;
}

.jTariffs-cell {
  min-width: 8rem;
  padding: 0.5rem;
  text-align: center;
  white-space: nowrap;
}

.jTariffs-cell[data-col="0"] {
  text-align: left;
  white-space: normal;
  border-radius: 1rem 0 0 1rem;
}

.jTariffs-cell[data-col="4"] {
  border-radius: 0 1rem 1rem 0;
}

.jTariffs-th {
  font-weight: 500;
  padding: 0.6875rem;
}

@media screen and (min-width: $medium) {
  #jTariffs {
    padding-bottom: 5rem;
  }
  .jTariffs-cell {
    min-width: 14.375rem;
    padding: 0.6875rem;
    font-size: 1.125rem;
  }
  .jTariffs-th {
    padding: 1.125rem;
  }
}
</style>
