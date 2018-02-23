import React, { Component } from 'react';
import { connect } from 'react-redux';


class BudgetView extends Component {
  
  

  render() {

   
    return (
      <div className="container-fluid BudgetView">
        Kalkyler
      </div>
    );
  }
}

export default connect(null, null)(BudgetView);
