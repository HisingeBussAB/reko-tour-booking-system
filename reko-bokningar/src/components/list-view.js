import React, { Component } from 'react';
import { connect } from 'react-redux';


class ListView extends Component {
  
  

  render() {

   
    return (
      <div className="container-fluid ListView">
        Utskick
      </div>
    );
  }
}

export default connect(null, null)(ListView);
