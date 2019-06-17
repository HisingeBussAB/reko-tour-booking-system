import React, { Component } from 'react'
import { connect } from 'react-redux'

class PendingBookings extends Component {
  render () {
    return (
      <div className="PendingBookings">
        <div className="container text-left" style={{maxWidth: '850px'}}>
          <h3 className="my-3 w-50 mx-auto text-center">PendingBookings</h3>
        </div>
      </div>
    )
  }
}

export default connect(null, null)(PendingBookings)
