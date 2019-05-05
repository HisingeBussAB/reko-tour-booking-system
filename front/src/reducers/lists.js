import {mergeObjectArrays} from '../utils'

export default function lists (state = {}, action) {
  switch (action.type) {
    case 'DATA_GROUPCUSTOMERS_SAVE':
      if (action.payload.id === 'all' || typeof state.lists.groupcustomers === 'undefined') {
        return {...state, lists: {groupcustomers: action.payload.lists.groupcustomers}}
      } else {
        return {...state, lists: {groupcustomers: mergeObjectArrays(state.lists.groupcustomers, action.payload.lists.groupcustomers, 'id')}}
      }
    default:
      return state
  }
}
