import axios from "axios";

export const updatePayerStatusBitrix = async (id:string) => {
  await axios.post(`https://infinitys.bitrix24.kz/rest/66/vkjaiufptgrokyk6/crm.automation.trigger/?target=DEAL_${id}&code=n5vbh`)
}