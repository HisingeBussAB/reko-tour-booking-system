import Config from '../config/config';
import axios from 'axios';

export function getToken(tokentype) {
  
  

  return dispatch => {
    
    return axios.post( Config.ApiUrl + '/token/' + tokentype, {
      apitoken: Config.ApiToken,
    }).then(
      response => {
        // Reducers may handle this to show the data and reset isFetching
        dispatch({ type: 'GET_USER_SUCCESS', tokentype,  response })
      },
      error => {
        // Reducers may handle this to reset isFetching
        dispatch({ type: 'GET_USER_FAILURE', tokentype,  error })
        // Rethrow so returned Promise is rejected
        throw error;
      }
    );
  
  };
}