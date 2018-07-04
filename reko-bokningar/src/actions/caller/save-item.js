import {networkAction} from '../'
import {apiPost, firebaseSavedItem} from '../../functions'

export async function saveItem (itemType, data) {
  const validItems = ['tours', 'categories']
  if (validItems.includes(itemType)) {
    networkAction(1, 'save new ' + itemType, dispatch)
    await apiPost('/tours/' + itemType, data)
      .then(response => {})
      .catch(error => {
        networkAction(0, 'save new ' + itemType)
      })
  } else {

  }
}

handleSave = (e) => {
  const {login} = this.props
  const {...state} = this.state

  this.submitToggle(true)
  apiPost('/tours/tour/new', {
    user           : login.user,
    jwt            : login.jwt,
    tourName       : state.tourName,
    tourDate       : state.tourDate,
    tourCategory   : state.tourCategory,
    tourInsurance  : state.tourInsurance,
    tourReservation: state.tourReservation,
    tourRoomOpt    : state.tourRoomOpt
  })
    .then((response) => {
      let temp
      try {
        temp = response.data.modifiedid
      } catch (e) {
        temp = 'all'
      }
      const modifiedid = temp
      const {onThenItem, onCatchItem} = this.props
      networkAction(1, 'update tour redux')
      apiPost('/tours/tour/get', {
        user  : login.user,
        jwt   : login.jwt,
        tourID: modifiedid
      })
        .then((response) => {
          networkAction(0, 'update tour redux')
          onThenItem(dispatch, response)
        })
        .catch((error) => {
          networkAction(0, 'update tour redux')
          onCatchItem(dispatch, error)
        })
      this.submitToggle(false)
      networkAction(0, 'save new tour')
    })
    .catch((error) => {
      console.log(error)
      this.submitToggle(false)
      networkAction(0, 'save new category')
    })
}
