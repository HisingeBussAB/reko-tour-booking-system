import React, { Component } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators }from 'redux';
import faSave from '@fortawesome/fontawesome-free-solid/faSave';
import faSquare from '@fortawesome/fontawesome-free-regular/faSquare';
import faCheckSquare from '@fortawesome/fontawesome-free-regular/faCheckSquare';
import faTrashAlt from '@fortawesome/fontawesome-free-regular/faTrashAlt';
import faSpinner from '@fortawesome/fontawesome-free-solid/faSpinner';
import FontAwesomeIcon from '@fortawesome/react-fontawesome';
import PropTypes from 'prop-types';


class CategoriesRow extends Component {
  /* NOTICE
  this.props.id
  recives -1 for new item
  output for new item must be id: 'new'
  */

  constructor (props) {
    super(props);
    this.state = {
      updatingSave: false,
      updatingActive: false,
      deleting: false,
      category: '',
    };
  }

  componentDidMount() {
    this.setState({category: this.props.category});
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.id !== this.props.id) {
      //for some reason id changed, component state needs reset.
      this.setState({
        category: nextProps.category,
        updatingSave: false,
        updatingActive: false,
        deleting: false,
      });
    }
    //cancel loaders on changes recived
    if (nextProps.category !== this.props.category) {
      this.setState({updatingSave: false});
    }
    if (nextProps.active !== this.props.active) {
      this.setState({updatingActive: false});
    }
  }

  handleCategoryChange = (val) => {
    this.setState({category: val});
  }

  saveCategory = (e, val) => {
    e.preventDefault();
    this.props.submitToggle(true);
    this.setState({updatingSave: true});
    console.log(val)
  }

  render() {

    return (
      <tr>
        <td className="align-middle pr-3 py-2 w-50">
          <input value={this.state.category} onChange={(e) => this.handleCategoryChange(e.target.value)} placeholder='Kategorinamn' type='text' className="rounded w-100" maxLength="35" style={{minWidth: '200px'}} />
        </td>
        <td className="align-middle px-3 py-2 text-center">
          {(((this.state.category === '') || (this.state.category !== undefined && this.state.category !== this.props.category))) && !this.state.updatingSave &&
            <span title="Spara ändring i kategorin"><FontAwesomeIcon icon={faSave} size="2x" className="primary-color custom-scale" onClick={(e) => this.saveCategory(e, e.target.value)}/></span>}
          {this.state.updatingSave &&
            <span title="Sparar ändring i kategorin..."><FontAwesomeIcon icon={faSpinner} size="2x" pulse className="primary-color"/></span> }
        </td>
        <td className="align-middle px-3 py-2 text-center">
          {this.state.updatingActive &&
            <span title="Sparar aktiv status..."><FontAwesomeIcon icon={faSpinner} size="2x" pulse className="primary-color"/></span> }
          {!this.state.updatingActive && this.props.active &&
            <span title="Inaktivera denna kategori"><FontAwesomeIcon icon={faCheckSquare} size="2x" onClick={null} className="primary-color custom-scale"/></span> }
          {!this.state.updatingActive && !this.props.active &&
            <span title="Aktivera denna kategori"><FontAwesomeIcon icon={faSquare} onClick={null} size="2x" className="primary-color custom-scale"/></span> }

        </td>
        <td className="align-middle pl-3 py-2 text-center">
          {!this.state.deleting &&
          <span title="Ta bord denna kategori permanent"><FontAwesomeIcon icon={faTrashAlt} onClick={null} size="2x" className="danger-color custom-scale"/></span>}
          {this.state.deleting &&
            <span title="Inaktivera denna kategori"><FontAwesomeIcon icon={faSpinner} size="2x" pulse className="danger-color"/></span>}
        </td>
      </tr>
    );
  }
}


CategoriesRow.propTypes = {
  category:     PropTypes.string,
  id:           PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  active:       PropTypes.bool,
  submitToggle: PropTypes.func,

};

const mapDispatchToProps = dispatch => bindActionCreators({

}, dispatch);


export default connect(null, mapDispatchToProps)(CategoriesRow);
