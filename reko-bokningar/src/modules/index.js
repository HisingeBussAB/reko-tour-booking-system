import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import login from '../reducers/login'
import errorPopup from '../reducers/error-popup'
import tours from '../reducers/tours'
import networkOperations from '../reducers/network-icon'

export default combineReducers({
  routing: routerReducer,
  login,
  errorPopup,
  networkOperations,
  tours
})
