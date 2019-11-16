import React, { Component } from 'react'
import { connect } from 'react-redux'
import BudgetViewMain from './budgets/budgets-main'
import EditBudgetGroup from './budgets/budgets-groups'
import BudgetEdit from './budgets/budgets-edit'
import { Route } from 'react-router-dom'

class BudgetView extends Component {
  render () {
    return (
      <div className="BudgetView text-center pt-3">
        <Route exact path="/kalkyler"                   component={BudgetViewMain} />
        <Route exact path="/kalkyler/kalkylgrupper/:id" component={EditBudgetGroup} />
        <Route exact path="/kalkyler/kalkyl/:id"        component={BudgetEdit} />
      </div>
    )
  }
}

export default connect(null, null)(BudgetView)
