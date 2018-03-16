import firebase from '../config/firebase';
import {apiPost} from './api-post';
import {onThenCategory, onCatchCategory} from './categories';

export function startFirebaseSub(user, jwt) {
  return function(dispatch){
    
    const toursCategories = firebase.database().ref('tours/categories');
    const today = Date.now();
    toursCategories.set({
      updated: today,
      id: ['all'],
    });
    toursCategories.on('value', function(snapshot) {
      dispatch({type: 'LOADING_START', payload: true});
      const snap = snapshot.val();
      if (snap.id.indexOf('all') !== -1) {
        apiPost('/tours/category/get', {
          user: user,
          jwt: jwt,
          categoryid: 'all',
        })
          .then(response => {
            onThenCategory(dispatch, response);
          })
          .catch(error => {
            onCatchCategory(dispatch, error);
          });

      } else {
        try {
          snap.id.map((item) => {
            if (Number.isInteger(item)) {
              apiPost('/tours/category/get', {
                user: user,
                jwt: jwt,
                categoryid: item,
              })
                .then(response => {
                  onThenCategory(dispatch, response);
                })
                .catch(error => {
                  onCatchCategory(dispatch, error);
                });
            }
            return item;
          });
        } catch(e) { 
          /*id isnt array, ignore*/ 
        }
      }
    });
  };
}