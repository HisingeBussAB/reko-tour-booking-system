import {apiPost} from '../../functions'
import {networkAction} from '../'

export function onThenCategory (dispatch, response) {
  try {
    if (response.data.success === false) {
      const msg = 'Ett fel har uppstått: ' + response.data.response
      dispatch({
        type   : 'ERROR_POPUP',
        payload: {visible: true, message: msg}
      })
    } else {
      dispatch({
        type   : 'TOURS_CATEGORIES_SAVE',
        payload: {id: response.data.requestedid, category: response.data.category}
      })
    }
  } catch (e) {
    dispatch({
      type   : 'ERROR_POPUP',
      payload: {visible: true, message: 'Felformaterat eller okänt svar från API.'}
    })
  }
}

export function onCatchCategory (dispatch, error) {
  let message
  try {
    message = error.response.data.response
  } catch (e) {
    message = 'Ett fel har uppstått under hämtning av kategorier.'
  }
  dispatch({
    type   : 'ERROR_POPUP',
    payload: {visible: true, message: message}
  })
}

export function getCategories (indata) {
  return async (dispatch) => {
    dispatch(networkAction(1, 'get categories action'))
    apiPost('/tours/category/get', indata)
      .then(response => {
        onThenCategory(dispatch, response)
        dispatch(networkAction(0, 'get categories action'))
      })
      .catch(error => {
        onCatchCategory(dispatch, error)
        dispatch(networkAction(0, 'get categories action'))
        throw error
      })
  }
}
