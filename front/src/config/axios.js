import axios from 'axios'
import Config from './config'

const myAxios = axios.create({
  baseURL: Config.ApiUrl,
  timeout: 7000,
  headers: {'X-API-Key': Config.ApiToken, 'Content-Type': 'application/json'}
})

export default myAxios
