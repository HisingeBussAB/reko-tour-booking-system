export {apiPost} from './api-post'
export {Login} from './login/login'
export {errorPopup} from './error-popup'
export {getCategories} from './categories'
export {
  startFirebaseSub,
  saveCategoryFirebaseNotice
} from './firebase-sub'
export function loading (bool, symantic = '') { return (dispatch) => { dispatch({type: 'LOADING_' + symantic, payload: bool}) } }
