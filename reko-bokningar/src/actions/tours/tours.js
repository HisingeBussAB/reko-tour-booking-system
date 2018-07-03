export function onThenTour (dispatch, response) {
  try {
    if (response.data.success === false) {
      const msg = 'Ett fel har uppstått: ' + response.data.response
      dispatch({
        type   : 'ERROR_POPUP',
        payload: {visible: true, message: msg}
      })
    } else {
      dispatch({
        type   : 'TOURS_TOUR_SAVE',
        payload: {id: response.data.requestedid, tour: response.data.tours}
      })
    }
  } catch (e) {
    dispatch({
      type   : 'ERROR_POPUP',
      payload: {visible: true, message: 'Felformaterat eller okänt svar från API.'}
    })
  }
}

export function onCatchTour (dispatch, error) {
  let message
  try {
    message = error.response.data.response
  } catch (e) {
    message = 'Ett fel har uppstått under hämtning av resor.'
  }
  dispatch({
    type   : 'ERROR_POPUP',
    payload: {visible: true, message: message}
  })
}
