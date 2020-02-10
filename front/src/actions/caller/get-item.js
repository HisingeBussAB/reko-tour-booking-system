import {networkAction, errorPopup} from '..'
import myAxios from '../../config/axios'
import {itemNameHuman} from '../../data/valid-api-calls'

export function getItem (itemType, item = 'all', suppressErrors = false) {
  return async (dispatch) => {
    if (itemNameHuman.hasOwnProperty(itemType)) {
      dispatch(networkAction(1, 'get items ' + itemType))
      const itemString = parseInt(item, 10).toString() === item.toString() ? '/' + item : ''
      try {
        const response = await myAxios.get('/' + itemType + itemString)
        dispatch(onThen(response, itemType, item, suppressErrors))
        return true
      } catch (e) {
        dispatch(onCatch(e, itemType, suppressErrors))
        return false
      } finally {
        dispatch(networkAction(0, 'get items ' + itemType))
      }
    } else {
      dispatch(errorPopup({visible: !suppressErrors, message: 'En felaktigt formaterad förfrågan till APIt har blockerats', suppressed: suppressErrors}))
      return false
    }
  }
}

function onThen (response, itemType, item, suppressErrors = false) {
  return (dispatch) => {
    const itemTypeUpper = itemType.toUpperCase()
    try {
      if (response.data.success !== true) {
        const responseMsg = typeof response.data.response === 'undefined' ? 'Felformaterad data från API.\nKunde inte hämta uppgifter.' : response.data.response
        const errorMsg = typeof response.data.error === 'undefined' ? false : response.data.error
        const msg = errorMsg === false ? 'Ett fel har uppstått: ' + responseMsg : 'Ett fel har uppstått: ' + errorMsg
        dispatch(errorPopup({visible: !suppressErrors, message: msg, suppressed: suppressErrors}))
      } else {
        dispatch({
          type   : 'DATA_' + itemTypeUpper + '_SAVE',
          payload: {id: item, [itemType]: response.data.response[itemType]}
        })
      }
    } catch (e) {
      dispatch(errorPopup({visible: !suppressErrors, message: 'Felformaterat eller okänt svar från API.', suppressed: suppressErrors}))
    }
  }
}

function onCatch (error, itemType, suppressErrors = false) {
  return (dispatch) => {
    let message
    try {
      message = typeof error.response.data.error === 'undefined' ? error.response.data.response : error.response.data.error
    } catch (e) {
      message = 'Ett fel har uppstått under hämtning av ' + itemNameHuman[itemType] + '.'
    }
    dispatch(errorPopup({visible: !suppressErrors, message: message, suppressed: suppressErrors}))
  }
}
