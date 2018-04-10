import firebase from '../config/firebase'
import {apiPost} from './index'
import {onThenCategory, onCatchCategory} from './categories'

export function startFirebaseSub (user, jwt) {
  return function (dispatch) {
    const toursCategories = firebase.database().ref('tours/categories')
    toursCategories.on('value', function (snapshot) {
      const snap = snapshot.val()
      try {
        if (snap.id.indexOf('all') !== -1) {
          dispatch({type: 'LOADING_START', payload: true})
          apiPost('/tours/category/get', {
            user: user,
            jwt: jwt,
            categoryid: 'all'
          })
            .then(response => {
              onThenCategory(dispatch, response)
              dispatch({type: 'LOADING_STOP', payload: false})
            })
            .catch(error => {
              onCatchCategory(dispatch, error)
              dispatch({type: 'LOADING_STOP', payload: false})
            })
        } else {
          snap.id.forEach((item) => {
            if (Number.isInteger(item)) {
              dispatch({type: 'LOADING_START', payload: true})
              apiPost('/tours/category/get', {
                user: user,
                jwt: jwt,
                categoryid: item
              })
                .then(response => {
                  onThenCategory(dispatch, response)
                  dispatch({type: 'LOADING_STOP', payload: false})
                })
                .catch(error => {
                  onCatchCategory(dispatch, error)
                  dispatch({type: 'LOADING_STOP', payload: false})
                })
            }
          })
        }
      } catch (e) {
      /* likley id isnt array, ignore */
        dispatch({type: 'LOADING_STOP', payload: false})
      }
    })
  }
}

export function saveCategoryFirebaseNotice (id) {
  const toursCategories = firebase.database().ref('tours/categories')
  const today = Date.now()
  toursCategories.set({
    updated: today,
    id: [id]
  })
}
