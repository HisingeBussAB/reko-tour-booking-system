import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import {faPlus, faArrowLeft, faFilter} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import {getItem} from '../../actions'
import BudgetGroupRow from '../../components/budgets/budgetgroup-row'
import update from 'immutability-helper'
import { dynamicSort } from '../../utils'

class EditBudgetGroup extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isSubmitting  : false,
      extragroups   : [],
      showOnlyActive: false
    }
  }

  componentDidMount () {
    this.reduxGetAllUpdate()
  }

  componentWillUnmount () {
    this.reduxGetAllUpdate()
  }

  reduxGetAllUpdate = () => {
    const {getItem} = this.props
    getItem('budgetgroups', 'all')
  }

  addRow = () => {
    const {extragroups = []} = this.state
    const extragroup = {
      id        : 'new',
      label     : '',
      isdisabled: false
    }
    const newextragroups = update(extragroups, {$push: [extragroup]})

    this.setState({extragroups: newextragroups})
  }

  submitToggle = (b) => {
    const validatedb = !!b
    this.setState({isSubmitting: validatedb})
  }

  toggleShowOnlyActive = (e) => {
    const {showOnlyActive = true} = this.state
    this.setState({showOnlyActive: !showOnlyActive})
  }

  removeExtraGroup = (i) => {
    const {extragroups} = this.state
    delete extragroups[i] // Statemutation!
  }

  render () {
    const {budgetgroups: budgetgroupsUnsorted = [], history} = this.props
    const {extragroups = [], isSubmitting = false, showOnlyActive = false} = this.state
    const budgetgroups = [...budgetgroupsUnsorted]
    budgetgroups.sort(dynamicSort('label'))
    let budgetgroupRowstmp
    try {
      budgetgroupRowstmp = budgetgroups.filter(item => { return !(showOnlyActive && item.isdisabled) }).map((item) => {
        return <BudgetGroupRow key={item.id} id={item.id} label={item.label} isDisabled={item.isdisabled} submitToggle={this.submitToggle} />
      })
    } catch (e) {
      budgetgroupRowstmp = null
    }
    const budgetgroupRows = budgetgroupRowstmp
    extragroups.forEach((item, i) => {
      budgetgroupRows.push(<BudgetGroupRow key={'new' + i} isNew index={i} id={item.id} remove={this.removeExtraGroup} label={item.label} isDisabled={item.isdisabled} submitToggle={this.submitToggle} />)
    })

    return (
      <div className="BudgetView BudgetGroups">

        <form autoComplete="off">
          <button onClick={() => { history.goBack() }} disabled={isSubmitting} type="button" title="Tillbaka till meny" className="mr-4 btn btn-primary btn-sm custom-scale position-absolute" style={{right: 0}}>
            <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={faArrowLeft} size="1x" />&nbsp;Meny</span>
          </button>
          <fieldset disabled={isSubmitting}>
            <div className="container text-left" style={{maxWidth: '850px'}}>

              <h3 className="my-4 w-50 mx-auto text-center">Kalkylgrupper</h3>
              <table className="table table-hover w-100">
                <thead>
                  <tr>
                    <th span="col" className="pr-3 py-2 text-center w-50">Gruppnamn</th>
                    <th span="col" className="px-3 py-2 text-center">Spara</th>
                    <th span="col" className="px-3 py-2 text-center">Aktiv{!showOnlyActive &&
                      <span title="Dölj inaktiva kalkylgrupper" className="seconday-color custom-scale cursor-pointer"><FontAwesomeIcon icon={faFilter} onClick={(e) => this.toggleShowOnlyActive(e)} size="lg" /></span> }
                    {showOnlyActive &&
                      <span title="Visa inaktiva kalkylgrupper" className="primary-color custom-scale  cursor-pointer"><FontAwesomeIcon icon={faFilter} onClick={(e) => this.toggleShowOnlyActive(e)} size="lg" /></span> }
                    </th>
                    <th span="col" className="pl-3 py-2 text-center">Ta bort</th>
                  </tr>
                </thead>
                <tbody>
                  {budgetgroupRows}
                  <tr>
                    <td colSpan="4" className="py-2">
                      <button onClick={this.addRow} disabled={isSubmitting} type="button" title="Lägg till flera kalkylgrupper" className="btn btn-primary custom-scale">
                        <span className="mt-1"><FontAwesomeIcon icon={faPlus} size="lg" /></span>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </fieldset>
        </form>
      </div>
    )
  }
}

EditBudgetGroup.propTypes = {
  getItem     : PropTypes.func,
  budgetgroups: PropTypes.array,
  history     : PropTypes.object
}

const mapStateToProps = state => ({
  categories  : state.tours.categories,
  budgetgroups: state.budgets.budgetgroups
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(EditBudgetGroup)
