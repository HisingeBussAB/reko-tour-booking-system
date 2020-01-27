import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import {faPlus, faSave, faMinus, faSpinner, faArrowLeft, faTrash} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import {getItem, putItem, postItem, deleteItem, getItemWeb} from '../../actions'
import { Typeahead } from 'react-bootstrap-typeahead'
import update from 'immutability-helper'
import { findByKey, getActivePlusSelectedCategories, dynamicSort } from '../../utils'
import { Redirect } from 'react-router-dom'
import ConfirmPopup from '../../components/global/confirm-popup'
import moment from 'moment'
import 'moment/locale/sv'

class NewTour extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isSubmitting       : false,
      id                 : 'new',
      label              : '',
      departuredate      : moment().format('YYYY-MM-DD'),
      insuranceprice     : 150,
      reservationfeeprice: 300,
      rooms              : [
        {id             : 'new',
          label          : '',
          price          : '0',
          size           : '0',
          numberavaliable: '0'}],
      catSelected    : [],
      webtourSelected: [],
      redirectTo     : false,
      isConfirming   : false
    }
  }

  componentDidMount () {
    this.reduxGetAllUpdate()
    const {...props} = this.props
    this.Initiate(props)
  }

  // eslint-disable-next-line camelcase
  UNSAFE_componentWillReceiveProps (nextProps) {
    const {tours, webtours} = this.props
    if (tours !== nextProps.tours || webtours !== nextProps.webtours) {
      this.Initiate(nextProps)
    }
  }

  reduxGetAllUpdate = () => {
    const {getItem, getItemWeb} = this.props
    getItem('categories', 'all')
    getItem('tours', 'all')
    getItemWeb('resor')
    getItemWeb('boenden')
  }

  Initiate = (nextProps) => {
    try {
      const tour = findByKey(nextProps.match.params.id, 'id', nextProps.tours)
      const webtour = findByKey(tour.webid, 'id', nextProps.webtours)
      if (typeof tour === 'object') {
        this.setState({
          id                 : tour.id,
          label              : tour.label,
          departuredate      : moment(tour.departuredate).format('YYYY-MM-DD'),
          insuranceprice     : tour.insuranceprice,
          reservationfeeprice: tour.reservationfeeprice,
          rooms              : tour.rooms,
          catSelected        : tour.categories,
          webtourSelected    : typeof webtour === 'undefined' ? [] : [webtour],
          redirectTo         : false,
          isConfirming       : false
        })
      }
    } catch (e) {
      // To early or bad imput, do nothing use default state.
    }
  }

  handleSave = async () => {
    const {id, rooms, label, departuredate, reservationfeeprice, insuranceprice, catSelected, webtourSelected} = this.state
    const {postItem, putItem, getItem} = this.props
    this.setState({isSubmitting: true})
    const webid = typeof webtourSelected !== 'undefined' && typeof webtourSelected[0] !== 'undefined' && typeof webtourSelected[0].id !== 'undefined' ? webtourSelected[0].id : null
    const tourData = {
      label              : label,
      insuranceprice     : insuranceprice,
      reservationfeeprice: reservationfeeprice,
      departuredate      : moment(departuredate).format('YYYY-MM-DD'),
      categories         : catSelected.map(cat => { return {id: cat.id} }),
      webid              : webid,
      rooms              : rooms.map((item, i) => {
        return {
          id             : item.id,
          label          : this['roomOpt' + i].getInstance().getInput().value,
          price          : Number(item.price).toString(),
          size           : Number(item.size).toString(),
          numberavaliable: Number(item.numberavaliable).toString()
        }
      })
    }
    const reply = id === 'new' ? await postItem('tours', tourData) : await putItem('tours', id, tourData)
    if (reply !== false && !isNaN(reply)) {
      getItem('categories', 'all')
      if (id === 'new') {
        this.setState({redirectTo: '/bokningar/resa/' + reply}, () => { this.setState({redirectTo: false}) })
      }
    }
    this.setState({isSubmitting: false})
  }

  handleChange = (e) => {
    this.setState({ [e.name]: e.value })
  }

  handleChangeRoomRow = (e, i) => {
    const {rooms} = this.state
    const newroom = update(rooms, {[[i]]: {[e.name]: {$set: e.value}}})
    this.setState({rooms: newroom})
  }

  deleteConfirm = (e) => {
    if (typeof e !== 'undefined') { e.preventDefault() }
    this.setState({isSubmitting: true, isConfirming: true})
  }

  doDelete = async (choice) => {
    const { id } = this.state
    const { deleteItem } = this.props
    this.setState({ isConfirming: false })
    if (!isNaN(id)) {
      if (choice === true) {
        if (typeof id !== 'undefined') {
          if (await deleteItem('tours', id)) {
            this.setState({isSubmitting: false, redirectTo: '/bokningar/'})
            return null
          }
        }
      }
    }
    this.setState({isSubmitting: false})
  }

  addRow = () => {
    const emptyRoom = {
      id             : 'new',
      label          : '',
      price          : '0',
      size           : '0',
      numberavaliable: '0'
    }
    const {rooms} = this.state
    const newroom = update(rooms, {$push: [emptyRoom]})
    this.setState({rooms: newroom})
  }

  removeRow = () => {
    const {rooms} = this.state
    if (rooms.length > 1) {
      const newrooms = update(rooms, {$splice: [[rooms.length - 1, 1]]})
      this.setState({rooms: newrooms})
    }
  }

  render () {
    const {id, isSubmitting, label, departuredate, insuranceprice, reservationfeeprice, rooms, catSelected, redirectTo, isConfirming, webtourSelected} = this.state
    const {categories, tours, history, webtours, webrooms = []} = this.props

    if (redirectTo !== false) { return <Redirect to={redirectTo} /> }

    const activecategories = getActivePlusSelectedCategories(categories, findByKey(id, 'id', tours))
    const roomtypesRaw = tours.filter(tour => {
      return typeof tour.rooms === 'object' && tour.rooms.length > 0
    }).map(tour => {
      return tour.rooms.map(room => { return room.label })
    })
    const roomsFromWeb = webrooms.map(t => { return t.boende })
    const roomtypes = [...new Set(roomtypesRaw.concat(roomsFromWeb).flat())]
    const activecategoriesSorted = [...activecategories]
    const webtoursSorted = [...webtours]
    const roomtypesSorted = [...roomtypes]
    roomtypesSorted.sort()
    webtoursSorted.sort(dynamicSort('namn'))
    activecategoriesSorted.sort(dynamicSort('label'))
    const roomRows = rooms.map((item, i) => {
      return (<tr key={item.id.toString() + i.toString()}>
        <td className="p-2 w-50 align-middle">
          <Typeahead className="rounded w-100 d-inline-block m-0"
            id={'roomOpt' + i}
            name="label"
            minLength={0}
            maxResults={15}
            flip
            clearButton
            paginationText="Visa fler resultat"
            emptyLabel=""
            disabled={isSubmitting}
            options={roomtypesSorted}
            placeholder="Rumstyp/Dagsresa"
            defaultSelected={typeof item.label === 'undefined' ? [] : [item.label]}
            // eslint-disable-next-line no-return-assign
            ref={(ref) => this['roomOpt' + i] = ref}
          />
        </td>
        <td className="p-2 align-middle"><input value={Number(isNaN(item.size) ? 0 : item.size).toFixed(0)} name="size" onChange={(e) => this.handleChangeRoomRow(e.target, i)} className="text-right rounded" placeholder="0" type="number" min="0" max="99" maxLength="2" step="1" style={{width: '75px'}} required /></td>
        <td className="pl-2 pr-1 text-nowrap align-middle"><input value={Number(isNaN(item.price) ? 0 : item.price).toFixed(0)} name="price" onChange={(e) => this.handleChangeRoomRow(e.target, i)} className="text-right rounded mr-1" type="number" placeholder="0" min="0" max="99999" maxLength="5" step="1" style={{width: '75px'}} required />kr</td>
        <td className="p-2 align-middle"><input value={Number(isNaN(item.numberavaliable) ? 0 : item.numberavaliable).toFixed(0)} name="numberavaliable" onChange={(e) => this.handleChangeRoomRow(e.target, i)} className="text-right rounded" type="number" placeholder="0" min="0" max="999" maxLength="3" step="1" style={{width: '75px'}} required /></td>
      </tr>)
    })

    return (
      <div className="TourView NewTour">
        {isConfirming && <ConfirmPopup doAction={this.doDelete} message={`Vill du verkligen ta bort resan:\n${label} ${moment(departuredate).format('D/M')}.\nGör bara detta om bokningar inte påbörjats, annars rekommenderas att bara inaktivera resan från huvudmenyn för bokningar.`} />}

        <form autoComplete="off">
          <button onClick={() => { history.goBack() }} disabled={isSubmitting} type="button" title="Tillbaka till meny" className="mr-4 btn btn-primary btn-sm custom-scale position-absolute" style={{right: 0}}>
            <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={faArrowLeft} size="1x" />&nbsp;Meny</span>
          </button>
          <fieldset disabled={isSubmitting}>
            <div className="container text-left" style={{maxWidth: '850px'}}>

              <h3 className="my-3 w-50 mx-auto text-center">{id !== 'new' ? 'Ändra resa: ' + label + ' ' + moment(departuredate).format('D/M') : 'Skapa ny resa'}</h3>
              <div className="container-fluid" style={{width: '85%'}}>
                <fieldset disabled={isSubmitting}>
                  <div className="text-center col-12 p-0 m-0">
                    {!isNaN(id) ? <button onClick={(e) => this.deleteConfirm(e)} disabled={isSubmitting} type="button" title="Radera resan med alla bokningar" className="btn btn-danger btn-sm custom-scale mr-5">
                      <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={isSubmitting ? faSpinner : faTrash} size="1x" />&nbsp;Radera</span>
                    </button> : null }
                    <button onClick={this.handleSave} disabled={isSubmitting} type="button" title="Radera resan med alla bokningar" className="btn btn-primary btn-sm custom-scale ml-5">
                      <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={isSubmitting ? faSpinner : faSave} size="1x" />&nbsp;Spara</span>
                    </button>
                  </div>
                  <div className="text-left col-12 px-0 py-0 m-0">
                    <label className="small w-100 text-left p-0 mx-0 mt-1 mb-0 d-block" htmlFor="tourName">Resans namn</label>
                    <input id="tourName" name="label" value={label} onChange={(e) => { this.handleChange(e.target) }} className="rounded w-100 d-inline-block m-0" placeholder="Resans namn" maxLength="99" type="text" required />
                  </div>
                  <div className="text-left col-12 px-0 py-0 m-0" style={{height: '57px'}}>
                    <label className="small w-100 text-left p-0 mx-0 mt-1 mb-0 d-block" htmlFor="tourCategories">Resekategorier:</label>
                    <Typeahead className="rounded w-100 d-inline-block m-0"
                      id="tourCategories"
                      name="tourCategories"
                      minLength={0}
                      maxResults={30}
                      flip
                      multiple
                      paginationText="Visa fler resultat"
                      emptyLabel=""
                      disabled={isSubmitting}
                      onChange={(catSelected) => { this.setState({ catSelected: catSelected }) }}
                      labelKey="label"
                      filterBy={['label']}
                      options={activecategoriesSorted}
                      selected={catSelected}
                      placeholder="Kategorier"
                      allowNew={false}
                      // eslint-disable-next-line no-return-assign
                      ref={(ref) => this._Category = ref}
                    />
                  </div>
                  <div className="text-left col-12 px-0 py-0 m-0">
                    <label className="small w-100 text-left p-0 mx-0 mt-1 mb-0 d-block" htmlFor="tourWeb">Kopplas till resa på hemsidan (valfritt):</label>
                    <Typeahead className="rounded w-100 d-inline-block m-0"
                      id="tourWeb"
                      name="tourWeb"
                      minLength={0}
                      maxResults={30}
                      flip
                      emptyLabel=""
                      clearButton
                      paginationText="Visa fler resultat"
                      disabled={isSubmitting}
                      onChange={(webtourSelected) => { this.setState({ webtourSelected: webtourSelected }) }}
                      labelKey="namn"
                      filterBy={['namn']}
                      options={webtoursSorted}
                      selected={webtourSelected}
                      placeholder="Fylls i om motsvarande resa finns och är aktiv på hemsidan"
                      allowNew={false}
                      // eslint-disable-next-line no-return-assign
                      ref={(ref) => this._Category = ref}
                    />
                  </div>
                  <div className="w-50 d-inline">
                    <label htmlFor="tourDate" className="d-block small mt-1 mb-0">Avresedatum (åååå-mm-dd)</label>
                    <input id="tourDate" name="departuredate" value={departuredate} onChange={(e) => { this.handleChange(e.target) }} className="rounded" type="date" style={{width: '166px'}} min="2000-01-01" max="3000-01-01" placeholder="0" required />
                  </div>
                  <div className="w-25 d-inline">
                    <label htmlFor="tourReservation" className="d-block small mt-1 mb-0">Anmälningsavgift</label>
                    <input id="tourReservation" name="reservationfeeprice" value={Number(reservationfeeprice).toFixed(0)} onChange={(e) => { this.handleChange(e.target) }} className="rounded text-right" type="number" style={{width: '75px'}} min="0" max="9999" placeholder="0" maxLength="4" step="1" required /> kr
                  </div>
                  <div className="w-25 d-inline">
                    <label htmlFor="tourInsurance" className="d-block small mt-1 mb-0">Avbeställningskydd</label>
                    <input id="tourInsurance" name="insuranceprice" value={Number(insuranceprice).toFixed(0)} onChange={(e) => { this.handleChange(e.target) }} className="rounded text-right" type="number" style={{width: '75px'}} min="0" max="9999" placeholder="0" maxLength="4" step="1" required /> kr
                  </div>
                </fieldset>
                <fieldset disabled={isSubmitting}>
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
                          {rooms.length > 1 &&
                          <button onClick={this.removeRow} disabled={isSubmitting} type="button" title="Ta bort sista raden boendealternativ" className="btn btn-danger custom-scale ml-3">
                            <span className="mt-1"><FontAwesomeIcon icon={faMinus} size="lg" /></span>
                          </button>}
                        </td>
                        <td className="p-2 text-right align-middle" colSpan="2">
                          <button onClick={this.handleSave} disabled={isSubmitting} type="button" title="Spara resan" className="btn btn-primary custom-scale">
                            <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={isSubmitting ? faSpinner : faSave} size="lg" />&nbsp;Spara</span>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </fieldset>
              </div>
            </div>
          </fieldset>
        </form>
      </div>
    )
  }
}

NewTour.propTypes = {
  getItem   : PropTypes.func,
  postItem  : PropTypes.func,
  putItem   : PropTypes.func,
  getItemWeb: PropTypes.func,
  deleteItem: PropTypes.func,
  categories: PropTypes.array,
  tours     : PropTypes.array,
  webtours  : PropTypes.array,
  webrooms  : PropTypes.array,
  match     : PropTypes.object,
  history   : PropTypes.object
}

const mapStateToProps = state => ({
  categories: state.tours.categories,
  tours     : state.tours.tours,
  webtours  : state.web.webtours,
  webrooms  : state.web.webrooms
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem,
  postItem,
  putItem,
  deleteItem,
  getItemWeb
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(NewTour)
