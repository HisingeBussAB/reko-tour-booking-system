import React, { Component } from 'react'
import { connect } from 'react-redux'
import {faSave, faSpinner, faTrash, faArrowLeft} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'

class PendingLeads extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isSubmitting : false,
      showExtended : false,
      showProcessed: false
    }
  }
  render () {
    const {isSubmitting} = this.state
    const {history} = this.props
    return (
      <div className="PendingLeads">
        <button onClick={() => { history.goBack() }} disabled={isSubmitting} type="button" title="Tillbaka till meny" className="mr-4 btn btn-primary btn-sm custom-scale position-absolute" style={{right: 0}}>
          <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={faArrowLeft} size="1x" />&nbsp;Meny</span>
        </button>
        <div className="container text-left" style={{maxWidth: '850px'}}>
          <h3 className="my-3 w-50 mx-auto text-center">PendingLeads</h3>
        </div>
      </div>
    )
  }
}

export default connect(null, null)(PendingLeads)
