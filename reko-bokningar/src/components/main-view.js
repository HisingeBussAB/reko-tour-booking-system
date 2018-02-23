import React, { Component } from 'react';
import { connect } from 'react-redux';
import Deadlines from './deadlines';
import LatePayments from './late-payments';


class MainView extends Component {
  
  

  render() {

   
    return (
      <div className="container-fluid MainView">
        <div className="row">
          <div className="col-md-6 col-sm-12 col-xs-12 custom-border-right px-2 py-4">
            <LatePayments />
          </div>
          <div className="col-md-6 col-sm-12 col-xs-12 px-2 py-4">
            <Deadlines />
          </div>
        </div>
      </div>
    );
  }
}

export default connect(null, null)(MainView);
