
export function errorPopup (status) {
  return function (dispatch) {
    dispatch({
      type   : 'ERROR_POPUP',
      payload: status
    })
  }
}
