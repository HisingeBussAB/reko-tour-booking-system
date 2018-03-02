export default function errorPopup(state = {}, action) {

  switch(action.type){
  
  case 'ERROR_POPUP':
    return action.payload;
  default:
    return state;

  }
}