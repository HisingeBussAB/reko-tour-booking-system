import React, { Component } from 'react';
import PropTypes from 'prop-types';





class ErrorPopup extends Component {

  
  closeMe = () => {
    this.props.sendClose(true);
  }

  

 


  render() {

    let style;
    let text;
    style = {
      color: '#0856fb',
      margin: '0 auto',
      position: 'absolute',
      top: '50%',
      transform: 'translateY(-50%)',
      textAlign: 'center',
      width: '100%',
      zIndex: '19000',
    };
    const textstyle = {
      paddingTop: '12px',
    };
    text = <p style={textstyle}>{this.props.message}</p>;
    
    return (<div className="Error" onClick={this.closeMe} style={style}>{text}</div>);

  }
}

ErrorPopup.propTypes = {
  message: PropTypes.string,
  sendClose: PropTypes.func,
};

export default (ErrorPopup);