import React, { Component } from 'react'
import PropTypes from 'prop-types'

class ConfirmPopup extends Component {

  processChoice = (e, choice) => {
    e.preventDefault()
    const {doAction} = this.props
    doAction(choice)
  }

  render () {
    const containerstyle = {
      backgroundColor: 'rgba(255,255,255,.7)',
      position       : 'fixed',
      top            : '0px',
      left           : '0px',
      width          : '100%',
      height         : '100%',
      zIndex         : '90000'
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
      whiteSpace: 'pre-line',
      fontSize  : '1.5em'
    }

    const {message} = this.props
    const text = <p style={textstyle}>{message}</p>

    return (
      <div className="ConfirmPopup" style={containerstyle}>
        <div className="Confirm" style={style}>
          {text}
          {/* Not buttons because they will be disabled inside the forms sumbit lock */}
          <div className="btn btn-success btn-lg text-uppercase mb-3 mr-5" onClick={(e) => this.processChoice(e, true)}>Ja</div>
          <div className="btn btn-danger btn-lg text-uppercase mb-3 ml-5" onClick={(e) => this.processChoice(e, false)} >Nej</div>
        </div>
      </div>)
  }
}

ConfirmPopup.propTypes = {
  doAction: PropTypes.func,
  message : PropTypes.string
}

export default ConfirmPopup
