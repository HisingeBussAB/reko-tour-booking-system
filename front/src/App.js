import React, { Component } from 'react'
import MainMenu from './components/global/main-menu'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import MyLoadable from './components/loader/myloadable'
import { Route } from 'react-router'
import LoginScreen from './screens/login'
import NetworkIcon from './components/global/network-icon'
import PropTypes from 'prop-types'
import Config from './config/config'
import firebase from './config/firebase'
import {firebaseItemSub} from './actions/firebase/firebase-item-sub'
import ErrorPopup from './components/global/error-popup'
import {LoginRefresh} from './actions'

/* eslint-disable react/display-name */
const MainView = MyLoadable({
  loader: () => import('./screens/main')
})

const TourView = MyLoadable({
  loader: () => import('./screens/tours')
})

const BudgetView = MyLoadable({
  loader: () => import('./screens/budget')
})

const ListView = MyLoadable({
  loader: () => import('./screens/list')
})
/* eslint-enable react/display-name */

class App extends Component {
  constructor (props) {
    super(props)
    this.refreshLogin = setInterval(() => {}, 10000)
    this.state = {
      showStatus       : false,
      showStatusMessage: ''
    }
  }

  componentWillReceiveProps (nextProps) {
    // Checks for login change and starts up firebase and refresh timer if login state change and login is detected.
    const {LoginRefresh = function () {}, firebaseItemSub = function () {}, login = {login: {login: false, user: 'none', jwt: 'none'}}} = this.props
    const prevLogin = login.login
    if (nextProps.login.login && nextProps.login.login !== prevLogin) {
      // Refresh login
      clearInterval(this.refreshLogin)
      this.refreshLogin = setInterval(() => {
        LoginRefresh(true)
      }, 40 * 60 * 1000)
      // Firebase
      firebase.auth().signInWithEmailAndPassword(Config.FirebaseLogin, Config.FirebasePwd)
        .then(() => {
          firebaseItemSub(nextProps.login.user, nextProps.login.jwt)
        })
        .catch(() => {
          this.setState({
            showStatus       : true,
            showStatusMessage: 'Kunde inte ansluta till WebSocket för klientsynkronisering! Programmet går fortfarande att använda men undvik att använda det på flera datorer samtidigt.'
          })
          setTimeout(() => {
            this.setState({
              showStatus       : false,
              showStatusMessage: ''
            })
          }, 35000)
        })
    }
  }

  componentDidCatch () {
    /* TODO log */
    window.alert('Kritiskt fel! Applikationen har krashat.')
    clearInterval(this.refreshLogin)
  }

  componentWillUnmount () {
    clearInterval(this.refreshLogin)
  }

  render () {
    const {isSuppressedPopup = true, login = {login: {login: false}}} = this.props
    const {showStatus = true, showStatusMessage = ''} = this.state
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
        {!isSuppressedPopup && <ErrorPopup /> }
        <NetworkIcon />
      </div>
    )
  }
}

App.propTypes = {
  LoginRefresh     : PropTypes.func,
  login            : PropTypes.object,
  firebaseItemSub  : PropTypes.func,
  isSuppressedPopup: PropTypes.bool
}

const mapStateToProps = state => ({
  login            : state.login,
  isSuppressedPopup: state.errorPopup.suppressed
})

const mapDispatchToProps = dispatch => bindActionCreators({
  firebaseItemSub,
  LoginRefresh
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(App)
