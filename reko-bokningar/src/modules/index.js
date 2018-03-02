
import { combineReducers } from 'redux';
import { routerReducer } from 'react-router-redux';
import login from '../reducers/login';
import errorPopup from '../reducers/error-popup';
import styles from '../reducers/change-styles';


export default combineReducers({
  routing: routerReducer,
  login,
  errorPopup,
  styles,
});