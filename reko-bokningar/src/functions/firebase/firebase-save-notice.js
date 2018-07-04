import firebase from '../../config/firebase'

export function firebaseSavedItem (id, type) {
  if (type === 'categories' || type === 'tours') {
    const itemRef = firebase.database().ref('tours/' + type)
    const today = Date.now()
    itemRef.set({
      updated: today,
      id     : [id]
    })
    return true
  }
  return false
}
