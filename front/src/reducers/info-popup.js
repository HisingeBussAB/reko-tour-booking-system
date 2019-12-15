export default function infoPopup (state = {}, action) {
  switch (action.type) {
    case 'INFO_POPUP':
      return {...state, ...action.payload}
    default:
      return state
  }
}
