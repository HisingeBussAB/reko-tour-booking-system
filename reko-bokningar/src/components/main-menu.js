import React, { Component } from 'react';
import { connect } from 'react-redux';
import Logo from '../img/logo.gif';
import SearchIcon from '../img/searchicon.png';
import { Link } from 'react-router-dom';
import PropTypes from 'prop-types';


class MainMenu extends Component {
  
  

  render() {

    const searchStyle = {
      backgroundImage: 'url(' + SearchIcon + ')',
    };
    
    return (
      <div className="MainMenu d-print-none">
        <nav className="my-1 mx-1">
          <div className="d-flex flex-wrap justify-content-between my-2 py-1">   
            <Link to={'/'}><img src={Logo} alt="Logo" className="rounded custom-scale my-2 mx-3" title="Till Startsida" id="mainLogo"/></Link>
            <Link to={'/bokningar/'} className="text-uppercase font-weight-bold btn btn-primary btn-lg custom-scale custom-wide-text my-2 mx-3 larger-btn">Resor &amp; Bokningar</Link>
            <Link to={'/kalkyler/'} className="text-uppercase font-weight-bold btn btn-primary btn-lg custom-scale custom-wide-text my-2 mx-3">Kalkyler</Link>
            <Link to={'/utskick/'} className="text-uppercase font-weight-bold btn btn-primary btn-lg custom-scale custom-wide-text my-2 mx-3 ">Utskick</Link>
          </div>
          <div className="d-flex flex-wrap justify-content-between mt-2 pb-1 pt-2">
            <input type="search" placeholder="Bokningsnr eller namn" style={searchStyle} className="rounded my-2 mx-3" />
            <select className="rounded my-2 mx-3">
              <option disabled selected hidden>BOKNINGSLÄGE</option>

              <option>Konstrundan i Skåne 30/3</option>
              <option>Konstrundan i Skåne 30/3</option>
              <option>Konstrundan i Skåne 30/3</option>
            </select>
            <select className="rounded my-2 mx-3">
              <option disabled selected hidden>SNABBGENVÄGAR</option>
              <option>Skapa resebokning</option>
              <option>Registrera betalning</option>
              <option>Registrera programbeställning</option>
            </select>
          </div>
        </nav>

      </div>
    );
  }
}

MainMenu.propTypes = {
  compressMenu:       PropTypes.bool,
};

const mapStateToProps = state => ({
  compressMenu: state.styles.mainmenu.compressed,
});

export default connect(mapStateToProps, null)(MainMenu);
