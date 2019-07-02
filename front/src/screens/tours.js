import React, { Component } from 'react'
import { connect } from 'react-redux'
import TourViewMain from './tours/tours-main'
import Categories from './tours/tours-categories'
import NewTourBooking from './tours/tours-booking'
import NewTour from './tours/tours-edit'
import { Route } from 'react-router-dom'

class TourView extends Component {
  render () {
    return (
      <div className="TourView text-center pt-3">
        <Route exact path="/bokningar" component={TourViewMain} />
        <Route exact path="/bokningar/resa/:id" component={NewTour} />
        <Route exact path="/bokningar/kategorier" component={Categories} />
        <Route exact path="/bokningar/bokning/:number" component={NewTourBooking} />
      </div>
    )
  }
}

export default connect(null, null)(TourView)
