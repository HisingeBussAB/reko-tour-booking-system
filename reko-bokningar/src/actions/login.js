
export function Login(response) {
  
  return function(dispatch){
    dispatch({
      type: 'LOGIN',
      payload: response
    });
    

    
  };
}