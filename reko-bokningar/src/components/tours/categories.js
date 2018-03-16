import React, { Component } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators }from 'redux';
import faSave from '@fortawesome/fontawesome-free-solid/faSave';
import faSquare from '@fortawesome/fontawesome-free-regular/faSquare';
import faCheckSquare from '@fortawesome/fontawesome-free-regular/faCheckSquare';
import faTrashAlt from '@fortawesome/fontawesome-free-regular/faTrashAlt';
import faSpinner from '@fortawesome/fontawesome-free-solid/faSpinner';
import faPlus from '@fortawesome/fontawesome-free-solid/faPlus';
import FontAwesomeIcon from '@fortawesome/react-fontawesome';
import PropTypes from 'prop-types';
import {getCategories, loading} from '../../actions';
import CategoriesRow from './categories/row';



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
    this.reduxGetAllUpdate();

  }

  componentWillUnmount() {
    this.reduxGetAllUpdate();
  }

  reduxGetAllUpdate = () => {this.props.getCategories({
    user: this.props.login.user,
    jwt: this.props.login.jwt,
    categoryid: 'all',
  });
  }




  

  render() {

    const categoriesArray = Object.values(this.props.categories);
    console.log(categoriesArray);
    

    let categoryRows;
    try {
      categoryRows = categoriesArraySorted.map((category, i) => {
        <CategoriesRow key={i} KeyId={i} id={category.id} category={category.category} active={category.active} />;
      });} catch(e) {
      categoryRows = null;
    }

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
  categories:         PropTypes.object,
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