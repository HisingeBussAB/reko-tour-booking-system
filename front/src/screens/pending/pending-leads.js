import React, { Component } from 'react'
import { connect } from 'react-redux'

class PendingLeads extends Component {
  render () {
    return (
      <div className="PendingLeads">
        <div className="container text-left" style={{maxWidth: '850px'}}>
          <h3 className="my-3 w-50 mx-auto text-center">PendingLeads</h3>
        </div>
      </div>
    )
  }
}

export default connect(null, null)(PendingLeads)
