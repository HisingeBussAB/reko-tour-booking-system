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
import PropTypes from 'prop-types';
import {getCategories,setCategories} from '../../actions';


class Categories extends Component {
  constructor (props) {
    super(props);
    this.state = {
      showStatus: false,
      showStatusMessage: '',
      isSubmitting: false,
      isUpdating: {save: [], activetoggle: [], delete: []},
      categoriesUnsaved: [],
    };
  }

  componentWillMount() {
    this.props.getCategories('tours/category/get', {
      user: this.props.login.user,
      jwt: this.props.login.jwt,
      categoryid: 'all',
    });

  }

  componentWillReceiveProps(nextProps) {
    this.setState({categoriesUnsaved: [...nextProps.categoriesSaved]});
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
    this.props.setCategories('tours/category/' + operationin, {
      user: this.props.login.user,
      jwt: this.props.login.jwt,
      categoryid: this.state.categoriesUnsaved[i].id,
      task: operationin,
      category: this.state.categoriesUnsaved[i].category,
      //active: active,
    });
  };



  

  render() {

    let catRowtemp;
    try {
      catRowtemp = this.state.categoriesUnsaved.map((category, i) => 
        
        <tr key={i}>
          <td className="align-middle pr-3 py-2 w-50">
            <input value={category.category} onChange={(e) => this.handleCategoryChange(i, 'category', e.target.value)} placeholder='Kategorinamn' type='text' className="rounded w-100" maxLength="35" style={{minWidth: '200px'}} />
          </td>
          <td className="align-middle px-3 py-2 text-center">
            {(((this.props.categoriesSaved[i] === undefined) || (this.props.categoriesSaved[i] !== undefined && category.category !== this.props.categoriesSaved[i].category))) && !this.state.isUpdating.save.includes(i) &&
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
    } catch(error) {
      catRowtemp = null;
    }

    const categoryRows = catRowtemp;
    

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
  categoriesSaved:    PropTypes.array,
  getCategories:      PropTypes.func,
  setCategories:      PropTypes.func,
};

const mapStateToProps = state => ({
  login: state.login,
  categoriesSaved: state.tours.categories,
});

const mapDispatchToProps = dispatch => bindActionCreators({
  getCategories,
  setCategories,
}, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(Categories);