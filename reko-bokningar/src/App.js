import React, { Component } from 'react';
import MainMenu from './components/main-menu';
import { connect } from 'react-redux';
import Loadable from 'react-loadable';
import Loader from './components/loader';
import { Route } from 'react-router-dom';
import LoginScreen from './components/login-screen';
import SaveIcon from './components/save-icon';
import ExpireChecker from './components/expire-checker';
import PropTypes from 'prop-types';

/*eslint-disable react/display-name */
const MainView = Loadable({
  loader: () => import('./components/main-view'),
  loading: () => <Loader fullScreen={false} />,
});

const TourView = Loadable({
  loader: () => import('./components/tour-view'),
  loading: () => <Loader fullScreen={false} />,
});

const BudgetView = Loadable({
  loader: () => import('./components/budget-view'),
  loading: () => <Loader fullScreen={false} />,
});

const ListView = Loadable({
  loader: () => import('./components/list-view'),
  loading: () => <Loader fullScreen={false} />,
});
/*eslint-enable react/display-name */



class App extends Component {
  

  componentWillMount() {
   
  }

  componentDidCatch() {
    /* TODO */
    //alert('Ett fel har inträffat, ladda om sidan eller nåt');
  }

  

  render() {

   
    

    return (
      <div className="App h-100">
        {this.props.loggedin ?
          <div>
            <MainMenu />
            <Route exact path="/" component={MainView} />
            <Route exact path="/bokningar/*" component={TourView} />
            <Route exact path="/kalkyler/*" component={BudgetView} />
            <Route exact path="/utskick/*" component={ListView} />
          </div> : 
          <LoginScreen />
        }
        <ExpireChecker />
        <SaveIcon />
      </div>
    );
  }
}

App.propTypes = {
  loggedin:           PropTypes.bool,
  showError:          PropTypes.bool,
  showErrorMessage:   PropTypes.string,
};

const mapStateToProps = state => ({
  loggedin: state.login.login,
  showError: state.errorPopup.visible,
  showErrorMessage: state.errorPopup.message,
});


export default connect(mapStateToProps, null)(App);
