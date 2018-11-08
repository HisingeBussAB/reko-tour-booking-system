import React, { Component } from 'react'
import { faTimes } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import { errorPopup } from '../../actions'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'

class ErrorPopup extends Component {
  closeMe = () => {
    const {errorPopup} = this.props
    errorPopup({visible: false, message: ''})
  }

  render () {
    const {error} = this.props
    let showme = 'none'
    if (!error.suppressed && error.visible) {
      showme = 'block'
    }

    const containerstyle = {
      backgroundColor: 'rgba(255,255,255,.7)',
      position       : 'fixed',
      top            : '0px',
      left           : '0px',
      width          : '100%',
      height         : '100%',
      zIndex         : '90000',
      display        : showme
    }

    const closebtnstyle = {
      border      : '1px solid #0856fb',
      color       : '#0856fb',
      position    : 'absolute',
      top         : '6px',
      right       : '6px',
      zIndex      : '90002',
      width       : '34px',
      height      : '34px',
      padding     : '3px',
      borderRadius: '100%',
      textAlign   : 'center'
    }

    const style = {
      color          : 'red',
      margin         : '0 auto',
      position       : 'fixed',
      top            : '35%',
      left           : '50%',
      transform      : 'translate(-50%)',
      textAlign      : 'center',
      width          : '60%',
      fontSize       : '1.2rem',
      backgroundColor: 'white',
      border         : '1px solid #0856fb',
      borderRadius   : '5px',
      minWidth       : '400px',
      zIndex         : '90001',
      padding        : '3px 22px'
    }
    const textstyle = {
      padding   : '20px',
      margin    : '0',
      whiteSpace: 'pre-line'
    }
    const text = <p style={textstyle}>{error.message}</p>

    const closebtn = <div className="custom-close-error" onClick={this.closeMe} style={closebtnstyle}><FontAwesomeIcon icon={faTimes} size="lg" /></div>

    return (
      <div className="ErrorPopup" style={containerstyle}>
        <div className="Error" style={style}>
          {text}
          {closebtn}
          <button className="btn btn-primary btn-sm text-uppercase mb-3" onClick={this.closeMe}>Stäng felmeddelandet</button>
        </div>
      </div>)
  }
}

ErrorPopup.propTypes = {
  errorPopup: PropTypes.func,
  error     : PropTypes.object
}

const mapStateToProps = state => ({
  error: state.errorPopup
})

const mapDispatchToProps = dispatch => bindActionCreators({
  errorPopup
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(ErrorPopup)
