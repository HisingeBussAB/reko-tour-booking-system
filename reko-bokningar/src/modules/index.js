
import { combineReducers } from 'redux';
import { routerReducer } from 'react-router-redux';
import login from '../reducers/login';
import errorPopup from '../reducers/error-popup';


export default combineReducers({
  routing: routerReducer,
  login,
  errorPopup,
});