import {networkAction, errorPopup} from '..'
import {apiPost, firebaseSavedItem} from '../../functions'
import {itemNameTranslation} from '../../data/valid-api-calls'
import {getItem} from './get-item'

export function saveItem (itemType, data, operation) {
  return async (dispatch, getState) => {
    const login = Object.freeze(getState().login)
    if (itemNameTranslation.hasOwnProperty(itemType)) {
      dispatch(networkAction(1, 'save new ' + itemType))
      try {
        data.user = login.user
        data.jwt = login.jwt
        const response = await apiPost('/tours/' + itemType + '/' + operation, data)
        let temp
        try { temp = response.data.modifiedid } catch (e) { temp = 'all' }
        const id = operation === 'delete' ? 'all' : temp
        temp = undefined
        await dispatch(getItem(itemType, id))
        firebaseSavedItem(id, itemType)
        return true
      } catch (e) {
        try {
          if (typeof e.response.data.response !== 'undefined' && e.response.data.response.length > 0) {
            dispatch(errorPopup({visible: true, message: e.response.data.response, suppressed: false}))
          } else {
            dispatch(errorPopup({visible: true, message: 'Kunde utföra åtgärden.\nOkänt eller inget svar från API.', suppressed: false}))
          }
          return false
        } catch (e) {
          dispatch(errorPopup({visible: true, message: 'Kunde utföra åtgärden.\nOkänt eller inget svar från API.', suppressed: false}))
          return false
        }
      } finally {
        dispatch(networkAction(0, 'save new ' + itemType))
      }
    } else {
      dispatch(errorPopup({visible: true, message: 'En felaktigt formaterad förfrågan till APIt har blockerats', suppressed: false}))
      return false
    }
  }
}
