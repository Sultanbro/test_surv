<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оцените вашего тренера</title>
  
    <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    
<!--container-->
<section class="container">

	<!--questionBox-->
	<div class="questionBox" id="app">


             <div  style="width:100%">

		<!-- transition -->
		<transition :duration="{ enter: 500, leave: 300 }" enter-active-class="animated zoomIn" leave-active-class="animated zoomOut" mode="out-in">

            

           
                <!--qusetionContainer-->
                <div class="questionContainer" v-if="questionIndex<quiz.questions.length" v-bind:key="questionIndex">

                    <div class="header">
                        <h1 class="title is-6"><img src="/admin/images/logo.png" alt="" style="width:160px;"></h1>
                      
                    </div>

                    <!-- questionTitle -->
                    <h2 class="titleContainer title">@{{ quiz.questions[questionIndex].text }}</h2>

                    <!-- quizOptions -->
                    <div class="optionContainer" v-if="quiz.questions[questionIndex].type === 'star'">
                      <div class="rating">
                        <ul class="list">
                          <li @click="rate(star)" v-for="star in 10" :class="{ 'active': star <= stars }" :key="star" class="star">
                          <i :class="star <= stars ? 'fa fa-star' : 'fa fa-star'"></i> 
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div class="optionContainer" v-else>
                        <template v-for="(response, index) in quiz.questions[questionIndex].responses">
                            <div class="option" v-if="response.type"  :class="{ 'is-selected': userResponses[questionIndex] == index}" :key="index">
                                @{{ index | charIndex }}.<input type="text" v-model="response.text" @keyup="addAnswer(response.text, index)" :value="response.text" class="inp" placeholder="Напишите свой вариант"> 
                            </div>
                            <div class="option"  v-else   @click="selectOption(index)" :class="{ 'is-selected': userResponses[questionIndex] == index}" :key="index">
                                @{{ index | charIndex }}. @{{ response.text }}
                            </div>
                        </template>
                    </div>

                    <!--quizFooter: navigation and progress-->
                    <footer class="questionFooter">

                        <!--pagination-->
                        <nav class="pagination" role="navigation" aria-label="pagination">

                            <!-- back button -->
                            <!-- <a class="button" v-on:click="prev();" :disabled="questionIndex < 1">
                                Назад
                            </a> -->

                            <!-- next button -->
                                <a class="button cen" v-if="(userResponses[questionIndex]!=null)" :class="(userResponses[questionIndex]==null)?'':'is-active'" v-on:click="next();" :disabled="questionIndex>=quiz.questions.length">
                                Оценить
                                </a>

                        </nav>
                        <!--/pagination-->

                    </footer>
                    <!--/quizFooter-->

                </div>
                <!--/questionContainer-->

                <!--quizCompletedResult-->
                <div v-if="questionIndex >= quiz.questions.length" v-bind:key="questionIndex" class="quizCompleted has-text-centered">

      
                    <span class="icon" style="display:none">
                      <i class="fa" :class="score()>3?'fa-check-circle-o is-active':'fa-check-circle-o is-active'"></i>
                  </span>

                    <!--resultTitleBlock-->
                    <h1 class="title is-6"><img src="/admin/images/logo.png" alt="" style="width:160px;margin-bottom:15px;"></h1>
                    <h2 class="title pacifico">
                        Спасибо!
                    </h2>
                    <p class="subtitle">
                        Огромное спасибо за то, что уделили время для оценки. <br>
                        Ваши ответы помогают нам улучшить качество обучения. <br>
                        Хорошего вам дня! 
                    </p>
                        <br>
            
                    <!--/resultTitleBlock-->

                </div>
                <!--/quizCompetedResult-->

            </transition>
        </div>

	</div>
	<!--/questionBox-->

</section>
<!--/container-->


 

<style>

* {
    box-sizing:border-box;
    margin: 0;
    padding: 0;
}
body {
  font-family: "Roboto", sans-serif;
  min-height: 100vh;
  font-size: 14px;
  background: #cfd8dc;
  /* mocking native UI */
  cursor: default !important;
  /* remove text selection cursor */
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
  /* remove text selection */
  user-drag: none;
  /* disbale element dragging */
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #04BBFF;
background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='481' height='400.8' viewBox='0 0 1080 900'%3E%3Cg fill-opacity='0.04'%3E%3Cpolygon fill='%23444' points='90 150 0 300 180 300'/%3E%3Cpolygon points='90 150 180 0 0 0'/%3E%3Cpolygon fill='%23AAA' points='270 150 360 0 180 0'/%3E%3Cpolygon fill='%23DDD' points='450 150 360 300 540 300'/%3E%3Cpolygon fill='%23999' points='450 150 540 0 360 0'/%3E%3Cpolygon points='630 150 540 300 720 300'/%3E%3Cpolygon fill='%23DDD' points='630 150 720 0 540 0'/%3E%3Cpolygon fill='%23444' points='810 150 720 300 900 300'/%3E%3Cpolygon fill='%23FFF' points='810 150 900 0 720 0'/%3E%3Cpolygon fill='%23DDD' points='990 150 900 300 1080 300'/%3E%3Cpolygon fill='%23444' points='990 150 1080 0 900 0'/%3E%3Cpolygon fill='%23DDD' points='90 450 0 600 180 600'/%3E%3Cpolygon points='90 450 180 300 0 300'/%3E%3Cpolygon fill='%23666' points='270 450 180 600 360 600'/%3E%3Cpolygon fill='%23AAA' points='270 450 360 300 180 300'/%3E%3Cpolygon fill='%23DDD' points='450 450 360 600 540 600'/%3E%3Cpolygon fill='%23999' points='450 450 540 300 360 300'/%3E%3Cpolygon fill='%23999' points='630 450 540 600 720 600'/%3E%3Cpolygon fill='%23FFF' points='630 450 720 300 540 300'/%3E%3Cpolygon points='810 450 720 600 900 600'/%3E%3Cpolygon fill='%23DDD' points='810 450 900 300 720 300'/%3E%3Cpolygon fill='%23AAA' points='990 450 900 600 1080 600'/%3E%3Cpolygon fill='%23444' points='990 450 1080 300 900 300'/%3E%3Cpolygon fill='%23222' points='90 750 0 900 180 900'/%3E%3Cpolygon points='270 750 180 900 360 900'/%3E%3Cpolygon fill='%23DDD' points='270 750 360 600 180 600'/%3E%3Cpolygon points='450 750 540 600 360 600'/%3E%3Cpolygon points='630 750 540 900 720 900'/%3E%3Cpolygon fill='%23444' points='630 750 720 600 540 600'/%3E%3Cpolygon fill='%23AAA' points='810 750 720 900 900 900'/%3E%3Cpolygon fill='%23666' points='810 750 900 600 720 600'/%3E%3Cpolygon fill='%23999' points='990 750 900 900 1080 900'/%3E%3Cpolygon fill='%23999' points='180 0 90 150 270 150'/%3E%3Cpolygon fill='%23444' points='360 0 270 150 450 150'/%3E%3Cpolygon fill='%23FFF' points='540 0 450 150 630 150'/%3E%3Cpolygon points='900 0 810 150 990 150'/%3E%3Cpolygon fill='%23222' points='0 300 -90 450 90 450'/%3E%3Cpolygon fill='%23FFF' points='0 300 90 150 -90 150'/%3E%3Cpolygon fill='%23FFF' points='180 300 90 450 270 450'/%3E%3Cpolygon fill='%23666' points='180 300 270 150 90 150'/%3E%3Cpolygon fill='%23222' points='360 300 270 450 450 450'/%3E%3Cpolygon fill='%23FFF' points='360 300 450 150 270 150'/%3E%3Cpolygon fill='%23444' points='540 300 450 450 630 450'/%3E%3Cpolygon fill='%23222' points='540 300 630 150 450 150'/%3E%3Cpolygon fill='%23AAA' points='720 300 630 450 810 450'/%3E%3Cpolygon fill='%23666' points='720 300 810 150 630 150'/%3E%3Cpolygon fill='%23FFF' points='900 300 810 450 990 450'/%3E%3Cpolygon fill='%23999' points='900 300 990 150 810 150'/%3E%3Cpolygon points='0 600 -90 750 90 750'/%3E%3Cpolygon fill='%23666' points='0 600 90 450 -90 450'/%3E%3Cpolygon fill='%23AAA' points='180 600 90 750 270 750'/%3E%3Cpolygon fill='%23444' points='180 600 270 450 90 450'/%3E%3Cpolygon fill='%23444' points='360 600 270 750 450 750'/%3E%3Cpolygon fill='%23999' points='360 600 450 450 270 450'/%3E%3Cpolygon fill='%23666' points='540 600 630 450 450 450'/%3E%3Cpolygon fill='%23222' points='720 600 630 750 810 750'/%3E%3Cpolygon fill='%23FFF' points='900 600 810 750 990 750'/%3E%3Cpolygon fill='%23222' points='900 600 990 450 810 450'/%3E%3Cpolygon fill='%23DDD' points='0 900 90 750 -90 750'/%3E%3Cpolygon fill='%23444' points='180 900 270 750 90 750'/%3E%3Cpolygon fill='%23FFF' points='360 900 450 750 270 750'/%3E%3Cpolygon fill='%23AAA' points='540 900 630 750 450 750'/%3E%3Cpolygon fill='%23FFF' points='720 900 810 750 630 750'/%3E%3Cpolygon fill='%23222' points='900 900 990 750 810 750'/%3E%3Cpolygon fill='%23222' points='1080 300 990 450 1170 450'/%3E%3Cpolygon fill='%23FFF' points='1080 300 1170 150 990 150'/%3E%3Cpolygon points='1080 600 990 750 1170 750'/%3E%3Cpolygon fill='%23666' points='1080 600 1170 450 990 450'/%3E%3Cpolygon fill='%23DDD' points='1080 900 1170 750 990 750'/%3E%3C/g%3E%3C/svg%3E");
}
.pacifico {
    font-family: 'Open Sans';
    font-size: 23px;
    color:#22953b;
}
.button {
  transition: 0.3s;
}


.subtitle {
  font-weight: normal;
}

.animated {
  transition-duration: 0.15s;
}

.container {
  margin: 0 0.5rem;
  width: 100%;
  display: flex;
  justify-content: center;
}

.questionBox {
    max-width: 100%;
  width: 400px;
  min-height: 30rem;
  background: #fafafa;
  position: relative;
  display: flex;
  border-radius: 0.5rem;
  overflow: hidden;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);
  
}
.questionBox .header {
  background: #f0f4fa;
  padding: 1.5rem;
  text-align: center;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}
.questionBox .header h1 {
  font-weight: bold;
  margin-bottom: 1rem !important;
}
.questionBox .header .progressContainer {
  width: 60%;
  margin: 0 auto;
}
.questionBox .header .progressContainer > progress {
  margin: 0;
  border-radius: 5rem;
  overflow: hidden;
  border: none;
  color: #04b4f5;
}
.questionBox .header .progressContainer > progress::-moz-progress-bar {
  background: #04b4f5;
}
.questionBox .header .progressContainer > progress::-webkit-progress-value {
  background: #04b4f5;
}
.questionBox .header .progressContainer > p {
  margin: 0;
  margin-top: 0.5rem;
}
.questionBox .titleContainer {
  text-align: center;
  margin: 0 auto;
  font-size: 18px;
  padding: 1.5rem;
}
.questionBox .quizForm {
  display: block;
  white-space: normal;
  height: 100%;
  width: 100%;
}
.questionBox .quizForm .quizFormContainer {
  height: 100%;
  margin: 15px 18px;
}
.questionBox .quizForm .quizFormContainer .field-label {
  text-align: left;
  margin-bottom: 0.5rem;
}
.questionBox .quizCompleted {
    width: 100%;
    height: 100%;
    padding: 1rem;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction:column;
}
.questionBox .quizCompleted > .icon {
  color: #ff5252;
  font-size: 5rem;
}
.questionBox .quizCompleted > .icon .is-active {
  color: #00e676;
}
.questionBox .questionContainer {
  white-space: normal;
  width: 100%;
}
.questionBox .questionContainer .optionContainer {
  margin-top: 12px;
}
.first {
    padding: 25px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.first p {
    font-family: 'Roboto';
    font-size: 14px;
    color: #045e92;
}
.questionBox .questionContainer .optionContainer .option {
  border-radius: 290486px;
  font-size: 12px;
  padding: 9px 18px;
  margin: 0 18px;
  margin-bottom: 12px;
  transition: 0.3s;
  cursor: pointer;
  background-color: #f0f4fa;
  color: rgba(0, 0, 0, 0.85);
  border: transparent 1px solid;
}
.questionBox .questionContainer .optionContainer .option.is-selected {
  border-color: rgba(0, 0, 0, 0.25);
  background-color: white;
}
.questionBox .questionContainer .optionContainer .option:hover {
  background-color: #04b4f5;
  border-color: #04b4f5;
}
.questionBox .questionContainer .optionContainer .option:active {
  transform: scaleX(0.9);
}
.questionBox .questionContainer .questionFooter {
  background: rgba(0, 0, 0, 0.025);
  border-top: 1px solid rgba(0, 0, 0, 0.1);
  width: 100%;
  align-self: flex-end;
}
.questionBox .questionContainer .questionFooter .pagination {
  padding: 15px;
}

.pagination {
  display: flex;
  justify-content: space-between;
}

.button {
  padding: 0.5rem 1rem;
  border: 1px solid rgba(0, 0, 0, 0.25);
  border-radius: 5rem;
  margin: 0 0.25rem;
  transition: 0.3s;
}
.button:hover {
  cursor: pointer;
  background: #eceff1;
  border-color: rgba(0, 0, 0, 0.25);
}
.button.is-active {
  background: #04b4f5;
  color: white;
  border-color: transparent;
}
.button.is-active:hover {
  background: #0a2ffe;
}

@media screen and (min-width: 769px) {
  .questionBox {
    align-items: center;
    justify-content: center;
  }
  .questionBox .questionContainer {
    display: flex;
    flex-direction: column;
  }
}
@media screen and (max-width: 768px) {
  .sidebar {
    height: auto !important;
    border-radius: 6px 6px 0px 0px;
  }
  .questionBox,
  .questionBox .quizCompleted {
      min-height:100vh
  }
}
.inp {
    background: transparent;
    border: 0;
    width: 90%;
    outline: transparent;
    font-family: 'Roboto';
    margin-left: 2px;
    font-size: 12px;
}
/**** */

.cen {
    margin: 0 auto;
}
.rating {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
      -ms-flex-direction: column;
          flex-direction: column;
  -webkit-box-align: center;
      -ms-flex-align: center;
          align-items: center;
  padding: 50px;
  color: #b7b7b7;
  background: #fff;
  border-radius: 8px;
  -webkit-box-shadow: 0 6px 33px rgba(19, 18, 18, 0.09);
          box-shadow: 0 6px 33px rgba(19, 18, 18, 0.09);
}

.rating .list {
  padding: 0;
  margin: 0 20px 0 0;
}

.rating .list:hover .star {
  color: #ffe100;
}

.rating .list .star {
  display: inline-block;
  font-size: 20px;
  -webkit-transition: all .2s ease-in-out;
  transition: all .2s ease-in-out;
  cursor: pointer;
}

.rating .list .star:hover ~ .star:not(.active) {
  color: inherit;
}

.rating .list .star:first-child {
  margin-left: 0;
}

.rating .list .star.active {
  color: #ffe100;
}

.rating .info {
  margin-top: 15px;
  font-size: 40px;
  text-align: center;
  display: table;
}

.rating .info .divider {
  margin: 0 5px;
  font-size: 30px;
}

.rating .info .score-max {
  font-size: 30px;
  vertical-align: sub;
}

</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.24.0/axios.min.js" integrity="sha512-u9akINsQsAkG9xjc1cnGF4zw5TFDwkxuc9vUp5dltDWYCSmyd0meygbvgXrlc/z7/o4a19Fb5V0OUE58J7dcyw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>



var quiz = {
      user: "BP",
      questions: [
        @foreach($quiz as $question)
         {
            text: "{{ $question['q'] }}",
            type: "{{ $question['type'] }}",
            responses: [
              @if($question['type'] == 'variant' || $question['type'] == 'answer')
               @foreach($question['answers'] as $a)
                @if($a['text'] != 'Другое')
                 { text: "{{ $a['text'] }}" },
                @else
                 { text: "", type: 'input' },
                @endif
               @endforeach
              @else
                { text: "", type: 'star' },
              @endif
            ]
         },
         @endforeach
      ]
   },
   userResponseSkelaton = Array(quiz.questions.length).fill(null);

var app = new Vue({
   el: "#app",
   data: {
      quiz: quiz,
      questionIndex: 0,
      stars: 0,
      userResponses: userResponseSkelaton,
      isActive: false,
      welcome: false,
   },
   filters: {
      charIndex: function(i) {
         return String.fromCharCode(97 + i);
      }
   },
   methods: {

      selectOption: function(index) {
         Vue.set(this.userResponses, this.questionIndex, index);
         //console.log(this.userResponses);
      },

      addAnswer: function(text, index) {
        if(text.length != 0) {
            Vue.set(this.userResponses, this.questionIndex, index);
        }
      },
      
      next: function() {
         if (this.questionIndex < this.quiz.questions.length) {
          this.questionIndex++;
          this.stars = 0;
         }
           
        
      },

      prev: function() {
         if (this.quiz.questions.length > 0) this.questionIndex--;
      },

      rate(star) {
        if (typeof star === 'number' && star <= 10 && star >= 0) {
          
          this.quiz.questions[this.questionIndex].responses[0].text = star;
          this.stars = star;
          Vue.set(this.userResponses, this.questionIndex, 0);
        }
      },

      // Return "true" count in userResponses
      score: function() {
         var answers = [];

         for (let i = 0; i < this.userResponses.length; i++) {
            answers[i + 1] = this.quiz.questions[i].responses[this.userResponses[i]].text;
         }
         
         axios.post('/api/intellect/save_estimate_trainer', {
                phone: '{{ $phone }}',
                answer1: answers[1],
                answer2: answers[2],
                answer3: answers[3],
                answer4: answers[4],
                answer5: answers[5],
                answer6: answers[6],
                answer7: answers[7],
                answer8: answers[8],
                answer9: answers[9],
            })
            .then(response => {})
            .catch(error => console.log('Error'))
        }
   }
});

</script>
</body>
</html>