export default function pendingfromweb (state = {}, action) {
  switch (action.type) {
    case 'DATA_PENDINGCOUNT_SAVE':
      return {...state, count: action.payload.pendingcount}
    case 'DATA_PENDINGNEWSLETTER_SAVE':
      return {...state, newsletter: action.payload.pendingnewsletter}
    default:
      return state
  }
}
