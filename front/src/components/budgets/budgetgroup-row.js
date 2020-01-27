import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import {faSave, faSpinner, faTrash} from '@fortawesome/free-solid-svg-icons'
import {faSquare, faCheckSquare} from '@fortawesome/free-regular-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import {putItem, postItem, deleteItem} from '../../actions'
import ConfirmPopup from '../global/confirm-popup'

class BudgetGroupRow extends Component {
  /* NOTICE
  this.props.id
  recives -1 for new item
  output for new item must be id: 'new'
  */

  constructor (props) {
    super(props)
    const {label = ''} = this.props
    this.state = {
      updatingSave  : false,
      updatingActive: false,
      deleting      : false,
      label         : label,
      isConfirming  : false
    }
  }

  // eslint-disable-next-line camelcase
  UNSAFE_componentWillReceiveProps (nextProps) {
    const {id = 'new', label = 'new', isDisabled = false} = this.props
    if (nextProps.id !== id) {
    // for some reason id changed, component state needs reset.
      this.setState({
        updatingSave  : false,
        updatingActive: false,
        deleting      : false,
        label         : label,
        isConfirming  : false
      })
    }
    // cancel loaders on changes recived
    if (nextProps.label !== label) {
      this.setState({updatingSave: false})
    }
    if (nextProps.isDisabled !== isDisabled) {
      this.setState({updatingActive: false})
    }
  }
saveGroups = async (e) => {
  e.preventDefault()
  this.setState({updatingSave: true})
  const {putItem, postItem, isNew = false, isDisabled, id = 'new', remove = () => {}, index = null, submitToggle} = this.props
  const {label} = this.state
  submitToggle(true)
  const data = {
    label     : label,
    isDisabled: isDisabled
  }

  if (isNew) {
    if (await postItem('budgetgroups', data)) {
      remove(index)
      submitToggle(false)
    } else {
      submitToggle(false)
    }
  }

  if (!isNew) {
    if (await putItem('budgetgroups', id, data)) {
      remove(index)
      submitToggle(false)
    } else {
      submitToggle(false)
    }
  }
}

toggleActive = async (e, toggle) => {
  e.preventDefault()
  this.setState({updatingActive: true})
  const {label} = this.state
  const {putItem, isDisabled, id = 'new', submitToggle} = this.props
  submitToggle(true)
  const data = {
    label     : label,
    isDisabled: !isDisabled
  }

  if (!await putItem('budgetgroups', id, data)) {
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
  const {deleteItem, id = 'new', submitToggle} = this.props
  if (choice === true) {
    const {label} = this.state
    const data = {
      label: label
    }
    if (!await deleteItem('budgetgroups', id, data)) {
      this.setState({deleting: false})
    }
  } else {
    this.setState({deleting: false})
  }
  submitToggle(false)
}

handleLabelChange = (val) => {
  this.setState({label: val})
}

render () {
  const {label: propsLabel = 'new', isDisabled: propsActiveReversed = false, isNew} = this.props
  const propsActive = !propsActiveReversed
  const {
    label: stateLabel = 'new',
    updatingSave = false,
    updatingActive = false,
    deleting = false,
    isConfirming
  } = this.state

  return (

    <tr>
      <td className="align-middle pr-3 py-2 w-50">
        {isConfirming && <ConfirmPopup doAction={this.doDelete} message={'Vill du verkligen ta bort kalkylgruppen:\n' + propsLabel} />}
        <input value={stateLabel} onChange={(e) => this.handleLabelChange(e.target.value)} placeholder="Kalkylgruppsnamn" type="text" className="rounded w-100" maxLength="35" style={{minWidth: '200px'}} />
      </td>
      <td className="align-middle px-3 py-2 text-center">
        {(((stateLabel === '' || stateLabel === undefined) || (stateLabel !== propsLabel))) && !updatingSave &&
        <span title="Spara ändring i kalkylgruppen" className="primary-color custom-scale cursor-pointer"><FontAwesomeIcon icon={faSave} size="2x" onClick={(e) => this.saveGroups(e)} /></span>}
        {updatingSave &&
        <span title="Sparar ändring i kalkylgruppen..." className="primary-color"><FontAwesomeIcon icon={faSpinner} size="2x" pulse /></span> }
      </td>
      <td className="align-middle px-3 py-2 text-center">
        {updatingActive &&
        <span title="Sparar aktiv status..." className="primary-color"><FontAwesomeIcon icon={faSpinner} size="2x" pulse /></span> }
        {!updatingActive && propsActive && !isNew &&
        <span title="Inaktivera denna kalkylgrupp" className="primary-color custom-scale cursor-pointer"><FontAwesomeIcon icon={faCheckSquare} size="2x" onClick={(e) => this.toggleActive(e, false)} /></span> }
        {!updatingActive && !propsActive && !isNew &&
        <span title="Aktivera denna kalkylgrupp" className="primary-color custom-scale cursor-pointer"><FontAwesomeIcon icon={faSquare} onClick={(e) => this.toggleActive(e, true)} size="2x" /></span> }
        {!updatingActive && isNew &&
        <span title="Spara kategorin först" className="text-secondary custom-scale cursor-pointer"><FontAwesomeIcon icon={faCheckSquare} size="2x" onClick={null} /></span> }

      </td>
      <td className="align-middle pl-3 py-2 text-center">
        {!deleting &&
          <span title="Ta bort denna kalkylgruppen permanent" className="danger-color custom-scale cursor-pointer"><FontAwesomeIcon icon={faTrash} onClick={(e) => this.deleteConfirm(e)} size="2x" /></span>}
        {deleting &&
        <span title="Tar bort denna kalkylgrupp..." className="danger-color"><FontAwesomeIcon icon={faSpinner} size="2x" pulse /></span>}
      </td>
    </tr>
  )
}
}

BudgetGroupRow.propTypes = {
  label       : PropTypes.string,
  putItem     : PropTypes.func,
  postItem    : PropTypes.func,
  deleteItem  : PropTypes.func,
  remove      : PropTypes.func,
  id          : PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  isDisabled  : PropTypes.bool,
  isNew       : PropTypes.bool,
  // eslint-disable-next-line react/boolean-prop-naming
  submitToggle: PropTypes.bool,
  index       : PropTypes.number
}

const mapDispatchToProps = dispatch => bindActionCreators({
  putItem,
  postItem,
  deleteItem
}, dispatch)

export default connect(null, mapDispatchToProps)(BudgetGroupRow)
