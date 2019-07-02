import {mergeObjectArrays} from '../utils'

export default function tours (state = {}, action) {
  switch (action.type) {
    case 'DATA_CATEGORIES_SAVE':
      if (action.payload.id === 'all' || typeof state.categories === 'undefined') {
        return {...state, categories: action.payload.categories}
      } else {
        return {...state, categories: mergeObjectArrays(state.categories, action.payload.categories, 'id')}
      }
    case 'DATA_TOURS_SAVE':
      if (action.payload.id === 'all' || typeof state.tours === 'undefined') {
        return {...state, tours: action.payload.tours}
      } else {
        return {...state, tours: mergeObjectArrays(state.tours, action.payload.tours, 'id')}
      }
    case 'DATA_BOOKINGS_SAVE':
      if (action.payload.id === 'all' || typeof state.bookings === 'undefined') {
        return {...state, bookings: action.payload.bookings}
      } else {
        return {...state, bookings: mergeObjectArrays(state.bookings, action.payload.bookings, 'id')}
      }
    default:
      return state
  }
}
