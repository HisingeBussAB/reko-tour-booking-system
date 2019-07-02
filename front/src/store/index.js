import { createStore, applyMiddleware, compose } from 'redux'
import { connectRouter, routerMiddleware } from 'connected-react-router'
import thunk from 'redux-thunk'
import { createBrowserHistory } from 'history'
import rootReducer from '../modules'
import Config from '../config/config'

export const history = createBrowserHistory()

const initialState = {
  errorPopup: {
    visible   : false,
    message   : '',
    suppressed: false
  },
  login: {
    login         : false,
    user          : 'anonymous',
    accessToken   : '',
    refreshToken  : '',
    autoAttempt   : Config.AutoLogin,
    accessExpires : 0,
    refreshExpires: 0
  },
  tours: {
    categories: [],
    tours     : [],
    bookings  : []
  },
  lists: {
    groupcustomers: [],
    newsletter    : []
  },
  web: {
    webtours: [],
    webcategories: [],
    webrooms: []
  },
  pendingfromweb: {
    count     : {},
    bookings  : [],
    leads     : [],
    newsletter: []
  },
  networkOperations: [],
}

const enhancers = []
const middleware = [
  routerMiddleware(history),
  thunk
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
  connectRouter(history)(rootReducer),
  initialState,
  composedEnhancers
)

export default store
