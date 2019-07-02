import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import {faPencilAlt, faSpinner} from '@fortawesome/free-solid-svg-icons'
import {faSquare, faCheckSquare } from '@fortawesome/free-regular-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import { putItem } from '../../actions'
import { Link } from 'react-router-dom'
import ConfirmPopup from '../global/confirm-popup'
import moment from 'moment'
import 'moment/locale/sv'

class TourRow extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isConfirming      : false,
      isUpdatingDisabled: false
    }
  }

  componentWillReceiveProps (nextProps) {
    const {id, isDisabled} = this.props
    if (nextProps.id !== id) {
      // for some reason id changed, component state needs reset.
      this.setState({
        isConfirming      : false,
        isUpdatingDisabled: false
      })
    }
    // cancel loaders on changes recived
    if (nextProps.isDisabled !== isDisabled) {
      this.setState({isUpdatingDisabled: false})
    }
  }

  toggleDisabled = async (choice) => {
    this.setState({isConfirming: false})
    const {label, putItem, isDisabled, id, submitToggle, reservationfeeprice, insuranceprice, departuredate, rooms, categories} = this.props
    if (choice === true) {
      this.setState({isUpdatingDisabled: true})
      
      submitToggle(true)
      const data = {
        label               : label,
        isDisabled          : !isDisabled,
        reservationfeeprice : reservationfeeprice,
        insuranceprice      : insuranceprice,
        departuredate       : departuredate,
        rooms               : rooms,              
        categories          : categories
      }
      if (!await putItem('tours', id, data)) {
        this.setState({isUpdatingDisabled: false})
      }
    }
    submitToggle(false)
  }

  deactivateConfirm = (e) => {
    e.preventDefault()
    const {submitToggle} = this.props
    submitToggle(true)
    this.setState({isDeleting: true})
    this.setState({isConfirming: true})
  }

  render () {
    const {label = 'error', id, isDisabled = false, departuredate = '1970-01-01'} = this.props
    const {
      isUpdatingDisabled = false,
      isConfirming = false
    } = this.state

    return (
      <tr>
        <td className="align-middle pr-3 py-2 w-75">
          {isConfirming && <ConfirmPopup doAction={this.toggleDisabled} message={'Vill du verkligen inaktivera resan:\n' + label + ' den ' + moment(departuredate).format('D/M - YY') + '?'} />}
          <Link to={'/bokningar/resa/' + id} className="">{label + ' ' + moment(departuredate).format('D/M')}</Link>
        </td>
        <td className="align-middle px-3 py-2 text-center">
          <Link to={'/bokningar/resa/' + id} ><span title="Redigera denna resa" className="primary-color cursor-pointer"><FontAwesomeIcon icon={faPencilAlt} size="lg" /></span></Link>
        </td>
        <td className="align-middle px-3 py-2 text-center">
          {isUpdatingDisabled &&
            <span title="Sparar aktiv status..." className="primary-color"><FontAwesomeIcon icon={faSpinner} size="lg" pulse /></span> }
          {!isUpdatingDisabled && !isDisabled &&
            <span title="Inaktivera denna resa" className="primary-color custom-scale cursor-pointer"><FontAwesomeIcon icon={faCheckSquare} onClick={(e) => this.deactivateConfirm(e)} size="lg" /></span> }
          {!isUpdatingDisabled && isDisabled &&
            <span title="Aktivera denna resa" className="primary-color custom-scale  cursor-pointer"><FontAwesomeIcon icon={faSquare} onClick={(e) => {e.preventDefault(); this.toggleDisabled(true)}} size="lg" /></span> }

        </td>
      </tr>

    )
  }
}

TourRow.propTypes = {
  label     : PropTypes.string,
  id        : PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  putItem   : PropTypes.func,
  isDisabled: PropTypes.bool,
  insuranceprice : PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  reservationfeeprice: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  departuredate: PropTypes.oneOfType([PropTypes.string, PropTypes.number])
}

const mapDispatchToProps = dispatch => bindActionCreators({
  putItem
}, dispatch)

export default connect(null, mapDispatchToProps)(TourRow)
