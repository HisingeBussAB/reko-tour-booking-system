import React, { Component } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators }from 'redux';
import update from 'immutability-helper';
import faSave from '@fortawesome/fontawesome-free-solid/faSave';
import faSquare from '@fortawesome/fontawesome-free-regular/faSquare';
import faCheckSquare from '@fortawesome/fontawesome-free-regular/faCheckSquare';
import faTrashAlt from '@fortawesome/fontawesome-free-regular/faTrashAlt';
import faSpinner from '@fortawesome/fontawesome-free-solid/faSpinner';
import faPlus from '@fortawesome/fontawesome-free-solid/faPlus';
import FontAwesomeIcon from '@fortawesome/react-fontawesome';
import Config from '../../config/config';
import {myAxios} from '../../config/axios';
import PropTypes from 'prop-types';
import {getCategories, loading} from '../../actions';



class Categories extends Component {
  constructor (props) {
    super(props);
    this.state = {
      showStatus: false,
      showStatusMessage: '',
      isSubmitting: true,
      isUpdating: {save: [], activetoggle: [], delete: []},
      categoriesSaved: [],
      categoriesUnsaved: [],
    };
  }

  componentWillMount() {
    if (typeof this.props.categories === 'object' && this.props.categories.length > 0) {
      this.setState({categoriesSaved: this.props.categories, categoriesUnsaved: this.props.categories}); 
    } else {
      this.getCategory('all');
    }
  }

  componentWillUnmount() {
    //Load updated categories to redux
    this.reduxUpdate();
  }

  reduxUpdate = () => {this.props.getCategories({
    user: this.props.login.user,
    jwt: this.props.login.jwt,
    categoryid: 'all',
  });
  }

  getCategory = (id, fromUpdate = null) => {
    this.props.loading(true);
    myAxios.post( Config.ApiUrl + '/tours/category/get', {
      user: this.props.login.user,
      jwt: this.props.login.jwt,
      categoryid: id,
    })
      .then(response => {
        if (response.data.category !== undefined) {
          if (response.data.category.length > 1) {
            this.setState({categoriesSaved: response.data.category, categoriesUnsaved: response.data.category});
            if (this.props.categories !== response.data.category) {
              //Redux state missmatch, update
              this.reduxUpdate();
            }
          } else {
            let i = this.state.categoriesSaved.findIndex(function (obj) { return obj.id === response.data.category[0].id; });
            this.setState({categoriesSaved: update(this.state.categoriesSaved, {[i]: {$set: response.data.category[0]}}), categoriesUnsaved: update(this.state.categoriesUnsaved, {[i]: {$set: response.data.category[0]}})});
            if (fromUpdate !== null) {
              this.setState({isUpdating: update(this.state.isUpdating, {[fromUpdate] : {$apply: (x) => {x.pop(i); return x;}}})});
            }
          }
          
        }
        if (response.data.response !== undefined) {
          this.setState({showStatus: true, showStatusMessage: response.data.response});
        }
        this.setState({isSubmitting: false});
        this.props.loading(false);
      })
      .catch(error => {
        this.props.loading(false);
        if (error.response === undefined) {
          this.setState({isSubmitting: false, showStatus: true, showStatusMessage: 'Okänt fel. Inget svar från API.'});
        } else {
          this.setState({isSubmitting: false, showStatus: true, showStatusMessage: error.response.data.response});
        }
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
    this.props.loading(true);
    this.setState({isSubmitting: true});
    let operation = operationin;
    let active = this.state.categoriesUnsaved[i].active;
    if (operationin === 'save') {
      this.setState({isUpdating: update(this.state.isUpdating, {save: {$push: [i]}})});
      if (this.state.categoriesUnsaved[i].id === '' || this.state.categoriesUnsaved[i].id === null) {
        operation = 'new';
      } else {
        operation = 'save';
      }
    }

    if (operationin === 'activetoggle') {
      
      if (this.state.categoriesUnsaved[i].id === '' || this.state.categoriesUnsaved[i].id === null) {
        this.setState({isSubmitting: false, showStatus: true, showStatusMessage: 'Du kan inte ändra aktiv status på en kategori som inte är sparad. Spara kategorin först.'});
        this.props.loading(false);
        return;
      }
      if (this.state.categoriesUnsaved[i].category !== this.state.categoriesSaved[i].category) {
        this.setState({isSubmitting: false, showStatus: true, showStatusMessage: 'Du kan inte ändra status på en kategori med namnändring som inte är sparad ännu. Spara namnet först.'});
        this.props.loading(false);
        return;
      }
      this.setState({isUpdating: update(this.state.isUpdating, {activetoggle: {$push: [i]}})});
      active = active ? false : true;
      operation = 'save';
    }

    if (operationin === 'delete') {
      if (this.state.categoriesUnsaved[i].id === '' || this.state.categoriesUnsaved[i].id === null) {
        this.setState({isSubmitting: false, categoriesUnsaved: update(this.state.categoriesUnsaved, {$splice: [[i, 1]]})});
        return;
      }
      this.setState({isUpdating: update(this.state.isUpdating, {delete: {$push: [i]}})});
      operation = 'delete';
    }



    this.setState({isSubmitting: true});
    this.props.loading(true);
    myAxios.post( Config.ApiUrl + '/tours/category/' + operation, {
      user: this.props.login.user,
      jwt: this.props.login.jwt,
      task: operationin,
      categoryid: this.state.categoriesUnsaved[i].id,
      category: this.state.categoriesUnsaved[i].category,
      active: active,
    })
      .then(response => {
        if (response.data.modifiedid !== undefined) {
          if (operationin !== 'delete') {
            this.getCategory(response.data.modifiedid, operationin);
          } else {
            let i = this.state.categoriesSaved.findIndex(function (obj) { return obj.id === this.state.categoriesUnsaved[i].id; });
            this.setState({isUpdating: update(this.state.isUpdating, {delete: {$apply: (x) => {x.pop(i); return x;}}})});
          }
        }
        this.setState({isSubmitting: false, showStatus: true, showStatusMessage: response.data.response});
        this.props.loading(false);
      })
      .catch(error => {
        this.setState({isSubmitting: false, showStatus: true, showStatusMessage: error.response.data.response});
        this.props.loading(false);
      });

  };



  

  render() {

    const categoryRows = this.state.categoriesUnsaved.map((category, i) => 
      
      <tr key={i}>
        <td className="align-middle pr-3 py-2 w-50">
          <input value={category.category} onChange={(e) => this.handleCategoryChange(i, 'category', e.target.value)} placeholder='Kategorinamn' type='text' className="rounded w-100" maxLength="35" style={{minWidth: '200px'}} />
        </td>
        <td className="align-middle px-3 py-2 text-center">
          {(((this.state.categoriesSaved[i] === undefined) || (this.state.categoriesSaved[i] !== undefined && category.category !== this.state.categoriesSaved[i].category))) && !this.state.isUpdating.save.includes(i) &&
            <span title="Spara ändring i kategorin"><FontAwesomeIcon icon={faSave} size="2x" className="primary-color custom-scale" onClick={(e) => this.handleSend(e, i, 'save')}/></span>}  
          {this.state.isUpdating.save.includes(i) &&
            <span title="Inaktivera denna kategori"><FontAwesomeIcon icon={faSpinner} size="2x" pulse className="primary-color"/></span> }        
        </td>   
        <td className="align-middle px-3 py-2 text-center">
          {this.state.isUpdating.activetoggle.includes(i) && 
            <span title="Inaktivera denna kategori"><FontAwesomeIcon icon={faSpinner} size="2x" pulse className="primary-color"/></span> }        
          {!this.state.isUpdating.activetoggle.includes(i) && category.active &&
            <span title="Inaktivera denna kategori"><FontAwesomeIcon icon={faCheckSquare} size="2x" onClick={(e) => this.handleSend(e, i, 'activetoggle')} className="primary-color custom-scale"/></span> }
          {!this.state.isUpdating.activetoggle.includes(i) && !category.active &&  
            <span title="Aktivera denna kategori"><FontAwesomeIcon icon={faSquare} onClick={(e) => this.handleSend(e, i, 'activetoggle')} size="2x" className="primary-color custom-scale"/></span> }

        </td>          
        <td className="align-middle pl-3 py-2 text-center">
          {!this.state.isUpdating.delete.includes(i) && 
          <span title="Ta bord denna kategori permanent"><FontAwesomeIcon icon={faTrashAlt} onClick={(e) => this.handleSend(e, i, 'delete')} size="2x" className="danger-color custom-scale"/></span>}
          {this.state.isUpdating.delete.includes(i) && 
            <span title="Inaktivera denna kategori"><FontAwesomeIcon icon={faSpinner} size="2x" pulse className="danger-color"/></span>}
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
  getCategories:      PropTypes.func,
  loading:            PropTypes.func,
  categories:         PropTypes.array,
};

const mapStateToProps = state => ({
  login: state.login,
  showStatus: state.errorPopup.visible,
  showStatusMessage: state.errorPopup.message,
  categories: state.tours.categories,
});

const mapDispatchToProps = dispatch => bindActionCreators({
  getCategories,
  loading
}, dispatch);



export default connect(mapStateToProps, mapDispatchToProps)(Categories);