import React from 'react'
import { render } from 'react-dom'
import { Provider } from 'react-redux'
import { ConnectedRouter } from 'react-router-redux'
import { Route } from 'react-router-dom'
import registerServiceWorker from './registerServiceWorker'
import store, { history } from './store'
import Loadable from 'react-loadable'
import Loader from './components/loader'

import '../node_modules/bootstrap/dist/css/bootstrap.min.css'
import '../node_modules/react-datepicker/dist/react-datepicker.css'
import './styles/index.css'

Storage.prototype.setObject = function (key, value) {
  this.setItem(key, JSON.stringify(value))
}

Storage.prototype.getObject = function (key) {
  const value = this.getItem(key)
  return value && JSON.parse(value)
}

const target = document.querySelector('#root')

const App = Loadable({
  loader: () => import('./App'),
  // eslint-disable-next-line
  loading: () => <Loader fullScreen={true} />,
})

render(
  <Provider store={store}>
    <ConnectedRouter history={history}>
      <div>
        <Route component={App} />
      </div>
    </ConnectedRouter>
  </Provider>,
  target
)
registerServiceWorker()
