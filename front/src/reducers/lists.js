import {mergeObjectArrays} from '../utils'

export default function lists (state = {}, action) {
  switch (action.type) {
    case 'DATA_GROUPCUSTOMERS_SAVE':
      if (action.payload.id === 'all' || typeof state.groupcustomers === 'undefined') {
        return {...state, groupcustomers: action.payload.groupcustomers}
      } else {
        return {...state, groupcustomers: mergeObjectArrays(state.groupcustomers, action.payload.groupcustomers, 'id')}
      }
    case 'DATA_CUSTOMERS_SAVE':
      if (action.payload.id === 'all' || typeof state.customers === 'undefined') {
        return {...state, customers: action.payload.customers}
      } else {
        return {...state, customers: mergeObjectArrays(state.customers, action.payload.customers, 'id')}
      }
    case 'DATA_NEWSLETTER_SAVE':
      if (action.payload.id === 'all' || typeof state.newsletter === 'undefined') {
        return {...state, newsletter: action.payload.newsletter}
      } else {
        return {...state, newsletter: mergeObjectArrays(state.newsletter, action.payload.newsletter, 'id')}
      }
      case 'DATA_BOOKINGSSEARCHLIST_SAVE':
      if (action.payload.id === 'all' || typeof state.bookingssearchlist === 'undefined') {
        return {...state, bookingssearchlist: action.payload.bookingssearchlist}
      } else {
        return {...state, bookingssearchlist: mergeObjectArrays(state.bookingssearchlist, action.payload.bookingssearchlist, 'id')}
      }
    default:
      return state
  }
}
