import React, { Component } from 'react'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import { getItem } from '../../actions'
import { faFilter } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import { bindActionCreators } from 'redux'
import BudgetRow from '../../components/budgets/budget-row'

class BudgetViewMain extends Component {
  constructor (props) {
    super(props)
    this.state = {
      limit                : 10,
      showOnlyActiveBudgets: true,
      isSubmitting         : false
    }
  }

  componentDidMount () {
    this.reduxGetAllUpdate()
  }

  reduxGetAllUpdate = () => {
    const {getItem} = this.props
    getItem('budgetgroups', 'all')
    getItem('budgets', 'all')
  }

  toggleBoolean = (variableName, value) => {
    const bool = !!value
    this.setState({[variableName]: !bool})
  }

  submitToggle = (b) => {
    const validatedb = !!b
    this.setState({isSubmitting: validatedb})
  }

  render () {
    const {budgetgroups = [], budgets = []} = this.props
    const {limit = 10, showOnlyActiveBudgets = true, isSubmitting} = this.state
    const BudgetGroupsRows = budgetgroups.filter(group => { return !group.isdisabled }).map((group) => {
      return <li key={group.id} className="list-group-item list-group-item-action">{group.label}</li>
    })

    const Budgets = budgets.slice(0, limit).filter(budget => { return !(budget.isdisabled && showOnlyActiveBudgets) }).map((budget) => {
      return <BudgetRow submitToggle={this.submitToggle} key={budget.id} budget={budget} />
    })

    return (
      <div className="BudgetViewMain">
        <h3 className="my-4">Resekalkyler</h3>
        <div className="container-fluid pt-2">
          <div className="row">
            <div className="col-lg-4 col-md-12">
              <h4 className="w-75 my-3 mx-auto">Kalkylgrupper</h4>
              <Link to={'/kalkyler/kalkylgrupper/ny'} className="btn w-75 btn-primary my-3 mx-auto py-2">Redigera kalkylgrupper</Link>
              <ul className="w-75 my-3 mx-auto list-group">
                {BudgetGroupsRows}
              </ul>
              <p className="w-75 my-3 py-2 mx-auto px-1 text-justify d-block">Kalkygrupper är ett frivlligt tillägg för analys och kan användas för att gruppera resekalkyler, tex kan en grupp göras för en resa som går varje år. Sedan kan varje års kalkyl för resan kopplas mot gruppen och jämföras.</p>
            </div>
            <div className="col-lg-8 col-md-12">
              <form>
                <fieldset disabled={isSubmitting}>
                  <h4 className="w-75 my-3 mx-auto">Kalkyler</h4>
                  <Link to={'/kalkyler/kalkyl/ny'} className="btn w-75 btn-primary my-3 mx-auto py-2">Skapa ny kalkyl</Link>
                  <table className="w-75 my-3 mx-auto table-sm table-hover">
                    <thead>
                      <tr>
                        <th className="py-2">Kalyler</th>
                        <th className="align-middle text-center py-2">
                          {!showOnlyActiveBudgets &&
                          <span title="Dölj inaktiva kalkyler" className="seconday-color custom-scale cursor-pointer"><FontAwesomeIcon icon={faFilter} onClick={(e) => { e.preventDefault(); this.toggleBoolean('showOnlyActiveBudgets', showOnlyActiveBudgets) }} size="lg" /></span> }
                          {showOnlyActiveBudgets &&
                          <span title="Visa inaktiva kalkyler" className="primary-color custom-scale  cursor-pointer"><FontAwesomeIcon icon={faFilter} onClick={(e) => { e.preventDefault(); this.toggleBoolean('showOnlyActiveBudgets', showOnlyActiveBudgets) }} size="lg" /></span> }
                        </th>
                        <th />
                      </tr>
                    </thead>
                    <tbody>
                      {Budgets}
                    </tbody>
                  </table>
                </fieldset>
              </form>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

BudgetViewMain.propTypes = {
  getItem     : PropTypes.func,
  budgetgroups: PropTypes.array,
  budgets     : PropTypes.array
}

const mapStateToProps = state => ({
  budgetgroups: typeof state.budgets === 'object' && typeof state.budgets.budgetgroups === 'object' ? state.budgets.budgetgroups : [],
  budgets     : typeof state.budgets === 'object' && typeof state.budgets.budgets === 'object' ? state.budgets.budgets : []
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(BudgetViewMain)
