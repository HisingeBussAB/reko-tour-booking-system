import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import faPencilAlt from '@fortawesome/fontawesome-free-solid/faPencilAlt'
import faSquare from '@fortawesome/fontawesome-free-regular/faSquare'
import faCheckSquare from '@fortawesome/fontawesome-free-regular/faCheckSquare'
import faTrashAlt from '@fortawesome/fontawesome-free-regular/faTrashAlt'
import faSpinner from '@fortawesome/fontawesome-free-solid/faSpinner'
import FontAwesomeIcon from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import { saveItem } from '../../actions'
import { Link } from 'react-router-dom'

class TourRow extends Component {
  /* NOTICE
  this.props.id
  recives -1 for new item
  output for new item must be id: 'new'
  */

  constructor (props) {
    super(props)
    this.state = {
      updatingActive: false,
      deleting      : false
    }
  }

  componentWillReceiveProps (nextProps) {
    const {id = 'new', isActive = true} = this.props
    if (nextProps.id !== id) {
      // for some reason id changed, component state needs reset.
      this.setState({
        updatingActive: false,
        deleting      : false
      })
    }
    // cancel loaders on changes recived
    if (nextProps.isActive !== isActive) {
      this.setState({updatingActive: false})
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

    if (await saveItem('categories', data, 'save')) {
      submitToggle(false)
    } else {
      submitToggle(false)
    }
  }

  doDelete = async (e) => {
    e.preventDefault()
    this.setState({deleting: true})
    const {category} = this.state
    const {saveItem, isNew = false, isActive, id = 'new', submitToggle, index = null, remove = () => {}} = this.props
    submitToggle(true)
    if (!isNew) {
      const data = {
        category  : category,
        active    : !isActive,
        categoryid: id,
        task      : 'delete'
      }

      if (await saveItem('categories', data, 'delete')) {
        submitToggle(false)
      } else {
        submitToggle(false)
      }
    } else {
      remove(index)
      submitToggle(false)
    }
  }

  render () {
    const {tour, id, isActive} = this.props
    const {
      updatingActive = false,
      deleting = false
    } = this.state
    return (
      <tr>
        <td className="align-middle pr-3 py-2 w-50">
          <Link to={'/bokningar/resa/' + id} className="">{tour}</Link>
        </td>
        <td className="align-middle px-3 py-2 text-center">
          <Link to={'/bokningar/resa/' + id} className="primary-color"><FontAwesomeIcon icon={faPencilAlt} size="lg" /></Link>
        </td>
        <td className="align-middle px-3 py-2 text-center">
          {updatingActive &&
            <span title="Sparar aktiv status..." className="primary-color"><FontAwesomeIcon icon={faSpinner} size="lg" pulse /></span> }
          {!updatingActive && isActive &&
            <span title="Inaktivera denna resa" className="primary-color custom-scale"><FontAwesomeIcon icon={faCheckSquare} size="lg" onClick={(e) => this.toggleActive(e, false)} /></span> }
          {!updatingActive && !isActive &&
            <span title="Aktivera denna resa" className="primary-color custom-scale"><FontAwesomeIcon icon={faSquare} onClick={(e) => this.toggleActive(e, true)} size="lg" /></span> }

        </td>
        <td className="align-middle pl-3 py-2 text-center">
          {!deleting &&
          <span title="Ta bord denna resa permanent" className="danger-color custom-scale"><FontAwesomeIcon icon={faTrashAlt} onClick={(e) => this.doDelete(e)} size="lg" /></span>}
          {deleting &&
            <span title="Tar bort denna resa" className="danger-color"><FontAwesomeIcon icon={faSpinner} size="lg" pulse /></span>}
        </td>
      </tr>
    )
  }
}

TourRow.propTypes = {
  tour        : PropTypes.string,
  id          : PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  isActive    : PropTypes.bool,
  isNew       : PropTypes.bool,
  submitToggle: PropTypes.func,
  saveItem    : PropTypes.func,
  index       : PropTypes.number,
  remove      : PropTypes.func
}

const mapDispatchToProps = dispatch => bindActionCreators({
  saveItem
}, dispatch)

export default connect(null, mapDispatchToProps)(TourRow)
