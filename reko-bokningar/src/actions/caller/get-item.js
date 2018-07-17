import {networkAction, errorPopup} from '../'
import {apiPost} from '../../functions'
import {itemNameTranslation, itemNameHuman} from './valid-calls'

export function getItem (itemType, item = 'all') {
  return async (dispatch, getState) => {
    console.log('get start')
    const login = Object.freeze(getState().login)
    if (itemNameTranslation.hasOwnProperty(itemType)) {
      dispatch(networkAction(1, 'get items ' + itemType))
      try {
        const data = {user: login.user, jwt: login.jwt, [itemNameTranslation[itemType]]: item}
        const response = await apiPost('/tours/' + itemType + '/get', data)
        dispatch(onThen(response, itemType))
        return true
      } catch (e) {
        dispatch(onCatch(e, itemType))
        return false
      } finally {
        dispatch(networkAction(0, 'get items ' + itemType))
      }
    } else {
      dispatch(errorPopup({visible: true, message: 'En felaktigt formaterad förfrågan till APIt har blockerats', suppressed: false}))
      return false
    }
  }
}

function onThen (response, itemType) {
  return (dispatch) => {
    console.log('get item then start')
    const itemTypeUpper = itemType.toUpperCase()
    try {
      if (response.data.success !== true) {
        const msg = 'Ett fel har uppstått: ' + response.data.response
        dispatch(errorPopup({visible: true, message: msg, suppressed: false}))
      } else {
        dispatch({
          type   : 'TOURS_' + itemTypeUpper + '_SAVE',
          payload: {id: response.data.requestedid, [itemType]: response.data[itemType]}
        })
      }
    } catch (e) {
      dispatch(errorPopup({visible: true, message: 'Felformaterat eller okänt svar från API.', suppressed: false}))
    }
  }
}

function onCatch (error, itemType) {
  return (dispatch) => {
    let message
    try {
      message = error.response.data.response
    } catch (e) {
      message = 'Ett fel har uppstått under hämtning av' + itemNameHuman[itemType] + '.'
    }
    dispatch(errorPopup({visible: true, message: message, suppressed: false}))
  }
}
