import { createStore, applyMiddleware, compose } from 'redux'
import { routerMiddleware } from 'react-router-redux'
import thunk from 'redux-thunk'
import createHistory from 'history/createBrowserHistory'
import rootReducer from '../modules'
import Config from '../config/config'

export const history = createHistory()

const initialState = {
  errorPopup: {
    visible   : false,
    message   : '',
    suppressed: false
  },
  login: {
    login      : false,
    user       : 'anonymous',
    jwt        : '',
    autoAttempt: Config.AutoLogin
  },
  tours: {
    categories: []
  },
  networkOperations: []
}

const enhancers = []
const middleware = [
  thunk,
  routerMiddleware(history)
]

if (process.env.NODE_ENV === 'development') {
  const freeze = require('redux-freeze')
  middleware.push(freeze)
  const devToolsExtension = window.window.__REDUX_DEVTOOLS_EXTENSION__

  if (typeof devToolsExtension === 'function') {
    enhancers.push(devToolsExtension())
  }
}

const composedEnhancers = compose(
  applyMiddleware(...middleware),
  ...enhancers
)

const store = createStore(
  rootReducer,
  initialState,
  composedEnhancers
)

export default store
