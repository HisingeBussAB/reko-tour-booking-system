import myAxios from '../../config/axios'
import {errorPopup} from '../error-popup'
import {networkAction} from '..'

export function Login (usr) {
  return async (dispatch) => {
    dispatch(networkAction(1, 'login action'))
    const errprep = 'Inloggningsförsök misslyckades! '
    const isBearer = typeof usr.refreshToken === 'string' && usr.refreshToken.length > 5
    const auth = isBearer
      ? 'Bearer ' + usr.refreshToken
      : 'Basic ' + btoa(usr.user + ':' + usr.pwd)
    const url = isBearer ? 'auth/refresh' : 'auth'
    const data = isBearer ? {user: usr.user} : null

    myAxios.defaults.headers.common['Authorization'] = auth
    try {
      const response = await myAxios.post('/users/' + url, data)
      let payload = {login: false}
      try {
        if (typeof response.data.login !== 'undefined') {
          payload = {...payload, ...response.data}
        }
      } catch (e) {
        dispatch(errorPopup({visible: true, message: errprep + 'Okänt svar från API.', suppressed: true}))
        dispatch(networkAction(0, 'login action'))
        localStorage.setObject('user', {
          user          : usr.user,
          refreshToken  : false,
          refreshExpires: 0
        })
      }
      localStorage.setObject('user', {
        user          : usr.user,
        refreshToken  : payload.refresh.token,
        refreshExpires: payload.refresh.expires
      })
      const reduxUser = {
        login         : payload.login,
        user          : usr.user,
        accessToken   : payload.access.token,
        refreshToken  : payload.refresh.token,
        accessExpires : payload.access.expires,
        refreshExpires: payload.refresh.expires
      }
      myAxios.defaults.headers.common['Authorization'] = 'Bearer ' + payload.access.token

      dispatch({type: 'LOGIN', payload: reduxUser})
      dispatch(networkAction(0, 'login action'))
    } catch (error) {
      localStorage.setObject('user', {
        user          : usr.user,
        refreshToken  : false,
        refreshExpires: 0
      })
      let errormsg = errprep + 'Ett fel har uppstått i inloggningen.'
      try {
        if (error.response.data.response !== undefined) {
          errormsg = errprep + error.response.data.response
        } else {
          errormsg = errprep + 'Felformaterat eller inget svar från server.'
        }
      } catch (e) {
        errormsg = errprep + 'Felformaterat eller inget svar från server.'
      }
      dispatch(errorPopup({visible: true, message: errormsg, suppressed: true}))
      dispatch(networkAction(0, 'login action', dispatch))
      throw error
    }
  }
}
