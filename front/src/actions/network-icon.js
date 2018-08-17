export function networkAction (status, actionid = 'unnamed network action') {
  return function (dispatch) {
    if (status === 'start' || status === 1 || status === true) {
      dispatch({
        type   : 'NET_START',
        payload: actionid
      })
    } else {
      dispatch({
        type   : 'NET_STOP',
        payload: actionid
      })
    }
  }
}
