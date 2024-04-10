
<template>
  <VCard class="faq-card">
    <VCardTitle class="faq-card-header">
      <div>Статьи</div>
      <VSpacer />
      <div class="faq-card-actions">
        <VBtn

          variant="text"
          color="green-darken-2"
          size="small"
          @click="saveFAQ"
          v-if="isEdit"
        >
          Сохранить
        </VBtn>
        <VBtn
          variant="text"
          icon="mdi-pencil"
          color="blue-darken-2"
          size="small"
          @click="isEdit = !isEdit"

        />
      </div>
    </VCardTitle>
    <VDivider />
    <VContainer class="faq-card-body">
      <VRow>
        <VCol cols="3" class="faq-card-list ">
          <div class="scrollable flex-grow-1">
            <ArticleList :items="items"
            @selectItem="selectItem"
            />

            <p
                v-if="items.length < 1"
              class="no-questions"
            >
              Добавитьте новый вопрос
            </p>
          </div>

          <div class="faq-list-add">
            <VBtn
              block
              @click="addItem"
            >Добавить</VBtn>
          </div>
        </VCol>
        <VCol cols="9" clasa="faq-card-content">
          <ArticleContent
          :isEdit="isEdit"
          :selectedItem="selectedItem"
          />
        </VCol>
      </VRow>
    </VContainer>
  </VCard>
</template>


<script>
import ArticleContent from '@/views/pages/article/ArticleContent.vue';
import ArticleList from '@/views/pages/article/ArticleList.vue';


    export default {
        components: {
            ArticleContent,
            ArticleList
        },

        data() {
            return {
                selectedItem:null,
                isEdit:false,
                items: [
                    {
                        id: 1,
                        title: 'Item #1',
                        subtitle: 'subtitle',
                        content:null,
                    },
                ],
            }
        },

        methods: {
            selectItem(newItem) {
                this.selectedItem = newItem
            },

            addItem() {
                const newId = this.items.length > 0 ? this.items[this.items.length - 1].id + 1 : 1;
                const newItem = {
                    id: newId,
                    title: `Item #${newId}`,  // Changed 'name' to 'title' for consistency
                    subtitle: 'subtitle',
                    content:null,
                };
                this.items.push(newItem);
                this.selectItem(newItem);
            }  
        },
    }
</script>



<style lang="scss">
.faq-card {
  display: flex;
  flex-flow: column;
  // 48px - pa-6  in layout
  min-height: calc(100vh - (48px + var(--v-layout-top) + var(--v-layout-bottom)));
  ul {
    list-style: none;
  }
}

.faq-card-header{
  display: flex;
  align-items: center;
}

.faq-card-body{
  flex: 1;
  display: flex;
  flex-flow: column;
  max-height: calc(100% - 59px);
  padding: 12px;
  > .v-row{
    flex: 1;
    max-height: calc(100% + 24px);
  }
}

.faq-card-list{
  display: flex;
  flex-flow: column;
  max-height: 100%;
  outline: 1px solid rgba(var(--v-border-color),var(--v-border-opacity));
}

.faq-list-add {
  padding: 0 15px;
}

.no-questions{
  opacity: 0.7;
  padding: 0 15px;
  margin-bottom: 20px;
}

.faq-card-content{
  display: flex;
  flex-flow: column;
  height: 100%;
}
</style>




