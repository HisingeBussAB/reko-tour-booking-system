import React, { Component } from 'react';
import { connect } from 'react-redux';
import update from 'react-addons-update';
import faSave from '@fortawesome/fontawesome-free-solid/faSave';
import faSquare from '@fortawesome/fontawesome-free-regular/faSquare';
import faCheckSquare from '@fortawesome/fontawesome-free-regular/faCheckSquare';
import faTrashAlt from '@fortawesome/fontawesome-free-regular/faTrashAlt';
import faSpinner from '@fortawesome/fontawesome-free-solid/faSpinner';
import faPlus from '@fortawesome/fontawesome-free-solid/faPlus';
import FontAwesomeIcon from '@fortawesome/react-fontawesome';
import Config from '../../config/config';
import axios from 'axios';
import PropTypes from 'prop-types';



class Categories extends Component {
  constructor (props) {
    super(props);
    this.state = {
      showError: false,
      showErrorMessage: '',
      isSubmitting: false,
      categories: [
        {id: '', category: 'Skidresa',  active: true},
        {id: '', category: 'Dagsresa',  active: false},
      ]
    };
  }


  addRow = () => {
    const newRow = [{id: '', category: '',  active: true}];
    this.setState({categories: update(this.state.categories, {$push: newRow})});
  }

  handleChange = (key, val) => {
    this.setState({[key]: val});
  }

  handleRoomChange = (i, key, val) => {
    this.setState({categories: update(this.state.roomTypes, {[i]: {[key]: {$set: val}}})});
  }

  roomOptions = (action, e) => {
    e.preventDefault();
    if (action === 'add') {
      const newRoomOpt = [{type: '', price: '', reserved: ''}];
      this.setState({categories: update(this.state.roomTypes, {$push: newRoomOpt})});
    }
    if (action === 'remove') {
      const index = (this.state.roomTypes.length-1);
      this.setState({categories: update(this.state.roomTypes, {$splice: [[index, 1]]})});
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

    const categoryRows = this.state.categories.map((category, i) => 
      
      <tr key={i}>
        <td className="align-middle pr-3 py-2 w-50">
          <input value={category.category} placeholder='Kategorinamn' type='text' className="rounded w-100" maxLength="35" style={{minWidth: '200px'}} />
        </td>
        <td className="align-middle px-3 py-2 text-center">
          <span title="Spara ändring i namnet på kategorin"><FontAwesomeIcon icon={faSave} size="2x" className="primary-color custom-scale"/></span>
        </td>   
        <td className="align-middle px-3 py-2 text-center">
          {category.active ? 
            <span title="Inaktivera denna kategori"><FontAwesomeIcon icon={faCheckSquare} size="2x" className="primary-color custom-scale"/></span>
            : <span title="Aktivera denna kategori"><FontAwesomeIcon icon={faSquare} size="2x" className="primary-color custom-scale"/></span> }
        </td>          
        <td className="align-middle pl-3 py-2 text-center">
          <span title="Ta bord denna kategori permanent"><FontAwesomeIcon icon={faTrashAlt} size="2x" className="danger-color custom-scale"/></span>
        </td>   
      </tr>);
    

    return (
      <div className="TourViewNewTour">

        <form onSubmit={this.handleSubmit}>
          <fieldset disabled={this.state.isSubmitting}>
            <div className="container text-left" style={{maxWidth: '650px'}}>
              <h3 className="my-4 w-50 mx-auto text-center">Resekategorier</h3>
              <table className="table table-hover w-100">
                <thead>
                  <tr>
                    <th span="col" className="pr-3 py-2 text-center w-50">Kategori</th>
                    <th span="col" className="px-3 py-2 text-center">Spara</th>
                    <th span="col" className="px-3 py-2 text-center">Aktiv</th>
                    <th span="col" className="pl-3 py-2 text-center">Ta bort</th>
                  </tr>
                </thead>
                <tbody>
                  {categoryRows}
                  <tr>
                    <td colSpan="4" className="py-2">
                      <button onClick={this.addRow} disabled={this.state.isSubmitting} type="button" title="Lägg till flera kategorier" className="btn btn-primary custom-scale">
                        <FontAwesomeIcon icon={faPlus} size="lg" className="mt-1"/>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </fieldset>
        </form>
        <div>{this.state.showErrorMessage}</div>
      </div>
    );
  }
}


Categories.propTypes = {
  login:              PropTypes.object,
};

const mapStateToProps = state => ({
  login: state.login,
});


export default connect(mapStateToProps, null)(Categories);