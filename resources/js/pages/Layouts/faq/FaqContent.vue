<template>
	<div class="faq-content">
		<p class="faq-content-title">
			{{ itemContent.title }}
		</p>
		<div
			class="faq-content-body"
			v-html="filteredContent.html"
		/>
	</div>
</template>


<script>
export default {
	name: 'FaqContent',
	props: {
		itemContent: {
			type: Object,
			default: () => {
			}
		}
	},
	data() {
		return {
			content: [
				{
					id: 1001,
					html: '<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p align="center"><span style="font-family: helvetica, arial, sans-serif;"><strong><span style="font-size: x-large;">БАЗА ЗНАНИЙ КОМПАНИИ </span></strong></span></p>\n' +
								'<p align="center"><span style="font-family: helvetica, arial, sans-serif;"><strong><span style="font-size: x-large;"><span style="color: #236fa1;">Business Partner</span></span></strong></span></p>\n' +
								'<p align="center">&nbsp;</p>\n' +
								'<p align="center"><em><span style="font-family: helvetica, arial, sans-serif;"><a href="https://bpartners.kz/" target="_blank"><img src="https://bpartners.kz/bpartners/1572104616.jpg" alt="картинка" width="499" height="300"></a></span></em></p>\n' +
								'<p align="center">&nbsp;</p>\n' +
								'<p align="center">&nbsp;</p>\n' +
								'<p align="center"><span style="font-size: medium; font-family: helvetica, arial, sans-serif;">Этот документ содержит правила, регламенты, описание, процессы и прочую информацию</span></p>\n' +
								'<p align="center"><span style="font-size: medium; font-family: helvetica, arial, sans-serif;">необходимую для правильного выполнения работы сотрудником</span></p>\n' +
								'<p align="center">&nbsp;</p>\n' +
								'<p align="center">&nbsp;</p>\n' +
								'<p align="center">&nbsp;</p>\n' +
								'<p align="center">&nbsp;</p>\n' +
								'<h1 class="dtH1" align="center"><strong><span style="color: #000080; font-family: helvetica, arial, sans-serif; font-size: x-large;">Цели и задачи данной базы знаний</span></strong></h1>\n' +
								'<p>&nbsp;</p>\n' +
								'<blockquote dir="ltr" style="margin-right: 0px;">\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;"><span style="font-size: 12pt; font-family: helvetica, arial, sans-serif;"><strong>Цель данной базы знаний </strong>- дать новым сотрудникам максимум информации о компании, о наших продуктах и правилах.</span></p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;">&nbsp;</p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;"><span style="font-size: 12pt; font-family: helvetica, arial, sans-serif;">Данная книга составлена и подготовлена для облегчения процесса обучения новых сотрудников компании. </span></p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;"><span style="font-size: 12pt; font-family: helvetica, arial, sans-serif;">Книга составлена таким образом, что Вы можете найти здесь ответы на все Ваши вопросы, касающиеся Вашей работы.</span></p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;">&nbsp;</p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;">&nbsp;</p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><strong>Задачи</strong>, которые мы ставили при написании данной книги самые простые и жизненные:</span></p>\n' +
								'<ul>\n' +
								'<li style="list-style-type: none;">\n' +
								'<ul>\n' +
								'<li>\n' +
								'<div class="MsoNormal" style="margin: 0cm 0cm 0pt; mso-layout-grid-align: none; mso-pagination: none;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;">Облегчить начало работы новых сотрудников компании;</span></div>\n' +
								'</li>\n' +
								'<li>\n' +
								'<div class="MsoNormal" style="margin: 0cm 0cm 0pt; mso-layout-grid-align: none; mso-pagination: none;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;">Передача опыта и знаний успешных работников начинающим менеджерам;</span></div>\n' +
								'</li>\n' +
								'<li>\n' +
								'<div class="MsoNormal" style="margin: 0cm 0cm 0pt; mso-layout-grid-align: none; mso-pagination: none;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;">Вовлечение сотрудников компании в рабочий процесс, и как следствие, совместное совершенствование этой книги.</span></div>\n' +
								'</li>\n' +
								'</ul>\n' +
								'</li>\n' +
								'</ul>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;">Именно поэтому, если Вы подошли к руководителю отдела с вопросом, ответ на который̆ есть в книге, Вы услышите: «посмотрите в Корпоративной Книге пожалуйста».</span></p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;">&nbsp;</p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;">При этом, мы постоянно стараемся дополнять и совершенствовать данную книгу.</span></p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;" align="left"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;">Именно поэтому, если у Вас все-таки возник вопрос, ответ на который̆ не раскрыт в данной книге, и важен при Вашей работе-то обязательно скажите об этом руководителю Вашего отдела или группы.</span></p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;">&nbsp;</p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;">&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'</blockquote>'
				},
				{
					id: 1002,
					html: '<div class="bp-text"><p>&nbsp;</p>\n' +
								'<p style="text-align: center; padding-left: 80px;" align="center"><span style="font-size: 18pt; font-family: helvetica, arial, sans-serif;"><strong><span style="color: #000080;">Принципы которыми должны руководствоваться сотрудники нашей компании.</span></strong></span></p>\n' +
								'<p style="text-align: center; padding-left: 80px;">В компании есть нравственные и деловые принципы, которые разделяют наши сотрудники. Иначе говоря, это принципы/правила, привлекающие людей с аналогичными взглядами на жизнь, карьеру, достижения и т.д. Бизнес успешен только если участники проекта понимают, зачем они здесь, и в каком направлении должна двигаться компания. Персонал должен быть единым организмом, работающим ради достижения результата, и в принципах мы должны быть одинаковыми от от младших сотрудников до ТОП-менеджеров.</p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'<table style="border-collapse: collapse; width: 100%; height: 1122.4px; border-style: hidden;" border="0">\n' +
								'<tbody>\n' +
								'<tr style="height: 121.6px;">\n' +
								'<td style="width: 11.4703%; height: 121.6px; text-align: center;"><span style="color: #236fa1; font-size: 15pt;"><strong><span style="font-family: helvetica, arial, sans-serif;">1</span></strong></span></td>\n' +
								'<td style="width: 88.5297%; height: 121.6px;">\n' +
								'<p style="padding-left: 40px;">&nbsp;</p>\n' +
								'<p style="padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><strong><span style="color: #236fa1;">Обязательность&nbsp;– мы имеем привычку без проволочек и без напоминаний делать</span></strong> то, о чем была договоренность с другими, или то, что наметили сами себе.&nbsp;Потому что мы обязательные люди – мы умеем рассчитывать свое время, отвечать за свои поступки и сдерживать данное им слово.</span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 140px;">\n' +
								'<td style="width: 11.4703%; height: 140px; text-align: center;"><strong style="color: #236fa1; font-family: sans-serif; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">2</span></strong></td>\n' +
								'<td style="width: 88.5297%; height: 140px;">\n' +
								'<p style="padding-left: 40px;">&nbsp;</p>\n' +
								'<p style="padding-left: 40px;"><span style="font-size: 12pt; font-family: helvetica, arial, sans-serif;"><span style="color: #236fa1;"><strong>Мы заботимся о друг друге и о ценностях компании. </strong></span>Забота — комплекс действий, основанных на позитивных чувствах (часто — любовь, уважение) к объекту (человеку, животному, или предмету), и нацеленных на бескорыстное удовлетворение потребностей того объекта. Заботу может проявлять как животное к своему потомству, так и человек к какому-нибудь существу или предмету.</span></p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 176.8px;">\n' +
								'<td style="width: 11.4703%; height: 176.8px; text-align: center;"><strong style="color: #236fa1; font-size: 20px; text-align: center;"><span style="font-family: helvetica, arial, sans-serif;">3</span></strong></td>\n' +
								'<td style="width: 88.5297%; padding-left: 40px; height: 176.8px;">\n' +
								'<p>&nbsp;</p>\n' +
								'<p><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>Саморазвитие</strong> — </span>это обязательное условие нашей компании которое мы все соблюдаем, чтобы вырасти в должности, своём обществе и преуспеть в своём деле. Саморазвитие стали ключевыми словами современного человека. Сейчас существует огромное количество разных источников, откуда можно почерпнуть новые знания: интернет, книжные магазины, библиотеки, всевозможные курсы, тренинги, семинары. И для этого в нашей компании есть специальный функционал для прохождения интерактивных курсов.&nbsp;&nbsp;</span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 140px;">\n' +
								'<td style="width: 11.4703%; height: 140px; text-align: center;"><strong style="text-align: center; color: #236fa1; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">4</span></strong></td>\n' +
								'<td style="width: 88.5297%; padding-left: 40px; height: 140px;">\n' +
								'<p>&nbsp;</p>\n' +
								'<p><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><strong><span style="color: #236fa1;">Мы все должны быть вовлечены в процессы компании, в работу,</span> </strong>это такое состояние, когда мы испытываем страсть к своей работе, приверженность бизнес интересам компании, готовы связать свое будущее с компанией, рекомендовать ее своему окружению в качестве работодателя и отожествлять свои цели и ценности с интересами компании.&nbsp;</span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 121.6px;">\n' +
								'<td style="width: 11.4703%; height: 121.6px; text-align: center;"><strong style="text-align: center; color: #236fa1; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">5</span></strong></td>\n' +
								'<td style="width: 88.5297%; padding-left: 40px; height: 121.6px;"><br>\n' +
								'<p><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>Мы договариваемся устно</strong>,</span> так как договориться возможно только в разговоре (а не в длинных переписках по почте или в мессенджерах). Нужно что-то решить — созвонитесь! А уже результаты договоренностей зафиксируйте письменно.</span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 153.6px;">\n' +
								'<td style="width: 11.4703%; height: 153.6px; text-align: center;"><strong style="text-align: center; color: #236fa1; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">6</span></strong></td>\n' +
								'<td style="width: 88.5297%; height: 153.6px;">\n' +
								'<p style="padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>Любые обсуждения внутри компании мы не должны выносить за ее пределы.</strong> </span>Обсуждение проектов вне компании – нарушение наших договорных отношений с заказчиками, а это нехорошо.</span></p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 140px;">\n' +
								'<td style="width: 11.4703%; height: 140px; text-align: center;"><strong style="text-align: center; color: #236fa1; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">7</span></strong></td>\n' +
								'<td style="width: 88.5297%; height: 140px;">\n' +
								'<p style="padding-left: 40px;">&nbsp;</p>\n' +
								'<p style="padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>Мы инновационны – </strong></span>мы должны регулярно предлагать Руководителю новые идей, нестандартные решения, новый&nbsp; улучшенный подход к работе, формировать новое лучшее видение ситуации, выходя за рамки стереотипов, и обеспечивая качественный рост эффективности процессов и предоставляемых Компанией услуг.&nbsp;</span></p>\n' +
								'<p style="padding-left: 40px;">&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 55.2px;">\n' +
								'<td style="width: 11.4703%; height: 55.2px; text-align: center;"><strong style="text-align: center; color: #236fa1; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">8</span></strong></td>\n' +
								'<td style="width: 88.5297%; height: 55.2px;">\n' +
								'<p style="padding-left: 40px;">&nbsp;</p>\n' +
								'<p style="padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>Понимайте, что делаете, и объясняете это своим окружающим. </strong></span>Поясняйте свои решения, чтобы ваши действия были понятны, почему и зачем. Непонятно — спросите. В первую очередь найдите ответ в Базе Знаний. Не нашли спросите у своего руководителя. Если он не смог объяснить, то спроси у того, кто выше.</span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 18.4px;">\n' +
								'<td style="width: 11.4703%; height: 18.4px; text-align: center;"><strong style="text-align: center; color: #236fa1; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">9</span></strong></td>\n' +
								'<td style="width: 88.5297%; height: 18.4px;">\n' +
								'<p style="padding-left: 40px;">&nbsp;</p>\n' +
								'<p style="padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>Мы хвалим всех прилюдно, а вот ругаем наедине.</strong></span> Хвалите коллег чаще, а вот критикуйте мешьше. Не меньше 70% пряников! И не тех, которыми можно ударить!</span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 18.4px;">\n' +
								'<td style="width: 11.4703%; height: 18.4px; text-align: center;"><strong style="text-align: center; color: #236fa1; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">10</span></strong></td>\n' +
								'<td style="width: 88.5297%; height: 18.4px;">\n' +
								'<p style="padding-left: 40px;">&nbsp;</p>\n' +
								'<p style="padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>Мы должны быть командой,</strong></span> потому что наши достижения это результат работы команды. Мы поддерживаем друг друга и помогаем новичкам - только так мы сможем преуспеть.</span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 18.4px;">\n' +
								'<td style="width: 11.4703%; height: 18.4px; text-align: center;"><strong style="text-align: center; color: #236fa1; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">11</span></strong></td>\n' +
								'<td style="width: 88.5297%; height: 18.4px;">\n' +
								'<p style="padding-left: 40px;">&nbsp;</p>\n' +
								'<p style="padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>Наша компания предоставляет равные возможности для всех:</strong></span> нам не важен пол, возраст и национальность. У нас нет родственного блата. Сделать карьеру благодаря труду и результативности у нас может каждый.</span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 18.4px;">\n' +
								'<td style="width: 11.4703%; height: 18.4px; text-align: center;"><strong style="text-align: center; color: #236fa1; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">12</span></strong></td>\n' +
								'<td style="width: 88.5297%; height: 18.4px;">\n' +
								'<p style="padding-left: 40px;">&nbsp;</p>\n' +
								'<p style="padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>Мы уверены, что нет ничего невозможного,</strong></span> и только мы сами определяем границы наших возможностей. Мы добиваемся результата с помощью упорного труда и настойчивости.</span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'</tbody>\n' +
								'</table>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p></div>'
				},
				{
					id: 1003,
					html: '<div class="bp-text"><p class="MsoNormal" style="margin-bottom: 0.0001pt; text-align: center; line-height: normal; padding-left: 40px;" align="center"><span style="color: #0a5bd4; font-family: helvetica, arial, sans-serif; font-size: 18pt;"><strong>Мотивационные программы</strong></span></p>\n' +
								'<p class="MsoNormal" style="margin-bottom: 0.0001pt; text-align: center; line-height: normal; padding-left: 40px;" align="center"><span style="color: #0a5bd4; font-family: helvetica, arial, sans-serif; font-size: 18pt;"><strong>ДЛЯ ВСЕХ операторов и сотрудников&nbsp;</strong></span></p>\n' +
								'<p class="MsoNormal" style="margin-bottom: 0.0001pt; text-align: center; line-height: normal; padding-left: 40px;" align="center"><span style="font-family: helvetica, arial, sans-serif; color: #0a5bd4; font-size: 18pt;"><strong>колл-центра «</strong><strong><span lang="EN-US">Business</span></strong>&nbsp;<strong><span lang="EN-US">Partners</span></strong><strong>»</strong></span></p>\n' +
								'<p class="MsoNormal" style="margin-bottom: 0.0001pt; text-align: justify; line-height: normal; padding-left: 40px;"><span style="font-size: 20pt; font-family: helvetica, arial, sans-serif;">&nbsp;</span></p>\n' +
								'<p class="MsoNormal" style="text-align: justify; line-height: normal; margin: 0cm 7.05pt 0.0001pt 14.2pt; padding-left: 40px;"><span style="font-size: 12pt; font-family: helvetica, arial, sans-serif;"><span style="color: #236fa1;"><strong>1.</strong>&nbsp;<strong>«Приведи друга»</strong>&nbsp;</span>- хочешь получить премию 10 000 тенге? Нужно всего лишь пригласить знакомого, друга к нам на работу, который должен отработать 1 месяц.</span></p>\n' +
								'<p class="MsoNormal" style="text-align: justify; line-height: normal; margin: 0cm 7.05pt 0.0001pt 14.2pt; padding-left: 40px;"><span style="font-size: 12pt; font-family: helvetica, arial, sans-serif;">&nbsp;</span></p>\n' +
								'<p class="MsoNormal" style="text-align: justify; line-height: normal; margin: 0cm 7.05pt 0.0001pt 14.2pt; padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>2. «Обучение»</strong></span>&nbsp;-&nbsp;</span><span style="font-family: -apple-system, BlinkMacSystemFont, Segoe UI, PingFang SC, Hiragino Sans GB, Microsoft YaHei, Helvetica Neue, Helvetica, Arial, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol;">до 20 000 тенге. Нужно проходить назначенное Вам&nbsp;</span><span style="font-family: -apple-system, BlinkMacSystemFont, Segoe UI, PingFang SC, Hiragino Sans GB, Microsoft YaHei, Helvetica Neue, Helvetica, Arial, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol;">обучение, это может быть просмотр видео, чтение книг или изучение&nbsp;</span><span style="font-family: -apple-system, BlinkMacSystemFont, Segoe UI, PingFang SC, Hiragino Sans GB, Microsoft YaHei, Helvetica Neue, Helvetica, Arial, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol;">базы знаний. За это Вы зарабатываете дополнительные деньги.</span></p>\n' +
								'<p class="MsoNormal" style="line-height: normal; margin: 0cm 7.05pt 0.0001pt 14.2pt; padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;">&nbsp;</span></p>\n' +
								'<p class="MsoNormal" style="text-align: justify; line-height: normal; margin: 0cm 7.05pt 0.0001pt 14.2pt; padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><strong><span style="color: #236fa1;">3. «Забери сегодня свои денежный бонус»</span>&nbsp;</strong>-&nbsp;</span><span style="font-family: helvetica, arial, sans-serif;">достигший первым цель своего отдела&nbsp;</span><span style="font-family: helvetica, arial, sans-serif;">за день, забирает 1000 или больше бонусами к оплате, каждый день!</span></p>\n' +
								'<p class="MsoNormal" style="text-align: justify; line-height: normal; margin: 0cm 7.05pt 0.0001pt 14.2pt; padding-left: 40px;">&nbsp;</p>\n' +
								'<p class="MsoNormal" style="text-align: justify; line-height: normal; margin: 0cm 7.05pt 0.0001pt 14.2pt; padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>4.</strong>&nbsp;<strong>«Лучший сотрудник»</strong>&nbsp;</span>-&nbsp;</span><span style="font-family: helvetica, arial, sans-serif;">нужно победить какой либо ежемесячной&nbsp;</span><span style="font-family: helvetica, arial, sans-serif;">номинации и получите брендированный приз, признание коллег,&nbsp;</span><span style="font-family: helvetica, arial, sans-serif;">сертификаты и награды. Возможность оказаться на доске почета, а&nbsp;</span><span style="font-family: helvetica, arial, sans-serif;">также поехать отдыхать куда ни будь&nbsp;в интересные страны.</span></p>\n' +
								'<p class="MsoNormal" style="text-align: justify; line-height: normal; margin: 0cm 7.05pt 0.0001pt 49.65pt; padding-left: 40px;"><span style="font-size: 12pt; font-family: helvetica, arial, sans-serif;">&nbsp;</span></p>\n' +
								'<p class="MsoNormal" style="margin-bottom: 0.0001pt; line-height: normal; padding-left: 40px;"><span style="font-size: 12pt; font-family: helvetica, arial, sans-serif;">&nbsp;</span></p>\n' +
								'<p class="MsoNormal" style="margin-bottom: 0.0001pt; line-height: normal; padding-left: 40px; text-align: center;"><span style="font-size: 16pt; font-family: helvetica, arial, sans-serif;">&nbsp;</span><span style="color: #236fa1;"><strong><span style="font-family: helvetica, arial, sans-serif; font-size: 16pt; text-align: center;">Всем Удачи!!!</span></strong></span></p>\n' +
								'<p class="MsoNormal" style="margin-bottom: 0.0001pt; line-height: normal; padding-left: 40px; text-align: center;">&nbsp;</p>\n' +
								'<p class="MsoNormal" style="margin-bottom: 0.0001pt; line-height: normal; padding-left: 40px; text-align: center;">&nbsp;</p></div>'
				},
				{
					id: 1004,
					html: '<div class="bp-text"><p style="text-align: center;"><span style="font-size: 16pt; color: #236fa1;"><strong>Как создать и наполнить базу знаний</strong></span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p><strong>В процессе запуска нового проекта Вам нужно будет создать базу знаний</strong>&nbsp;для операторов и руководителей проекта, которой они будут руководствоваться в процессе работы после Вашего ухода на запуск нового проекта. В процессе ведения проекта, руководитель группы и подразделения будут эти данные дополнять и модернизировать.&nbsp;</p>\n' +
								'<p>Все что Вы будете получать от Заказчика в процессе запуска нового проекта, и все что Вы считаете будет нужным для работы сотрудников данного проекта, нужно заполнять в базу знаний.</p>\n' +
								'<p><strong>Обязательные данные которые должны быть в Базе знаний, это:</strong></p>\n' +
								'<ol>\n' +
								'<li>данные о компании заказчике</li>\n' +
								'<li>данные о продукте с которым именно наши специалисты работают</li>\n' +
								'<li>описание программы в которой придется работать оператору и руководительскому составу</li>\n' +
								'<li>скрипты работы для оператора (составляются именно тут&nbsp;<a href="https://skr.sh/sEXKlUHCYL7" target="_blank">https://skr.sh/sEXKlUHCYL7)</a></li>\n' +
								'<li>Алгоритмы, регламенты, нормы и прочие правила которыми нужно руководствоваться в работе.</li>\n' +
								'</ol>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p style="text-align: center;"><span style="font-size: 14pt;"><strong style="color: #236fa1; text-align: center;">Алгоритм создания Базы знаний</strong></span></p>\n' +
								'<p><strong>1. База знаний создается</strong>&nbsp;по запросу у Вашего руководителя, либо Вами самостоятельно если у Вас есть доступ к созданию, об этом Вы можете узнать вот тут&nbsp;<a href="https://skr.sh/sEXImOMKwa1" target="_blank">https://skr.sh/sEXImOMKwa1</a></p>\n' +
								'<p><strong>2. База знаний называется</strong>&nbsp;именем:</p>\n' +
								'<ul>\n' +
								'<li>для оператора "название проекта (оператору)"</li>\n' +
								'<li>для руководителей "название проекта (руководителю)"</li>\n' +
								'</ul>\n' +
								'<p><strong>3. Далее в базе создается понятная структура</strong>&nbsp;страниц, вот пример&nbsp;<a href="https://skr.sh/sEXwMq3TQAE" target="_blank">https://skr.sh/sEXwMq3TQAE</a>&nbsp;</p>\n' +
								'<p><strong>4. Наполните страницы:</strong></p>\n' +
								'<ul>\n' +
								'<li>утвержденное форматирование заголовка 16, жирный шрифт sans-serif, темносиний цвет&nbsp;<a href="https://skr.sh/sEXEF3rqNTL" target="_blank">https://skr.sh/sEXEF3rqNTL</a>&nbsp;</li>\n' +
								'<li>утвержденное форматирование подзаголовка, 14, жирный шрифт sans-serif, темносиний цвет</li>\n' +
								'<li>утвержденное форматирование текста, 12 размер, обычный шрифт sans-serif, черный цвет</li>\n' +
								'</ul>\n' +
								'<p><strong>5. Делайте более привлекательной Базу знаний</strong>&nbsp;наполняя ее картинками, анимациями, видео и аудио информацией</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p><em>*больше информации в видео в Базе знаний руководителя отдела:&nbsp;<a href="https://skr.sh/sEXxN8lw6qx" target="_blank">https://skr.sh/sEXxN8lw6qx</a>&nbsp;</em></p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p></div>'
				},
				{
					id: 1005,
					html: '<div class="bp-text"><p><span style="color: #236fa1; font-size: 14pt;"><strong>1. Читая книгу </strong></span></p>\n' +
								'<p style="padding-left: 40px;"><img src="/bpartners/1659025765.jpg" alt="" width="574" height="228"></p>\n' +
								'<p><span style="font-size: 14pt; color: #236fa1;"><strong>2. просматривая видео&nbsp;</strong></span></p>\n' +
								'<p style="padding-left: 40px;"><img src="/bpartners/1659025647.jpg" alt="" width="571" height="251"></p>\n' +
								'<p><span style="font-size: 14pt; color: #236fa1;"><strong>3. а также изучая Базу знаний</strong></span></p>\n' +
								'<p style="padding-left: 40px;"><img src="/bpartners/1659025714.jpg" alt="" width="568" height="210"></p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>каждый сотрудник получает бонусы на свой счет в профиле, а для того чтобы ему начислялись правильно бонусы, Вам нужно добавить их при создании страниц в Базе знаний, или при добавлении книг для чтения, а также при добавлении видеороликов в раздел видео обучения.&nbsp;</p>\n' +
								'<p><span style="font-size: 15pt; color: #236fa1;"><strong>Добавляются бонусы по формуле:</strong></span></p>\n' +
								'<ul>\n' +
								'<li><strong>за каждую страницу в БАЗЕ ЗНАНИЙ</strong> нужно выставить 10 бонусов, то есть Вы создали одну страницу, далее наполнили ее контентом, а после Вам нужно выставить к ней минимум 2 тестовых вопроса <a href="https://skr.sh/sF9wnCypL8Y" target="_blank">https://skr.sh/sF9wnCypL8Y</a>, на которые суммарно у Вас есть 10 бонусов. Значит если у Вас 2 вопроса, Вы на каждый тестовый вопрос даете 5 бонусов&nbsp; <a href="https://skr.sh/sF9s07LwzEm" target="_blank">https://skr.sh/sF9s07LwzEm</a> , если у Вас 3 вопроса, то по 3 бонуса за каждый вопрос и так далее.&nbsp;</li>\n' +
								'</ul>\n' +
								'<p>&nbsp;</p>\n' +
								'<ul>\n' +
								'<li><strong>за каждую страницу в разделе ЧТЕНИЯ КНИГ</strong> нужно выставить тоже 10 бонусов. Когда пользователь читает книгу, то на тестовые вопросы он отвечает ни на каждой странице, а на каждой 20й или 40й странице (в разных книгах разные интервалы) и тестовых вопросов может быть от 5 до 10, и тут формула добавления бонусов работает по такой схеме: так как одна страница это 10 бонусов, а в книгах может быть например с 1 - 40 страницу &nbsp; = 5 вопросов значит 40 страниц *10 бонусов и поделить на 5 = 80 баллов на каждый вопрос.</li>\n' +
								'</ul>\n' +
								'<p>&nbsp;</p>\n' +
								'<ul>\n' +
								'<li><strong>за каждую минуту в разделе ВИДЕО ОБУЧЕНИЯ</strong> выставляется 5 бонусов, например 3 минуты = 15 бонусов,&nbsp;10 минут = 50 бонусов и тд.&nbsp;Если видео например 20 минут и там есть 4 вопроса, то за каждый вопрос только по 25 бонусов выставить можно. Формула: 20 минут * 5 бонусов = 100 бонусов на все вопросы / на кол-во вопросов = кол-во бонусов на каждый из вопросов.&nbsp;</li>\n' +
								'</ul>\n' +
								'<p>&nbsp;</p>\n' +
								'<p><strong style="color: #236fa1; font-size: 20px;">Также Вам нужно выставить проходной балл.</strong></p>\n' +
								'<p>Это значит, на сколько из тестовых вопросов должен ответить сотрудник, чтобы система его пропустила к следующим страницам в книге или видео, т к если он не наберет проходной балл, то система ему сообщит чтобы еще раз все перечитал и пересмотрел и после ответил, а страницы Базы знаний открыты все сразу и там отвечать можно на любые страницы.</p>\n' +
								'<p>Поэтому выставьте проходной балл, <a href="https://skr.sh/sF9QXIg229A" target="_blank">https://skr.sh/sF9QXIg229A</a> на данном скрине показано, что сотруднику который будет отвечать на тестовые вопросы, нужно ответить верно всего на два вопроса из трех, и тогда система его пропустит к следующим страницам книги, тоже самое и про видео. В базе знаний так же выставляется проходной балл, и при этом все страницы сразу открыты для изучения. Проходной балл выставляйте на 1 меньше, чем в общем вопросов, например: 4 вопроса, значит проходной балл будет 3, или если 7 вопросов, то проходной балл будет 6, а если 3 вопроса, то проходной будет 2 и так далее. Исключение только если 1 или 2 вопроса, то будет минимум будет 2 вопроса проходных к 2м вопросам и 1 к одному.</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p><strong style="color: #236fa1; font-size: 20px;">Сколько бонусов сотрудник получит при правильных ответах ?</strong></p>\n' +
								'<p>Пример который мы выше рассмотрели, когда сотруднику нужно ответить на 2 вопроса из 3х, для того чтобы дальше читать. Допустим он ответил на 2 вопроса, и тут за каждый правильный ответ на тестовый вопрос дается по 66 бонусов <a href="https://skr.sh/sF9LdRmjvrT" target="_blank">https://skr.sh/sF9LdRmjvrT</a> , тогда сотрудник получит на свой счет 132 бонуса, а если сотрудник правильно ответит на все 3 вопроса, то он получит на свой счет 3 * 66 = 198 бонусов.</p>\n' +
								'<p>По такой же логике работает начисление бонусов за видео обучение и ответы на вопросы в Базе знаний.</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p></div>'
				},
				{
					id: 1006,
					html: '<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p align="center"><span style="font-family: helvetica, arial, sans-serif;"><strong><span style="font-size: x-large;">БАЗА ЗНАНИЙ КОМПАНИИ </span></strong></span></p>\n' +
								'<p align="center"><span style="font-family: helvetica, arial, sans-serif;"><strong><span style="font-size: x-large;"><span style="color: #236fa1;">Business Partner</span></span></strong></span></p>\n' +
								'<p align="center">&nbsp;</p>\n' +
								'<p align="center"><em><span style="font-family: helvetica, arial, sans-serif;"><a href="https://bpartners.kz/" target="_blank"><img src="https://bpartners.kz/bpartners/1572104616.jpg" alt="картинка" width="499" height="300"></a></span></em></p>\n' +
								'<p align="center">&nbsp;</p>\n' +
								'<p align="center">&nbsp;</p>\n' +
								'<p align="center"><span style="font-size: medium; font-family: helvetica, arial, sans-serif;">Этот документ содержит правила, регламенты, описание, процессы и прочую информацию</span></p>\n' +
								'<p align="center"><span style="font-size: medium; font-family: helvetica, arial, sans-serif;">необходимую для правильного выполнения работы сотрудником</span></p>\n' +
								'<p align="center">&nbsp;</p>\n' +
								'<p align="center">&nbsp;</p>\n' +
								'<p align="center">&nbsp;</p>\n' +
								'<p align="center">&nbsp;</p>\n' +
								'<h1 class="dtH1" align="center"><strong><span style="color: #000080; font-family: helvetica, arial, sans-serif; font-size: x-large;">Цели и задачи данной базы знаний</span></strong></h1>\n' +
								'<p>&nbsp;</p>\n' +
								'<blockquote dir="ltr" style="margin-right: 0px;">\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;"><span style="font-size: 12pt; font-family: helvetica, arial, sans-serif;"><strong>Цель данной базы знаний </strong>- дать новым сотрудникам максимум информации о компании, о наших продуктах и правилах.</span></p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;">&nbsp;</p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;"><span style="font-size: 12pt; font-family: helvetica, arial, sans-serif;">Данная книга составлена и подготовлена для облегчения процесса обучения новых сотрудников компании. </span></p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;"><span style="font-size: 12pt; font-family: helvetica, arial, sans-serif;">Книга составлена таким образом, что Вы можете найти здесь ответы на все Ваши вопросы, касающиеся Вашей работы.</span></p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;">&nbsp;</p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;">&nbsp;</p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><strong>Задачи</strong>, которые мы ставили при написании данной книги самые простые и жизненные:</span></p>\n' +
								'<ul>\n' +
								'<li style="list-style-type: none;">\n' +
								'<ul>\n' +
								'<li>\n' +
								'<div class="MsoNormal" style="margin: 0cm 0cm 0pt; mso-layout-grid-align: none; mso-pagination: none;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;">Облегчить начало работы новых сотрудников компании;</span></div>\n' +
								'</li>\n' +
								'<li>\n' +
								'<div class="MsoNormal" style="margin: 0cm 0cm 0pt; mso-layout-grid-align: none; mso-pagination: none;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;">Передача опыта и знаний успешных работников начинающим менеджерам;</span></div>\n' +
								'</li>\n' +
								'<li>\n' +
								'<div class="MsoNormal" style="margin: 0cm 0cm 0pt; mso-layout-grid-align: none; mso-pagination: none;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;">Вовлечение сотрудников компании в рабочий процесс, и как следствие, совместное совершенствование этой книги.</span></div>\n' +
								'</li>\n' +
								'</ul>\n' +
								'</li>\n' +
								'</ul>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;">Именно поэтому, если Вы подошли к руководителю отдела с вопросом, ответ на который̆ есть в книге, Вы услышите: «посмотрите в Корпоративной Книге пожалуйста».</span></p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;">&nbsp;</p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;">При этом, мы постоянно стараемся дополнять и совершенствовать данную книгу.</span></p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;" align="left"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;">Именно поэтому, если у Вас все-таки возник вопрос, ответ на который̆ не раскрыт в данной книге, и важен при Вашей работе-то обязательно скажите об этом руководителю Вашего отдела или группы.</span></p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;">&nbsp;</p>\n' +
								'<p class="MsoNormal" style="margin: 0cm 0cm 0pt; padding-left: 40px;">&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'</blockquote>'
				},
				{
					id: 1007,
					html: '<div class="bp-text"><p>&nbsp;</p>\n' +
								'<p style="text-align: center; padding-left: 80px;" align="center"><span style="font-size: 18pt; font-family: helvetica, arial, sans-serif;"><strong><span style="color: #000080;">Принципы которыми должны руководствоваться сотрудники нашей компании.</span></strong></span></p>\n' +
								'<p style="text-align: center; padding-left: 80px;">В компании есть нравственные и деловые принципы, которые разделяют наши сотрудники. Иначе говоря, это принципы/правила, привлекающие людей с аналогичными взглядами на жизнь, карьеру, достижения и т.д. Бизнес успешен только если участники проекта понимают, зачем они здесь, и в каком направлении должна двигаться компания. Персонал должен быть единым организмом, работающим ради достижения результата, и в принципах мы должны быть одинаковыми от от младших сотрудников до ТОП-менеджеров.</p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'<table style="border-collapse: collapse; width: 100%; height: 1122.4px; border-style: hidden;" border="0">\n' +
								'<tbody>\n' +
								'<tr style="height: 121.6px;">\n' +
								'<td style="width: 11.4703%; height: 121.6px; text-align: center;"><span style="color: #236fa1; font-size: 15pt;"><strong><span style="font-family: helvetica, arial, sans-serif;">1</span></strong></span></td>\n' +
								'<td style="width: 88.5297%; height: 121.6px;">\n' +
								'<p style="padding-left: 40px;">&nbsp;</p>\n' +
								'<p style="padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><strong><span style="color: #236fa1;">Обязательность&nbsp;– мы имеем привычку без проволочек и без напоминаний делать</span></strong> то, о чем была договоренность с другими, или то, что наметили сами себе.&nbsp;Потому что мы обязательные люди – мы умеем рассчитывать свое время, отвечать за свои поступки и сдерживать данное им слово.</span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 140px;">\n' +
								'<td style="width: 11.4703%; height: 140px; text-align: center;"><strong style="color: #236fa1; font-family: sans-serif; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">2</span></strong></td>\n' +
								'<td style="width: 88.5297%; height: 140px;">\n' +
								'<p style="padding-left: 40px;">&nbsp;</p>\n' +
								'<p style="padding-left: 40px;"><span style="font-size: 12pt; font-family: helvetica, arial, sans-serif;"><span style="color: #236fa1;"><strong>Мы заботимся о друг друге и о ценностях компании. </strong></span>Забота — комплекс действий, основанных на позитивных чувствах (часто — любовь, уважение) к объекту (человеку, животному, или предмету), и нацеленных на бескорыстное удовлетворение потребностей того объекта. Заботу может проявлять как животное к своему потомству, так и человек к какому-нибудь существу или предмету.</span></p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 176.8px;">\n' +
								'<td style="width: 11.4703%; height: 176.8px; text-align: center;"><strong style="color: #236fa1; font-size: 20px; text-align: center;"><span style="font-family: helvetica, arial, sans-serif;">3</span></strong></td>\n' +
								'<td style="width: 88.5297%; padding-left: 40px; height: 176.8px;">\n' +
								'<p>&nbsp;</p>\n' +
								'<p><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>Саморазвитие</strong> — </span>это обязательное условие нашей компании которое мы все соблюдаем, чтобы вырасти в должности, своём обществе и преуспеть в своём деле. Саморазвитие стали ключевыми словами современного человека. Сейчас существует огромное количество разных источников, откуда можно почерпнуть новые знания: интернет, книжные магазины, библиотеки, всевозможные курсы, тренинги, семинары. И для этого в нашей компании есть специальный функционал для прохождения интерактивных курсов.&nbsp;&nbsp;</span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 140px;">\n' +
								'<td style="width: 11.4703%; height: 140px; text-align: center;"><strong style="text-align: center; color: #236fa1; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">4</span></strong></td>\n' +
								'<td style="width: 88.5297%; padding-left: 40px; height: 140px;">\n' +
								'<p>&nbsp;</p>\n' +
								'<p><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><strong><span style="color: #236fa1;">Мы все должны быть вовлечены в процессы компании, в работу,</span> </strong>это такое состояние, когда мы испытываем страсть к своей работе, приверженность бизнес интересам компании, готовы связать свое будущее с компанией, рекомендовать ее своему окружению в качестве работодателя и отожествлять свои цели и ценности с интересами компании.&nbsp;</span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 121.6px;">\n' +
								'<td style="width: 11.4703%; height: 121.6px; text-align: center;"><strong style="text-align: center; color: #236fa1; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">5</span></strong></td>\n' +
								'<td style="width: 88.5297%; padding-left: 40px; height: 121.6px;"><br>\n' +
								'<p><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>Мы договариваемся устно</strong>,</span> так как договориться возможно только в разговоре (а не в длинных переписках по почте или в мессенджерах). Нужно что-то решить — созвонитесь! А уже результаты договоренностей зафиксируйте письменно.</span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 153.6px;">\n' +
								'<td style="width: 11.4703%; height: 153.6px; text-align: center;"><strong style="text-align: center; color: #236fa1; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">6</span></strong></td>\n' +
								'<td style="width: 88.5297%; height: 153.6px;">\n' +
								'<p style="padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>Любые обсуждения внутри компании мы не должны выносить за ее пределы.</strong> </span>Обсуждение проектов вне компании – нарушение наших договорных отношений с заказчиками, а это нехорошо.</span></p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 140px;">\n' +
								'<td style="width: 11.4703%; height: 140px; text-align: center;"><strong style="text-align: center; color: #236fa1; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">7</span></strong></td>\n' +
								'<td style="width: 88.5297%; height: 140px;">\n' +
								'<p style="padding-left: 40px;">&nbsp;</p>\n' +
								'<p style="padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>Мы инновационны – </strong></span>мы должны регулярно предлагать Руководителю новые идей, нестандартные решения, новый&nbsp; улучшенный подход к работе, формировать новое лучшее видение ситуации, выходя за рамки стереотипов, и обеспечивая качественный рост эффективности процессов и предоставляемых Компанией услуг.&nbsp;</span></p>\n' +
								'<p style="padding-left: 40px;">&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 55.2px;">\n' +
								'<td style="width: 11.4703%; height: 55.2px; text-align: center;"><strong style="text-align: center; color: #236fa1; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">8</span></strong></td>\n' +
								'<td style="width: 88.5297%; height: 55.2px;">\n' +
								'<p style="padding-left: 40px;">&nbsp;</p>\n' +
								'<p style="padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>Понимайте, что делаете, и объясняете это своим окружающим. </strong></span>Поясняйте свои решения, чтобы ваши действия были понятны, почему и зачем. Непонятно — спросите. В первую очередь найдите ответ в Базе Знаний. Не нашли спросите у своего руководителя. Если он не смог объяснить, то спроси у того, кто выше.</span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 18.4px;">\n' +
								'<td style="width: 11.4703%; height: 18.4px; text-align: center;"><strong style="text-align: center; color: #236fa1; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">9</span></strong></td>\n' +
								'<td style="width: 88.5297%; height: 18.4px;">\n' +
								'<p style="padding-left: 40px;">&nbsp;</p>\n' +
								'<p style="padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>Мы хвалим всех прилюдно, а вот ругаем наедине.</strong></span> Хвалите коллег чаще, а вот критикуйте мешьше. Не меньше 70% пряников! И не тех, которыми можно ударить!</span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 18.4px;">\n' +
								'<td style="width: 11.4703%; height: 18.4px; text-align: center;"><strong style="text-align: center; color: #236fa1; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">10</span></strong></td>\n' +
								'<td style="width: 88.5297%; height: 18.4px;">\n' +
								'<p style="padding-left: 40px;">&nbsp;</p>\n' +
								'<p style="padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>Мы должны быть командой,</strong></span> потому что наши достижения это результат работы команды. Мы поддерживаем друг друга и помогаем новичкам - только так мы сможем преуспеть.</span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 18.4px;">\n' +
								'<td style="width: 11.4703%; height: 18.4px; text-align: center;"><strong style="text-align: center; color: #236fa1; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">11</span></strong></td>\n' +
								'<td style="width: 88.5297%; height: 18.4px;">\n' +
								'<p style="padding-left: 40px;">&nbsp;</p>\n' +
								'<p style="padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>Наша компания предоставляет равные возможности для всех:</strong></span> нам не важен пол, возраст и национальность. У нас нет родственного блата. Сделать карьеру благодаря труду и результативности у нас может каждый.</span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'<tr style="height: 18.4px;">\n' +
								'<td style="width: 11.4703%; height: 18.4px; text-align: center;"><strong style="text-align: center; color: #236fa1; font-size: 20px;"><span style="font-family: helvetica, arial, sans-serif;">12</span></strong></td>\n' +
								'<td style="width: 88.5297%; height: 18.4px;">\n' +
								'<p style="padding-left: 40px;">&nbsp;</p>\n' +
								'<p style="padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>Мы уверены, что нет ничего невозможного,</strong></span> и только мы сами определяем границы наших возможностей. Мы добиваемся результата с помощью упорного труда и настойчивости.</span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'</td>\n' +
								'</tr>\n' +
								'</tbody>\n' +
								'</table>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p>\n' +
								'<p style="padding-left: 80px;">&nbsp;</p></div>'
				},
				{
					id: 1008,
					html: '<div class="bp-text"><p class="MsoNormal" style="margin-bottom: 0.0001pt; text-align: center; line-height: normal; padding-left: 40px;" align="center"><span style="color: #0a5bd4; font-family: helvetica, arial, sans-serif; font-size: 18pt;"><strong>Мотивационные программы</strong></span></p>\n' +
								'<p class="MsoNormal" style="margin-bottom: 0.0001pt; text-align: center; line-height: normal; padding-left: 40px;" align="center"><span style="color: #0a5bd4; font-family: helvetica, arial, sans-serif; font-size: 18pt;"><strong>ДЛЯ ВСЕХ операторов и сотрудников&nbsp;</strong></span></p>\n' +
								'<p class="MsoNormal" style="margin-bottom: 0.0001pt; text-align: center; line-height: normal; padding-left: 40px;" align="center"><span style="font-family: helvetica, arial, sans-serif; color: #0a5bd4; font-size: 18pt;"><strong>колл-центра «</strong><strong><span lang="EN-US">Business</span></strong>&nbsp;<strong><span lang="EN-US">Partners</span></strong><strong>»</strong></span></p>\n' +
								'<p class="MsoNormal" style="margin-bottom: 0.0001pt; text-align: justify; line-height: normal; padding-left: 40px;"><span style="font-size: 20pt; font-family: helvetica, arial, sans-serif;">&nbsp;</span></p>\n' +
								'<p class="MsoNormal" style="text-align: justify; line-height: normal; margin: 0cm 7.05pt 0.0001pt 14.2pt; padding-left: 40px;"><span style="font-size: 12pt; font-family: helvetica, arial, sans-serif;"><span style="color: #236fa1;"><strong>1.</strong>&nbsp;<strong>«Приведи друга»</strong>&nbsp;</span>- хочешь получить премию 10 000 тенге? Нужно всего лишь пригласить знакомого, друга к нам на работу, который должен отработать 1 месяц.</span></p>\n' +
								'<p class="MsoNormal" style="text-align: justify; line-height: normal; margin: 0cm 7.05pt 0.0001pt 14.2pt; padding-left: 40px;"><span style="font-size: 12pt; font-family: helvetica, arial, sans-serif;">&nbsp;</span></p>\n' +
								'<p class="MsoNormal" style="text-align: justify; line-height: normal; margin: 0cm 7.05pt 0.0001pt 14.2pt; padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>2. «Обучение»</strong></span>&nbsp;-&nbsp;</span><span style="font-family: -apple-system, BlinkMacSystemFont, Segoe UI, PingFang SC, Hiragino Sans GB, Microsoft YaHei, Helvetica Neue, Helvetica, Arial, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol;">до 20 000 тенге. Нужно проходить назначенное Вам&nbsp;</span><span style="font-family: -apple-system, BlinkMacSystemFont, Segoe UI, PingFang SC, Hiragino Sans GB, Microsoft YaHei, Helvetica Neue, Helvetica, Arial, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol;">обучение, это может быть просмотр видео, чтение книг или изучение&nbsp;</span><span style="font-family: -apple-system, BlinkMacSystemFont, Segoe UI, PingFang SC, Hiragino Sans GB, Microsoft YaHei, Helvetica Neue, Helvetica, Arial, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol;">базы знаний. За это Вы зарабатываете дополнительные деньги.</span></p>\n' +
								'<p class="MsoNormal" style="line-height: normal; margin: 0cm 7.05pt 0.0001pt 14.2pt; padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;">&nbsp;</span></p>\n' +
								'<p class="MsoNormal" style="text-align: justify; line-height: normal; margin: 0cm 7.05pt 0.0001pt 14.2pt; padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><strong><span style="color: #236fa1;">3. «Забери сегодня свои денежный бонус»</span>&nbsp;</strong>-&nbsp;</span><span style="font-family: helvetica, arial, sans-serif;">достигший первым цель своего отдела&nbsp;</span><span style="font-family: helvetica, arial, sans-serif;">за день, забирает 1000 или больше бонусами к оплате, каждый день!</span></p>\n' +
								'<p class="MsoNormal" style="text-align: justify; line-height: normal; margin: 0cm 7.05pt 0.0001pt 14.2pt; padding-left: 40px;">&nbsp;</p>\n' +
								'<p class="MsoNormal" style="text-align: justify; line-height: normal; margin: 0cm 7.05pt 0.0001pt 14.2pt; padding-left: 40px;"><span style="font-family: helvetica, arial, sans-serif; font-size: 12pt;"><span style="color: #236fa1;"><strong>4.</strong>&nbsp;<strong>«Лучший сотрудник»</strong>&nbsp;</span>-&nbsp;</span><span style="font-family: helvetica, arial, sans-serif;">нужно победить какой либо ежемесячной&nbsp;</span><span style="font-family: helvetica, arial, sans-serif;">номинации и получите брендированный приз, признание коллег,&nbsp;</span><span style="font-family: helvetica, arial, sans-serif;">сертификаты и награды. Возможность оказаться на доске почета, а&nbsp;</span><span style="font-family: helvetica, arial, sans-serif;">также поехать отдыхать куда ни будь&nbsp;в интересные страны.</span></p>\n' +
								'<p class="MsoNormal" style="text-align: justify; line-height: normal; margin: 0cm 7.05pt 0.0001pt 49.65pt; padding-left: 40px;"><span style="font-size: 12pt; font-family: helvetica, arial, sans-serif;">&nbsp;</span></p>\n' +
								'<p class="MsoNormal" style="margin-bottom: 0.0001pt; line-height: normal; padding-left: 40px;"><span style="font-size: 12pt; font-family: helvetica, arial, sans-serif;">&nbsp;</span></p>\n' +
								'<p class="MsoNormal" style="margin-bottom: 0.0001pt; line-height: normal; padding-left: 40px; text-align: center;"><span style="font-size: 16pt; font-family: helvetica, arial, sans-serif;">&nbsp;</span><span style="color: #236fa1;"><strong><span style="font-family: helvetica, arial, sans-serif; font-size: 16pt; text-align: center;">Всем Удачи!!!</span></strong></span></p>\n' +
								'<p class="MsoNormal" style="margin-bottom: 0.0001pt; line-height: normal; padding-left: 40px; text-align: center;">&nbsp;</p>\n' +
								'<p class="MsoNormal" style="margin-bottom: 0.0001pt; line-height: normal; padding-left: 40px; text-align: center;">&nbsp;</p></div>'
				},
				{
					id: 1009,
					html: '<div class="bp-text"><p style="text-align: center;"><span style="font-size: 16pt; color: #236fa1;"><strong>Как создать и наполнить базу знаний</strong></span></p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p><strong>В процессе запуска нового проекта Вам нужно будет создать базу знаний</strong>&nbsp;для операторов и руководителей проекта, которой они будут руководствоваться в процессе работы после Вашего ухода на запуск нового проекта. В процессе ведения проекта, руководитель группы и подразделения будут эти данные дополнять и модернизировать.&nbsp;</p>\n' +
								'<p>Все что Вы будете получать от Заказчика в процессе запуска нового проекта, и все что Вы считаете будет нужным для работы сотрудников данного проекта, нужно заполнять в базу знаний.</p>\n' +
								'<p><strong>Обязательные данные которые должны быть в Базе знаний, это:</strong></p>\n' +
								'<ol>\n' +
								'<li>данные о компании заказчике</li>\n' +
								'<li>данные о продукте с которым именно наши специалисты работают</li>\n' +
								'<li>описание программы в которой придется работать оператору и руководительскому составу</li>\n' +
								'<li>скрипты работы для оператора (составляются именно тут&nbsp;<a href="https://skr.sh/sEXKlUHCYL7" target="_blank">https://skr.sh/sEXKlUHCYL7)</a></li>\n' +
								'<li>Алгоритмы, регламенты, нормы и прочие правила которыми нужно руководствоваться в работе.</li>\n' +
								'</ol>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p style="text-align: center;"><span style="font-size: 14pt;"><strong style="color: #236fa1; text-align: center;">Алгоритм создания Базы знаний</strong></span></p>\n' +
								'<p><strong>1. База знаний создается</strong>&nbsp;по запросу у Вашего руководителя, либо Вами самостоятельно если у Вас есть доступ к созданию, об этом Вы можете узнать вот тут&nbsp;<a href="https://skr.sh/sEXImOMKwa1" target="_blank">https://skr.sh/sEXImOMKwa1</a></p>\n' +
								'<p><strong>2. База знаний называется</strong>&nbsp;именем:</p>\n' +
								'<ul>\n' +
								'<li>для оператора "название проекта (оператору)"</li>\n' +
								'<li>для руководителей "название проекта (руководителю)"</li>\n' +
								'</ul>\n' +
								'<p><strong>3. Далее в базе создается понятная структура</strong>&nbsp;страниц, вот пример&nbsp;<a href="https://skr.sh/sEXwMq3TQAE" target="_blank">https://skr.sh/sEXwMq3TQAE</a>&nbsp;</p>\n' +
								'<p><strong>4. Наполните страницы:</strong></p>\n' +
								'<ul>\n' +
								'<li>утвержденное форматирование заголовка 16, жирный шрифт sans-serif, темносиний цвет&nbsp;<a href="https://skr.sh/sEXEF3rqNTL" target="_blank">https://skr.sh/sEXEF3rqNTL</a>&nbsp;</li>\n' +
								'<li>утвержденное форматирование подзаголовка, 14, жирный шрифт sans-serif, темносиний цвет</li>\n' +
								'<li>утвержденное форматирование текста, 12 размер, обычный шрифт sans-serif, черный цвет</li>\n' +
								'</ul>\n' +
								'<p><strong>5. Делайте более привлекательной Базу знаний</strong>&nbsp;наполняя ее картинками, анимациями, видео и аудио информацией</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p><em>*больше информации в видео в Базе знаний руководителя отдела:&nbsp;<a href="https://skr.sh/sEXxN8lw6qx" target="_blank">https://skr.sh/sEXxN8lw6qx</a>&nbsp;</em></p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p></div>'
				},
				{
					id: 1010,
					html: '<div class="bp-text"><p><span style="color: #236fa1; font-size: 14pt;"><strong>1. Читая книгу </strong></span></p>\n' +
								'<p style="padding-left: 40px;"><img src="/bpartners/1659025765.jpg" alt="" width="574" height="228"></p>\n' +
								'<p><span style="font-size: 14pt; color: #236fa1;"><strong>2. просматривая видео&nbsp;</strong></span></p>\n' +
								'<p style="padding-left: 40px;"><img src="/bpartners/1659025647.jpg" alt="" width="571" height="251"></p>\n' +
								'<p><span style="font-size: 14pt; color: #236fa1;"><strong>3. а также изучая Базу знаний</strong></span></p>\n' +
								'<p style="padding-left: 40px;"><img src="/bpartners/1659025714.jpg" alt="" width="568" height="210"></p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>каждый сотрудник получает бонусы на свой счет в профиле, а для того чтобы ему начислялись правильно бонусы, Вам нужно добавить их при создании страниц в Базе знаний, или при добавлении книг для чтения, а также при добавлении видеороликов в раздел видео обучения.&nbsp;</p>\n' +
								'<p><span style="font-size: 15pt; color: #236fa1;"><strong>Добавляются бонусы по формуле:</strong></span></p>\n' +
								'<ul>\n' +
								'<li><strong>за каждую страницу в БАЗЕ ЗНАНИЙ</strong> нужно выставить 10 бонусов, то есть Вы создали одну страницу, далее наполнили ее контентом, а после Вам нужно выставить к ней минимум 2 тестовых вопроса <a href="https://skr.sh/sF9wnCypL8Y" target="_blank">https://skr.sh/sF9wnCypL8Y</a>, на которые суммарно у Вас есть 10 бонусов. Значит если у Вас 2 вопроса, Вы на каждый тестовый вопрос даете 5 бонусов&nbsp; <a href="https://skr.sh/sF9s07LwzEm" target="_blank">https://skr.sh/sF9s07LwzEm</a> , если у Вас 3 вопроса, то по 3 бонуса за каждый вопрос и так далее.&nbsp;</li>\n' +
								'</ul>\n' +
								'<p>&nbsp;</p>\n' +
								'<ul>\n' +
								'<li><strong>за каждую страницу в разделе ЧТЕНИЯ КНИГ</strong> нужно выставить тоже 10 бонусов. Когда пользователь читает книгу, то на тестовые вопросы он отвечает ни на каждой странице, а на каждой 20й или 40й странице (в разных книгах разные интервалы) и тестовых вопросов может быть от 5 до 10, и тут формула добавления бонусов работает по такой схеме: так как одна страница это 10 бонусов, а в книгах может быть например с 1 - 40 страницу &nbsp; = 5 вопросов значит 40 страниц *10 бонусов и поделить на 5 = 80 баллов на каждый вопрос.</li>\n' +
								'</ul>\n' +
								'<p>&nbsp;</p>\n' +
								'<ul>\n' +
								'<li><strong>за каждую минуту в разделе ВИДЕО ОБУЧЕНИЯ</strong> выставляется 5 бонусов, например 3 минуты = 15 бонусов,&nbsp;10 минут = 50 бонусов и тд.&nbsp;Если видео например 20 минут и там есть 4 вопроса, то за каждый вопрос только по 25 бонусов выставить можно. Формула: 20 минут * 5 бонусов = 100 бонусов на все вопросы / на кол-во вопросов = кол-во бонусов на каждый из вопросов.&nbsp;</li>\n' +
								'</ul>\n' +
								'<p>&nbsp;</p>\n' +
								'<p><strong style="color: #236fa1; font-size: 20px;">Также Вам нужно выставить проходной балл.</strong></p>\n' +
								'<p>Это значит, на сколько из тестовых вопросов должен ответить сотрудник, чтобы система его пропустила к следующим страницам в книге или видео, т к если он не наберет проходной балл, то система ему сообщит чтобы еще раз все перечитал и пересмотрел и после ответил, а страницы Базы знаний открыты все сразу и там отвечать можно на любые страницы.</p>\n' +
								'<p>Поэтому выставьте проходной балл, <a href="https://skr.sh/sF9QXIg229A" target="_blank">https://skr.sh/sF9QXIg229A</a> на данном скрине показано, что сотруднику который будет отвечать на тестовые вопросы, нужно ответить верно всего на два вопроса из трех, и тогда система его пропустит к следующим страницам книги, тоже самое и про видео. В базе знаний так же выставляется проходной балл, и при этом все страницы сразу открыты для изучения. Проходной балл выставляйте на 1 меньше, чем в общем вопросов, например: 4 вопроса, значит проходной балл будет 3, или если 7 вопросов, то проходной балл будет 6, а если 3 вопроса, то проходной будет 2 и так далее. Исключение только если 1 или 2 вопроса, то будет минимум будет 2 вопроса проходных к 2м вопросам и 1 к одному.</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p><strong style="color: #236fa1; font-size: 20px;">Сколько бонусов сотрудник получит при правильных ответах ?</strong></p>\n' +
								'<p>Пример который мы выше рассмотрели, когда сотруднику нужно ответить на 2 вопроса из 3х, для того чтобы дальше читать. Допустим он ответил на 2 вопроса, и тут за каждый правильный ответ на тестовый вопрос дается по 66 бонусов <a href="https://skr.sh/sF9LdRmjvrT" target="_blank">https://skr.sh/sF9LdRmjvrT</a> , тогда сотрудник получит на свой счет 132 бонуса, а если сотрудник правильно ответит на все 3 вопроса, то он получит на свой счет 3 * 66 = 198 бонусов.</p>\n' +
								'<p>По такой же логике работает начисление бонусов за видео обучение и ответы на вопросы в Базе знаний.</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p>\n' +
								'<p>&nbsp;</p></div>'
				},
			]
		}
	},
	computed: {
		filteredContent(){
			return this.content.find(c => c.id === this.itemContent.contentId)
		}
	}
}
</script>
