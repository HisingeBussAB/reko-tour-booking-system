import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import {faPencilAlt, faSpinner} from '@fortawesome/free-solid-svg-icons'
import {faSquare, faCheckSquare, faTrashAlt} from '@fortawesome/free-regular-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import { getItem, putItem, postItem, deleteItem } from '../../actions'
import { Link } from 'react-router-dom'
import ConfirmPopup from '../global/confirm-popup'

class TourRow extends Component {
  constructor (props) {
    super(props)
    const {label = ''} = this.props
    this.state = {
      updatingDisabled: false,
      deleting        : false,
      label           : label,
      isConfirming    : false
    }
  }

  // key={'tour' + tour.id} id={tour.id} tour={tour.tour} isActive={tour.active} departure={tour.departure}

  componentWillReceiveProps (nextProps) {
    const {id = 'new', label = 'new', isDisabled = false} = this.props
    if (nextProps.id !== id) {
      // for some reason id changed, component state needs reset.
      this.setState({
        updatingDisabled: false,
        deleting        : false,
        label           : label,
        isConfirming    : false
      })
    }
    // cancel loaders on changes recived
    if (nextProps.isActive !== isDisabled) {
      this.setState({updatingDisabled: false})
    }
  }

  toggleDisabled = async (e, toggle) => {
    e.preventDefault()
    this.setState({updatingDisabled: true})
    const {label} = this.state
    const {putItem, isDisabled, id = 'new', submitToggle} = this.props
    submitToggle(true, 'tour')
    const data = {
      label     : category,
      isDisabled: !isDisabled
    }

    if (!await putItem('categories', id, data)) {
      this.setState({updatingActive: false})
    }
    submitToggle(false)


    e.preventDefault()
    this.setState({updatingDisabled: true})
    const {tour = '', saveItem, isActive, id = 'new'} = this.props

    const data = {
      label     : category,
      isDisabled: !isDisabled
    }
    /*
    if (!await saveItem('tours', data, 'save')) {
      this.setState({updatingActive: false})
    } */
  }

  deleteConfirm = (e) => {
    e.preventDefault()
    this.setState({deleting: true})
    this.setState({isConfirming: true})
  }

  doDelete = async (choice) => {
    this.setState({isConfirming: false})
    const {tour = '', saveItem, id = 'new'} = this.props

    const data = {
      tour  : tour,
      tourid: id,
      task  : 'delete'
    }
    if (choice === true) {
      if (/*! await saveItem('tours', data, 'delete') */true) {
        this.setState({deleting: false})
      }
    } else {
      this.setState({deleting: false})
    }
  }

  render () {
    const {tour, id, isActive} = this.props
    const {
      updatingActive = false,
      deleting = false,
      isConfirming = false
    } = this.state

    return (
      <tr>
        <td className="align-middle pr-3 py-2 w-50">
          {isConfirming && <ConfirmPopup doAction={this.doDelete} message={'Vill du verkligen ta bort resan:\n' + tour} />}
          <Link to={'/bokningar/resa/' + id} className="">{tour}</Link>
        </td>
        <td className="align-middle px-3 py-2 text-center">
          <Link to={'/bokningar/resa/' + id} ><span title="Redigera denna resa" className="primary-color cursor-pointer"><FontAwesomeIcon icon={faPencilAlt} size="lg" /></span></Link>
        </td>
        <td className="align-middle px-3 py-2 text-center">
          {updatingActive &&
            <span title="Sparar aktiv status..." className="primary-color"><FontAwesomeIcon icon={faSpinner} size="lg" pulse /></span> }
          {!updatingActive && isActive &&
            <span title="Inaktivera denna resa" className="primary-color custom-scale cursor-pointer"><FontAwesomeIcon icon={faCheckSquare} size="lg" onClick={(e) => this.toggleActive(e, false)} /></span> }
          {!updatingActive && !isActive &&
            <span title="Aktivera denna resa" className="primary-color custom-scale  cursor-pointer"><FontAwesomeIcon icon={faSquare} onClick={(e) => this.toggleActive(e, true)} size="lg" /></span> }

        </td>
        <td className="align-middle pl-3 py-2 text-center">
          {!deleting &&
            <span title="Ta bort denna resa permanent" className="danger-color custom-scale cursor-pointer"><FontAwesomeIcon icon={faTrashAlt} onClick={(e) => this.deleteConfirm(e)} size="lg" /></span>}
          {deleting &&
            <span title="Tar bort denna resa" className="danger-color"><FontAwesomeIcon icon={faSpinner} size="lg" pulse /></span>}
        </td>
      </tr>

    )
  }
}

TourRow.propTypes = {
  label     : PropTypes.string,
  id        : PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  putItem   : PropTypes.func,
  postItem  : PropTypes.func,
  deleteItem: PropTypes.func,
  isDisabled: PropTypes.bool
}

const mapDispatchToProps = dispatch => bindActionCreators({
  postItem
}, dispatch)

export default connect(null, mapDispatchToProps)(TourRow)
