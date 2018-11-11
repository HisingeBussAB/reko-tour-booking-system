import {errorPopup} from '../error-popup'
import {networkAction} from '..'
import Config from '../../config/config'
import {Login} from './login'

export function LoginRefresh (retry) {
  return async (dispatch) => {
    dispatch(networkAction(1, 'login auto refresh'))
    const userObject = localStorage.getObject('user')
    const usr = {
      pwd           : Config.AutoLoginPwd,
      user          : Config.AutoUsername,
      refreshToken  : userObject.refreshToken,
      refreshExpires: userObject.refreshExpires
    }

    dispatch(Login(usr))
      .then(() => {})
      .catch(() => {
        dispatch(errorPopup({visible: true, message: 'Fel i automatisk uppdatering av inloggning!\nÄndringar kommer antagligen inte kunna sparas.\nProva uppdatera sidan (F5).', suppressed: false}))
        if (retry) {
          const retryTimer = setTimeout(() => {
            dispatch(errorPopup({visible: false, message: '', suppressed: false}))
            dispatch(LoginRefresh(false))
            clearTimeout(retryTimer)
          }, 2000)
        }
      })
      .finally(() => {
        dispatch(networkAction(0, 'login auto refresh'))
      })
  }
}
