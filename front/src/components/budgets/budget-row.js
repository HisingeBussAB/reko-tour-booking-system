import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { faSpinner, faTrash } from '@fortawesome/free-solid-svg-icons'
import { faSquare, faCheckSquare } from '@fortawesome/free-regular-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import { putItem } from '../../actions'
import moment from 'moment'
import 'moment/locale/sv'

class BudgetRow extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isUpdatingDisabled: false
    }
  }

  // eslint-disable-next-line camelcase
  UNSAFE_componentWillReceiveProps (nextProps) {
    const {id, isDisabled} = this.props
    if (nextProps.id !== id) {
      // for some reason id changed, component state needs reset.
      this.setState({
        isUpdatingDisabled: false
      })
    }
    // cancel loaders on changes recived
    if (nextProps.isDisabled !== isDisabled) {
      this.setState({isUpdatingDisabled: false})
    }
  }

  toggleDisabled = async (choice) => {
    const {submitToggle, budget = {}} = this.props
    if (choice === true) {
      this.setState({isUpdatingDisabled: true})

      submitToggle(true)
      const data = {

      }
      if (!await putItem('tours', budget.id, data)) {
        this.setState({isUpdatingDisabled: false})
      }
    }
    submitToggle(false)
  }

  deactivateConfirm = (e) => {
    e.preventDefault()
    const {submitToggle} = this.props
    submitToggle(true)
  }

  render () {
    const {budget = {}} = this.props
    const {
      isUpdatingDisabled = false
    } = this.state
    return (
      <tr>
        <td key={budget.id} className="align-middle pr-3 py-2 w-75">{budget.label}, {typeof budget.tourLabel !== 'undefined' && budget.tourLabel.toString().length > 0 ? 'Resa: ' + budget.tourLabel : null} {moment(budget.sortdate).format('YYYY MMM')}</td>
        <td className="align-middle px-3 py-2 text-center">
          {isUpdatingDisabled &&
          <span title="Sparar aktiv status..." className="primary-color"><FontAwesomeIcon icon={faSpinner} size="lg" pulse /></span> }
          {!isUpdatingDisabled && !budget.isDisabled &&
          <span title="Inaktivera denna kalkyl" className="primary-color custom-scale cursor-pointer"><FontAwesomeIcon icon={faCheckSquare} onClick={(e) => { e.preventDefault() }} size="lg" /></span> }
          {!isUpdatingDisabled && budget.isDisabled &&
          <span title="Aktivera denna kalkyl" className="primary-color custom-scale  cursor-pointer"><FontAwesomeIcon icon={faSquare} onClick={(e) => { e.preventDefault() }} size="lg" /></span> }

        </td>
        <td>
          <span title="Radera denna kalkyl permanent" className="danger-color custom-scale cursor-pointer"><FontAwesomeIcon icon={faTrash} onClick={(e) => { e.preventDefault() }} size="lg" /></span>
        </td>

      </tr>

    )
  }
}

BudgetRow.propTypes = {
  isDisabled  : PropTypes.bool,
  id          : PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  budget      : PropTypes.object,
  submitToggle: PropTypes.func
}

const mapDispatchToProps = dispatch => bindActionCreators({
  putItem
}, dispatch)

export default connect(null, mapDispatchToProps)(BudgetRow)
