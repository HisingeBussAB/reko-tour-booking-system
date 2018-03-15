import axios from 'axios';
import Config from './config';

export const myAxios = axios.create({
  baseURL: Config.ApiUrl,
  timeout: 7000,
  headers: {'Authorization': Config.ApiToken},
});


