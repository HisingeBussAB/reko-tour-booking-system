import React, { Component } from 'react'
import { faTimes } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import { infoPopup } from '../../actions'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'

class InfoPopup extends Component {
  closeMe = () => {
    const {infoPopup} = this.props
    infoPopup({visible: false, message: ''})
  }

  render () {
    const {info} = this.props
    let showme = 'none'
    if (!info.suppressed && info.visible) {
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
      color          : 'black',
      margin         : '0 auto',
      position       : 'fixed',
      top            : '35%',
      left           : '50%',
      transform      : 'translate(-50%)',
      textAlign      : 'left',
      width          : '60%',
      fontSize       : '1.05rem',
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
    const text = <p style={textstyle}>{info.message}</p>

    const closebtn = <div className="custom-close-info" onClick={this.closeMe} style={closebtnstyle}><FontAwesomeIcon icon={faTimes} size="lg" /></div>

    return (
      <div className="InfoPopup" style={containerstyle}>
        <div className="Info" style={style}>
          {text}
          <span className="d-block w-100 mx-auto text-center">{closebtn}</span>
          <button className="btn btn-primary btn-sm text-uppercase mb-3" onClick={this.closeMe}>Stäng inforutan</button>
        </div>
      </div>)
  }
}

InfoPopup.propTypes = {
  infoPopup: PropTypes.func,
  info     : PropTypes.object
}

const mapStateToProps = state => ({
  info: state.infoPopup
})

const mapDispatchToProps = dispatch => bindActionCreators({
  infoPopup
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(InfoPopup)
