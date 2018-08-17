import firebase from '../../config/firebase'
import {apiPost} from '../../functions'
import {networkAction} from '../'

export function firebaseItemSub (user, jwt, itemType) {
  return function (dispatch) {
    let temp = false
    if (itemType === 'categories') {
      temp = 'categoryid'
    } else if (itemType === 'tours') {
      temp = 'tourid'
    }

    const itemTypeid = temp
    temp = undefined
    if (itemTypeid === false) {
      return false
    }

    const toursCategories = firebase.database().ref('tours/' + itemType)
    toursCategories.on('value', function (snapshot) {
      const snap = snapshot.val()
      try {
        if (snap.id.indexOf('all') !== -1) {
          dispatch(networkAction(1, 'get all ' + itemType))
          apiPost('/tours/' + itemType + '/get', {
            user        : user,
            jwt         : jwt,
            [itemTypeid]: 'all'
          })
            .then(response => {
              dispatch(networkAction(0, 'get all ' + itemType))
            })
            .catch(error => {
              dispatch(networkAction(0, 'get all ' + itemType))
            })
        } else {
          snap.id.forEach((item) => {
            if (Number.isInteger(item)) {
              dispatch(networkAction(1, 'get ' + itemType + ' ' + item))
              apiPost('/tours/' + itemType + '/get', {
                user        : user,
                jwt         : jwt,
                [itemTypeid]: item
              })
                .then(response => {

                  dispatch(networkAction(0, 'get ' + itemType + ' ' + item))
                })
                .catch(error => {

                  dispatch(networkAction(0, 'get ' + itemType + ' ' + item))
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
