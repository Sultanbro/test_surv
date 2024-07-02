<script setup lang="ts">
import * as API from '@/stores/api/reflinker.mock'
import { useToast } from 'vue-toastification'

const toast = useToast()

const sites = ref(['http://job.bpartners.kz/'])
const site = ref('http://job.bpartners.kz/')


const links = ref<Array<API.RefLink>>([])
const siteLinks = computed(() => {
  return links.value.filter(link => link.site === site.value)
})
loadLinks()
async function loadLinks(){
  const result = await API.fetchRefLinks()
  if('errors' in result){
    return toast.error('Не удалось получить ссылки!')
  }
  links.value = result
  result.forEach(link => {
    if(!sites.value.includes(link.site)) sites.value.push(link.site)
  })
}
function addLink(){
  links.value.push({
    id: 0,
    name: '',
    info: '',
    site: site.value
  })
}
async function saveLink(index: number) {
  const id = await API.saveRefLink(siteLinks.value[index])
  if(typeof id === 'number'){
    siteLinks.value[index].id = id
    return toast.success('Сохранено')
  }
  toast.error('Не удалось сохранить ссылку!')
}
async function deleteLink(index: number) {
  if(!confirm('Вы уверены?')) return
  const link = siteLinks.value[index]
  const trueIndex = links.value.findIndex(l => l === link)
  if(!link.id) return links.value.splice(trueIndex, 1) && toast.success('Удалено')
  const id = await API.deleteRefLink(link)
  if(typeof id === 'number') return links.value.splice(trueIndex, 1) && toast.success('Удалено')
  toast.error('Не удалось удвлить ссылку!')
}

async function copyLink(index: number){
  const link = siteLinks.value[index]
  try {
    await navigator.clipboard.writeText(site.value + link.name);
    toast.info('Ссылка на страницу скопирована!')
  }
  catch (error) {
    toast.error('Не удалось скопировать ссылку!')
  }
}
</script>

<template>
  <VRow class="RefLinker">
    <VCol cols="12">
      <VCard>
        <VCardTitle>
          Генерация реферальных ссылок
          <VIcon
            size="24"
            icon="mdi-refresh"
            @click="loadLinks"
          />
        </VCardTitle>
        <VCardText>
          <VRow>
            <VCol cols=3>
              <VSelect
                label="Сайт"
                v-model="site"
                :items="sites"
              />
            </VCol>
          </VRow>
          <VTable>
            <thead>
              <th class="text-left" />
              <th class="text-left">Код</th>
              <th class="text-center">Информация</th>
              <th class="text-center">Действия</th>
            </thead>
            <tbody>
              <tr
                v-for="link, index in siteLinks"
                :key="index"
              >
                <td class="text-left">{{ index + 1 }}</td>
                <td class="text-left">
                  <input v-model="link.name" class="RefLinker-input">
                </td>
                <td class="text-center">
                  <input v-model="link.info" class="RefLinker-input">
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
                    @click="saveLink(index)"
                  />
                  <VBtn
                    density="compact"
                    icon="mdi-trash-can"
                    color="white"
                    class="text-red mx-4"
                    @click="deleteLink(index)"
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

<style lang="scss">
.RefLinker{
  &-input{
    border: none;
    background-color: transparent;
  }
}
</style>
