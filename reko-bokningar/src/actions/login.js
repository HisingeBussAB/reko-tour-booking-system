import {myAxios} from '../config/axios';
import {errorPopup} from './error-popup';
import {getToken} from './get-token';

export function Login(logindata) {

  return async (dispatch) => {
    const errprep = logindata.auto ? 'Automatisk inlogging misslyckades!\n' : '';
    dispatch({type: 'LOADING_START', payload: true});
    try {
      const token = await getToken('login');
      const response = await myAxios.post( '/auth', {
        user: logindata.user,
        pwd: logindata.pwd,
        logintoken: token.data.logintoken
      });
      let payload;
      if (logindata.isOnce) {
        payload = {
          login: false,
          autoAttempt: true,
        };
      } else {
        payload = {
          login: false,
          autoAttempt: false,
        };
      }
      try {
        if (typeof (response.data.login !== undefined) {
          payload = {...payload, ...response.data};
        }
      } catch(e) {
        dispatch(errorPopup({visible: true, message: errprep + 'Okänt svar från API.'}));
      }
      localStorage.setObject('user', {
        user: payload.user,
        userid: payload.once.userid,
        tokenid: payload.once.tokenid,
        token: payload.once.token,
        expires: payload.once.expires
      });
      payload.once = null; //clean once login before redux
      dispatch({type: 'LOGIN', payload: payload});
      dispatch({type: 'LOADING_STOP', payload: false});
    } catch (error) {
      let errormsg = errprep + 'Ett fel har uppstått i inloggningen.';
      try {
        if (error.response.data.response !== undefined) {
          errormsg = errprep + error.response.data.response;
        } else {
          errormsg = errprep + 'Felformaterat eller inget svar från server.';
        }
      } catch(e) {
        errormsg = errprep + 'Felformaterat eller inget svar från server.';
      }
      dispatch(errorPopup({visible: true, message: errormsg}));
      dispatch({type: 'LOGIN', payload: {autoAttempt: false}});
      dispatch({type: 'LOADING_STOP', payload: false});
      throw error;
    }
  };

}
