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
      showStatus: false,
      showStatusMessage: '',
      isSubmitting: false,
      categoriesSaved: [
        {id: '', category: 'Skidresa',  active: true},
        {id: '', category: 'Dagsresa',  active: false},
      ],
      categoriesUnsaved: [
        {id: '', category: 'Skidresa',  active: true},
        {id: '', category: 'Dagsresa',  active: false},
        {id: '', category: 'Smt else',  active: false},
      ]
    };
  }

  componentWillMount() {

  }


  addRow = () => {
    const newRow = [{id: '', category: '',  active: true}];
    this.setState({categoriesUnsaved: update(this.state.categoriesUnsaved, {$push: newRow})});
  }

  handleChange = (key, val) => {
    this.setState({[key]: val});
  }

  handleRoomChange = (i, key, val) => {
    this.setState({categoriesUnsaved: update(this.state.roomTypes, {[i]: {[key]: {$set: val}}})});
  }

  roomOptions = (action, e) => {
    e.preventDefault();
    if (action === 'add') {
      const newRoomOpt = [{type: '', price: '', reserved: ''}];
      this.setState({categoriesUnsaved: update(this.state.roomTypes, {$push: newRoomOpt})});
    }
    if (action === 'remove') {
      const index = (this.state.roomTypes.length-1);
      this.setState({categoriesUnsaved: update(this.state.roomTypes, {$splice: [[index, 1]]})});
    }
  }

  handleSubmit = (e, i, operation) => {
    e.preventDefault();

    if (operation === 'save') {
      if (this.state.categoriesUnsaved[i].id === '' || this.state.categoriesUnsaved[i].id === null) {
        operation = 'new';
      }
    }


    this.setState({isSubmitting: true});
    axios.post( Config.ApiUrl + '/api/token/submit', {
      apitoken: Config.ApiToken,
      user: this.props.login.user,
    })
      .then(response => {
        console.log(response);
        axios.post( Config.ApiUrl + '/api/tours/savecategory/' + operation, {
          submittoken: response.data.submittoken,
          apitoken: Config.ApiToken,
          user: this.props.login.user,
          jwt: this.props.login.jwt,
          categoryid: this.state.categoriesUnsaved[i].id,
          category: this.state.categoriesUnsaved[i].category,
          active: this.state.categoriesUnsaved[i].active,
        })
          .then(response => {
            console.log(response);
            this.setState({isSubmitting: false});
            this.setState({showStatus: true, showStatusMessage: response.data.response});
          })
          .catch(error => {
            console.log(error.response.data.response);
            console.log(error.response.data.login);
            this.setState({showStatus: true, showStatusMessage: error.response.data.response});
            this.setState({isSubmitting: false});
          });
      })
      .catch(error => {
        let message = 'Något har gått fel, får inget svar från API.';
        if (error.response !== undefined) {
          message = error.response.data.response;
        }
        this.setState({showStatus: true, showStatusMessage: message});
      });
  };

  

  render() {

    const categoryRows = this.state.categoriesUnsaved.map((category, i) => 
      
      <tr key={i}>
        <td className="align-middle pr-3 py-2 w-50">
          <input value={category.category} placeholder='Kategorinamn' type='text' className="rounded w-100" maxLength="35" style={{minWidth: '200px'}} />
        </td>
        <td className="align-middle px-3 py-2 text-center">
          {((this.state.categoriesSaved[i] === undefined) || (this.state.categoriesSaved[i] !== undefined && category.category !== this.state.categoriesSaved[i].category)) && 
            <span title="Spara ändring i kategorin"><FontAwesomeIcon icon={faSave} size="2x" className="primary-color custom-scale" onClick={(e) => this.handleSubmit(e, i, 'save')}/></span>}          
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
        {this.state.showStatus ? <div>{this.state.showStatusMessage}</div> : null}
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