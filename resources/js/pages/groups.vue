<template>
  <div>
    <a-alert v-if="message != null" :message="message" type="info" showIcon />

    <div class="row align-items-center">
      <div class="col-lg-3 col-md-6">
        <b-form-select
          v-model="activebtn"
          :options="statuses"
          size="md"
          @change="selectGroup"
          class="group-select col-lg-6 d-flex"
        >
          <template #first>
            <b-form-select-option :value="null" disabled
              >Выберите группу из списка</b-form-select-option
            >
          </template>
        </b-form-select>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="add-grade">
          <input type="text" class="form-control" v-model="new_status" />
          <button @click="addStatus" class="btn btn-success">
            Добавить группу
          </button>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 я" style="text-align: right">
        <div class="d-flex justify-content-between">
          <button
            class="btn btn-info rounded add-s ml-2"
            @click="showArchiveModal = true"
            title="Восстановить из архива"
          >
            <i class="fa fa-archive"></i>
          </button>
          <p class="mb-0">Название</p>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <input type="text" class="form-control" v-model="gname" />
      </div>
    </div>

    <div v-if="activebtn != null" class="row">
      <div class="col-lg-6 mb-3 mt-4">
        <div class="dialerlist bg mb-2">
          <div class="fl">Время работы с</div>
          <div class="fl">
            <input
              type="time"
              v-model="timeon"
              class="form-control scscsc"
              name="start_time"
            />
            <span class="before">до</span>
            <input
              type="time"
              v-model="timeoff"
              value=""
              class="form-control"
              name="end_time"
            />
          </div>
        </div>

        <div class="dialerlist bg mb-2">
          <div class="fl">
            Подтягивать время
            <i class="fa fa-cogs ml-2" @click="editTimeAddress()"></i>
          </div>
          <div class="fl">
            <input
              type="text"
              v-model="time_address_text"
              class="form-control scscsc"
              style="background: #fff"
              disabled
            />
          </div>
        </div>

     

        <div class="dialerlist bg mb-2">
          <div class="fl">ID диалера 
             <i class="fa fa-info-circle ml-2" 
                v-b-popover.hover.right.html="'Нужен, чтобы <b>подтягивать часы</b> или <b>оценки диалогов</b> для контроля качества.<br>С сервиса cp.callibro.org'" 
                title="Диалер в U-Calls">
            </i>
          </div>
          <div class="fl d-flex">
            <input type="text" v-model="dialer_id" placeholder="ID" class="form-control scscsc" />
            <input type="number" v-model="script_id" placeholder="ID скрипта" class="form-control scscsc" />
          </div>
        </div>

        <div class="dialerlist bg mb-2 py-1">
          <div class="fl">Оценки контроля качества
            <i class="fa fa-info-circle ml-2" 
                v-b-popover.hover.right.html="'Заполнять оценки диалогов и критерии на странице <b>Контроль качества</b>, либо подтягивать их по крону с cp.callibro.org'" 
                title="Оценки контроля качества">
            </i>
          </div>
          <div class="fl d-flex">
            <b-form-radio v-model="quality"  name="some-radios" value="ucalls" class="mr-3">C U-calls</b-form-radio>
            <b-form-radio v-model="quality"  name="some-radios" value="local">Ручная</b-form-radio>
          </div>
        </div>


        <div class="dialerlist bg mb-2">
          <div class="fl">Кол-во рабочих дней</div>
          <div class="fl">
            <input
              type="number"
              v-model="workdays"
              class="form-control scscsc"
              min="1"
              max="7"
            />
          </div>
        </div>

        <div class="dialerlist bg mb-2 py-1">
          <b-form-checkbox
            v-model="editable_time"
            :value="1"
            :unchecked-value="0"
          >
            Табель редактируется
          </b-form-checkbox>
        </div>

        <div class="dialerlist bg mb-2 py-1">
          <b-form-checkbox
            v-model="paid_internship"
            :value="1"
            :unchecked-value="0"
          >
            Оплачиваемая стажировка
          </b-form-checkbox>
        </div>

        <button
          @click="showKPI = !showKPI"
          class="btn btn-primary rounded mr-2"
        >
          <i class="fa fa-star-half-o"></i> KPI группы
        </button>

        <button
          @click="showBonuses = !showBonuses"
          class="btn btn-primary mr-2 rounded"
        >
          <i class="fa fa-star-half-o"></i> Бонусы
        </button>
      </div>

      <div class="col-lg-6 mb-3 mt-4 sssz">
        <div class="dialerlist blu">
          <multiselect
            v-model="bgs"
            :options="bookgroups"
            :multiple="true"
            :close-on-select="false"
            :clear-on-select="false"
            :preserve-search="true"
            placeholder="Выберите группы книг"
            label="name"
            track-by="name"
            :taggable="true"
            @tag="addTag"
          >
          </multiselect>
        </div>
        <div class="dialerlist blu">
          <multiselect
            v-model="corps"
            :options="corp_books"
            :multiple="true"
            :close-on-select="false"
            :clear-on-select="false"
            :preserve-search="true"
            placeholder="Выберите корп книги"
            label="name"
            track-by="name"
            :taggable="true"
            @tag="addTag"
          >
          </multiselect>
        </div>

        <div class="blu">
          <b-form-checkbox
            class="mt-3"
            v-model="show_payment_terms"
            :value="1"
            :unchecked-value="0"
          >
            Показывать в профиле
          </b-form-checkbox>

          <h5 class="mt-3">Условия оплаты труда</h5>
          <textarea v-model="payment_terms" class="form-control"></textarea>
        </div>
      </div>

      <div class="col-lg-12 mb-3 mt-3">
        <h6 class="mb-2">Сотрудники</h6>
        <div class="dialerlist">
          <div class="fl" style="flex-direction: column">
            <multiselect
              v-model="value"
              :options="options"
              :multiple="true"
              :close-on-select="false"
              :clear-on-select="false"
              :preserve-search="true"
              placeholder="Выберите"
              label="email"
              track-by="email"
              :taggable="true"
              @tag="addTag"
            >
            </multiselect>
            <a href="#" @click="showAlert()">Удалить всех пользователей</a>
          </div>
        </div>
      </div>
      <div class="col-lg-12 mb-3">
        <button @click="saveusers" class="btn btn-success mr-2 rounded">
          Сохранить
        </button>
        <button
          @click.stop="deleted"
          class="btn btn-danger mr-2 rounded"
        >
          <i class="fa fa-trash"></i> Удалить группу
        </button>
      </div>

      <sidebar
        title="KPI"
        :open="showKPI"
        @close="showKPI = false"
        v-if="showKPI"
        width="72%"
      >
        <t-kpi
          :activeuserid="activeuserid"
          :is_admin="true"
          :group="activebtn"
          :group_id="group_id"
        />
      </sidebar>

      <sidebar
        title="Бонусы"
        :open="showBonuses"
        @close="showBonuses = false"
        v-if="showBonuses"
        width="72%"
      >
        <table class="table table-bordered table-sm">
          <tr>
            <th class="left mark">Наименование</th>
            <th class="mark">Активность</th>
            <th class="mark">Ед.изм</th>
            <th class="mark">Кол-во</th>
            <th class="mark">ПД</th>
            <th class="mark">Сумма , тг</th>
            <th class="mark">Описание</th>
          </tr>

          <tr v-for="(bonus, index) in bonuses" :key="index">
            <td>
              <input
                type="text"
                class="form-control form-control-sm"
                v-model="bonus.title"
              />
            </td>
            <td class="left">
              <select
                v-model="bonus.activity_id"
                class="form-control form-control-sm"
              >
                <option :value="0">Нет активности</option>
                <option
                  :value="activity.id"
                  v-for="activity in activities"
                  :key="activity.id"
                >
                  {{ activity.name }}
                </option>
              </select>
            </td>
            <td class="left">
              <select v-model="bonus.unit" class="form-control form-control-sm">
                <option
                  :value="unit.value"
                  v-for="unit in units"
                  :key="unit.value"
                >
                  {{ unit.title }}
                </option>
              </select>
            </td>
            <td class="left">
              <input
                type="text"
                class="form-control form-control-sm"
                v-model="bonus.quantity"
              />
            </td>
            <td class="left">
              <select
                v-model="bonus.daypart"
                class="form-control form-control-sm"
              >
                <option
                  :value="daypart.value"
                  v-for="daypart in dayparts"
                  :key="daypart.value"
                >
                  {{ daypart.title }}
                </option>
              </select>
            </td>
            <td class="left">
              <input
                type="text"
                class="form-control form-control-sm"
                v-model="bonus.sum"
              />
            </td>
            <td class="left">
              <textarea
                class="form-control form-control-sm"
                v-model="bonus.text"
              ></textarea>
            </td>
          </tr>
        </table>

        <p v-if="showAfterEdit">
          Не забудьте нажать на кнопку "Сохранить", чтобы сохранить изменения и
          удаления
        </p>

        <div class="d-flex">
          <button
            class="btn btn-success btn-sm rounded mr-2"
            @click="saveBonus"
          >
            Сохранить
          </button>
          <button class="btn btn-primary btn-sm rounded" @click="addBonus">
            Добавить
          </button>
          <button
            class="btn btn-danger btn-sm rounded"
            v-if="showDeleteButton"
            @click="before_deleteBonus"
          >
            Удалить
          </button>
        </div>
      </sidebar>

      <b-modal id="bv-modal" hide-footer>
        <template #modal-title> Подтвердите удаление </template>
        <div class="d-block">
          <p>
            Вы уверены, что хотите удалить выбранные бонусы? На прошедшие дни
            это не повлияет.
          </p>
        </div>
        <div class="d-flex">
          <b-button
            class="mt-3 mr-1"
            variant="danger"
            block
            @click="deleteBonus"
            >Удалить</b-button
          >
          <b-button
            variant="primary"
            class="mt-3 ml-1"
            block
            @click="$bvModal.hide('bv-modal')"
            >Отмена</b-button
          >
        </div>
      </b-modal>
    </div>

    <!-- Modal  -->
    <a-modal
      v-model="showEditTimeAddress"
      title="Подтягивать часы"
      @ok="saveTimeAddress()"
      :width="700"
      class="modalle"
    >
      <div class="row">
        <div class="col-5 mt-1">
          <p class="">
            Источник часов

            <i
              class="fa fa-info-circle"
              v-b-popover.hover.right.html="'При смене источника, новые данные в табеле будут только со дня смены источника'"
            />


          </p>
        </div>
        <div class="col-7">
          <select class="form-control form-control-sm" v-model="time_address">
            <option
              :value="key"
              v-for="(time, key) in time_variants"
              :key="key"
            >
              {{ time }}
            </option>
          </select>
        </div>
      </div>

      <div class="row mt-1">
        <div class="col-12">
          <p class="">Исключения

            <i
              class="fa fa-info-circle"
              v-b-popover.hover.right.html="'Часы выбранных сотрудников, не будут копироваться из аналитики в табель'"
            />
          </p>
        </div>
        <div class="col-12 mt-1">
          <multiselect
            v-model="time_exceptions"
            :options="time_exceptions_options"
            :multiple="true"
            :close-on-select="false"
            :clear-on-select="false"
            :preserve-search="true"
            placeholder="Выберите, кого не связывать"
            label="email"
            track-by="email"
            :taggable="true"
            @tag="addExceptionTag"
          >
          </multiselect>
        </div>
      </div>
    </a-modal>

    <!-- Modal restore archived group -->
    <b-modal
      v-model="showArchiveModal"
      size="md"
      title="Восстановить из архива"
      @ok="restoreGroup()"
      class="modalle"
    >
      <div>
        <div class="col-5">
          <p class="">Группа</p>
        </div>
        <div class="col-7">
          <select v-model="restore_group" class="form-control form-control-sm">
            <option
              :value="archived_group.name"
              v-for="(archived_group, key) in archived_groups"
              :key="key"
            >
              {{ archived_group.name }}
            </option>
          </select>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script>
import "ant-design-vue/dist/antd.css";
export default {
  name: "groups",
  props: [
    "statuseses",
    "book_groups",
    "corpbooks",
    "activeuserid",
    "archived_groupss",
  ],
  data() {
    return {
      message: null,
      activebtn: null,
      statuses: [],
      bonuses: [],
      activities: [],
      restore_group: null,
      new_status: "",
      value: [], // selected users
      options: [], // users options
      bgs: [], // selected bookgrroups
      corps: [], // selected corp_books
      bookgroups: [], // bookgroups options
      corp_books: [], // corp_books options
      archived_groups: [],
      payment_terms: "", // Условия оплаты труда в группе
      timeon: "09:00",
      timeoff: "18:00",
      dialer_id: null,
      script_id: null,
      // time edit
      time_address: 0,
      editable_time: 0,
      time_address_text: "Из табеля",
      time_variants: [],
      workdays: 5,
      quality: 'local',
      time_exceptions: [],
      time_exceptions_options: [],
      showEditTimeAddress: false,
      paid_internship: 0,
      show_payment_terms: 1,
      zarplata: "0",
      showKPI: false,
      showBonuses: false,
      showDeleteButton: false,
      showArchiveModal: false,
      showAfterEdit: false,
      group_id: 0,
      gname: "", // Название группы
      zoom_link: "", // Ссылка zoom для обучения стажеров
      bp_link: "",
      units: [
        {
          title: "За единицу",
          value: "one",
        },
        {
          title: "За все",
          value: "all",
        },
      ],
      dayparts: [
        {
          title: "Весь день",
          value: 0,
        },
        {
          title: "09:00 - 13:00",
          value: 1,
        },
        {
          title: "14:00 - 19:00",
          value: 2,
        },
        {
          title: "12:00 - 16:00",
          value: 3,
        },
        {
          title: "17:00 - 21:00",
          value: 4,
        },
      ],
    };
  },
  created() {
    axios.post("/timetracking/users", {}).then((response) => {
      this.options = response.data.users;
    });
  },

  mounted() {
    this.statuses = JSON.parse(this.statuseses);
    this.archived_groups = JSON.parse(this.archived_groupss);
    this.bookgroups = JSON.parse(this.book_groups);
    this.corp_books = JSON.parse(this.corpbooks);
  },

  methods: {
    saveBonus() {
      axios
        .post("/timetracking/users/bonus/save", {
          bonuses: this.bonuses,
        })
        .then((response) => {
          this.$message.info("Успешно сохранено");
          this.bonuses = response.data.bonuses;
          this.messageoff();
        })
        .catch((error) => {
          console.log(error.response);
          this.$message.info(error.response);
        });
    },

    addBonus() {
      this.bonuses.push({
        id: 0,
        title: "Нет названия",
        sum: 0,
        group_id: this.group_id,
        activity_id: 0,
        unit: "one",
        quantity: 0,
        daypart: 0,
      });
    },

    addTag(newTag) {
      const tag = {
        email: newTag,
        ID: newTag,
      };
      this.options.push(tag);
      this.value.push(tag);
    },

    addExceptionTag(newTag) {
      const tag = {
        email: newTag,
        ID: newTag,
      };
      this.time_exceptions_options.push(tag);
      this.time_exceptions.push(tag);
    },

    addBookGroupTag(newTag) {
      const tag = {
        bookgroup: newTag,
        id: newTag,
      };
      this.bookgroups.push(tag);
      this.bgs.push(tag);
    },

    addCorpBookTag(newTag) {
      const tag = {
        corp: newTag,
        id: newTag,
      };
      this.corps.push(tag);
    },

    messageoff() {
      setTimeout(() => {
        this.message = null;
      }, 3000);
    },
    selectGroup() {


      let loader = this.$loading.show();

      axios
        .post("/timetracking/users", {
          group: this.activebtn,
        })
        .then((response) => {
          if (response.data) {
            this.gname = response.data.name;
            this.value = response.data.users;
            this.bgs = response.data.book_groups;
            this.timeon = response.data.timeon;
            this.timeoff = response.data.timeoff;
            this.group_id = response.data.group_id;
            this.zoom_link = response.data.zoom_link;
            this.bp_link = response.data.bp_link;
            this.dialer_id = response.data.dialer_id;
            this.script_id = response.data.script_id;
            this.quality = response.data.quality;
            this.corps = response.data.corp_books;
            this.bonuses = response.data.bonuses;
            this.activities = response.data.activities;
            this.payment_terms = response.data.payment_terms;
            this.time_address = response.data.time_address;
            this.workdays = response.data.workdays;
            this.paid_internship = response.data.paid_internship;
            this.show_payment_terms = response.data.show_payment_terms;
            this.statuses = response.data.groups;
            this.archived_groups = response.data.archived_groups;

            this.editable_time = response.data.editable_time;
            if (this.time_address != -1 || this.time_address != 0)
              this.time_address_text = "Из аналитики";
            if (this.time_address == -1) this.time_address_text = "Из U-calls";
            if (this.time_address == 0) this.time_address_text = "Из табеля";

            loader.hide();
          } else {
            this.value = [];
          }
        });
    },

    saveusers() {
      // save group data
      let loader = this.$loading.show();

      axios
        .post("/timetracking/users/group/save", {
          group: this.activebtn,
          gname: this.gname,
          users: this.value,
          book_groups: this.bgs,
          corp_books: this.corps, 
          timeon: this.timeon,
          timeoff: this.timeoff,
          zoom_link: this.zoom_link,
          workdays: this.workdays,
          script_id: this.script_id,
          dialer_id: this.dialer_id,
          bp_link: this.bp_link,
          payment_terms: this.payment_terms,
          editable_time: this.editable_time,
          quality: this.quality,
          paid_internship: this.paid_internship,
          show_payment_terms: this.show_payment_terms,
        })
        .then((response) => {
          this.statuses = Object.values(response.data.groups);
          this.activebtn = response.data.group;
          this.$message.info("Успешно сохранено");
          this.messageoff();

          loader.hide();
        })
        .catch((error) => {
          console.log(error.response);
          this.$message.info(error.response);
          loader.hide();
        });
    },
    addStatus() {
      if (this.new_status.length > 0) {
        axios
          .post("/timetracking/group/save", {
            group: this.new_status,
          })
          .then((response) => {
            if (response.data.status == 1) {
              this.$message.success("Добавлено");
              this.statuses.push(this.new_status);
            } else {
              this.$message.error(
                'Название "' +
                  this.new_status +
                  '" не свободно, выберите другое имя для группы!'
              );
            }

            this.selectGroup(this.new_status);
            this.new_status = "";
          });
      }
    },
    deleted() {
      if (confirm("Вы уверены что хотите удалить группу?")) {
        axios
          .post("/timetracking/group/delete", {
            group: this.activebtn,
          })
          .then((response) => {
            this.$message.info("Удалена");
          });

        let ind = this.statuses.indexOf(status);
        if (index > -1) this.statuses.splice(ind, 1);
        this.statuses.splice(ind, 1);
        this.activebtn = null;
      }
    },
    showAlert() {
      if (confirm("Вы уверены что хотите удалить всех пользователей?")) {
        this.value = [];
      }
    },

    before_deleteBonus() {
      this.bonuses.forEach((bonus, key) => {
        if (bonus.checked) this.$bvModal.show("bv-modal");
      });
    },
    deleteBonus() {
      this.bonuses.forEach((bonus, key) => {
        this.showAfterEdit = true;
        if (bonus.checked) bonus.deleted = true;
        this.$bvModal.hide("bv-modal");
      });
    },

    editTimeAddress() {
      this.showEditTimeAddress = true;

      axios
        .post("/timetracking/settings/get_time_addresses", {
          group_id: this.activebtn,
        })
        .then((response) => {
          this.time_variants = response.data.time_variants;
          this.time_exceptions_options = response.data.time_exceptions_options;
          this.time_exceptions = response.data.time_exceptions;
        })
        .catch((error) => {
          alert(error);
        });
    },

    restoreGroup() {
      if (!confirm("Вы уверены что хотите восстановить группу?")) {
        return "";
      }

      let loader = this.$loading.show();
      axios
        .post("/timetracking/groups/restore", {
          id: this.restore_group,
        })
        .then((response) => {
          this.$message.success("Восстановлен!");
          this.selectGroup(this.restore_group);
          this.restore_group = null;
          this.showArchiveModal = false;
          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          this.$message.error("Ошибка!");
          alert(error);
        });
    },

    saveTimeAddress() {
      axios
        .post("/timetracking/settings/save_time_addresses", {
          group_id: this.activebtn,
          time_address: this.time_address,
          time_exceptions: this.time_exceptions,
        })
        .then((response) => {
          if (this.time_address != -1 || this.time_address != 0)
            this.time_address_text = "Из аналитики";
          if (this.time_address == -1) this.time_address_text = "Из U-calls";
          if (this.time_address == 0) this.time_address_text = "Из табеля";

          this.time_address_text =
            this.time_variants !== undefined
              ? this.time_variants[this.time_address]
              : "Из табеля";
        })
        .catch((error) => {
          alert(error);
        });

      this.showEditTimeAddress = false;
    },
  },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="scss">
.ant-tabs {
  overflow: visible;
}

.listprof {
  display: flex;
  flex-wrap: wrap;
  margin-top: 20px;
}

.profitem {
  margin-right: 10px;
  margin-bottom: 5px;
}

.add-grade {
  display: flex;
  max-width: 500px;
}

.dialerlist {
  display: flex;
  align-items: center;
  margin: 0;
  &.bg {
    background: #f1f1f1;
    padding-left: 15px;
  }
  .fl {
    flex: 1;
    display: flex;
    align-items: center;
  }
}

.group-select {
  border-radius: 0;
  max-width: 100%;
}

p.choose {
  line-height: 31px;
  margin-right: 15px;
}
span.before {
  padding: 0 10px;
}
.multiselect__tags {
  border-radius: 0 !important;
}
.multiselect__tag {
  display: block !important;
  max-width: max-content !important;
}
.blu .multiselect__tag {
  background: #017cff !important;
}
@media (min-width: 1000px) {
  .multiselect__tags-wrap {
    flex-wrap: wrap;
    display: flex !important;
  }
  .multiselect__tag {
    flex: 0 0 49%;
    /* margin-left: 1% !important; */
    margin-right: 1% !important;
    max-width: 49% !important;
  }
}

@media (min-width: 1300px) {
  .multiselect__tag {
    flex: 0 0 32%;
    /* margin-left: 1% !important; */
    margin-right: 1% !important;
    max-width: 32% !important;
  }
}
@media (min-width: 1700px) {
  .multiselect__tag {
    flex: 0 0 24%;
    /* margin-left: 1% !important; */
    margin-right: 1% !important;
    max-width: 24% !important;
  }
}
.scscsc {
  margin-left: 15px;
}
.sssz button {
  margin-top: 1px;
}
.add-grade input {
  border-radius: 0;
}
</style>
