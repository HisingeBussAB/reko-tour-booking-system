import firebase from '../../config/firebase'
import {apiPost} from '../../functions'
import {onThenCategory, onCatchCategory} from '../tours/categories'
import {networkAction} from '../'

export function firebaseCategoriesSub (user, jwt) {
  return function (dispatch) {
    const toursCategories = firebase.database().ref('tours/categories')
    toursCategories.on('value', function (snapshot) {
      const snap = snapshot.val()
      try {
        if (snap.id.indexOf('all') !== -1) {
          dispatch(networkAction(1, 'get all cat firebase act'))
          apiPost('/tours/category/get', {
            user      : user,
            jwt       : jwt,
            categoryid: 'all'
          })
            .then(response => {
              onThenCategory(dispatch, response)
              dispatch(networkAction(0, 'get all cat firebase act'))
            })
            .catch(error => {
              onCatchCategory(dispatch, error)
              dispatch(networkAction(0, 'get all cat firebase act'))
            })
        } else {
          snap.id.forEach((item) => {
            if (Number.isInteger(item)) {
              dispatch(networkAction(1, 'get ' + item + ' cat firebase act'))
              apiPost('/tours/category/get', {
                user      : user,
                jwt       : jwt,
                categoryid: item
              })
                .then(response => {
                  onThenCategory(dispatch, response)
                  dispatch(networkAction(0, 'get ' + item + ' cat firebase act'))
                })
                .catch(error => {
                  onCatchCategory(dispatch, error)
                  dispatch(networkAction(0, 'get ' + item + ' cat firebase act'))
                })
            }
          })
        }
      } catch (e) {
      /* firebase data malformated, ignore */
      }
    })
  }
}
