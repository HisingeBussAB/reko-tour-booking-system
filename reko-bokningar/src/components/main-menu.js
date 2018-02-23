import React, { Component } from 'react';
import { connect } from 'react-redux';
import Logo from '../img/logo.gif';
import SearchIcon from '../img/searchicon.png';


class MainMenu extends Component {
  
  

  render() {

    const searchStyle = {
      backgroundImage: 'url(' + SearchIcon + ')',
    };
    
    return (
      <div className="MainMenu d-print-none">
        <nav className="my-4 mx-5">
          <div className="d-flex flex-wrap justify-content-between">
            <img src={Logo} alt="Logo" className="rounded custom-scale my-2 ml-3 mr-5" title="Till Startsida" id="mainLogo"/>
            <button className="text-uppercase font-weight-bold btn btn-primary btn-lg custom-scale custom-wide-text my-2 mx-3">Resor &amp; Bokningar</button>
            <button className="text-uppercase font-weight-bold btn btn-primary btn-lg custom-scale custom-wide-text my-2 mx-3">Kalkyler</button>
            <button className="text-uppercase font-weight-bold btn btn-primary btn-lg custom-scale custom-wide-text my-2 mx-3">Utskick</button>
          </div>
          <div className="d-flex flex-wrap justify-content-between my-4">
            <input type="search" placeholder="Bokningsnr eller namn" style={searchStyle} className="rounded my-2 mx-3"/>
            <select className="rounded my-2 mx-3">
              <option disabled selected hidden>Visa bokningsläge</option>
              <option>Resan kul</option>
            </select>
            <select className="rounded my-2 mx-3">
              <option disabled selected hidden>Sabbgenvägar</option>
              <option>Resan kul</option>
            </select>
          </div>
        </nav>

      </div>
    );
  }
}

export default connect(null, null)(MainMenu);
