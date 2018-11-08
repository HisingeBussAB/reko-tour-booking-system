import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import {faSave, faSpinner} from '@fortawesome/free-solid-svg-icons'
import {faSquare, faCheckSquare, faTrashAlt} from '@fortawesome/free-regular-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import {getItem, saveItem} from '../../actions'
import ConfirmPopup from '../global/confirm-popup'

class CategoriesRow extends Component {
  /* NOTICE
  this.props.id
  recives -1 for new item
  output for new item must be id: 'new'
  */

  constructor (props) {
    super(props)
    const {category = ''} = this.props
    this.state = {
      updatingSave  : false,
      updatingActive: false,
      deleting      : false,
      category      : category,
      isConfirming  : false
    }
  }

  componentWillReceiveProps (nextProps) {
    const {id = 'new', category = 'new', isActive = true} = this.props
    if (nextProps.id !== id) {
      // for some reason id changed, component state needs reset.
      this.setState({
        category      : nextProps.category,
        updatingSave  : false,
        updatingActive: false,
        deleting      : false
      })
    }
    // cancel loaders on changes recived
    if (nextProps.category !== category) {
      this.setState({updatingSave: false})
    }
    if (nextProps.isActive !== isActive) {
      this.setState({updatingActive: false})
    }
  }

  handleCategoryChange = (val) => {
    this.setState({category: val})
  }

  saveCategory = async (e) => {
    e.preventDefault()
    this.setState({updatingSave: true})
    const {saveItem, isNew = false, isActive, id = 'new', remove = () => {}, index = null, submitToggle} = this.props
    const {category} = this.state
    submitToggle(true)
    const action = isNew ? 'new' : 'save'
    const data = {
      category  : category,
      active    : isActive,
      categoryid: isNew ? 'new' : id,
      task      : 'save'
    }

    if (await saveItem('categories', data, action)) {
      remove(index)
      submitToggle(false)
    } else {
      submitToggle(false)
    }
  }

  toggleActive = async (e, toggle) => {
    e.preventDefault()
    this.setState({updatingActive: true})
    const {category} = this.state
    const {saveItem, isActive, id = 'new', submitToggle} = this.props
    submitToggle(true)
    const data = {
      category  : category,
      active    : !isActive,
      categoryid: id,
      task      : 'activetoggle'
    }

    if (!await saveItem('categories', data, 'save')) {
      this.setState({updatingActive: false})
    }
    submitToggle(false)
  }

  deleteConfirm = (e) => {
    e.preventDefault()
    const {submitToggle, isNew = false, remove = () => {}, index = null} = this.props
    submitToggle(true)
    this.setState({deleting: true})
    if (!isNew) {
      this.setState({isConfirming: true})
    } else {
      remove(index)
      submitToggle(false)
    }
  }

  doDelete = async (choice) => {
    this.setState({isConfirming: false})
    const {saveItem, id = 'new', submitToggle} = this.props
    if (choice === true) {
      const {category} = this.state
      const data = {
        category  : category,
        categoryid: id,
        task      : 'delete'
      }
      if (!await saveItem('categories', data, 'delete')) {
        this.setState({deleting: false})
      }
    } else {
      this.setState({deleting: false})
    }
    submitToggle(false)
  }

  render () {
    const {category: propsCategory = 'new', isActive: propsActive = false, isNew} = this.props
    const {
      category: stateCategory = 'new',
      updatingSave = false,
      updatingActive = false,
      deleting = false,
      isConfirming
    } = this.state
    return (

      <tr>
        <td className="align-middle pr-3 py-2 w-50">
          {isConfirming && <ConfirmPopup doAction={this.doDelete} message={'Vill du verkligen ta bort kategorin:\n' + propsCategory} />}
          <input value={stateCategory} onChange={(e) => this.handleCategoryChange(e.target.value)} placeholder="Kategorinamn" type="text" className="rounded w-100" maxLength="35" style={{minWidth: '200px'}} />
        </td>
        <td className="align-middle px-3 py-2 text-center">
          {(((stateCategory === '' || stateCategory) !== undefined && stateCategory !== propsCategory)) && !updatingSave &&
            <span title="Spara ändring i kategorin" className="primary-color custom-scale cursor-pointer"><FontAwesomeIcon icon={faSave} size="2x" onClick={(e) => this.saveCategory(e)} /></span>}
          {updatingSave &&
            <span title="Sparar ändring i kategorin..." className="primary-color"><FontAwesomeIcon icon={faSpinner} size="2x" pulse /></span> }
        </td>
        <td className="align-middle px-3 py-2 text-center">
          {updatingActive &&
            <span title="Sparar aktiv status..." className="primary-color"><FontAwesomeIcon icon={faSpinner} size="2x" pulse /></span> }
          {!updatingActive && propsActive && !isNew &&
            <span title="Inaktivera denna kategori" className="primary-color custom-scale cursor-pointer"><FontAwesomeIcon icon={faCheckSquare} size="2x" onClick={(e) => this.toggleActive(e, false)} /></span> }
          {!updatingActive && !propsActive && !isNew &&
            <span title="Aktivera denna kategori" className="primary-color custom-scale cursor-pointer"><FontAwesomeIcon icon={faSquare} onClick={(e) => this.toggleActive(e, true)} size="2x" /></span> }
          {!updatingActive && isNew &&
            <span title="Spara kategorin först" className="text-secondary custom-scale cursor-pointer"><FontAwesomeIcon icon={faCheckSquare} size="2x" onClick={null} /></span> }

        </td>
        <td className="align-middle pl-3 py-2 text-center">
          {!deleting &&
          <span title="Ta bort denna kategori permanent" className="danger-color custom-scale cursor-pointer"><FontAwesomeIcon icon={faTrashAlt} onClick={(e) => this.deleteConfirm(e)} size="2x" /></span>}
          {deleting &&
            <span title="Tar bort denna kategori..." className="danger-color"><FontAwesomeIcon icon={faSpinner} size="2x" pulse /></span>}
        </td>
      </tr>
    )
  }
}

CategoriesRow.propTypes = {
  category    : PropTypes.string,
  id          : PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  isActive    : PropTypes.bool,
  isNew       : PropTypes.bool,
  submitToggle: PropTypes.func,
  saveItem    : PropTypes.func,
  index       : PropTypes.number,
  remove      : PropTypes.func
}

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem,
  saveItem
}, dispatch)

export default connect(null, mapDispatchToProps)(CategoriesRow)
