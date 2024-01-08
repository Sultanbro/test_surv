<template>
	<table
		v-if="fields && items"
		class="JobtronTable"
	>
		<slot
			v-if="!headless"
			name="thead"
			:fields="fields"
		>
			<thead class="JobtronTable-head">
				<slot name="theadBefore" />
				<tr
					class="JobtronTable-row"
					:class="{
						'JobtronTable-stickyHeader': stickyHeader
					}"
				>
					<template v-for="field in fields">
						<th
							v-if="!field.hide"
							:key="field.key"
							class="JobtronTable-th"
							:class="field.thClass"
							:colspan="field.colspan"
							:rowspan="field.rowspan"
						>
							<slot
								v-if="$scopedSlots[`header(${field.key})`]"
								:name="`header(${field.key})`"
								:field="field"
							/>
							<slot
								v-else
								name="header"
								:field="field"
							>
								{{ field.label }}
							</slot>
						</th>
					</template>
				</tr>
			</thead>
		</slot>
		<tbody class="JobtronTable-body">
			<template v-for="row, rowIndex in items">
				<tr
					:key="rowIndex"
					class="JobtronTable-row"
					:class="[trClassFn(row)]"
				>
					<td
						v-for="field in fields"
						:key="field.key"
						class="JobtronTable-td"
						:class="field.tdClass"
					>
						<slot
							v-if="$scopedSlots[`cell(${field.key})`]"
							:name="`cell(${field.key})`"
							:value="row[field.key]"
							:item="row"
							:field="field"
							:index="rowIndex"
						/>
						<slot
							v-else
							name="cell"
							:value="row[field.key]"
							:item="row"
							:field="field"
							:index="rowIndex"
						>
							{{ row[field.key] }}
						</slot>
					</td>
				</tr>
				<template v-if="$scopedSlots['afterRow']">
					<tr
						:key="'after' + rowIndex"
						class="JobtronTable-row JobtronTable-afterRow"
						:class="[trAfterClassFn(row)]"
					>
						<td
							class="JobtronTable-td"
							:colspan="trAfterColspan"
						>
							<slot
								:name="`afterRow`"
								:value="row"
								:index="rowIndex"
							/>
						</td>
					</tr>
				</template>
			</template>
		</tbody>
	</table>
</template>

<script>
//
export default {
	name: 'JobtronTable',
	components: {},
	props: {
		fields: {
			type: Array,
			required: true
		},
		items: {
			type: Array,
			required: true
		},
		stickyHeader: {
			type: Boolean,
			default: false
		},
		headless: {
			type: Boolean,
			default: false
		},
		trClassFn: {
			type: Function,
			default: () => ''
		},
		trAfterClassFn: {
			type: Function,
			default: () => ''
		},
		trAfterColspan: {
			type: Number,
			default: 9999
		}
	}
}
</script>

<style lang="scss">
.JobtronTable{
	width: 100%;
	max-width: 100%;
	border-spacing: 0px;
	border-radius: 1.2rem 1.2rem 0 0;
	border-collapse: separate;

	font-family: "Inter", sans-serif;
	text-align: center;
	color: #62788B;

	background-color: transparent;

	&-head{
		position: relative;
		overflow: hidden;
		z-index: 5;
		.JobtronTable-row{
			&:first-child{
				.JobtronTable-th{
					&:first-child{
						border-radius: 12px 0 0 0;
						&:before{
							content: '';
							position: absolute;
							top: -11px;
							left: -13px;
							transform: skewX(326deg);
							background-color: #fff;
							width: 20px;
							height: 20px;
							border-radius: 50px;
						}
					}
					&:last-child{
						border-radius: 0 12px 0 0;
					}
				}
			}
		}
	}

	&-row{
		position: relative;
    z-index: 2;
		&:last-child{
			// .JobtronTable-th,
			.JobtronTable-td{
				border-bottom: 1px solid #E7EAEA;
			}
		}
	}

	&-th,
	&-td{
		padding: 12px 15px;
		border-left: 1px solid #E7EAEA;
    border-top: 1px solid #E7EAEA;
		line-height: 1.2;
    font-size: 14px;
		&:last-child{
			border-right: 1px solid #E7EAEA;
		}
	}

	&-th{
		background-color: #f8f9fd;
		font-weight: 700;
	}
	&-td{
		background-color: #fff;
	}

	&-sticky{
		position: sticky;
		left: 0;
		z-index: 2;
		border-right: 1px solid #E7EAEA;
	}

	&-stickyHeader{
		position: sticky;
		top: 0;
		z-index: 2;
	}
}
</style>
