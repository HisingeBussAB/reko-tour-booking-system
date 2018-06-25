import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import faPlus from '@fortawesome/fontawesome-free-solid/faPlus'
import FontAwesomeIcon from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import {networkAction} from '../../actions'

class NewTour extends Component {
  constructor (props) {
    super(props)
    this.state = {}
  }

  render () {
    const {showStatus = false, showStatusMessage = ''} = this.state
    return (
      <div className="TourView NewTour">

        <form>
          <fieldset />
        </form>
      </div>
    )
  }
}

NewTour.propTypes = {
}

const mapStateToProps = state => ({
  login            : state.login,
  showStatus       : state.errorPopup.visible,
  showStatusMessage: state.errorPopup.message,
  categories       : state.tours.categories
})

const mapDispatchToProps = dispatch => bindActionCreators({
  networkAction
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(NewTour)
