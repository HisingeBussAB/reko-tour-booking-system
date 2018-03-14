import {apiPost} from './api-post';
import {errorPopup} from './error-popup';
import {getToken} from './get-token';

export function getCategories(action, indata = {}) {
  return (dispatch) => {
    dispatch({type: 'LOADING', payload: true});
    apiPost('/tours/category/get' , indata)
      .then(response => {
        console.log(response);
        dispatch({
          type: 'TOURS_SAVE_CATEGORIES',
          payload: response.data.category
        });
        dispatch({type: 'LOADING', payload: false});
      })
      .catch(error => {
        console.log(error);
        dispatch({
          type: 'ERROR_POPUP',
          payload: error.response.data.response
        });
        dispatch({type: 'LOADING', payload: false});
      });


      

    
  };
}

export function setCategories(action, indata = {}) {
  return (dispatch) => {
    dispatch({type: 'LOADING', payload: true});
    apiPost('/tours/category/save' , indata)
      .then(response =>{
        console.log(response);
        console.log("response");
        dispatch(getCategories('get', {
          user: indata.user,
          jwt: indata.jwt,
          categoryid: response.data.modifiedid,
        })
        );
      })
      .catch(error => {
        console.log(error);
        dispatch({
          type: 'ERROR_POPUP',
          payload: error.response.data.response
        });
        dispatch({type: 'LOADING', payload: false});
      });
      

      
    

    
  };
}