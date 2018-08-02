import {mergeObjectArrays} from '../utils'

export default function tours (state = {}, action) {
  switch (action.type) {
    case 'TOURS_CATEGORIES_SAVE':
      if (action.payload.id === 'all' || typeof state.categories === 'undefined') {
        return {...state, categories: action.payload.categories}
      } else {
        return {...state, categories: mergeObjectArrays(state.categories, action.payload.categories, 'id')}
      }
    case 'TOURS_TOURS_SAVE':
      if (action.payload.id === 'all' || typeof state.tours === 'undefined') {
        return {...state, tours: action.payload.tours}
      } else {
        return {...state, tours: mergeObjectArrays(state.tours, action.payload.tours, 'id')}
      }
    default:
      return state
  }
}
