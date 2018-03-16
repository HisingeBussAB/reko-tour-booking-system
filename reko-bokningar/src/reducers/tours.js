export default function tours(state = {}, action) {
  
  switch(action.type){

  case 'TOURS_CATEGORIES_SAVE':
    if (action.payload.id === 'all') {
      return {categories: action.payload.category};
    } else {
      let result = state.categories;
      result[action.payload.id] = action.payload.category[action.payload.id];
      return {categories: result};
    }
  default:
    return state;

  }
}
