import React, { Component } from 'react'
import { connect } from 'react-redux'
import Deadlines from '../components/main/deadlines'
import Pending from '../components/main/pending'
import LatePayments from '../components/main/late-payments'

class MainView extends Component {
  render () {
    return (
      <div className="container-fluid MainView">
        <div className="row">
          <div className="col-md-6 col-sm-12 col-xs-12 custom-border-right px-2 py-4">
            <LatePayments />
          </div>
          <div className="col-md-6 col-sm-12 col-xs-12 mx-0 px-0 py-4">
            <div className="container-fluid mx-0 px-0 w-100">
              <div className="row-fluid mx-0 px-0 w-100">
                <div className="col-12 mx-0 px-2 pb-4">
                  <Pending />
                </div>
                <div className="col-12 mx-0 px-2 pt-4 custom-border-top">
                  <Deadlines />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default connect(null, null)(MainView)
