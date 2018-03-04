import React, { Component } from 'react';
import { connect } from 'react-redux';
import Logo from '../img/logo.gif';
import SearchIcon from '../img/searchicon.png';
import { Link } from 'react-router-dom';


class MainMenu extends Component {
  constructor(props){
    super(props);
    this.state = {
      shortcutToggle: false,
      tourToggle: false,
    };    
  }
  
  shortcutToggler = (flag) => {
    if (flag === null) {
      this.state.shortcutToggle ? flag = false : flag = true;
    }
    this.setState({shortcutToggle: flag});
  }

  tourToggler = (flag) => {
    if (flag === null) {
      this.state.tourToggle ? flag = false : flag = true;
    }
    this.setState({tourToggle: flag});
  }

  render() {

    const searchStyle= {
      backgroundImage: 'url(' + SearchIcon + ')' 
    };

    const tours = 
    <div className="dropdown-wrapper my-2 mx-3 custom-order-sm-10">
      <div className="dropdown">
        <div className="list-group text-uppercase font-weight-bold dropdown-menu custom-dropdown" onMouseEnter={() => this.tourToggler(true)} onMouseLeave={() => this.tourToggler(false)}>
          <div className="list-group-item dropdown-toggle active custom-wide-text" onClick={() => this.tourToggler(null)}>Bokningsläge</div>
         
        </div>
      </div>
    </div>;

    const shortcuts = this.state.shortcutToggle ? [
      'Skapa bokning',
      'Programbeställningar',
      'Lägg in betalningar',
      'Ny resa',
      'Ny kalkyl',
    ] : [];
    
    
    return (
      <div className="MainMenu d-print-none">
 
        
      
        <nav className="my-1 mx-1">
          <div className="d-flex flex-wrap justify-content-between align-items-baseline my-2 py-1">   
            <Link to={'/'}><img src={Logo} alt="Logo" className="rounded custom-scale mx-3" title="Startsida" id="mainLogo"/></Link>
            <input type="search" placeholder="Bokningsnr eller namn" style={searchStyle} className="rounded my-2 mx-3" />
            <div className="dropdown-wrapper dropdown-wrapper-smaller my-2 mx-3 custom-order-sm-9">
              <div className="dropdown">
                <div className="list-group text-uppercase font-weight-bold dropdown-menu dropdown-menu-smaller custom-dropdown" onMouseEnter={() => this.shortcutToggler(true)} onMouseLeave={() => this.shortcutToggler(false)}>
                  <div className="list-group-item dropdown-toggle active custom-wide-text" onClick={() => this.shortcutToggler(null)} >Genvägar</div>
                  {shortcuts.map((shortcut, i) => { 
                    return <div className="list-group-item dropdown-item" key={i}>{shortcut}</div>;})
                  }
                  
                  
                </div>
              </div>
            </div>
            {tours}
            <Link to={'/bokningar/'} title="Resor, bokningar och betalningar" className="text-uppercase font-weight-bold btn btn-primary custom-scale custom-wide-text my-2 mx-3 larger-btn">Bokningar</Link>
            <Link to={'/kalkyler/'} title="Resekalkyler" className="text-uppercase font-weight-bold btn btn-primary custom-scale custom-wide-text my-2 mx-3">Kalkyler</Link>
            <Link to={'/utskick/'} title="Adresslistor för utskick" className="text-uppercase font-weight-bold btn btn-primary custom-scale custom-wide-text my-2 mx-3 ">Utskick</Link>
          </div>
        </nav>
          
      </div>
    );
  }
}




export default connect(null, null)(MainMenu);
