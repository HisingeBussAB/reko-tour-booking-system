import React, { Component } from 'react'
import fontawesome from '@fortawesome/fontawesome'
import faSpinner from '@fortawesome/fontawesome-free-solid/faSpinner'
import FontAwesomeIcon from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import {Login, errorPopup} from '../actions'
import Config from '../config/config'
import Logo from '../img/logo.gif'
import {getServerTime} from '../utils/common-calls'

fontawesome.library.add(faSpinner)

class LoginScreen extends Component {
  constructor (props) {
    super(props)
    const {login = {autoAttempt: false}} = this.props
    this.state = {
      issending: true,
      servertime: false,
      first: true,
      logindata: {
        pwd: Config.AutoLoginPwd,
        user: Config.AutoUsername,
        auto: login.autoAttempt,
        isOnce: false,
        onceExpires: 0,
        blockError: false
      }
    }
  }

  componentWillMount () {
    const {login = {login: false}} = this.props
    if (!login.login) {
      this.getLocalStorageToState()
      this.getUnixTime()
    }
  }

  componentWillReceiveProps (nextProps) {
    const {login = {login: false}} = this.props
    if (nextProps.login.login !== login.login && !nextProps.login.login) {
      this.getLocalStorageToState()
      this.getUnixTime()
    }
  }

  getUnixTime = () => {
    getServerTime()
      .then(response => {
        this.setState({servertime: Number(response)})
        this.runAutoLoginFirst(Number(response))
      })
      .catch(() => {
        const time = Math.round(+new Date() / 1000)
        this.setState({servertime: Number(time)})
        this.runAutoLoginFirst(Number(time))
      })
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
      if (typeof userObject.user === 'string' && typeof userObject.tokenid === 'string' &&
        typeof userObject.token === 'string' && typeof userObject.expires === 'number') {
        this.setState({logindata: {
          pwd: userObject.tokenid + Config.OnceLoginToken + userObject.token,
          user: userObject.user,
          auto: login.autoAttempt,
          isOnce: true,
          onceExpires: userObject.expires,
          blockError: login.autoAttempt // block error output if auto login is active (auto login is fired directly after failed once)
        }})
        return true
      }
    }
    return false
  }

  runAutoLoginFirst = (servertime, blockonce = false) => {
    // Destructors, defaults to App initialization state
    const {first = false} = this.state
    const {login = {autoAttempt: false}} = this.props
    const {logindata = {
      pwd: Config.AutoLoginPwd,
      user: Config.AutoUsername,
      auto: login.autoAttempt,
      isOnce: false,
      onceExpires: 0,
      blockError: false
    }} = this.state
    const {Login = function () {}} = this.props

    // Check if servertime is fetched. Run autosequence if found. App should re-rerender every time server time is recived.
    if (servertime !== false && typeof servertime === 'number') {
      if (!blockonce && logindata.isOnce && logindata.onceExpires > servertime - 120) {
        this.setState({issending: true})
        Login(logindata)
          .then(() => {
            // Component will unmount
          })
          .catch(() => {
            logindata.isOnce = false
            console.log(logindata)
            this.setState({issending: false, logindata: logindata})
            this.runAutoLoginFirst(servertime, true)
          })
      } else if (logindata.auto && typeof Config.AutoUsername === 'string' && typeof Config.AutoLoginPwd === 'string') {
        this.setState({issending: true})
        const newlogin = {
          pwd: Config.AutoLoginPwd,
          user: Config.AutoUsername,
          auto: true,
          isOnce: false,
          onceExpires: 0,
          blockError: false
        }
        Login(newlogin)
          .then(() => {
            // Component will unmount
          })
          .catch(() => {
            logindata.user = ''
            logindata.pwd = ''
            logindata.isOnce = false
            logindata.auto = false
            logindata.blockError = false
            this.setState({issending: false, logindata: logindata})
          })
      } else if (first) {
        // no autos & first try. reset and unlock
        this.setState({
          first: false,
          issending: false,
          logindata: {
            pwd: '',
            user: '',
            auto: false,
            isOnce: false,
            onceExpires: 0,
            blockError: false
          }
        })
      } else {
        this.setState({issending: false})
      }
    } else {
      this.setState({issending: false})
    }
  }

  handleUserChange = (event) => {
    const {logindata} = this.state
    this.setState({logindata: {...logindata, user: event.target.value}})
  }

  handlePwdChange = (event) => {
    const {logindata} = this.state
    this.setState({logindata: {...logindata, pwd: event.target.value}})
  }

  clearPwd = () => {
    const {logindata} = this.state
    this.setState({logindata: {...logindata, pwd: ''}})
  }

  clearUser = () => {
    const {logindata} = this.state
    this.setState({logindata: {...logindata, user: ''}})
  }

  handleSubmit = (event) => {
    event.preventDefault()
    const {Login} = this.props
    const {logindata} = this.state
    this.setState({issending: true})
    Login(logindata)
      .then(() => {
        // Component will unmount
      })
      .catch(() => {
        this.setState({issending: false})
      })
  }

  render () {
    // Destructors, defaults to App initialization state
    const {servertime = false} = this.state
    const {issending = false} = this.state
    const {logindata = {
      pwd: '',
      user: '',
      auto: false,
      isOnce: false,
      onceExpires: 0,
      blockError: false
    }} = this.state
    const {error = {message: ''}} = this.props

    const style = {
      color: '#0856fb',
      height: '650px',
      margin: '0 auto',
      position: 'absolute',
      top: '50%',
      transform: 'translateY(-50%)',
      textAlign: 'center',
      width: '100%',
      zIndex: '19999'
    }
    return (
      <div className="Login" style={style}>
        <p><img src={Logo} alt="Logo" className="rounded my-4" title="Till Startsida" id="mainLogo" /></p>
        <h1 className="my-4">Resesystem</h1>
        {(logindata.isOnce && logindata.onceExpires > servertime - 120) || (logindata.auto && typeof logindata.pwd === 'string' && typeof logindata.user === 'string')
          ? <div>
            <h3 className="mb-4">Försöker automatisk inloggning...</h3>
            <span className="my-4"><FontAwesomeIcon icon="spinner" pulse size="4x" /></span>
          </div>
          : <div>
            <h5 className="w-50 mx-auto my-3" style={{color: 'red'}}>{logindata.blockerror ? null : error.message}</h5>
            <h4 className="w-50 mx-auto mt-5 mb-3">Logga in</h4>
            <form onSubmit={this.handleSubmit}>
              <fieldset disabled={issending}>
                <div className="my-2 w-50 mx-auto"><label className="small d-block text-left pt-2 pl-3">Användarnamn:</label><input className="w-100 rounded" type="text" placeholder="Användarnamn" value={logindata.user} onFocus={this.clearUser} onChange={this.handleUserChange} /></div>
                <div className="my-2 w-50 mx-auto"><label className="small d-block text-left pt-2 pl-3">Lösenord:</label><input className="w-100 rounded" type="password" placeholder="Lösenord" value={logindata.pwd} onFocus={this.clearPwd} onChange={this.handlePwdChange} /></div>
                <div className="my-2 w-50 mx-auto"><input className="w-100 mt-4 rounded text-uppercase font-weight-bold btn btn-primary custom-wide-text" type="submit" value="Logga in" /></div>
              </fieldset>
            </form>
          </div>
        }
      </div>)
  }
}

LoginScreen.propTypes = {
  Login: PropTypes.func,
  login: PropTypes.object,
  error: PropTypes.object
}

const mapStateToProps = state => ({
  login: state.login,
  error: state.errorPopup
})

const mapDispatchToProps = dispatch => bindActionCreators({
  Login,
  errorPopup
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(LoginScreen)
