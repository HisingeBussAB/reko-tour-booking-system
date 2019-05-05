import React from 'react'
import { render } from 'react-dom'
import { Provider } from 'react-redux'
import { ConnectedRouter } from 'connected-react-router'
import { Route } from 'react-router'
import registerServiceWorker from './registerServiceWorker'
import store, { history } from './store'
import MyLoadable from './components/loader/myloadable'

import '../node_modules/bootstrap/dist/css/bootstrap.min.css'
import '../node_modules/react-datepicker/dist/react-datepicker.css'
import '../node_modules/react-bootstrap-typeahead/css/Typeahead.css'
import '../node_modules/react-bootstrap-typeahead/css/Typeahead-bs4.css'
import './styles/css/index.css'

Storage.prototype.setObject = function (key, value) {
  this.setItem(key, JSON.stringify(value))
}

Storage.prototype.getObject = function (key) {
  const value = this.getItem(key)
  return value && JSON.parse(value)
}

const target = document.querySelector('#root')

const App = MyLoadable({
  loader: () => import('./App')
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
