import firebase from '../../config/firebase'
import {apiPost} from '../../functions'
import {onThenTour, onCatchTour} from '../tours/tours'
import {networkAction} from '../'

export function firebaseToursSub (user, jwt) {
  return function (dispatch) {
    const toursCategories = firebase.database().ref('tours/tours')
    toursCategories.on('value', function (snapshot) {
      const snap = snapshot.val()
      try {
        if (snap.id.indexOf('all') !== -1) {
          dispatch(networkAction(1, 'get all tour firebase act'))
          apiPost('/tours/tour/get', {
            user      : user,
            jwt       : jwt,
            categoryid: 'all'
          })
            .then(response => {
              onThenTour(dispatch, response)
              dispatch(networkAction(0, 'get all tour firebase act'))
            })
            .catch(error => {
              onCatchTour(dispatch, error)
              dispatch(networkAction(0, 'get all tour firebase act'))
            })
        } else {
          snap.id.forEach((item) => {
            if (Number.isInteger(item)) {
              dispatch(networkAction(1, 'get ' + item + ' tour firebase act'))
              apiPost('/tours/tour/get', {
                user      : user,
                jwt       : jwt,
                categoryid: item
              })
                .then(response => {
                  onThenTour(dispatch, response)
                  dispatch(networkAction(0, 'get ' + item + ' tour firebase act'))
                })
                .catch(error => {
                  onCatchTour(dispatch, error)
                  dispatch(networkAction(0, 'get ' + item + ' tour firebase act'))
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
