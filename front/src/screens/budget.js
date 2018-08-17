import React, { Component } from 'react'
import { connect } from 'react-redux'

class BudgetView extends Component {
  render () {
    return (
      <div className="BudgetView text-center pt-3">
        <h3 className="my-4">Resekalkyler</h3>
        <div className="container-fluid pt-2">
          <div className="row">
            <div className="col-lg-12 col-md-12">
              <button className="btn w-75 btn-primary my-3 mx-auto py-2">Skapa ny kalkyl</button>
              <button className="btn w-75 btn-primary my-3 mx-auto py-2">Fler kalkyler</button>
              <p className="w-75 my-3 py-2 mx-auto px-1 text-justify d-block">Kalkylista</p>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default connect(null, null)(BudgetView)
