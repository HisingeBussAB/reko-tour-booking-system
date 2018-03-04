import React, { Component } from 'react';
import { connect } from 'react-redux';
import { Link } from 'react-router-dom';


class TourViewMain extends Component {
  

  render() {

   
    return (
      <div className="TourViewMain">


        <h3 className="my-4">Resor &amp; Bokningar</h3>
        <div className="container-fluid pt-2">
          <div className="row">
            <div className="col-lg-4 col-md-12">
              <h4 className="w-75 my-3 mx-auto">Bokningar</h4>
              <Link to={'/bokningar/nybokning'} className="btn w-75 btn-primary my-3 mx-auto py-2">Skapa ny bokning</Link>
              <button className="btn w-75 btn-primary my-3 mx-auto py-2">Skapa ny reservation</button>
              <button className="btn w-75 btn-primary my-4 mx-auto py-2">Spara programbeställningar</button>
              <p className="w-75 my-3 py-2 mx-auto px-1 text-justify d-block">Reservation används för att boka upp platser för förare/reseledare, preliminärbokningar &amp; grupper.</p>
            </div>
            <div className="col-lg-4 col-md-12">
              <h4 className="w-75 my-3 mx-auto">Betalningar</h4>
              <button className="btn w-75 btn-primary my-3 mx-auto py-2">Registrera betalning</button>
              <p className="w-75 my-3 py-2 mx-auto px-1 text-justify d-block">Senaste registrerad betalning:</p>
            </div>
            <div className="col-lg-4 col-md-12">
              <h4 className="w-75 my-3 mx-auto">Resor</h4>
              <Link to={'/bokningar/nyresa'} className="btn w-75 btn-primary my-3 mx-auto py-2">Skapa ny resa</Link>
              <button className="btn w-75 btn-primary my-3 mx-auto py-2">Ändra resekategorier</button>
              <p className="w-75 my-3 py-2 mx-auto px-1 text-justify d-block">Lista på resor här.</p>


            </div>
          </div>
        </div>
      </div>
    );
  }
}


export default connect(null, null)(TourViewMain);