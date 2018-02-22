import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import { ConnectedRouter } from 'react-router-redux';
import { Route } from 'react-router-dom';
import registerServiceWorker from './registerServiceWorker';
import store, { history } from './store';
import Loadable from 'react-loadable';
import Loader from './components/loader';

import '../node_modules/bootstrap/dist/css/bootstrap.min.css';
import './index.css';

const target = document.querySelector('#root');

const App = Loadable({
  //eslint-disable-next-line
  loader: () => import('./App'),
  loading: () => <Loader fullScreen={true} />,
});

render(
  <Provider store={store}>
    <ConnectedRouter history={history}>
      <div>
        <Route component={App} />
      </div>
    </ConnectedRouter>
  </Provider>,
  target
);
registerServiceWorker();
