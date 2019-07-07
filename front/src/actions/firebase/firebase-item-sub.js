import firebase from '../../config/firebase'
import {getItem} from '../'
import {itemNameHuman} from '../../data/valid-api-calls'

export function firebaseItemSub (itemType = 'all') {
  return function (dispatch) {
    const listenerTypeRefs = []
    if (itemType === 'all') {
      Object.entries(itemNameHuman).forEach(([key, value]) => {
        listenerTypeRefs.push(key)
      })
    } else {
      listenerTypeRefs.push(itemType)
    }

    listenerTypeRefs.forEach(type => {
      const itemRef = firebase.database().ref('tours/' + type)
      itemRef.on('value', function (snapshot) {
        const snap = snapshot.val()
        try {
          if (snap.id.indexOf('all') !== -1) {
            console.log('get all')
            dispatch(getItem(type, 'all', true))
          } else {
            snap.id.forEach((item) => {
              if (Number.isInteger(item)) {
                dispatch(getItem(type, Number(item), true))
              }
            })
          }
        } catch (e) {
          /* firebase data malformated, just ignore for now, syncing is not essential */
        }
      })
    })
  }
}