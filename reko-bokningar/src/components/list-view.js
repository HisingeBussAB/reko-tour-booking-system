import React, { Component } from 'react';
import { connect } from 'react-redux';


class ListView extends Component {
  
  

  render() {

   
    return (
      <div className="ListView text-center pt-3">
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
            
          </div>
        </div>
      </div>
    </div>
    );
  }
}

export default connect(null, null)(ListView);
