import React, { Component } from 'react';
import MainMenu from './components/main-menu';
import { connect } from 'react-redux';
import Loadable from 'react-loadable';
import Loader from './components/loader';
import { Route } from 'react-router-dom';
import LoginScreen from './components/login-screen';
import SaveIcon from './components/save-icon';
import ExpireChecker from './components/expire-checker';
import FirebaseComponent from './components/firebase-component';
import PropTypes from 'prop-types';
import Config from './config/config';
import firebase from './config/firebase';

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
  constructor(props){
    super(props);
    this.state = {
      showStatus: false,
      showStatusMessage: '',
      useFirebase: false,
    };
  }
  

  componentWillMount() {
    console.log(this.useFirbase)
    firebase.auth().signInWithEmailAndPassword(Config.FirebaseLogin, Config.FirebasePwd)
      .then(() => {
        this.setState({useFirebase: firebase.auth().currentUser});
      })
      .catch(() => {
        //TODO Add manual start on subscription failure
        //this.ManualStart(); TODO
        this.setState({
          useFirebase: false,
          showStatus: true,
          showStatusMessage: 'Kunde inte ansluta till WebSocket! Programmet går fortfarande att använda men undvik att använda det på flera datorer samtidigt.',
        });
        setTimeout(() => {
          this.setState({
            showStatus: false,
            showStatusMessage: '',
          });
        }, 35000);
      });

  }

  componentDidCatch() {
    /* TODO */
    //alert('Ett fel har inträffat, ladda om sidan eller nåt');
  }


    
  render() {

    
    console.log(this.state.useFirebase);
    return (
      <div className="App h-100">
        {this.props.loggedin ?
          <div>
            {this.state.showStatus ? 
              <div className="top-main-error m-2" style={{color: 'red', textAlign: 'center', fontSize: '1.22rem'}}>{this.state.showStatusMessage}</div>
              :
              null }
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
        {(this.state.useFirebase) ? 
          <FirebaseComponent />
          :
          null }
      </div>
    );
  }
}

App.propTypes = {
  loggedin:           PropTypes.bool,
};

const mapStateToProps = state => ({
  loggedin: state.login.login,
});

export default connect(mapStateToProps, null)(App);




