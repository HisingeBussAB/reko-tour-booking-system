import React, { Component } from 'react'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import { getItem } from '../../actions'
import { faFilter } from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import { bindActionCreators } from 'redux'
import { findByKey, dynamicSort } from '../../utils'


class BudgetViewMain extends Component {
  constructor (props) {
    super(props)
    this.state = {
      
    }
  }

  componentWillMount () {
    this.reduxGetAllUpdate()
  }

  reduxGetAllUpdate = () => {
    const {getItem} = this.props
    getItem('budgetgroups', 'all')
    // getItem('budgets', 'all')
  }

  toggleBoolean = (variableName, value) => {
    const bool = !!value
    this.setState({[variableName]: !bool})
  }

  render () {
    const {budgetgroups = []} = this.props
    let BudgetGroupsRowstmp
    try {
      budgetgroups.sort(dynamicSort('label'))
      BudgetGroupsRowstmp = budgetgroups.filter(group => { return !(group.isdisabled) }).map((group) => {
        return <li key={group.id} className="list-group-item list-group-item-action">{group.label}</li>
      })
    } catch (e) {
      BudgetGroupsRowstmp = null
    }

    const BudgetGroupsRows = BudgetGroupsRowstmp

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
              <h4 className="w-75 my-3 mx-auto">Kalkyler</h4>
              <Link to={'/bokningar/resa/ny'} className="btn w-75 btn-primary my-3 mx-auto py-2">Skapa ny kalkyl</Link>
              
            </div>
          </div>
        </div>
      </div>
    )
  }
}

BudgetViewMain.propTypes = {
  getItem     : PropTypes.func,
  budgetgroups: PropTypes.array
}

const mapStateToProps = state => ({
  budgetgroups: state.budgets.budgetgroups
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(BudgetViewMain)
