export default function styles(state = {}, action) {

  switch(action.type){
  
  case 'CHANGE_STYLES':
    return {...state, ...action.payload};
  default:
    return state;

  }
}