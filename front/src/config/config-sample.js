
const Config = {
  ApiUrl        : 'url to api', // Base url to api except /api
  ApiToken      : 'secret',
  AutoLoginPwd  : 'secret',
  AutoUsername  : 'username for auto login',
  AutoLogin     : true, // auto login attempt true/false
  OnceLoginToken: '+token+', // static token for one time login thru local storage
  FirebaseLogin : '',
  FirebasePwd   : ''
}

export const firebaseconfig = {
  apiKey           : '',
  authDomain       : '',
  databaseURL      : '',
  projectId        : '',
  storageBucket    : '',
  messagingSenderId: ''
}

export default Config
