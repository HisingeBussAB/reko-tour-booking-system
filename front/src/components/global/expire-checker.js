import React, { Component } from 'react'
import PropTypes from 'prop-types'
import {connect} from 'react-redux'
import {bindActionCreators} from 'redux'
import {Login, errorPopup, networkAction} from '../../actions'
import Config from '../../config/config'
import {getServerTime} from '../../functions'

class ExpireChecker extends Component {
  constructor (props) {
    super(props)
    this.state = {
      displayWarning: false,
      servertime    : 0
    }
    this.interval = setInterval(() => {
      this.checkTime()
    }, 1200000) // 1200000 is 3 times per hour
  }

  componentDidMount () {
    const delayFirst = setTimeout(() => { this.checkTime(); clearTimeout(delayFirst) }, 30000)
  }

  componentWillUnmount () {
    clearInterval(this.interval)
  }

  checkTime = () => {
    const {...props} = this.props
    props.networkAction(1, 'get timestamp')
    getServerTime()
      .then(response => {
        props.networkAction(0, 'get timestamp')
        if (typeof response === 'number') {
          this.setState({servertime: response}, () => this.doExpireCheck())
        } else {
          props.errorPopup('Felformatid tid skickad från servern. Något är fel i APIn. Spara arbetet.')
        }
      })
      .catch(() => {
        props.errorPopup('Kunde inte hämta tid från server. Något är fel i APIn. Spara arbetet.')
        props.networkAction(0, 'get timestamp')
      })
  }

  doExpireCheck = () => {
    const {...props} = this.props
    const {servertime} = this.state
    let onceExpires
    let loginExpires
    let silentAttempt = false
    let warningFlag = false
    try {
      onceExpires = Number(localStorage.getObject('user').expires)
    } catch (e) {
      onceExpires = 0
    }
    try {
      loginExpires = Number(props.login.expires)
    } catch (e) {
      loginExpires = 0
    }
    if (onceExpires - 3650 < servertime) {
      warningFlag = true
      if (onceExpires - 20 < servertime && typeof Config.OnceLoginToken === 'string' && Config.OnceLoginToken.length > 0) {
        silentAttempt = true
        this.doOnceLogin()
      }
    }

    if (loginExpires - 3650 < servertime && !silentAttempt) {
      warningFlag = true
      if (loginExpires - 20 < servertime) {
        silentAttempt = true
        this.doAutoLogin()
      }
    }
    if (warningFlag && !silentAttempt) {
      this.setState({displayWarning: true})
    }
  }

  doOnceLogin = () => {
    const {...props} = this.props
    try {
      const userData = localStorage.getObject('user')
      const logindata = {
        pwd        : userData.tokenid + Config.OnceLoginToken + userData.token,
        user       : userData.user,
        auto       : true,
        isOnce     : true,
        onceExpires: userData.expires,
        blockError : true
      }
      props.networkAction(1, 'expire re-login token')
      props.Login(logindata)
        .then(() => {
          this.setState({displayWarning: false})
          props.networkAction(0, 'expire re-login token')
        })
        .catch(() => {
          props.networkAction(0, 'expire re-login token')
          this.doAutoLogin()
        })
    } catch (e) {
      props.networkAction(0, 'expire re-login token')
      this.doAutoLogin()
    }
  }

  doAutoLogin = () => {
    const {...props} = this.props
    if (Config.AutoLogin && Config.AutoLogin === 'string' && Config.AutoLogin > 0 && Config.AutoUsername === 'string' && Config.AutoUsername > 0) {
      try {
        const logindata = {
          pwd       : Config.AutoLoginPwd,
          user      : Config.AutoUsername,
          auto      : true,
          isOnce    : false,
          blockError: true
        }
        props.networkAction(1, 'expire re-login auto')
        props.Login(logindata)
          .then(() => {
            this.setState({displayWarning: false})
            props.networkAction(0, 'expire re-login auto')
          })
          .catch(() => {
            this.setState({displayWarning: true})
            props.networkAction(0, 'expire re-login auto')
          })
      } catch (e) {
        this.setState({displayWarning: true})
        props.networkAction(0, 'expire re-login auto')
      }
    } else {
      this.setState({displayWarning: true})
    }
  }

  closeMe = () => {
    this.setState({displayWarning: false})
  }

  render () {
    const {login = {expires: -1}} = this.props
    const {servertime = 0, displayWarning = false} = this.state

    const style = {
      display: 'none'
    }

    const styleShow = {
      display        : 'block',
      position       : 'fixed',
      top            : '20px',
      left           : '50%',
      transform      : 'translateX(-50%)',
      color          : 'red',
      fontSize       : '1.5rem',
      fontWeight     : 'bold',
      zIndex         : '50000',
      backgroundColor: 'white',
      padding        : '25px',
      border         : '1px solid black',
      borderRadius   : '5px'
    }

    let minutesLeft
    try {
      minutesLeft = Math.round((login.expires - servertime) / 60)
    } catch (e) {
      minutesLeft = 'okänt antal'
    }

    return (
      <div className="ExpireChecker text-center" style={displayWarning ? styleShow : style}>
        <p>{'Inloggningen går ut om ' + minutesLeft + ' minuter.'}</p>
        <p>Spara arbetet och ladda om appen så snart som möjligt (tryck F5).</p>
        <button className="btn btn-primary text-uppercase py-1 px-3 m-1" onClick={this.closeMe}>Stäng</button>
      </div>)
  }
}

ExpireChecker.propTypes = {
  Login        : PropTypes.func,
  errorPopup   : PropTypes.func,
  login        : PropTypes.object,
  networkAction: PropTypes.func
}

const mapStateToProps = state => ({
  login: state.login,
  error: state.errorPopup
})

const mapDispatchToProps = dispatch => bindActionCreators({
  Login,
  errorPopup,
  networkAction
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(ExpireChecker)
