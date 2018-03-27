import {apiPost} from './api-post';


export function onThenCategory(dispatch, response) {
  try {
    if (response.data.success === false) {
      const msg = 'Ett fel har uppstått: ' + response.data.response;
      dispatch({
        type: 'ERROR_POPUP',
        payload: {visible: true, message: msg},
      });
    } else {
      dispatch({
        type: 'TOURS_CATEGORIES_SAVE',
        payload: {id: response.data.requestedid, category: response.data.category}
      });
    }
  } catch(e) {
    dispatch({
      type: 'ERROR_POPUP',
      payload: {visible: true, message: 'Felformaterat eller okänt svar från API.'}
    });
  }
  dispatch({type: 'LOADING_STOP', payload: false});
}

export function onCatchCategory(dispatch, error) {
  let message;
  try {
    message = error.response.data.response;
  } catch (e) {
    message = 'Ett fel har uppstått under hämtning av kategorier.';
  }
  dispatch({
    type: 'ERROR_POPUP',
    payload: {visible: true, message: message}
  });
  dispatch({type: 'LOADING_STOP', payload: false});
}

export function getCategories(indata) {
  return (dispatch) => {
    dispatch({type: 'LOADING_START', payload: true});
    apiPost('/tours/category/get' , indata)
      .then(response => {
        onThenCategory(dispatch, response);
      })
      .catch(error => {
        onCatchCategory(dispatch, error);
      });
  };
}
