import myAxios from '../config/axios';

export async function getToken(tokentype) {

  try {
    return await myAxios.post( '/token/' + tokentype);
  } catch (error) {
    throw error;
  }
}
