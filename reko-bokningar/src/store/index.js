import { createStore, applyMiddleware, compose } from 'redux';
import { routerMiddleware } from 'react-router-redux';
import thunk from 'redux-thunk';
import createHistory from 'history/createBrowserHistory';
import rootReducer from '../modules';
import Config from '../config/config';

export const history = createHistory();

const initialState = {
  errorPopup: {
    visible: false,
    message: '',
    suppressed: false,
  },
  login: {
    login: false,
    user: 'anonymous',
    jwt: '',
    autoAttempt: Config.AutoLogin,
  },
  loading: {
    inprogress: true,
    flags: null,
  },  
  tours: {
    categories: [],
  }
};
const enhancers = [];
const middleware = [
  thunk,
  routerMiddleware(history)
];

//eslint-disable-next-line
if (process.env.NODE_ENV === 'development') {
  const devToolsExtension = window.window.__REDUX_DEVTOOLS_EXTENSION__;

  if (typeof devToolsExtension === 'function') {
    enhancers.push(devToolsExtension());
  }
}

const composedEnhancers = compose(
  applyMiddleware(...middleware),
  ...enhancers
);

const store = createStore(
  rootReducer,
  initialState,
  composedEnhancers
);

export default store;