
import { combineReducers } from 'redux';
import { routerReducer } from 'react-router-redux';
import login from '../reducers/login';
import errorPopup from '../reducers/error-popup';
import loading from '../reducers/loading';
import tours from '../reducers/tours';


export default combineReducers({
  routing: routerReducer,
  login,
  errorPopup,
  loading,
  tours,
});