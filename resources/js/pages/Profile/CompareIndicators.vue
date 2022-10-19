<template>
<div class="index block _anim _anim-no-hide content" id="index">
    <div class="title index__title">
        Сравнение показателей
    </div>
    <div class="subtitle index__subtitle">
        Сравните Ваши показатели с другими сотрудниками
    </div>

    <div class="index__table">
        <div class="tabs custom-scroll">
            <div class="index__tabs tabs__wrapper">
                <div  class="index__tab tab__item is-active" onclick="switchTabs(this)"  data-index="1">Минуты разговора</div>
                <div  class="index__tab tab__item" onclick="switchTabs(this)"  data-index="2"> 3-6 НБ</div>
                <div class="index__tab tab__item"  onclick="switchTabs(this)"  data-index="3"> 7-30 НБ</div>
                <div  class="index__tab tab__item" onclick="switchTabs(this)"  data-index="4">31-60 НБ</div>
                <div  class="index__tab tab__item" onclick="switchTabs(this)"  data-index="5">61-90 НБ</div>
                <div  class="index__tab tab__item" onclick="switchTabs(this)"  data-index="6">6-30 РЕД</div>
                <div  class="index__tab tab__item" onclick="switchTabs(this)"  data-index="7">Среднее время диалога</div>
                <div  class="index__tab tab__item" onclick="switchTabs(this)"  data-index="8">ОКК</div>
                <div  class="index__tab tab__item" onclick="switchTabs(this)"  data-index="9">Контроль Качества</div>
            </div>
            <select class="select-css trainee-select mobile-select">
                <option value="1">Минуты разговора</option>
                <option value="2"> 3-6 НБ</option>
                <option value="3">7-30 НБ</option>
                <option value="4">31-60 НБ</option>
                <option value="5">61-90 НБ</option>
                <option value="6">6-30 РЕД</option>
                <option value="7">Среднее время диалога</option>
                <option value="8">ОКК</option>
                <option value="9">Контроль Качества</option>
            </select>
            <div class="tab__content">
                <div class="tab__content-item index__content is-active" data-content="1">
                    <table>

                        <colgroup>

                        </colgroup>

                        <thead>
                            <tr>
                                <th>Сотрудник</th>
                                <th>Ср.</th>
                                <th>План</th>
                                <th>Вып.</th>
                                <th>%</th>
                                <th>1</th>
                                <th>2</th>
                                <th>3</th>
                                <th>4</th>
                                <th>5</th>
                                <th>6</th>
                                <th>7</th>
                                <th>8</th>
                                <th>9</th>
                                <th>10</th>
                                <th>11</th>
                                <th>12</th>
                                <th>13</th>
                                <th>14</th>
                                <th>15</th>
                                <th>16</th>
                                <th>17</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="prize first-place">
                                <td><div class="large">Аппазова Карлыгаш</div></td>
                                <td><div class="medium">233</div></td>
                                <td><div class="medium">7020</div></td>
                                <td class="blue"><div>3026.00</div></td>
                                <td><div class="small">43.11</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>260</div></td>
                                <td class="red"><div>3</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>151</div></td>
                                <td class="red"><div>239</div></td>
                                <td class="red"><div>191</div></td>
                                <td class="red"><div>295</div></td>
                                <td class="red"><div>275</div></td>
                                <td class="green"><div>280</div></td>
                                <td class="green"><div>228</div></td>
                                <td class="green"><div>146</div></td>
                                <td ><div>150</div></td>
                                <td ><div>206</div></td>
                                <td ><div>120</div></td>
                                <td ><div>134</div></td>
                            </tr>
                            <tr class="prize second-place">
                                <td><div class="large">Аппазова Карлыгаш</div></td>
                                <td><div class="medium">233</div></td>
                                <td><div class="medium">7020</div></td>
                                <td class="blue"><div>3026.00</div></td>
                                <td><div class="small">43.11</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>260</div></td>
                                <td class="red"><div>3</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>151</div></td>
                                <td class="red"><div>239</div></td>
                                <td class="red"><div>191</div></td>
                                <td class="red"><div>295</div></td>
                                <td class="red"><div>275</div></td>
                                <td class="red"><div>280</div></td>
                                <td class="red"><div>228</div></td>
                                <td class="red"><div>146</div></td>
                                <td  class="red"><div>150</div></td>
                                <td  class="red"><div>206</div></td>
                                <td  class="red"><div>120</div></td>
                                <td  class="red"><div>134</div></td>
                            </tr>
                            <tr class="prize third-place">
                                <td><div class="large">Аппазова Карлыгаш</div></td>
                                <td><div class="medium">233</div></td>
                                <td><div class="medium">7020</div></td>
                                <td class="blue"><div>3026.00</div></td>
                                <td><div class="small">43.11</div></td>
                                <td class="green"><div>216</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="green"><div>260</div></td>
                                <td class="red"><div>3</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="green"><div>151</div></td>
                                <td class="red"><div>239</div></td>
                                <td class="red"><div>191</div></td>
                                <td class="green"><div>295</div></td>
                                <td class="red"><div>275</div></td>
                                <td class="green"><div>280</div></td>
                                <td class="green"><div>228</div></td>
                                <td class="green"><div>146</div></td>
                                <td ><div>150</div></td>
                                <td ><div>206</div></td>
                                <td ><div>120</div></td>
                                <td ><div>134</div></td>
                            </tr>
                            <tr>
                                <td><div class="large">Аппазова Карлыгаш</div></td>
                                <td><div class="medium">233</div></td>
                                <td><div class="medium">7020</div></td>
                                <td class="blue"><div>3026.00</div></td>
                                <td><div class="small">43.11</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>260</div></td>
                                <td class="red"><div>3</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>151</div></td>
                                <td class="red"><div>239</div></td>
                                <td class="red"><div>191</div></td>
                                <td class="red"><div>295</div></td>
                                <td class="red"><div>275</div></td>
                                <td class="green"><div>280</div></td>
                                <td class="green"><div>228</div></td>
                                <td class="green"><div>146</div></td>
                                <td ><div>150</div></td>
                                <td ><div>206</div></td>
                                <td ><div>120</div></td>
                                <td ><div>134</div></td>
                            </tr>
                            <tr>
                                <td><div class="large">Аппазова Карлыгаш</div></td>
                                <td><div class="medium">233</div></td>
                                <td><div class="medium">7020</div></td>
                                <td class="blue"><div>3026.00</div></td>
                                <td><div class="small">43.11</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>260</div></td>
                                <td class="green"><div>3</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="green"><div>151</div></td>
                                <td class="red"><div>239</div></td>
                                <td class="red"><div>191</div></td>
                                <td class="green"><div>295</div></td>
                                <td class="red"><div>275</div></td>
                                <td class="green"><div>280</div></td>
                                <td class="red"><div>228</div></td>
                                <td class="green"><div>146</div></td>
                                <td class="green"><div>150</div></td>
                                <td class="green"><div>206</div></td>
                                <td class="red"><div>120</div></td>
                                <td class="red"><div>134</div></td>
                            </tr>
                            <tr>
                                <td><div class="large">Аппазова Карлыгаш</div></td>
                                <td><div class="medium">233</div></td>
                                <td><div class="medium">7020</div></td>
                                <td class="blue"><div>3026.00</div></td>
                                <td><div class="small">43.11</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>260</div></td>
                                <td class="red"><div>3</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>151</div></td>
                                <td class="red"><div>239</div></td>
                                <td class="red"><div>191</div></td>
                                <td class="red"><div>295</div></td>
                                <td class="red"><div>275</div></td>
                                <td class="red"><div>280</div></td>
                                <td class="red"><div>228</div></td>
                                <td class="red"><div>146</div></td>
                                <td ><div>150</div></td>
                                <td ><div>206</div></td>
                                <td ><div>120</div></td>
                                <td ><div>134</div></td>
                            </tr>
                            <tr>
                                <td><div class="large">Аппазова Карлыгаш</div></td>
                                <td><div class="medium">233</div></td>
                                <td><div class="medium">7020</div></td>
                                <td class="blue"><div>3026.00</div></td>
                                <td><div class="small">43.11</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>260</div></td>
                                <td class="red"><div>3</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>151</div></td>
                                <td class="red"><div>239</div></td>
                                <td class="red"><div>191</div></td>
                                <td class="red"><div>295</div></td>
                                <td class="red"><div>275</div></td>
                                <td class="red"><div>280</div></td>
                                <td class="red"><div>228</div></td>
                                <td class="red"><div>146</div></td>
                                <td ><div>150</div></td>
                                <td ><div>206</div></td>
                                <td ><div>120</div></td>
                                <td ><div>134</div></td>
                            </tr>
                            <tr>
                                <td><div class="large">Аппазова Карлыгаш</div></td>
                                <td><div class="medium">233</div></td>
                                <td><div class="medium">7020</div></td>
                                <td class="blue"><div>3026.00</div></td>
                                <td><div class="small">43.11</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>260</div></td>
                                <td class="red"><div>3</div></td>
                                <td class="red"><div>216</div></td>
                                <td class="red"><div>151</div></td>
                                <td class="red"><div>239</div></td>
                                <td class="red"><div>191</div></td>
                                <td class="red"><div>295</div></td>
                                <td class="red"><div>275</div></td>
                                <td class="red"><div>280</div></td>
                                <td class="green"><div>228</div></td>
                                <td class="red"><div>146</div></td>
                                <td class="red"><div>150</div></td>
                                <td class="red"><div>206</div></td>
                                <td class="red"><div>120</div></td>
                                <td class="red"><div>134</div></td>
                            </tr>
                        </tbody>

                    </table>


                </div>
                <div class="tab__content-item index__content " data-content="2">
                    <table>

                        <colgroup>

                        </colgroup>

                        <thead>
                        <tr>
                            <th>Сотрудник</th>
                            <th>Ср.</th>
                            <th>План</th>
                            <th>Вып.</th>
                            <th>%</th>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
                            <th>7</th>
                            <th>8</th>
                            <th>9</th>
                            <th>10</th>
                            <th>11</th>
                            <th>12</th>
                            <th>13</th>
                            <th>14</th>
                            <th>15</th>
                            <th>16</th>
                            <th>17</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="prize first-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr class="prize second-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td  class="red"><div>150</div></td>
                            <td  class="red"><div>206</div></td>
                            <td  class="red"><div>120</div></td>
                            <td  class="red"><div>134</div></td>
                        </tr>
                        <tr class="prize third-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="green"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="green"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="green"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="green"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td class="green"><div>150</div></td>
                            <td class="green"><div>206</div></td>
                            <td class="red"><div>120</div></td>
                            <td class="red"><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td class="red"><div>150</div></td>
                            <td class="red"><div>206</div></td>
                            <td class="red"><div>120</div></td>
                            <td class="red"><div>134</div></td>
                        </tr>
                        </tbody>

                    </table>


                </div>
                <div class="tab__content-item index__content " data-content="3">
                    <table>

                        <colgroup>

                        </colgroup>

                        <thead>
                        <tr>
                            <th>Сотрудник</th>
                            <th>Ср.</th>
                            <th>План</th>
                            <th>Вып.</th>
                            <th>%</th>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
                            <th>7</th>
                            <th>8</th>
                            <th>9</th>
                            <th>10</th>
                            <th>11</th>
                            <th>12</th>
                            <th>13</th>
                            <th>14</th>
                            <th>15</th>
                            <th>16</th>
                            <th>17</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="prize first-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr class="prize second-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td  class="red"><div>150</div></td>
                            <td  class="red"><div>206</div></td>
                            <td  class="red"><div>120</div></td>
                            <td  class="red"><div>134</div></td>
                        </tr>
                        <tr class="prize third-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="green"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="green"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="green"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="green"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td class="green"><div>150</div></td>
                            <td class="green"><div>206</div></td>
                            <td class="red"><div>120</div></td>
                            <td class="red"><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td class="red"><div>150</div></td>
                            <td class="red"><div>206</div></td>
                            <td class="red"><div>120</div></td>
                            <td class="red"><div>134</div></td>
                        </tr>
                        </tbody>

                    </table>


                </div>
                <div class="tab__content-item index__content " data-content="4">
                    <table>

                        <colgroup>

                        </colgroup>

                        <thead>
                        <tr>
                            <th>Сотрудник</th>
                            <th>Ср.</th>
                            <th>План</th>
                            <th>Вып.</th>
                            <th>%</th>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
                            <th>7</th>
                            <th>8</th>
                            <th>9</th>
                            <th>10</th>
                            <th>11</th>
                            <th>12</th>
                            <th>13</th>
                            <th>14</th>
                            <th>15</th>
                            <th>16</th>
                            <th>17</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="prize first-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr class="prize second-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td  class="red"><div>150</div></td>
                            <td  class="red"><div>206</div></td>
                            <td  class="red"><div>120</div></td>
                            <td  class="red"><div>134</div></td>
                        </tr>
                        <tr class="prize third-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="green"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="green"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="green"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="green"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td class="green"><div>150</div></td>
                            <td class="green"><div>206</div></td>
                            <td class="red"><div>120</div></td>
                            <td class="red"><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td class="red"><div>150</div></td>
                            <td class="red"><div>206</div></td>
                            <td class="red"><div>120</div></td>
                            <td class="red"><div>134</div></td>
                        </tr>
                        </tbody>

                    </table>


                </div>
                <div class="tab__content-item index__content " data-content="5">
                    <table>

                        <colgroup>

                        </colgroup>

                        <thead>
                        <tr>
                            <th>Сотрудник</th>
                            <th>Ср.</th>
                            <th>План</th>
                            <th>Вып.</th>
                            <th>%</th>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
                            <th>7</th>
                            <th>8</th>
                            <th>9</th>
                            <th>10</th>
                            <th>11</th>
                            <th>12</th>
                            <th>13</th>
                            <th>14</th>
                            <th>15</th>
                            <th>16</th>
                            <th>17</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="prize first-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr class="prize second-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td  class="red"><div>150</div></td>
                            <td  class="red"><div>206</div></td>
                            <td  class="red"><div>120</div></td>
                            <td  class="red"><div>134</div></td>
                        </tr>
                        <tr class="prize third-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="green"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="green"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="green"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="green"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td class="green"><div>150</div></td>
                            <td class="green"><div>206</div></td>
                            <td class="red"><div>120</div></td>
                            <td class="red"><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td class="red"><div>150</div></td>
                            <td class="red"><div>206</div></td>
                            <td class="red"><div>120</div></td>
                            <td class="red"><div>134</div></td>
                        </tr>
                        </tbody>

                    </table>


                </div>
                <div class="tab__content-item index__content " data-content="6">
                    <table>

                        <colgroup>

                        </colgroup>

                        <thead>
                        <tr>
                            <th>Сотрудник</th>
                            <th>Ср.</th>
                            <th>План</th>
                            <th>Вып.</th>
                            <th>%</th>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
                            <th>7</th>
                            <th>8</th>
                            <th>9</th>
                            <th>10</th>
                            <th>11</th>
                            <th>12</th>
                            <th>13</th>
                            <th>14</th>
                            <th>15</th>
                            <th>16</th>
                            <th>17</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="prize first-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr class="prize second-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td  class="red"><div>150</div></td>
                            <td  class="red"><div>206</div></td>
                            <td  class="red"><div>120</div></td>
                            <td  class="red"><div>134</div></td>
                        </tr>
                        <tr class="prize third-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="green"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="green"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="green"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="green"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td class="green"><div>150</div></td>
                            <td class="green"><div>206</div></td>
                            <td class="red"><div>120</div></td>
                            <td class="red"><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td class="red"><div>150</div></td>
                            <td class="red"><div>206</div></td>
                            <td class="red"><div>120</div></td>
                            <td class="red"><div>134</div></td>
                        </tr>
                        </tbody>

                    </table>


                </div>
                <div class="tab__content-item index__content " data-content="7">
                    <table>

                        <colgroup>

                        </colgroup>

                        <thead>
                        <tr>
                            <th>Сотрудник</th>
                            <th>Ср.</th>
                            <th>План</th>
                            <th>Вып.</th>
                            <th>%</th>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
                            <th>7</th>
                            <th>8</th>
                            <th>9</th>
                            <th>10</th>
                            <th>11</th>
                            <th>12</th>
                            <th>13</th>
                            <th>14</th>
                            <th>15</th>
                            <th>16</th>
                            <th>17</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="prize first-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr class="prize second-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td  class="red"><div>150</div></td>
                            <td  class="red"><div>206</div></td>
                            <td  class="red"><div>120</div></td>
                            <td  class="red"><div>134</div></td>
                        </tr>
                        <tr class="prize third-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="green"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="green"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="green"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="green"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td class="green"><div>150</div></td>
                            <td class="green"><div>206</div></td>
                            <td class="red"><div>120</div></td>
                            <td class="red"><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td class="red"><div>150</div></td>
                            <td class="red"><div>206</div></td>
                            <td class="red"><div>120</div></td>
                            <td class="red"><div>134</div></td>
                        </tr>
                        </tbody>

                    </table>


                </div>
                <div class="tab__content-item index__content " data-content="8">
                    <table>

                        <colgroup>

                        </colgroup>

                        <thead>
                        <tr>
                            <th>Сотрудник</th>
                            <th>Ср.</th>
                            <th>План</th>
                            <th>Вып.</th>
                            <th>%</th>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
                            <th>7</th>
                            <th>8</th>
                            <th>9</th>
                            <th>10</th>
                            <th>11</th>
                            <th>12</th>
                            <th>13</th>
                            <th>14</th>
                            <th>15</th>
                            <th>16</th>
                            <th>17</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="prize first-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr class="prize second-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td  class="red"><div>150</div></td>
                            <td  class="red"><div>206</div></td>
                            <td  class="red"><div>120</div></td>
                            <td  class="red"><div>134</div></td>
                        </tr>
                        <tr class="prize third-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="green"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="green"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="green"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="green"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td class="green"><div>150</div></td>
                            <td class="green"><div>206</div></td>
                            <td class="red"><div>120</div></td>
                            <td class="red"><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td class="red"><div>150</div></td>
                            <td class="red"><div>206</div></td>
                            <td class="red"><div>120</div></td>
                            <td class="red"><div>134</div></td>
                        </tr>
                        </tbody>

                    </table>


                </div>
                <div class="tab__content-item index__content " data-content="9">
                    <table>

                        <colgroup>

                        </colgroup>

                        <thead>
                        <tr>
                            <th>Сотрудник</th>
                            <th>Ср.</th>
                            <th>План</th>
                            <th>Вып.</th>
                            <th>%</th>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
                            <th>7</th>
                            <th>8</th>
                            <th>9</th>
                            <th>10</th>
                            <th>11</th>
                            <th>12</th>
                            <th>13</th>
                            <th>14</th>
                            <th>15</th>
                            <th>16</th>
                            <th>17</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="prize first-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr class="prize second-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td  class="red"><div>150</div></td>
                            <td  class="red"><div>206</div></td>
                            <td  class="red"><div>120</div></td>
                            <td  class="red"><div>134</div></td>
                        </tr>
                        <tr class="prize third-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="green"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="green"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="green"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="green"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="green"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="green"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="green"><div>146</div></td>
                            <td class="green"><div>150</div></td>
                            <td class="green"><div>206</div></td>
                            <td class="red"><div>120</div></td>
                            <td class="red"><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="red"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td ><div>150</div></td>
                            <td ><div>206</div></td>
                            <td ><div>120</div></td>
                            <td ><div>134</div></td>
                        </tr>
                        <tr>
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td class="blue"><div>3026.00</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>260</div></td>
                            <td class="red"><div>3</div></td>
                            <td class="red"><div>216</div></td>
                            <td class="red"><div>151</div></td>
                            <td class="red"><div>239</div></td>
                            <td class="red"><div>191</div></td>
                            <td class="red"><div>295</div></td>
                            <td class="red"><div>275</div></td>
                            <td class="red"><div>280</div></td>
                            <td class="green"><div>228</div></td>
                            <td class="red"><div>146</div></td>
                            <td class="red"><div>150</div></td>
                            <td class="red"><div>206</div></td>
                            <td class="red"><div>120</div></td>
                            <td class="red"><div>134</div></td>
                        </tr>
                        </tbody>

                    </table>


                </div>

            </div>

        </div>
    </div>


</div>
</template>

<script>
export default {
    name: "CompareIndicators", 
    props: {},
    data: function () {
        return {
            fields: [], 
        };
    },
    methods: {
        
    }
};
</script>