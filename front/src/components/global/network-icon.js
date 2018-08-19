import React, { Component } from 'react'
import faSyncAlt from '@fortawesome/fontawesome-free-solid/faSyncAlt'
import FontAwesomeIcon from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'

class NetworkIcon extends Component {
  render () {
    const {networkOperations = []} = this.props
    let isNetworkActive
    try {
      isNetworkActive = networkOperations.length > 0
    } catch (e) {
      isNetworkActive = false
    }

    const style = {
      display: 'none'
    }

    const styleShow = {
      display     : 'block',
      position    : 'fixed',
      bottom      : '0',
      right       : '0',
      color       : '#0856fb',
      fontSize    : '1.5rem',
      fontWeight  : 'bold',
      zIndex      : '90010',
      margin      : '5px',
      marginBottom: '8px',
      opacity     : '0.67',
      height      : '30px',
      width       : '30px'
    }

    return (
      <div className="SaveIcon text-center" style={isNetworkActive ? styleShow : style}>
        <FontAwesomeIcon icon={faSyncAlt} size="1x" spin />
      </div>)
  }
}

NetworkIcon.propTypes = {
  networkOperations: PropTypes.array
}

const mapStateToProps = state => ({
  networkOperations: state.networkOperations
})

export default connect(mapStateToProps, null)(NetworkIcon)
