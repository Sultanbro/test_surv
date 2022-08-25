<template>
<div class="stats px-3 py-1">
    <!-- top line -->
    <div class="d-flex mb-2 mt-2 jcsb aifs">
        
         <div class="d-flex aic mr-2">
            <div class="d-flex aic mr-2">
                <span>Показывать:</span>
                <input type="number" min="1" max="100" v-model="pageSize" class="form-control ml-2 input-sm" />
            </div>
            <super-filter
                :groups="groups"
            />
            <span class="ml-2"> 
                Найдено: {{ items.length }}
            </span>
        </div>

        <button class="btn rounded btn-outline-success" @click="addKpi">
            <i class="fa fa-plus mr-2"></i>
            <span>Добавить</span>
        </button>
    </div>
    
    <!-- table -->

    <table class="j-table">
        <thead>
            <tr class="table-heading">
                
                <th class="first-column">
                    <i class="fa fa-cogs" @click="adjustFields"></i>
                </th>

                <th v-for="(field, i) in fields" :key="i" :class="field.class">
                    {{ field.name }}
                </th>

                <th>Действия</th>

            </tr>

        </thead>

        <tbody>

            <template v-for="(item, i) in page_items">
                <tr :key="i">
                    <td  @click="expand(i)" class="pointer">
                        <div class="d-flex px-2">
                            <i class="fa fa-minus mt-1" v-if="item.expanded"></i>
                            <i class="fa fa-plus mt-1" v-else></i>
                            <span class="ml-2">{{ i + 1 }}</span>
                        </div>
                    </td>
                    <td  v-for="(field, f) in fields" :key="f" :class="field.class"> 

                        <div v-if="field.key == 'target'" >
                            <superselect
                                v-if="item.target == null || item.id == 0"
                                class="w-full" 
                                :values="item.target == null ? [] : [item.target]" 
                                :single="true"
                                @choose="(target) => item.target = target"
                                @remove="() => item.target = null"
                                :key="i" /> 
                            <div v-else class="d-flex aic">
                                <i class="fa fa-user ml-2" v-if="item.target.type == 1"></i> 
                                <i class="fa fa-users ml-2" v-if="item.target.type == 2"></i> 
                                <i class="fa fa-briefcase ml-2" v-if="item.target.type == 3"></i> 
                                <span class="ml-2">{{ item.target.name }}</span>
                                
                            </div>
                        </div>

                        <div v-else-if="field.key == 'stats'" :class="field.class">
                            <i class="fa fa-chart-bar btn btn-primary p-1" @click="showKpiStats(i)"></i>
                        </div>

                        <div v-else-if="non_editable_fields.includes(field.key)" :class="field.class">
                           {{ item[field.key] }}
                        </div>

                        <div v-else :class="field.class">
                            <input type="text" class="form-control" v-model="item[field.key]" @change="validate(item[field.key], field.key)" /> 
                        </div>

                    </td>
                    <td >
                        <i class="fa fa-save ml-2 mr-1 btn btn-success p-1" @click="saveKpi(i)"></i>
                        <i class="fa fa-trash btn btn-danger p-1" @click="deleteKpi(i)"></i>
                    </td>
                </tr>

                <template v-if="item.items !== undefined">
                    <tr class="collapsable" :class="{'active': item.expanded}" :key="i + 'a'">
                        <td :colspan="fields.length + 2">
                            <div class="table__wrapper">
                                <kpi-items
                                    :kpi_id="item.id"
                                    :items="item.items" 
                                    :expanded="item.expanded"
                                    :activities="activities"
                                    :groups="groups"
                                    :completed_80="item.completed_80"
                                    :completed_100="item.completed_100"
                                    :lower_limit="item.lower_limit"
                                    :upper_limit="item.upper_limit"
                                />
                            </div>
                        </td>
                    </tr>                
                </template>
              
            </template>

          
        </tbody>
     </table>
      

    <!-- pagination -->
    <jw-pagination
        class="mt-3"
        :key="paginationKey"
        :items="items"
        :labels="{
            first: '<<',
            last: '>>',
            previous: '<',
            next: '>'
        }"
        @changePage="onChangePage"
        :pageSize="+pageSize"
    ></jw-pagination>

</div>
</template>

<script>
export default {
    name: "Stats", 
    props: {

    },
    data() {
        return {
            active: 1,
            items: [],
            groups: {}
        }
    },

    created() {
       // this.fetchData()
    },
    methods: {
 
    } 
}
</script>