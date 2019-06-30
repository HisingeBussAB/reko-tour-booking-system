import {networkAction, errorPopup} from '..'
import axios from 'axios'
import Config from '../../config/config'

const myAxios = axios.create({
  baseURL: Config.WebsiteHeadless,
  timeout: 7000,
  headers: {'Content-Type': 'application/json'},
  credentials: false
})

export function getItemWeb (itemType) {
  return async (dispatch) => {
    if (itemType === 'resor' || itemType === 'boenden' || itemType === 'kategorier') {
      dispatch(networkAction(1, 'get items ' + itemType))
      try {
        const response = await myAxios.get('/' + itemType)
        dispatch(onThen(response, itemType))
        return true
      } catch (e) {
        dispatch(onCatch(e, itemType))
        return false
      } finally {
        dispatch(networkAction(0, 'get items ' + itemType))
      }
    } else {
      dispatch(errorPopup({visible: true, message: 'Kan ignoreras. Fel i laddning från hemsidan. En felaktigt formaterad förfrågan till APIt har blockerats', suppressed: false}))
      return false
    }
  }
}

function onThen (response, itemType, item = 'all') {
  return (dispatch) => {
    const itemTypeUpper = itemType.toUpperCase()
    try {
      if (typeof response.data !== 'object') {
        const responseMsg = typeof response.data === 'undefined' ? ' Fel i laddning från hemsidan.\nKan ignoreras. Kunde inte hämta uppgifter.' : response.data
        const errorMsg = false
        const msg = errorMsg === false ? 'Kan ignoreras.\nFel i laddning från hemsidan. Ett fel har uppstått: ' + responseMsg : 'Kan ignoreras.\nFel i laddning från hemsidan. Ett fel har uppstått: ' + errorMsg
        dispatch(errorPopup({visible: true, message: msg, suppressed: false}))
      } else {
        dispatch({
          type   : 'DATA_WEB_' + itemTypeUpper + '_SAVE',
          payload: {id: item, [itemType]: response.data}
        })
      }
    } catch (e) {
      dispatch(errorPopup({visible: true, message: '\nFel i laddning från hemsidan. Kan ignoreras.', suppressed: false}))
    }
  }
}

function onCatch (error, itemType) {
  return (dispatch) => {
    let message
    try {
      message = 'Ett fel ' + error.message + '\nhar uppstått under hämtning från hemsidan av ' + itemType + '\n.Fel i laddning från hemsidan. Kan ignoreras.'
    } catch (e) {
      message = 'Ett fel har uppstått under hämtning från hemsidan av ' + itemType + '\n.Fel i laddning från hemsidan. Kan ignoreras.'
    }
    dispatch(errorPopup({visible: true, message: message, suppressed: false}))
  }
}
