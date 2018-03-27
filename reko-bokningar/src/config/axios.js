import axios from 'axios';
import Config from './config';

const myAxios = axios.create({
  baseURL: Config.ApiUrl,
  timeout: 7000,
  headers: {'Authorization': Config.ApiToken},
});

export default myAxios;
