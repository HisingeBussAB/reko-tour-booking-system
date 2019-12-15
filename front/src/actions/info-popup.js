
export function infoPopup (status) {
  return function (dispatch) {
    dispatch({
      type   : 'INFO_POPUP',
      payload: status
    })
  }
}
