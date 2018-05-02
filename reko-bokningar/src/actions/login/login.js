import myAxios from '../../config/axios'
import {errorPopup} from '../error-popup'
import {getToken} from '../get-token'
import {networkAction} from '../'

export function Login (logindata) {
  return async (dispatch) => {
    dispatch(networkAction(1, 'login action'))
    const errprep = logindata.auto ? 'Automatisk inlogging misslyckades!\n' : ''
    try {
      const token = await getToken('login')
      const response = await myAxios.post('/auth', {
        user      : logindata.user,
        pwd       : logindata.pwd,
        logintoken: token.data.logintoken
      })
      let payload
      if (logindata.isOnce) {
        payload = {
          login      : false,
          autoAttempt: true
        }
      } else {
        payload = {
          login      : false,
          autoAttempt: false
        }
      }
      try {
        if (typeof response.data.login !== 'undefined') {
          payload = {...payload, ...response.data}
        }
      } catch (e) {
        dispatch(errorPopup({visible: true, message: errprep + 'Okänt svar från API.', suppressed: true}))
        dispatch(networkAction(0, 'login action'))
      }
      localStorage.setObject('user', {
        user   : payload.once.user,
        tokenid: payload.once.tokenid,
        token  : payload.once.token,
        expires: payload.once.expires
      })
      payload.once = null // clean once login before redux
      dispatch({type: 'LOGIN', payload: payload})
      dispatch(networkAction(0, 'login action'))
    } catch (error) {
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
      dispatch({type: 'LOGIN', payload: {autoAttempt: false}})
      dispatch(networkAction(0, 'login action', dispatch))
      throw error
    }
  }
}
