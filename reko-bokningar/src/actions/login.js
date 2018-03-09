import Config from '../config/config';
import axios from 'axios';
import {errorPopup} from './error-popup'

export function Login(logindata) {
  
  return (dispatch) =>{
    axios.post( Config.ApiUrl + '/token/login', {
      apitoken: Config.ApiToken,
    })
      .then(response => {
        axios.post( Config.ApiUrl + '/auth', {
          user: logindata.user,
          pwd: logindata.pwd,
          apitoken: Config.ApiToken,
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
              //do nothing. default login fail state for payload already set
            }
            dispatch({type: 'LOGIN', payload: payload}
            );
          })
          .catch(error => {
            let errormsg = 'Ett fel har uppstått i inloggningen.';
            try {
              if (error.response.data.response !== undefined) {
                errormsg = error.response.data.response;
              }
            } catch(e) {
              errormsg = 'Ett fel har uppstått i inloggningen. Felformaterat eller inget svar från API.';
            }
            dispatch(errorPopup({visible: true, message: errormsg}));
            dispatch({type: 'LOGIN', payload: {autoAttempt: false}});
          });
      })
      .catch(error => {
        let errormsg = 'Ett fel har uppstått i inloggningen vid begäran av säkerhetstoken.';
        try {
          if (error.response.data.response !== undefined) {
            errormsg = error.response.data.response;
          }
        } catch(e) {
          errormsg = 'Ett fel har uppstått i inloggningen vid begäran av säkerhetstoken. Felformaterat eller inget svar från API.';
        }
        dispatch(errorPopup({visible: true, message: errormsg}));
        dispatch({type: 'LOGIN', payload: {autoAttempt: false}});
      });
    

    
  };
}