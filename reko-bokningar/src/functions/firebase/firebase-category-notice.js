import firebase from '../../config/firebase'

export default function firebaseSavedCategory (id) {
  const toursCategories = firebase.database().ref('tours/categories')
  const today = Date.now()
  toursCategories.set({
    updated: today,
    id     : [id]
  })
}
