import {mergeObjectArrays} from '../utils'

export default function budgets (state = {}, action) {
  switch (action.type) {
    case 'DATA_BUDGETGROUPS_SAVE':
      if (action.payload.id === 'all' || typeof state.budgetgroups === 'undefined') {
        return {...state, budgetgroups: action.payload.budgetgroups}
      } else {
        return {...state, budgetgroups: mergeObjectArrays(state.budgetgroups, action.payload.budgetgroups, 'id')}
      }
    case 'DATA_BUDGETS_SAVE':
      if (action.payload.id === 'all' || typeof state.budgets === 'undefined') {
        return {...state, budgets: action.payload.budgets}
      } else {
        return {...state, budgets: mergeObjectArrays(state.budgets, action.payload.budgets, 'id')}
      }
    default:
      return state
  }
}
