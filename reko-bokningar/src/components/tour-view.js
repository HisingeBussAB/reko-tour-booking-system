import React, { Component } from 'react';
import { connect } from 'react-redux';
import TourViewMain from './tours'
import NewTour from './tours/new-tour';
import Categories from './tours/categories';
import { Route } from 'react-router-dom';



class TourView extends Component {
  


  render() {

   
    return (
      <div className="TourView text-center pt-3">
        <Route exact path="/bokningar" component={TourViewMain} />
        <Route exact path="/bokningar/nyresa" component={NewTour} />
        <Route exact path="/bokningar/kategorier" component={Categories} />
      </div>
    );
  }
}


export default connect(null, null)(TourView);
