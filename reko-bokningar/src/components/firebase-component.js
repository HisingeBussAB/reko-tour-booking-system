import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { bindActionCreators }from 'redux';
import { connect } from 'react-redux';
import firebase from '../config/firebase';

class FirebaseComponent extends Component {

  
  

 


  render() {

    return (
      <div className="firebase-handler" style={{display:'none'}}>
      </div>);

  }
}

FirebaseComponent.propTypes = {

};

const mapDispatchToProps = dispatch => bindActionCreators({

}, dispatch);


export default connect(null, mapDispatchToProps)(FirebaseComponent);