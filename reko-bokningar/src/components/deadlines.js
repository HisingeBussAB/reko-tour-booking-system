import React, { Component } from 'react';
import { connect } from 'react-redux';


class Deadlines extends Component {


  render() {


    return (
      <div className="Deadlines text-center">
        <h3>Deadlines</h3>
        <table></table>

      </div>
    );
  }
}

export default connect(null, null)(Deadlines);
