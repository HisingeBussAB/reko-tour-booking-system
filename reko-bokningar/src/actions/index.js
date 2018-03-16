export {Login} from './login';
export {errorPopup} from './error-popup';
export {getCategories} from './categories';
export {startFirebaseSub} from './firebase-sub';
export function loading(bool, symantic='') {return (dispatch) => { dispatch({type: 'LOADING_'+symantic, payload: bool});};}
