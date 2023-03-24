<!--<template>-->
<!--	<div class="structure-company">-->
<!--		<template v-for="(department, index) in departments">-->
<!--			<StructureGroup :department="department"/>-->
<!--		</template>-->
<!--	</div>-->
<!--</template>-->

<!--<script>-->
<!--	import StructureGroup from "./StructureGroup";-->

<!--	const photo = 'https://eq7sa6brf7w.exactdn.com/wp-content/uploads/2019/02/man.png?strip=all&lossy=1&ssl=1';-->
<!--	const resultText = 'Жулимэ — это суета материального мира, попытка сделать из жулимэ монету. Если у тебя ворс неблагородный, если ты делаешь монету из плохого ворса, то ты не сигмач.';-->
<!--	export default {-->
<!--		name: 'StructureComp',-->
<!--		components: {-->
<!--			StructureGroup-->
<!--		},-->
<!--		data() {-->
<!--			return {-->
<!--				ceo: {-->
<!--					department: 'Коммерческий департамент',-->
<!--					employeesCount: 192,-->
<!--					director: {-->
<!--						fullName: 'Адиль Каримов',-->
<!--						position: 'Генеральный директор',-->
<!--						birthday: '10.10.1985',-->
<!--						phone: '+7(700)5654323',-->
<!--						email: 'test.test@gmail.com',-->
<!--						photo: photo-->
<!--					},-->
<!--				},-->
<!--				departments: [-->
<!--					{-->
<!--						department: 'Коммерческий департамент',-->
<!--						employeesCount: 100,-->
<!--						director: {-->
<!--							fullName: 'Идрак Мирзализаде',-->
<!--							position: 'Административный директор',-->
<!--							birthday: '10.10.1985',-->
<!--							phone: '+7(700)5654323',-->
<!--							email: 'test.test@gmail.com',-->
<!--							photo: photo-->
<!--						},-->
<!--						departmentChildren: [-->
<!--							{-->
<!--								department: 'Административный отдел',-->
<!--								employeesCount: 19,-->
<!--								result: resultText,-->
<!--								director: {-->
<!--									fullName: 'Лилиан Левина',-->
<!--									position: 'Директор по персоналу',-->
<!--									birthday: '10.10.1985',-->
<!--									phone: '+7(700)5654323',-->
<!--									email: 'test.test@gmail.com',-->
<!--									photo: photo-->
<!--								},-->
<!--								departmentChildren: [-->
<!--									{-->
<!--										department: 'Совет директоров',-->
<!--										employeesCount: 9,-->
<!--										result: resultText,-->
<!--										director: {-->
<!--											fullName: 'Никита Косов',-->
<!--											position: 'Председатель совета директоров',-->
<!--											birthday: '10.10.1985',-->
<!--											phone: '+7(700)5654323',-->
<!--											email: 'test.test@gmail.com',-->
<!--											photo: photo-->
<!--										},-->
<!--									},-->
<!--									{-->
<!--										department: 'Служба стратегического управления',-->
<!--										employeesCount: 17,-->
<!--										result: resultText,-->
<!--										director: {-->
<!--											fullName: 'Григорий Квадратов',-->
<!--											position: 'Начальник отдела',-->
<!--											birthday: '10.10.1985',-->
<!--											phone: '+7(700)5654323',-->
<!--											email: 'test.test@gmail.com',-->
<!--											photo: photo-->
<!--										},-->
<!--									},-->
<!--									{-->
<!--										department: 'Совет руководителей',-->
<!--										employeesCount: 17,-->
<!--										result: resultText,-->
<!--										director: {-->
<!--											fullName: 'Светлана Шу',-->
<!--											position: 'Начальник отдела',-->
<!--											birthday: '10.10.1985',-->
<!--											phone: '+7(700)5654323',-->
<!--											email: 'test.test@gmail.com',-->
<!--											photo: photo-->
<!--										},-->
<!--									}-->
<!--								]-->
<!--							}-->
<!--						]-->
<!--					}-->
<!--				]-->
<!--			}-->
<!--		}-->
<!--	}-->
<!--</script>-->
