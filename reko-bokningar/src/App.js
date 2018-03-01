import React, { Component } from 'react';
import MainMenu from './components/main-menu';
import { connect } from 'react-redux';
import Loadable from 'react-loadable';
import Loader from './components/loader';
import { Route } from 'react-router-dom';
import Config from './config/config';
import axios from 'axios';
import ErrorPopup from './components/error-popup';

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
      showError: false,
      showErrorMessage: '',
    };    
  }

  componentWillMount() {
    /* Auto authenticate user */
    axios.post( Config.ApiUrl + '/api/token/login', {
      apitoken: Config.ApiToken,
    })
      .then(response => {
        axios.post( Config.ApiUrl + '/api/auth', {
          user: Config.AutoUsername,
          pwd: Config.AutoLoginPwd,
          apitoken: Config.ApiToken,
          logintoken: response.data.logintoken,
        })
          .then(response => {
            console.log(response.data);
          })
          .catch(error => {
            console.log(error.response.data.response);
            this.setState({showError: true, showErrorMessage: error.response.data.response});
          });
      })
      .catch(error => {
        console.log(error.response.data.response);
        this.setState({showError: true, showErrorMessage: error.response.data.response});
      });


    
  }

  componentDidCatch() {
    /* TODO */
    //alert('Ett fel har inträffat, ladda om sidan eller nåt');
  }

  sendErrorClose = (val) => {
    console.log(val);
    if (val) {
      this.setState({showError: false});
    }
  }

  render() {

   
    

    return (
      <div className="App h-100">
        <MainMenu />
        {this.state.showError ? <ErrorPopup sendClose={this.sendErrorClose} message={this.state.showErrorMessage}/> : null }
        <Route exact path="/" component={MainView} />
        <Route exact path="/bokningar" component={TourView} />
        <Route exact path="/kalkyler" component={BudgetView} />
        <Route exact path="/utskick" component={ListView} />
      </div>
    );
  }
}

export default connect(null, null)(App);
