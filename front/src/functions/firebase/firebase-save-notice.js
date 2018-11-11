import firebase from '../../config/firebase'
import {itemNameHuman} from '../../data/valid-api-calls'

export function firebaseSavedItem (id, type) {
  if (itemNameHuman.hasOwnProperty(type)) {
    try {
      const itemRef = firebase.database().ref('tours/' + type)
      const today = Date.now()
      itemRef.set({
        updated: today,
        id     : [id]
      })
      return true
    } catch (e) {
      return false
    }
  }
  return false
}
