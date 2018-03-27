import myAxios from '../config/axios';

export async function apiPost(url, payload) {
  try {
    return await myAxios.post(url, payload);
  } catch (error) {
    throw error;
  }
}
