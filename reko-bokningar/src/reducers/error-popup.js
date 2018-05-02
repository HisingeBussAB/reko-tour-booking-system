export default function errorPopup (state = {}, action) {
  switch (action.type) {
    case 'ERROR_POPUP':
      return {...state, ...action.payload}
    default:
      return state
  }
}
