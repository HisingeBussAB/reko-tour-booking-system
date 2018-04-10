import React, { Component } from 'react'
import MainMenu from './components/main-menu'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import Loadable from 'react-loadable'
import Loader from './components/loader'
import { Route } from 'react-router-dom'
import LoginScreen from './components/login-screen'
import SaveIcon from './components/save-icon'
import ExpireChecker from './components/expire-checker'
import PropTypes from 'prop-types'
import Config from './config/config'
import firebase from './config/firebase'
import {startFirebaseSub} from './actions'
import ErrorPopup from './components/error-popup'

/* eslint-disable react/display-name */
const MainView = Loadable({
  loader: () => import('./components/main-view'),
  loading: () => <Loader fullScreen={false} />
})

const TourView = Loadable({
  loader: () => import('./components/tour-view'),
  loading: () => <Loader fullScreen={false} />
})

const BudgetView = Loadable({
  loader: () => import('./components/budget-view'),
  loading: () => <Loader fullScreen={false} />
})

const ListView = Loadable({
  loader: () => import('./components/list-view'),
  loading: () => <Loader fullScreen={false} />
})
/* eslint-enable react/display-name */

class App extends Component {
  constructor (props) {
    super(props)
    this.state = {
      showStatus: false,
      showStatusMessage: ''
    }
  }

  componentWillReceiveProps (nextProps) {
    // Checks for login change and starts up firebase if login state change and login is detected.
    const {startFirebaseSub = function () {}, login = {login: {login: false, user: 'none', jwt: 'none'}}} = this.props
    const prevLogin = login.login
    if (nextProps.login.login && nextProps.login.login !== prevLogin) {
      firebase.auth().signInWithEmailAndPassword(Config.FirebaseLogin, Config.FirebasePwd)
        .then(() => {
          startFirebaseSub(nextProps.login.user, nextProps.login.jwt)
        })
        .catch(() => {
        // TODO
        // manuallt download sub data?
          this.setState({
            showStatus: true,
            showStatusMessage: 'Kunde inte ansluta till WebSocket! Programmet går fortfarande att använda men undvik att använda det på flera datorer samtidigt.'
          })
          setTimeout(() => {
            this.setState({
              showStatus: false,
              showStatusMessage: ''
            })
          }, 35000)
        })
    }
  }

  componentDidCatch () {
    /* TODO */
    // alert('Ett fel har inträffat, ladda om sidan eller nåt');
  }

  render () {
    const {isSuppressedPopup = true, login = {login: {login: false}}} = this.props
    const {showStatus = true, showStatusMessage = 'Okänt fel, inget state'} = this.state
    return (
      <div className="App h-100">
        {login.login
          ? <div>
            {showStatus
              ? <div className="top-main-error m-2" style={{color: 'red', textAlign: 'center', fontSize: '1.22rem'}}>{showStatusMessage}</div>
              : null }
            <MainMenu />
            <Route exact path="/" component={MainView} />
            <Route exact path="/bokningar/*" component={TourView} />
            <Route exact path="/kalkyler/*" component={BudgetView} />
            <Route exact path="/utskick/*" component={ListView} />
          </div>
          : <LoginScreen />
        }
        {!isSuppressedPopup ? <ErrorPopup /> : null }
        <ExpireChecker />
        <SaveIcon />
      </div>
    )
  }
}

App.propTypes = {
  login: PropTypes.object,
  startFirebaseSub: PropTypes.func,
  isSuppressedPopup: PropTypes.bool
}

const mapStateToProps = state => ({
  login: state.login,
  isSuppressedPopup: state.errorPopup.suppressed
})

const mapDispatchToProps = dispatch => bindActionCreators({
  startFirebaseSub
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(App)
