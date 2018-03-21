import {merge_object_arrays} from '../utils';

export default function tours(state = {}, action) {

  switch(action.type){

  case 'TOURS_CATEGORIES_SAVE':
    if (action.payload.id === 'all') {
      return {categories: action.payload.category};
    } else {

      return {categories: merge_object_arrays(state.categories, action.payload.category, 'id')};
    }
  default:
    return state;

  }
}

