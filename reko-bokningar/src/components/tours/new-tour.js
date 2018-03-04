import React, { Component } from 'react';
import { connect } from 'react-redux';
import DatePicker from 'react-datepicker';
import moment from 'moment';
import 'moment/locale/sv';
import update from 'react-addons-update';
import fontawesome from '@fortawesome/fontawesome';
import faPlus from '@fortawesome/fontawesome-free-solid/faPlus';
import faMinus from '@fortawesome/fontawesome-free-solid/faMinus';
import faSave from '@fortawesome/fontawesome-free-solid/faSave';
import faSpinner from '@fortawesome/fontawesome-free-solid/faSpinner';
import FontAwesomeIcon from '@fortawesome/react-fontawesome';
import Config from '../../config/config';
import axios from 'axios';
import PropTypes from 'prop-types';

fontawesome.library.add(faPlus, faMinus, faSave, faSpinner);

moment.locale('sv');


class NewTour extends Component {
  constructor (props) {
    super(props);
    this.state = {
      showError: false,
      showErrorMessage: '',
      isSubmitting: false,
      startDate: moment(),
      tourName: '',
      reservationFee: 300,
      insuranceFee: 150,
      roomTypes: [
        {type: 'Enkelrum',  price: '', reserved: ''},
        {type: 'Dubbelrum', price: '', reserved: ''}
      ]
    };
  }


  handleDateChange = (date) => {
    this.setState({
      startDate: date,
    });
  }

  handleChange = (key, val) => {
    this.setState({[key]: val});
  }

  handleRoomChange = (i, key, val) => {
    this.setState({roomTypes: update(this.state.roomTypes, {[i]: {[key]: {$set: val}}})});
  }

  roomOptions = (action, e) => {
    e.preventDefault();
    if (action === 'add') {
      const newRoomOpt = [{type: '', price: '', reserved: ''}];
      this.setState({roomTypes: update(this.state.roomTypes, {$push: newRoomOpt})});
    }
    if (action === 'remove') {
      const index = (this.state.roomTypes.length-1);
      this.setState({roomTypes: update(this.state.roomTypes, {$splice: [[index, 1]]})});
    }
  }

  handleSubmit = (e) => {
    e.preventDefault();
    this.setState({isSubmitting: true});
    axios.post( Config.ApiUrl + '/api/tours/savetour', {
      apitoken: Config.ApiToken,
      user: this.props.login.user,
      jwt: this.props.login.jwt,
      startDate: this.state.startDate,
      tourName: this.state.tourName,
      reservationFee: this.state.reservationFee,
      insuranceFee: this.state.insuranceFee,
      roomTypes: this.state.roomTypes,
    })
      .then(response => {
        console.log(response);
        this.setState({isSubmitting: false});
      })
      .catch(error => {
        console.log(error.response.data.response);
        console.log(error.response.data.login);
        this.setState({showError: true, showErrorMessage: error.response.data.response});
        this.setState({isSubmitting: false});
      });
    

  }


  render() {

    const roomRows = this.state.roomTypes.map((room, i) => 
      <div className="container-fluid m-0 p-0" key={i}>
      
        <div className="row mb-1">
          <div className="col-sm-12 col-md-6 text-left mb-1">
            <input value={room.type} onChange={(e) => this.handleRoomChange(i, 'type', e.target.value)} placeholder='Rumstyp' type='text' className="rounded w-75" maxLength="35" style={{minWidth: '300px'}} />
          </div>
          <div className="col-sm-12 col-md-3 text-left mb-1">
            <input value={room.price} onChange={(e) => this.handleRoomChange(i, 'price', e.target.value)} placeholder='0' type='number' pattern="[0-9]*" inputMode="numeric" maxLength="5" min="0" max="99999" className="rounded custom-price-input" /> kr
          </div>
          <div className="col-sm-12 col-md-3 text-left mb-1">
            <input value={room.reserved} onChange={(e) => this.handleRoomChange(i, 'reserved', e.target.value)} placeholder='0' type='number' pattern="[0-9]*" inputMode="numeric" maxLength="2" min="0" max="99" className="rounded d-block mr-auto custom-doubledigit-input" />
          </div>
        </div>
      </div>);
    

    return (
      <div className="TourViewNewTour">

        <form onSubmit={this.handleSubmit}>
          <fieldset disabled={this.state.isSubmitting}>
            <div className="container px-5">
              <h3 className="my-4 w-50 mx-auto">Skapa ny resa</h3>
              <div className="row mb-2">
                <div className="col-sm-12 col-md-6 text-left">
                  <label className="small d-block text-left pt-2 pl-1 text-left w-75 mr-auto">Resa:</label>
                  <input value={this.state.tourName} onChange={e => this.handleChange('tourName', e.target.value)} placeholder='Resans namn' type='text' className="rounded w-75 d-block mr-auto" maxLength="25" style={{minWidth: '300px'}} />
                </div>
                <div className="col-sm-12 col-md-6 text-left">
                  <label className="small d-block text-left pt-2 pl-1 text-left mr-auto date-picker-input">Datum:</label>
                  <div className="mr-auto">
                    <DatePicker
                      dateFormat="YYYY-MM-DD"
                      selected={this.state.startDate}
                      onChange={this.handleDateChange} 
                      showWeekNumbers
                      locale="sv"
                      disabled={this.state.isSubmitting}
                      className="rounded date-picker-input"
                    />
                  </div>
                </div>
              </div>

              <div className="row">
                <h5 className="col-12 d-block mt-4 text-left">Boende och priser:</h5>
                <h6 className="col-12 d-block text-left small">(för dagsresa lägg in &quot;Dagsresa&quot; som ett av alternativen för rumstyp)</h6>
              </div>
              <div className="row">
                <div className="col-sm-12 col-md-6 text-left">
                  <label className="small d-block text-left pt-2 pl-1">Rumstyp/Dagsresa:</label>
                </div>
                <div className="col-sm-12 col-md-3 text-left">
                  <label className="small d-block text-left pt-2 pl-1">Pris (inkl tillägg):</label>
                </div>
                <div className="col-sm-12 col-md-3 text-left">
                  <label className="small d-block mr-auto text-left pt-2 pl-1">Antal tillgängliga:</label>
                </div>
              </div>
              {roomRows}
              <div className="row">
                <div className="col-12 text-left">
                  <button disabled={this.state.isSubmitting} type="button" onClick={(e) => this.roomOptions('add', e)} title="Lägg till flera rumsalternativ" className="btn btn-primary mr-4 my-1 custom-scale"><FontAwesomeIcon icon="plus" size="1x" className="mt-1"/></button>
                  <button disabled={this.state.isSubmitting} type="button" onClick={(e) => this.roomOptions('remove', e)} title="Ta bort det nedersta rumsalternativet" className="btn btn-danger my-1 custom-scale"><FontAwesomeIcon icon="minus" size="1x" className="mt-1" /></button>
                </div>
              </div>
              <div className="row">
                <h5 className="col-12 d-block mt-4 text-left">Avbeställningskydd & anmälningsavgift:</h5>
              </div>     
              <div className="row">
              
                <div className="col-12 text-left">
                  <label className="small d-block text-left custom-price-input mr-auto pt-2">Anmälningsavgift:</label>
                  <input placeholder='300' value={this.state.reservationFee} onChange={e => this.handleChange('reservationFee', e.target.value)} pattern="[0-9]*" inputMode="numeric" maxLength="4" min="0" max="9999" type='number' className="rounded custom-price-input" /> kr
                </div>
                <div className="col-12 text-left">
                  <label className="small d-block mr-auto text-left custom-price-input pt-2">Avbeställningsskydd:</label>
                  <input placeholder='0' value={this.state.insuranceFee} onChange={e => this.handleChange('insuranceFee', e.target.value)} pattern="[0-9]*" inputMode="numeric" maxLength="4" min="0" max="9999" type='number' className="rounded custom-price-input" /> kr
                </div>
              </div>
              <div className="row mt-2">
                <div className="col-12 text-left">
                  <button disabled={this.state.isSubmitting} type="submit" title="Spara uppgifterna" className="btn btn-lg btn-primary text-uppercase font-weight-bold custom-scale custom-wide-text mt-4 px-5 d-flex flex-nowrap align-items-center">
                    { this.state.isSubmitting ? <span className="d-flex flex-nowrap align-items-center"><FontAwesomeIcon icon="spinner" pulse size="2x" className="mr-3 py-1"/><span className="mt-1 loading-text">Sparar</span></span>
                      : <span className="d-flex flex-nowrap align-items-center"><FontAwesomeIcon icon="save" size="2x" className="mr-3 py-1"/><span className="mt-1">Spara resan</span></span>}
                  </button>
                </div>
              </div>
            </div>
          </fieldset>
        </form>
        <div>{this.state.showErrorMessage}</div>
      </div>
    );
  }
}


NewTour.propTypes = {
  login:              PropTypes.object,
};

const mapStateToProps = state => ({
  login: state.login,
});


export default connect(mapStateToProps, null)(NewTour);