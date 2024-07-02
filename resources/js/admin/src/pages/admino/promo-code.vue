<script setup lang="ts">
import { useToast } from 'vue-toastification'
import axios from 'axios';

interface IPromo{
  name: string
  code: string
  expired_at: string
  rate: string
}

const toast = useToast()

function addLink(){
  links.value.push({
    name: '',
    code: '',
    expired_at: '',
    rate: ''
  })
}

const links = ref<Array<IPromo>>([])

const getPromo = async () => {
  try {
    const response = await axios.get('promo-codes')
    return links.value = response.data.data as IPromo[]
  } catch (error) {
    console.error(error)
  }
}
const postPromo = async (promo: IPromo) => {
  const toast = useToast();
  console.log(promo)
  try {
    await axios.post('promo-codes', {

        name: promo.name,
        code: promo.code,
        expired_at: promo.expired_at,
        rate: promo.rate,

    });
    toast.success('Промокод успешно сохранен');
  } catch (error) {
    toast.error('Ошибка при отправке промокода');
    console.error(error);
  }
};

async function copyLink(index: number){
  const link = links.value[index]
  try {
    await navigator.clipboard.writeText( link.name);
    toast.info('Ссылка на страницу скопирована!')
  }
  catch (error) {
    toast.error('Не удалось скопировать ссылку!')
  }
}

const deleteCode = async (code: string) => {

  const toast = useToast();

  try {
    await axios.delete('promo-codes', {
      data: { code: code }
    });
    toast.success('Промокод успешно отправлен');
  } catch (error) {
    toast.error('Ошибка при отправке промокода');
    console.error(error);
  }
}

function handleExpiredAtChange(promo: IPromo, date: string) {
  const time = promo.expired_at.split(' ')[1] || '00:00:00';
  promo.expired_at = `${date} ${time}`;
}
onMounted(() => {
  getPromo()
})
watch(links, (newValue, oldValue) => {
  console.log(newValue);
});
</script>

<template>
  <VRow class="RefLinker">
    <VCol cols="12">
      <VCard>
        <VCardTitle>
          Генерация промокода
          <VIcon
            size="24"
            icon="mdi-refresh"
          />
        </VCardTitle>
        <VCardText>
          <VTable>
            <thead>
            <th class="text-left" />
            <th class="text-center">Название</th>
            <th class="text-center">Код</th>
            <th class="text-center">Дата окончания</th>
            <th class="text-center">Ставка</th>
            <th class="text-center">Действия</th>
            </thead>
            <tbody>
            <tr
              v-for="link, index in links"
              :key="index"
            >
              <td class="text-left">{{ index + 1 }}</td>
              <td class="text-center ">
                <input v-model="link.name" class="Promo-code text-center">
              </td>
              <td class="text-center  promo-data" >
                <input v-model="link.code" class="Promo-code text-center">
              </td>
              <td class="text-center  promo-data" >
                <input type="date" :value="link.expired_at.split(' ')[0]" class="Promo-code" @change="handleExpiredAtChange(link, $event.target.value)">
              </td>
              <td class="text-center  promo-data" >
                <input v-model="link.rate" class="Promo-code text-center">
              </td>
              <td class="text-center">
                <VBtn
                  density="compact"
                  icon="mdi-content-copy"
                  color="white"
                  class="text-green mx-4"
                  @click="copyLink(index)"
                />
                <VBtn
                  density="compact"
                  icon="mdi-content-save"
                  color="white"
                  class="text-blue mx-4"
                  @click="postPromo(link)"
                />
                <VBtn
                  density="compact"
                  icon="mdi-trash-can"
                  color="white"
                  class="text-red mx-4"
                  @click="deleteCode(link.code)"
                />
              </td>
            </tr>
            </tbody>
          </VTable>
          <VBtn @click="addLink">Добавить</VBtn>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<style lang="scss" scoped>


.Promo-code{
  margin: 0;
  font-size: 16px;
  border: 1px solid #8f8f8f;
  justify-content: center;
  border-radius: 12px;
}
</style>
