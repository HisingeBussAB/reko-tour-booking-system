import {networkAction, errorPopup} from '..'
import {firebaseSavedItem} from '../../functions'
import myAxios from '../../config/axios'
import {itemNameHuman} from '../../data/valid-api-calls'
import {getItem} from './get-item'

export function postItem (itemType, data) {
  return async (dispatch, getState) => {
    if (itemNameHuman.hasOwnProperty(itemType)) {
      dispatch(networkAction(1, 'post new ' + itemType))
      try {
        const response = await myAxios.post('/' + itemType, data)
        const id = response.data.response.updatedid
        dispatch(getItem(itemType, id))
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
        dispatch(networkAction(0, 'post new ' + itemType))
      }
    } else {
      dispatch(errorPopup({visible: true, message: 'En felaktigt formaterad förfrågan till APIt har blockerats', suppressed: false}))
      return false
    }
  }
}
