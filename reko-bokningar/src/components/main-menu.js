import React, { Component } from 'react';
import { connect } from 'react-redux';
import Loader from './loader';
import Logo from '../img/logo.gif';


class MainMenu extends Component {
  
  

  render() {
    
    return (
      <div className="MainMenu">
        <img src={Logo} alt="Logo"/>
      </div>
    );
  }
}

export default connect(null, null)(MainMenu);
