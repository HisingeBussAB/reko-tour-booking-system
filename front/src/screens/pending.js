import React, { Component } from 'react'
import { connect } from 'react-redux'
import { Route } from 'react-router-dom'
import PendingBookings from './pending/pending-bookings'
import PendingLeads from './pending/pending-leads'
import PendingNewsletter from './pending/pending-newsletter'

class PendingView extends Component {
  render () {
    return (
      <div className="PendingView text-center pt-3">
        <Route exact path="/pending" component={PendingBookings} />
        <Route exact path="/pending/bookings" component={PendingBookings} />
        <Route exact path="/pending/leads" component={PendingLeads} />
        <Route exact path="/pending/newsletter" component={PendingNewsletter} />
      </div>
    )
  }
}

export default connect(null, null)(PendingView)
