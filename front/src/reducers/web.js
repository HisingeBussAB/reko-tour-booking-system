import {mergeObjectArrays} from '../utils'

export default function web (state = {}, action) {
  switch (action.type) {
    case 'DATA_WEB_RESOR_SAVE':
        return {...state, webtours: action.payload.resor}
    case 'DATA_WEB_KATEGORIER_SAVE':
        return {...state, webcategories: action.payload.kategorier}
    case 'DATA_WEB_BOENDEN_SAVE':    
        return {...state, webrooms: action.payload.boenden}
    default:
      return state
  }
}
