import React, { Component } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators }from 'redux';
import faPlus from '@fortawesome/fontawesome-free-solid/faPlus';
import FontAwesomeIcon from '@fortawesome/react-fontawesome';
import PropTypes from 'prop-types';
import {getCategories, loading} from '../../actions';
import CategoriesRow from './categories/row';
import update from 'immutability-helper';


class Categories extends Component {
  constructor (props) {
    super(props);
    this.state = {
      showStatus: false,
      showStatusMessage: '',
      isSubmitting: false,
      extracategories: [],
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

  addRow = () => {
    const newcategory = {
      id: 'new',
      category: '',
      active: true,
    };
    const newextracategories = update(this.state.extracategories, {$push: [newcategory]});

    this.setState({extracategories: newextracategories});
    console.log(this.state.extracategories)
  }


  submitToggle = (b) => {
    let validatedb;
    try {
      validatedb = b ? true : false;
    } catch(e) {
      validatedb = false;
    }
    this.setState({isSubmitting: validatedb});
  }


  render() {
    console.log(this.state.extracategories);
    console.log("rendering...")
    let categoryRows;
    try {
      categoryRows = this.props.categories.map((category) => {
        return <CategoriesRow key={category.id} id={category.id} category={category.category} active={category.active} submitToggle={this.submitToggle}/>;
      });} catch(e) {
      categoryRows = null;
    }
    this.state.extracategories.forEach((item, i) => {
      console.log('here is an extra')
      categoryRows.push(<CategoriesRow key={('new' + i)} id={item.id} category={item.category} active={item.active} submitToggle={this.submitToggle}/>);
    });

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
