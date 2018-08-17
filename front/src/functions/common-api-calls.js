import myAxios from '../config/axios.js'

export async function getServerTime () {
  try {
    const response = await myAxios.get('/timestamp')
    if (typeof response.data.servertime === 'number') {
      return response.data.servertime
    } else {
      throw new TypeError('Expected timestamp as number, got: ' + typeof response.data.servertime)
    }
  } catch (error) {
    throw error
  }
}
