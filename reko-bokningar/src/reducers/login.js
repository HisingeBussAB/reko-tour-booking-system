export default function login(state = {}, action) {

  switch(action.type){
  
  case 'LOGIN':
    return {...state, ...action.payload};
  default:
    return state;

  }
}