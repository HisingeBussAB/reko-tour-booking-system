import firebase from '../../config/firebase'
import {itemNameTranslation} from '../../data/valid-api-calls'

export function firebaseSavedItem (id, type) {
  if (itemNameTranslation.hasOwnProperty(type)) {
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
