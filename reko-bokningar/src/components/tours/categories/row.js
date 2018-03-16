import React, { Component } from 'react';
import { connect } from 'react-redux';
import faSave from '@fortawesome/fontawesome-free-solid/faSave';
import faSquare from '@fortawesome/fontawesome-free-regular/faSquare';
import faCheckSquare from '@fortawesome/fontawesome-free-regular/faCheckSquare';
import faTrashAlt from '@fortawesome/fontawesome-free-regular/faTrashAlt';
import faSpinner from '@fortawesome/fontawesome-free-solid/faSpinner';
import FontAwesomeIcon from '@fortawesome/react-fontawesome';
import PropTypes from 'prop-types';



class CategoriesRow extends Component {
  constructor (props) {
    super(props);
    this.state = {
      isUpdating: {save: [], activetoggle: [], delete: []},
      category: '', 
      id: 'new',
      active: true,
    };
  }

  componentDidMount() {
    this.setState({category: this.props.category});
  }

  componentWillRecieveProps(nextProps) {
    this.setState({category: nextProps.category, id: nextProps.id, active: nextProps.active});
  }

  handleCategoryChange = (val) => {
    this.setState({category: val});
  }

      
  render() {
    
    let i = this.props.KeyId;

    return (
      <tr>
        <td className="align-middle pr-3 py-2 w-50">
          <input value={this.state.category} onChange={(e) => this.handleCategoryChange(e.target.value)} placeholder='Kategorinamn' type='text' className="rounded w-100" maxLength="35" style={{minWidth: '200px'}} />
        </td>
        <td className="align-middle px-3 py-2 text-center">
          {(((this.state.categoriesSaved[i] === undefined) || (this.state.categoriesSaved[i] !== undefined && this.state.category.category !== this.state.categoriesSaved[i].category))) && !this.state.isUpdating.save.includes(i) &&
            <span title="Spara ändring i kategorin"><FontAwesomeIcon icon={faSave} size="2x" className="primary-color custom-scale" onClick={(e) => this.handleSend(e, i, 'save')}/></span>}  
          {this.state.isUpdating.save.includes(i) &&
            <span title="Inaktivera denna kategori"><FontAwesomeIcon icon={faSpinner} size="2x" pulse className="primary-color"/></span> }        
        </td>   
        <td className="align-middle px-3 py-2 text-center">
          {this.state.isUpdating.activetoggle.includes(i) && 
            <span title="Inaktivera denna kategori"><FontAwesomeIcon icon={faSpinner} size="2x" pulse className="primary-color"/></span> }        
          {!this.state.isUpdating.activetoggle.includes(i) && this.state.category.active &&
            <span title="Inaktivera denna kategori"><FontAwesomeIcon icon={faCheckSquare} size="2x" onClick={(e) => this.handleSend(e, i, 'activetoggle')} className="primary-color custom-scale"/></span> }
          {!this.state.isUpdating.activetoggle.includes(i) && !this.state.category.active &&  
            <span title="Aktivera denna kategori"><FontAwesomeIcon icon={faSquare} onClick={(e) => this.handleSend(e, i, 'activetoggle')} size="2x" className="primary-color custom-scale"/></span> }

        </td>          
        <td className="align-middle pl-3 py-2 text-center">
          {!this.state.isUpdating.delete.includes(i) && 
          <span title="Ta bord denna kategori permanent"><FontAwesomeIcon icon={faTrashAlt} onClick={(e) => this.handleSend(e, i, 'delete')} size="2x" className="danger-color custom-scale"/></span>}
          {this.state.isUpdating.delete.includes(i) && 
            <span title="Inaktivera denna kategori"><FontAwesomeIcon icon={faSpinner} size="2x" pulse className="danger-color"/></span>}
        </td>   
      </tr>
    );
  }
}


CategoriesRow.propTypes = {
  category:   PropTypes.string,
};



export default connect(null, null)(CategoriesRow);