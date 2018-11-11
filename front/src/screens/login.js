import React, { Component } from 'react'
import {faSpinner} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import {connect} from 'react-redux'
import {bindActionCreators} from 'redux'
import {Login, networkAction} from '../actions'
import Config from '../config/config'
import Logo from '../img/logo.gif'
import {getServerTime} from '../functions'

class LoginScreen extends Component {
  constructor (props) {
    super(props)
    const {login = {autoAttempt: false}} = this.props
    this.state = {
      serverTime: false,
      isSending : true,
      triedAuto : false,
      loginData : {
        pwd           : Config.AutoLoginPwd,
        user          : Config.AutoUsername,
        auto          : login.autoAttempt,
        refreshToken  : false,
        refreshExpires: 0
      }
    }
  }

  componentWillMount () {
    const {login = {login: false}} = this.props
    if (!login.login) {
      this.getLocalStorageToState()
    }
  }

  componentWillReceiveProps (nextProps) {
    // Will activate on logout and try loggin back in
    const {login = {login: false}} = this.props
    if (nextProps.login.login !== login.login && !nextProps.login.login) {
      this.getLocalStorageToState()
    }
  }

  getLocalStorageToState = () => {
    const {login = {autoAttempt: false}} = this.props
    let userObject = null
    try {
      userObject = localStorage.getObject('user')
    } catch (e) {
      userObject = null
    }
    if (userObject !== null) {
      if (typeof userObject.user === 'string' && typeof userObject.refreshToken === 'string' && typeof userObject.refreshExpires === 'number') {
        this.setState({loginData: {
          pwd           : Config.AutoLoginPwd,
          user          : userObject.user,
          auto          : login.autoAttempt,
          refreshToken  : userObject.refreshToken,
          refreshExpires: userObject.refreshExpires
        }}, () => { this.getUnixTime() })
        return true
      }
    }
    this.setState({loginData: {
      pwd           : Config.AutoLoginPwd,
      user          : Config.AutoUsername,
      auto          : login.autoAttempt,
      refreshToken  : false,
      refreshExpires: 0
    }}, () => { this.getUnixTime() })
    return false
  }

  getUnixTime = () => {
    const {networkAction} = this.props
    networkAction(1, 'login get time')
    getServerTime()
      .then(response => {
        this.setState({serverTime: Number(response)})
        this.autoSequenceLauncher(Number(response))
        networkAction(0, 'login get time')
      })
      .catch(() => {
        // use client time if server is not sending timestamp
        const time = Number(Math.round(+new Date() / 1000))
        this.setState({serverTime: Number(time)})
        this.autoSequenceLauncher(time)
        networkAction(0, 'login get time')
      })
  }

  autoSequenceLauncher = (serverTime) => {
    const {login = {autoAttempt: false}} = this.props
    const {
      triedAuto = true,
      loginData = {
        pwd           : Config.AutoLoginPwd,
        user          : Config.AutoUsername,
        auto          : login.autoAttempt,
        refreshToken  : false,
        refreshExpires: 0
      }} = this.state
    if (typeof loginData.refreshToken === 'string' && loginData.refreshToken.length > 1 && serverTime < (loginData.refreshExpires - 600)) {
      // Try refresh with 10 minutes margin
      this.tryLogin('refresh')
    } else if (triedAuto === false && loginData.auto && typeof loginData.user === 'string' && typeof loginData.pwd === 'string' &&
        loginData.user.length > 0 && loginData.pwd.length > 0) {
      // Use auto credentials if they exist
      this.tryLogin('auto')
    }
  }

  tryLogin = (auto = false) => {
    const {networkAction, Login = function () {}} = this.props
    const {loginData} = this.state
    this.setState({isSending: true})
    networkAction(1, 'trying login')
    Login(loginData)
      .then(() => {
        // Component will unmount here
      })
      .catch(() => {
        if (auto === 'auto') { this.setState({triedAuto: true}) }
        if (auto === 'refresh') {
          localStorage.setObject('user', {
            user          : loginData.user,
            refreshToken  : false,
            refreshExpires: 0
          })
          this.getLocalStorageToState()
          return false
        }
        this.setState(prevState => ({isSending: false,
          loginData: {
            ...prevState.loginData,
            pwd: ''
          }}))
      })
      .finally(() => {
        networkAction(0, 'trying login')
      })
  }

  handleUserChange = (event) => {
    const {loginData} = this.state
    this.setState({loginData: {...loginData, user: event.target.value}})
  }

  handlePwdChange = (event) => {
    const {loginData} = this.state
    this.setState({loginData: {...loginData, pwd: event.target.value}})
  }

  clearPwd = () => {
    const {loginData} = this.state
    this.setState({loginData: {...loginData, pwd: ''}})
  }

  clearUser = () => {
    const {loginData} = this.state
    this.setState({loginData: {...loginData, user: ''}})
  }

  handleSubmit = (event) => {
    event.preventDefault()
    this.tryLogin()
  }

  render () {
    // Destructors, defaults to App initialization state
    const {serverTime = false, triedAuto = true, isSending = false,
      loginData = {
        pwd           : '',
        user          : '',
        auto          : false,
        refreshToken  : false,
        refreshExpires: 0
      }} = this.state
    const {error = {message: ''}} = this.props

    const style = {
      color    : '#0856fb',
      height   : '650px',
      margin   : '0 auto',
      position : 'absolute',
      top      : '50%',
      transform: 'translateY(-50%)',
      textAlign: 'center',
      width    : '100%',
      zIndex   : '19999'
    }
    return (
      <div className="Login" style={style}>
        <p><img src={Logo} alt="Logo" className="rounded my-4" title="Till Startsida" id="mainLogo" /></p>
        <h1 className="my-4">Resesystem</h1>
        {(typeof loginData.refreshToken === 'string' && loginData.refreshToken.length > 1 && serverTime < (loginData.refreshExpires - 600)) ||
        (triedAuto === false && loginData.auto && typeof loginData.user === 'string' && typeof loginData.pwd === 'string' && loginData.user.length > 0 && loginData.pwd.length > 0)
          ? <div>
            <h3 className="mb-4">Försöker automatisk inloggning...</h3>
            <span className="my-4"><FontAwesomeIcon icon={faSpinner} pulse size="4x" /></span>
          </div>
          : <div>
            <h5 className="w-50 mx-auto my-3" style={{color: 'red'}}>{loginData.blockerror ? null : error.message}</h5>
            <h4 className="w-50 mx-auto mt-5 mb-3">Logga in</h4>
            <form onSubmit={this.handleSubmit}>
              <fieldset disabled={isSending}>
                <div className="my-2 w-50 mx-auto"><label className="small d-block text-left pt-2 pl-3">Användarnamn:</label><input className="w-100 rounded" type="text" placeholder="Användarnamn" value={loginData.user} onFocus={this.clearUser} onChange={this.handleUserChange} /></div>
                <div className="my-2 w-50 mx-auto"><label className="small d-block text-left pt-2 pl-3">Lösenord:</label><input className="w-100 rounded" type="password" placeholder="Lösenord" value={loginData.pwd} onFocus={this.clearPwd} onChange={this.handlePwdChange} /></div>
                <div className="my-2 w-50 mx-auto"><input className="w-100 mt-4 rounded text-uppercase font-weight-bold btn btn-primary custom-wide-text" type="submit" value="Logga in" /></div>
              </fieldset>
            </form>
          </div>
        }
      </div>)
  }
}

LoginScreen.propTypes = {
  Login        : PropTypes.func,
  login        : PropTypes.object,
  error        : PropTypes.object,
  networkAction: PropTypes.func
}

const mapStateToProps = state => ({
  login: state.login,
  error: state.errorPopup
})

const mapDispatchToProps = dispatch => bindActionCreators({
  Login,
  networkAction
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(LoginScreen)
