export default function networkOperations (state = [], action) {
  switch (action.type) {
    case 'NET_START':
      return state.concat([action.payload])
    case 'NET_STOP':
      return state.filter(el => el !== action.payload)
    default:
      return state
  }
}
