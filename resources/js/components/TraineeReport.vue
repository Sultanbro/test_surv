<template>
<div class="trainee_report">
                                    
    <div>
        <div class="ramka mt-2" v-for="(report_date, rdate) in trainee_report">
            <div class="date-select">
                <p>{{ report_date.date }}</p>
            </div>
            <p class="mt-2"><b>{{ report_date.group }}</b></p>
            <table class="table b-table table-striped table-bordered table-sm">
                <thead>
                    <th class="text-left t-name table-title" style="background:#90d3ff;min-width: 170px;">Суть работы</th>
                    <th class="text-left t-name table-title" style="background:#90d3ff;min-width: 170px;">График работы</th>
                    <th class="text-left t-name table-title" style="background:#90d3ff;min-width: 200px;">Какая ЗП?</th>
                    <th class="text-left t-name table-title" style="background:#90d3ff;min-width: 230px;">Оценка тренера</th>
                    <th class="text-left t-name table-title" style="background:#90d3ff">Рекомендации</th>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-left t-name table-title align-middle">
                            <div v-for="answer in report_date['quiz'][1]" :key="answer.id" class="d-flex">
                                <progress-bar
                                    :percentage="Number(answer.percent)"
                                    :label="answer.text + ' (' + answer.count + ')'"
                                    :class="'active'"
                                ></progress-bar>
                            </div>
                        </td>
                        <td class="text-left t-name table-title align-middle">
                            <div v-for="answer in report_date['quiz'][2]" :key="answer.id" class="d-flex">
                                <progress-bar
                                    :percentage="Number(answer.percent)"
                                    :label="answer.text + ' (' + answer.count + ')'"
                                    :class="'active'"
                                ></progress-bar>
                            </div>
                        </td>
                        <td class="text-left t-name table-title align-middle">
                            <div v-for="answer in report_date['quiz'][3]" :key="answer.id" class="d-flex">
                                <progress-bar
                                    :percentage="Number(answer.percent)"
                                    :label="answer.text + ' (' + answer.count + ')'"
                                    :class="'active'"
                                ></progress-bar>
                            </div>
                        </td>
                        <td class="text-left t-name table-title align-middle" >
                            <div class="d-flex" style="flex-direction:column">
                                <Rating :grade="Number(report_date['quiz'][4]['avg']).toFixed(0)" :maxStars="10" :hasCounter="false" />
                                <p class="mb-0">{{ report_date['quiz'][4]['avg'] + ' (' + report_date['quiz'][4]['count'] + ')' }}</p>
                            </div>
                        </td>
                        <td class="text-left t-name table-title align-middle">
                            <div v-for="(answer, ind) in report_date['quiz'][5]"  :key="ind" class="d-flex">
                                <p class="fz12">{{ answer }}</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

        
            

            <table class="table b-table table-striped table-bordered table-sm">
                <thead>
                    <th class="text-left t-name table-title" style="background:#90d3ff">Приглашенные</th>
                    <th class="text-center t-name table-title">1 день</th>
                    <th class="text-center t-name table-title">2 день</th>
                    <th class="text-center t-name table-title">3 день</th>
                    <th class="text-center t-name table-title">4 день</th>
                    <th class="text-center t-name table-title">5 день</th>
                    <th class="text-center t-name table-title">6 день</th>
                    <th class="text-center t-name table-title">7 день</th>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-left t-name table-title align-middle" style="background:#90d3ff">
                            {{ report_date['presence'][0] }}
                        </td>
                        <td class="text-center t-name table-title align-middle">{{ report_date['presence'][1] }}</td>
                        <td class="text-center t-name table-title align-middle">{{ report_date['presence'][2] }}</td>
                        <td class="text-center t-name table-title align-middle">{{ report_date['presence'][3] }}</td>
                        <td class="text-center t-name table-title align-middle">{{ report_date['presence'][4] }}</td>
                        <td class="text-center t-name table-title align-middle">{{ report_date['presence'][5] }}</td>
                        <td class="text-center t-name table-title align-middle">{{ report_date['presence'][6] }}</td>
                        <td class="text-center t-name table-title align-middle">{{ report_date['presence'][7] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
                                    
                                    


</div>
</template>

<script>
import Rating from './ui/Rating.vue';
export default {
    components: { Rating },
    name: "TraineeReport", 
    props: ['groups', 'trainee_report'],
    data() {
        return {
           report_group_id: 0,
        }
    },
}
</script>

<style>

.wrap {
    background: aliceblue;
    margin-bottom: 10px;
    padding-top: 15px;
}
.ramka {
    border: 1px solid #dee2e6;
    box-shadow: 0 8px 10px 10px #f7f7f7;
    padding: 15px;
}
.date-select {
    width: 250px;
}
.fz12 {
    font-size: 12px;
    margin-bottom: 0;
    line-height: 20px;
    color: #000 !important;
}
.ramka .rating {
    padding: 0;
}
</style> 
