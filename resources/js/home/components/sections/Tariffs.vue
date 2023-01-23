<template>
	<section id="jTariffs">
		<a
			id="prices"
			class="ancor"
			name="prices"
		/>
		<div class="section-content">
			<div class="jTariffs-header-wrapper jTariffs-header">
				<h2 class="jTariffs-header jHeader">
					{{ $lang(lang, 'prices-header') }}
				</h2>
			</div>
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
								>
									{{ td }}
								</th>
							</template>
							<template v-else>
								<td
									v-for="(td, dkey) in tr"
									:key="'r' + rkey + 'd' + dkey"
									:data-col="dkey"
									class="jTariffs-td jTariffs-cell"
									@mouseover="activeCol = dkey"
								>
									{{ td }}
								</td>
							</template>
						</tr>
						<tr>
							<th />
							<td
								v-for="td in 4"
								:key="td"
							>
								<a
									class="jButton jButton-tariffs-four"
									href="/register"
								>
									{{ $lang(lang, 'prices-register') }}
								</a>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="jButton-tariffs-one">
					<a
						class="jButton"
						href="/register"
					>
						{{ $lang(lang, 'prices-register') }}
					</a>
				</div>
			</div>
		</div>
	</section>
</template>

<script>
export default {
	name: 'SectionTariffs',
	computed: {
		lang() {
			return this.$root.$data.lang
		},
		table() {
			return this.$lang(this.lang, 'prices-table')
		},
		isMedium() {
			return this.$viewportSize.width >= 1260
		}
	},
	data() {
		return {
			activeCol: -1,
			image: require('../../assets/img/tariffs.png').default,
			usdRate: 0,
			kztRate: 0,
			selectedValute: ''

		}
	},
}
</script>

<style lang="scss">
@import '../../assets/scss/app.variables.scss';

#jTariffs {
  width: 100%;
  margin-top: 5rem;
}

@media (max-width: $small) {
  #jTariffs {
    margin-top: 3rem;
  }
}

.jTariffs-header {
  margin-right: 1rem;
}

.jTariffs-content {
  overflow-x: auto;
}

.jTariffs-header-wrapper {
  display: flex;
  align-items: center;
  width: fit-content;
  margin-left: auto;
  margin-right: auto;
  margin-bottom: 3rem;
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
    background-repeat: no-repeat;
  }
}

.jTariffs-image-wrap {
  display: flex;
  flex-flow: column;
  align-items: center;
  gap: 1rem;
}

.jTariffs-image {
  max-width: 100%;
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
  font-weight: 700;
  padding: 0.6875rem;
}

@media screen and (min-width: $medium) {
  #jTariffs {
    margin-top: 3rem;
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

a.jButton-tariffs-four {
  margin: 1rem auto;
  font-size: 0.8rem;
}

.jButton-tariffs-one {
  display: none;
}

@media (max-width: 900px) {
  a.jButton-tariffs-four {
    display: none;
  }

  .jButton-tariffs-one {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
  }
}

@media (max-width: 768px) {
  .jTariffs-cell {
    min-width: 5rem;
  }
}

@media (max-width: 670px) {
  .jTariffs-cell {
    min-width: 4rem;
    font-size: 12px;
  }
}

@media (max-width: 530px) {
  .jTariffs-th {
    padding: 0.5rem;
  }
  .jTariffs-cell {
    min-width: 2rem;
    font-size: 10px;
  }
}

@media (max-width: 480px) {
  .jTariffs-cell {
    font-size: 9px;
  }
}

@media (max-width: 430px) {
  .jTariffs-cell {
    font-size: 8px;
  }
}

@media (max-width: 400px) {
  .jTariffs-cell {
    font-size: 7px;
  }
}

@media (max-width: 366px) {
  .jTariffs-cell {
    font-size: 6px;
  }
}

@media (max-width: 366px) {
  .jTariffs-cell {
    font-size: 6px;
  }
}
</style>
