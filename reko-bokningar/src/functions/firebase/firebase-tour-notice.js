import firebase from '../../config/firebase'

export function firebaseSavedTour (id) {
  const toursTours = firebase.database().ref('tours/tours')
  const today = Date.now()
  toursTours.set({
    updated: today,
    id     : [id]
  })
}
