import {networkAction, errorPopup} from '../'
import {apiPost, firebaseSavedItem} from '../../functions'
import {itemNameTranslation, itemNameHuman} from './valid-calls'
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
        // TODO Add verbose in use response from database
        const humanAction = operation === 'delete' ? 'ta bort' : 'spara/ändra'
        const humanDelete = operation === 'delete' ? 'Den här ' + itemNameHuman[itemType] + ' används troligen,\nresor/bokningar måste tas bort först.' : ''
        dispatch(errorPopup({visible: true, message: 'Kunde inte ' + humanAction + ' ' + itemNameHuman[itemType] + '.\n' + humanDelete, suppressed: false}))
        return false
      } finally {
        dispatch(networkAction(0, 'save new ' + itemType))
      }
    } else {
      dispatch(errorPopup({visible: true, message: 'En felaktigt formaterad förfrågan till APIt har blockerats', suppressed: false}))
      return false
    }
  }
}
