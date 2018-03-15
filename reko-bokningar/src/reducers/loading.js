export default function loading(state = {}, action) {

  switch(action.type){
  
  case 'LOADING':
    return {inprogress: action.payload};
  case 'LOADING_FLAGS':
    return {flags: action.payload};
  default:
    return state;
  }
}