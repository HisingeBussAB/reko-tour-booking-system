import React, { Component } from 'react';
import MainMenu from './components/main-menu';
import { connect } from 'react-redux';
import Loadable from 'react-loadable';
import Loader from './components/loader';
import { Route } from 'react-router-dom';


const MainView = Loadable({
  //eslint-disable-next-line
  loader: () => import('./components/main-view'),
  loading: () => <Loader fullScreen={false} />,
});

const TourView = Loadable({
  //eslint-disable-next-line
  loader: () => import('./components/tour-view'),
  loading: () => <Loader fullScreen={false} />,
});

const BudgetView = Loadable({
  //eslint-disable-next-line
  loader: () => import('./components/budget-view'),
  loading: () => <Loader fullScreen={false} />,
});

const ListView = Loadable({
  //eslint-disable-next-line
  loader: () => import('./components/list-view'),
  loading: () => <Loader fullScreen={false} />,
});




class App extends Component {

  componentWillMount() {
    /* Auto authenticate user */
  }

  componentDidCatch() {
    /* TODO */
    alert('Ett fel har inträffat, ladda om sidan eller nåt')
  }

  render() {
    return (
      <div className="App h-100">
        <MainMenu />
        <Route exact path="/" component={MainView} />
        <Route exact path="/bokningar" component={TourView} />
        <Route exact path="/kalkyler" component={BudgetView} />
        <Route exact path="/utskick" component={ListView} />
      </div>
    );
  }
}

export default connect(null, null)(App);
