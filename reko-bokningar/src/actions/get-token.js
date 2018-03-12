import Config from '../config/config';
import {myAxios} from '../config/axios';

export async function getToken(tokentype) {
  
  
  try {
    return await myAxios.post( Config.ApiUrl + '/token/' + tokentype, {
      apitoken: Config.ApiToken
    });
  } catch (error) {
    throw error;
  }
}