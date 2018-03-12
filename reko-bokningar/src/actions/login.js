import Config from '../config/config';
import {myAxios} from '../config/axios';
import {errorPopup} from './error-popup';
import {getToken} from './get-token';

export function Login(logindata) {
  
  const errprep = logindata.auto ? 'Automatisk inlogging misslyckades! ' : '';
  return async (dispatch) => {
    try {
      let token = await getToken('login');
      let post = await myAxios.post( '/auth', {
        user: logindata.user,
        pwd: logindata.pwd,
        logintoken: token.data.logintoken,
        apitoken: Config.ApiToken
      });
      dispatch({
        type: 'POST',
        payload: post
      });
      dispatch({type: 'LOGIN', payload: errprep});
    } catch (error) {
      dispatch('error: ' + error);
      dispatch(errorPopup({visible: true, message: errprep}));
      throw error;
    }
  };

  /*
  return dispatch => {  
    getToken('login')
      .then(response => {
        return myAxios.post( '/auth', {
          user: logindata.user,
          pwd: logindata.pwd,
          logintoken: response.data.logintoken,
        })
          .then(response => {
            let payload = {
              login: false,
              autoAttempt: false,
            };         
            try {
              if (response.data.login !== undefined) {
                payload = {...payload, ...response.data};
              }
            } catch(e) {
              dispatch(errorPopup({visible: true, message: errprep + 'Okänt svar från API.'}));
            }
            dispatch({type: 'LOGIN', payload: payload});
          })
          .catch(error => {
            let errormsg = errprep + 'Ett fel har uppstått i inloggningen.';
            try {
              if (error.response.data.response !== undefined) {
                errormsg = errprep + error.response.data.response;
              }
            } catch(e) {
              errormsg = errprep + 'Ett fel har uppstått i inloggningen. Felformaterat eller inget svar från API.';
            }
            dispatch(errorPopup({visible: true, message: errormsg}));
            dispatch({type: 'LOGIN', payload: {autoAttempt: false}});
            throw error;
          });

      })
      .catch(error => {
        let errormsg = errprep + 'Ett fel har uppstått i inloggningen vid begäran av säkerhetstoken.';
        try {
          if (error.response.data.response !== undefined) {
            errormsg = errprep + error.response.data.response;
          }
        } catch(e) {
          errormsg = errprep + 'Ett fel har uppstått i inloggningen vid begäran av säkerhetstoken. Felformaterat eller inget svar från API.';
        }
        dispatch(errorPopup({visible: true, message: errormsg}));
        dispatch({type: 'LOGIN', payload: {autoAttempt: false}});
      });

  };*/
}