import React, { Component } from 'react'
import { Link } from 'react-router-dom'
import { connect } from 'react-redux'

class ListViewMain extends Component {
  render () {
    return (
      <div className="ListViewMain text-center pt-3">
        <h3 className="my-4">Utskick</h3>
        <div className="container-fluid pt-2">
          <div className="row">
            <div className="col-lg-4 col-md-12">
              <h4 className="w-75 my-3 mx-auto">Postutskick</h4>

            </div>
            <div className="col-lg-4 col-md-12">
              <h4 className="w-75 my-3 mx-auto">E-postutskick</h4>
              

            </div>
            <div className="col-lg-4 col-md-12">
              <h4 className="w-75 my-3 mx-auto">Hantera register</h4>
              <Link to={'/utskick/nyhetsbrev'} className="btn w-75 btn-primary my-3 mx-auto py-2">Nyhetsbrev (e-post)</Link>
              <button className="btn w-75 btn-primary my-3 mx-auto py-2">Register enskilda</button>
              <Link to={'/utskick/gruppregister'} className="btn w-75 btn-primary my-3 mx-auto py-2">Gruppregister</Link>

            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default connect(null, null)(ListViewMain)
