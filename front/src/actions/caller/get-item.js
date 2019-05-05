import {networkAction, errorPopup} from '..'
import myAxios from '../../config/axios'
import {itemNameHuman} from '../../data/valid-api-calls'

export function getItem (itemType, item = 'all') {
  return async (dispatch) => {
    if (itemNameHuman.hasOwnProperty(itemType)) {
      dispatch(networkAction(1, 'get items ' + itemType))
      const itemString = parseInt(item).toString() === item ? '/' + item : ''
      try {
        const response = await myAxios.get('/' + itemType + itemString)
        dispatch(onThen(response, itemType, item))
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

function onThen (response, itemType, item) {
  return (dispatch) => {
    const itemTypeUpper = itemType.toUpperCase()
    try {
      if (response.data.success !== true) {
        const responseMsg = typeof response.data.response === 'undefined' ? 'Felformaterad data från API.\nKunde inte hämta uppgifter.' : response.data.response
        const errorMsg = typeof response.data.error === 'undefined' ? false : response.data.error
        const msg = errorMsg === false ? 'Ett fel har uppstått: ' + responseMsg : 'Ett fel har uppstått: ' + errorMsg
        dispatch(errorPopup({visible: true, message: msg, suppressed: false}))
      } else {
        dispatch({
          type   : 'DATA_' + itemTypeUpper + '_SAVE',
          payload: {id: item, [itemType]: response.data.response[itemType]}
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
      message = typeof error.response.data.error === 'undefined' ? error.response.data.response : error.response.data.error
    } catch (e) {
      message = 'Ett fel har uppstått under hämtning av' + itemNameHuman[itemType] + '.'
    }
    dispatch(errorPopup({visible: true, message: message, suppressed: false}))
  }
}
