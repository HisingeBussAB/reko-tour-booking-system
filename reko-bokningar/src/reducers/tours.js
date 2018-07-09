import {mergeObjectArrays} from '../utils'

export default function tours (state = {}, action) {
  switch (action.type) {
    case 'TOURS_CATEGORIES_SAVE':
      if (action.payload.id === 'all') {
        return {categories: action.payload.categories}
      } else {
        return {categories: mergeObjectArrays(state.categories, action.payload.categories, 'id')}
      }
    case 'TOURS_TOUR_SAVE':
      if (action.payload.id === 'all') {
        return {tours: action.payload.tour}
      } else {
        return {tours: mergeObjectArrays(state.tours, action.payload.tour, 'id')}
      }
    default:
      return state
  }
}
