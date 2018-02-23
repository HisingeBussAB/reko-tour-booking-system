import React, { Component } from 'react';
import { connect } from 'react-redux';


class TourView extends Component {
  
  

  render() {

   
    return (
      <div className="container-fluid TourView">
        Bokningar
      </div>
    );
  }
}

export default connect(null, null)(TourView);
