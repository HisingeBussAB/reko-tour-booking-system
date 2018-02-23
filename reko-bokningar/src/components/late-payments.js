import React, { Component } from 'react';
import { connect } from 'react-redux';


class LatePayments extends Component {
  
  

  render() {

   
    return (
      <div className="LatePayments text-center">
        <h3>Sena betalningar</h3>
        <table></table>
      </div>
    );
  }
}

export default connect(null, null)(LatePayments);
