export default function loading (state = {}, action) {
  switch (action.type) {
  // works the same but easier to read in redux flow
    case 'LOADING':
      return {inprogress: action.payload}
    case 'LOADING_START':
      return {inprogress: action.payload}
    case 'LOADING_STOP':
      return {inprogress: action.payload}
    case 'LOADING_':
      return {inprogress: action.payload}
    default:
      return state
  }
}
