import { combineReducers } from 'redux'
import login from '../reducers/login'
import errorPopup from '../reducers/error-popup'
import infoPopup from '../reducers/info-popup'
import tours from '../reducers/tours'
import lists from '../reducers/lists'
import budgets from '../reducers/budgets'
import networkOperations from '../reducers/network-icon'
import pendingfromweb from '../reducers/pendingfromweb'
import web from '../reducers/web'

export default combineReducers({
  login,
  errorPopup,
  infoPopup,
  tours,
  lists,
  budgets,
  pendingfromweb,
  web,
  networkOperations
})
