import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import faPlus from '@fortawesome/fontawesome-free-solid/faPlus'
import faSave from '@fortawesome/fontawesome-free-solid/faSave'
import faMinus from '@fortawesome/fontawesome-free-solid/faMinus'
import FontAwesomeIcon from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import {networkAction} from '../../actions'
import {onThenTour, onCatchTour} from '../../actions/tours/save-item'
import {apiPost, firebaseSavedTour} from '../../functions'
import update from 'immutability-helper'

class NewTour extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isSubmitting   : false,
      tourName       : '',
      tourDate       : new Date().toLocaleDateString('sv-SE'),
      tourCategory   : '',
      tourInsurance  : 150,
      tourReservation: 300,
      tourRoomOpt    : [
        {
          roomType : '',
          roomSize : '',
          roomPrice: '',
          roomCount: ''
        }
      ]
    }
  }

  componentWillMount () {
    const {networkAction = () => {}, getCategories = () => {}, login = {user: 'anonymous', jwt: 'none'}} = this.props
    networkAction(1, 'updating categories')
    getCategories({
      user      : login.user,
      jwt       : login.jwt,
      categoryid: 'all'
    }).then(() => { networkAction(0, 'updating categories') })
  }

  componentDidMount () {
    const {login, match} = this.props
    let temp = 'all'
    try {
      temp = match.params.id
    } catch (e) {
      temp = 'all'
    }
    const id = temp !== 'ny' && typeof Number(temp) === 'number' ? temp : 'all'
    apiPost('/tours/tour/get', {
      user  : login.user,
      jwt   : login.jwt,
      tourID: id
    })
      .then((response) => {
        console.log(response)
      })
      .catch((error) => {
        console.log(error)
      })
  }

  handleChange = (target, i = false) => {
    const {tourRoomOpt} = this.state
    if (i === false && target.name.includes('tour')) {
      this.setState({[target.name]: target.value})
    } else if (target.name.includes('room')) {
      const targetKey = target.name.substring(0, target.name.indexOf('['))
      const newtourRoomOpt = [...tourRoomOpt]
      newtourRoomOpt[i] = update(newtourRoomOpt[i], {[targetKey]: {$set: target.value}})
      this.setState({tourRoomOpt: newtourRoomOpt})
    }
  }

  handleSave = (e) => {
    const {login} = this.props
    const {...state} = this.state
    networkAction(1, 'save new tour')
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

  submitToggle = (b) => {
    const {networkAction} = this.props
    const validb = !!b
    networkAction(Number(validb), 'saving new tour')
    this.setState({isSubmitting: validb})
  }

  addRow = () => {
    const emptyRoom = {
      roomType : '',
      roomSize : '',
      roomPrice: '',
      roomCount: ''
    }
    const {tourRoomOpt} = this.state
    const newtourRoomOpt = update(tourRoomOpt, {$push: [emptyRoom]})
    this.setState({tourRoomOpt: newtourRoomOpt})
  }

  removeRow = () => {
    const {tourRoomOpt} = this.state
    if (tourRoomOpt.length > 1) {
      const newtourRoomOpt = update(tourRoomOpt, {$splice: [[tourRoomOpt.length - 1, 1]]})
      this.setState({tourRoomOpt: newtourRoomOpt})
    }
  }

  render () {
    const {tourName, tourCategory, tourDate, tourRoomOpt, tourInsurance, tourReservation, isSubmitting = false, showStatus = false, showStatusMessage = ''} = this.state
    const {categories = []} = this.props

    let temp
    try {
      temp = categories.map((category) => {
        if (category.active) {
          return <option key={category.id} value={category.id} style={{color: 'black'}}>{category.category}</option>
        } else {
          return null
        }
      })
    } catch (e) {
      temp = null
    }
    const categoryOptions = temp
    temp = undefined

    const roomRows = tourRoomOpt.map((item, i) => {
      return (<tr key={i}>
        <td className="p-2 w-50 align-middle"><input value={item.roomType} id={'roomType[' + i + ']'} name={'roomType[' + i + ']'} onChange={(e) => this.handleChange(e.target, i)} className="rounded w-100" placeholder="Rumstyp/Dagsresa" maxLength="99" type="text" required /></td>
        <td className="p-2 align-middle"><input value={item.roomSize} id={'roomSize[' + i + ']'} name={'roomSize[' + i + ']'} onChange={(e) => this.handleChange(e.target, i)} className="text-right rounded" placeholder="0" type="number" min="0" max="99" maxLength="2" step="1" style={{width: '75px'}} required /></td>
        <td className="pl-2 pr-1 text-nowrap align-middle"><input value={item.roomPrice} id={'roomPrice[' + i + ']'} name={'roomPrice[' + i + ']'} onChange={(e) => this.handleChange(e.target, i)} className="text-right rounded mr-1" type="number" placeholder="0" min="0" max="99999" maxLength="5" step="1" style={{width: '75px'}} required />kr</td>
        <td className="p-2 align-middle"><input value={item.roomCount} id={'roomCount[' + i + ']'} name={'roomCount[' + i + ']'} onChange={(e) => this.handleChange(e.target, i)} className="text-right rounded" type="number" placeholder="0" min="0" max="999" maxLength="3" step="1" style={{width: '75px'}} required /></td>
      </tr>)
    })

    return (
      <div className="TourView NewTour">
        <form>
          <fieldset disabled={isSubmitting}>
            <div className="container text-left" style={{maxWidth: '650px'}}>
              <h3 className="my-4 w-50 mx-auto text-center">Skapa ny resa</h3>
              <fieldset>
                <div>
                  <label htmlFor="tourName" className="d-block small mt-1 mb-0">Resans namn</label>
                  <input id="tourName" name="tourName" value={tourName} onChange={e => this.handleChange(e.target)} className="rounded w-100" placeholder="Resans namn" maxLength="99" type="text" required />
                </div>
                <div>
                  <label htmlFor="tourCategory" className="d-block small mt-1 mb-0">Huvudkategori</label>
                  <select id="tourCategory" name="tourCategory" onChange={e => this.handleChange(e.target)} className="rounded w-100" value={tourCategory} required>
                    <option disabled hidden value="">-Välj-</option>
                    {categoryOptions}
                  </select>
                </div>
                <div className="w-50 d-inline">
                  <label htmlFor="tourDate" className="d-block small mt-1 mb-0">Avresedatum (åååå-mm-dd)</label>
                  <input id="tourDate" name="tourDate" value={tourDate} onChange={e => this.handleChange(e.target)} className="rounded" type="date" style={{width: '166px'}} min="2000-01-01" max="3000-01-01" placeholder="0" required />
                </div>
                <div className="w-25 d-inline">
                  <label htmlFor="tourReservation" className="d-block small mt-1 mb-0">Anmälningsavgift</label>
                  <input id="tourReservation" name="tourReservation" value={tourReservation} onChange={e => this.handleChange(e.target)} className="rounded text-right" type="number" style={{width: '75px'}} min="0" max="9999" placeholder="0" maxLength="4" step="1" required /> kr
                </div>
                <div className="w-25 d-inline">
                  <label htmlFor="tourInsurance" className="d-block small mt-1 mb-0">Avbeställningskydd</label>
                  <input id="tourInsurance" name="tourInsurance" value={tourInsurance} onChange={e => this.handleChange(e.target)} className="rounded text-right" type="number" style={{width: '75px'}} min="0" max="9999" placeholder="0" maxLength="4" step="1" required /> kr
                </div>
              </fieldset>
              <fieldset>
                <table className="table table-borderless table-sm table-hover w-100 mx-auto mt-3">
                  <thead>
                    <tr>
                      <th span="col" className="p-2 text-center w-75 font-weight-normal">Boende</th>
                      <th span="col" className="p-2 text-center font-weight-normal small">Pers/rum</th>
                      <th span="col" className="p-2 text-center font-weight-normal small">Pris/pers</th>
                      <th span="col" className="p-2 text-center font-weight-normal small">Antal bokade</th>
                    </tr>
                  </thead>
                  <tbody>
                    {roomRows}
                    <tr>
                      <td className="p-2 align-middle" colSpan="2">
                        <button onClick={this.addRow} disabled={isSubmitting} type="button" title="Lägg till flera boendealternativ" className="btn btn-primary custom-scale">
                          <span className="mt-1"><FontAwesomeIcon icon={faPlus} size="lg" /></span>
                        </button>
                        {tourRoomOpt.length > 1 &&
                        <button onClick={this.removeRow} disabled={isSubmitting} type="button" title="Ta bort sista raden boendealternativ" className="btn btn-danger custom-scale ml-3">
                          <span className="mt-1"><FontAwesomeIcon icon={faMinus} size="lg" /></span>
                        </button>}
                      </td>
                      <td className="p-2 text-right align-middle" colSpan="2">
                        <button onClick={this.handleSave} disabled={isSubmitting} type="button" title="Spara resan" className="btn btn-primary custom-scale">
                          <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={faSave} size="lg" />&nbsp;Spara</span>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </fieldset>
            </div>
          </fieldset>
        </form>
      </div>
    )
  }
}

NewTour.propTypes = {
  networkAction: PropTypes.func,
  getCategories: PropTypes.func,
  categories   : PropTypes.array,
  login        : PropTypes.object,
  match        : PropTypes.object
}

const mapStateToProps = state => ({
  login            : state.login,
  showStatus       : state.errorPopup.visible,
  showStatusMessage: state.errorPopup.message,
  categories       : state.tours.categories
})

const mapDispatchToProps = dispatch => bindActionCreators({
  networkAction,
  getCategories
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(NewTour)
