export default function loading(state = {}, action) {

  switch(action.type){
  
  case 'LOADING':
    return {inprogress: action.payload};
  default:
    return state;
  }
}