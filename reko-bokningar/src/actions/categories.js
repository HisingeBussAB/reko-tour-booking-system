import {apiPost} from './api-post';

export function getCategories(indata = {}) {
  return (dispatch) => {
    dispatch({type: 'LOADING', payload: true});
    apiPost('/tours/category/get' , indata)
      .then(response => {
        if (response.data.category !== undefined) {
          dispatch({
            type: 'TOURS_SAVE_CATEGORIES',
            payload: response.data.category
          });
        }
        dispatch({type: 'LOADING', payload: false});
      })
      .catch(error => {
        dispatch({
          type: 'ERROR_POPUP',
          payload: error.response.data.response
        });
        dispatch({type: 'LOADING', payload: false});
      });


      

    
  };
}