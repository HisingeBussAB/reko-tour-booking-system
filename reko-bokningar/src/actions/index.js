export {Login} from './login';
export {errorPopup} from './error-popup';
export {getCategories} from './categories';
export function loading(bool) {return (dispatch) => { dispatch({type: 'LOADING', payload: bool});};}