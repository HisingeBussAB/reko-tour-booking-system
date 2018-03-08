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
      isSubmitting: true,
      categoriesSaved: [],
      categoriesUnsaved: [],
    };
  }

  componentWillMount() {
    this.getCategory('all');
  }

  getCategory = (id) => {
    axios.post( Config.ApiUrl + '/api/tours/category/get', {
      apitoken: Config.ApiToken,
      user: this.props.login.user,
      jwt: this.props.login.jwt,
      categoryid: id,
    })
      .then(response => {
        if (response.data.category !== undefined) {
          if (response.data.category.length > 1) {
            this.setState({categoriesSaved: response.data.category, categoriesUnsaved: response.data.category});
          } else {
            let i = this.state.categoriesSaved.findIndex(function (obj) { return obj.id === response.data.category[0].id; });
            this.setState({categoriesUnsaved: update(this.state.categoriesUnsaved, {[i]: {$set: response.data.category[0]}})});
            this.setState({categoriesSaved: update(this.state.categoriesSaved, {[i]: {$set: response.data.category[0]}})});
          }
        }
        if (response.data.response !== undefined) {
          this.setState({showStatus: true, showStatusMessage: response.data.response});
        }
        this.setState({isSubmitting: false});
      })
      .catch(error => {
        this.setState({showStatus: true, showStatusMessage: error.response.data.response});
        this.setState({isSubmitting: false});
      });

  }

  addRow = () => {
    const newRow = [{id: '', category: '',  active: true}];
    this.setState({categoriesUnsaved: update(this.state.categoriesUnsaved, {$push: newRow})});
  }

  
  handleCategoryChange = (i, key, val) => {
    this.setState({categoriesUnsaved: update(this.state.categoriesUnsaved, {[i]: {[key]: {$set: val}}})});
  }


  handleSend = (e, i, operationin) => {
    e.preventDefault();
    this.setState({isSubmitting: true});
    let operation = operationin;
    let active = this.state.categoriesUnsaved[i].active;
    if (operationin === 'save') {
      if (this.state.categoriesUnsaved[i].id === '' || this.state.categoriesUnsaved[i].id === null) {
        operation = 'new';
      } else {
        operation = 'save';
      }
    }

    if (operationin === 'activetoggle') {
      if (this.state.categoriesUnsaved[i].id === '' || this.state.categoriesUnsaved[i].id === null) {
        this.setState({showStatus: true, showStatusMessage: 'Du kan inte ändra aktiv status på en kategori som inte är sparad. Spara kategorin först.'});
        return;
      }
      if (this.state.categoriesUnsaved[i].category !== this.state.categoriesSaved[i].category) {
        this.setState({showStatus: true, showStatusMessage: 'Du kan inte ändra status på en kategori med namnändring som inte är sparad ännu. Spara namnet först.'});
        return;
      }
      active = active ? false : true;
      operation = 'save';
    }

    if (operationin === 'delete') {
      if (this.state.categoriesUnsaved[i].id === '' || this.state.categoriesUnsaved[i].id === null) {
        this.setState({categoriesUnsaved: update(this.state.categoriesUnsaved, {$splice: [[i, 1]]})});
        return;
      }
      operation = 'delete';
    }



    this.setState({isSubmitting: true});
    axios.post( Config.ApiUrl + '/api/token/submit', {
      apitoken: Config.ApiToken,
      user: this.props.login.user,
    })
      .then(response => {
        axios.post( Config.ApiUrl + '/api/tours/category/' + operation, {
          submittoken: response.data.submittoken,
          apitoken: Config.ApiToken,
          user: this.props.login.user,
          jwt: this.props.login.jwt,
          task: operationin,
          categoryid: this.state.categoriesUnsaved[i].id,
          category: this.state.categoriesUnsaved[i].category,
          active: active,
        })
          .then(response => {
            if (response.data.modifiedid !== undefined) {
              this.getCategory(response.data.modifiedid);
            }
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
          <input value={category.category} onChange={(e) => this.handleCategoryChange(i, 'category', e.target.value)} placeholder='Kategorinamn' type='text' className="rounded w-100" maxLength="35" style={{minWidth: '200px'}} />
        </td>
        <td className="align-middle px-3 py-2 text-center">
          {((this.state.categoriesSaved[i] === undefined) || (this.state.categoriesSaved[i] !== undefined && category.category !== this.state.categoriesSaved[i].category)) && 
            <span title="Spara ändring i kategorin"><FontAwesomeIcon icon={faSave} size="2x" className="primary-color custom-scale" onClick={(e) => this.handleSend(e, i, 'save')}/></span>}          
        </td>   
        <td className="align-middle px-3 py-2 text-center">
          {category.active ? 
            <span title="Inaktivera denna kategori"><FontAwesomeIcon icon={faCheckSquare} size="2x" onClick={(e) => this.handleSend(e, i, 'activetoggle')} className="primary-color custom-scale"/></span>
            : <span title="Aktivera denna kategori"><FontAwesomeIcon icon={faSquare} onClick={(e) => this.handleSend(e, i, 'activetoggle')} size="2x" className="primary-color custom-scale"/></span> }
        </td>          
        <td className="align-middle pl-3 py-2 text-center">
          <span title="Ta bord denna kategori permanent"><FontAwesomeIcon icon={faTrashAlt} onClick={(e) => this.handleSend(e, i, 'delete')} size="2x" className="danger-color custom-scale"/></span>
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