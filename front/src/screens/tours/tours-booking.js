import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import {faPlus, faSave, faSpinner, faArrowLeft, faTrash, faCheck, faCheckSquare, faSquare} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import {getItem, putItem, postItem, deleteItem, getItemWeb} from '../../actions'
import { Typeahead } from 'react-bootstrap-typeahead'
import update from 'immutability-helper'
import { findByKey, getActivePlusSelectedTours, dynamicSort } from '../../utils'
import { Redirect } from 'react-router-dom'
import ConfirmPopup from '../../components/global/confirm-popup'
import moment from 'moment'
import 'moment/locale/sv'
import BookingsCustomer from '../../components/tours/booking-customer'
import _ from 'lodash'

class NewTourBooking extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isSubmitting: false,
      id          : 'new',
      number      : 'new',
      bookingdate : moment().format('YYYY-MM-DD'),
      bookinggroup: false,
      paydate1    : moment().format('YYYY-MM-DD'),
      paydate2    : moment().format('YYYY-MM-DD'),
      usepaydate1 : true,
      customers   : [],
      tourSelected: [{'label': ''}],
      redirectTo  : false,
      isConfirming: false
    }
  }

  componentDidMount () {
    const {getItem, getItemWeb, ...props} = this.props
    getItem('bookings', 'all')
    getItem('categories', 'all')
    getItem('tours', 'all')
    getItem('customers', 'all')
    getItemWeb('resor', 'all')
    this.Initate(props)
  }

  // eslint-disable-next-line camelcase
  UNSAFE_componentWillReceiveProps (nextProps) {
    const {bookings, tours} = this.props
    if (bookings !== nextProps.bookings || tours !== nextProps.tours) {
      this.Initate(nextProps)
    }
  }

  Initate = (nextProps) => {
    const newState = {
      redirectTo  : false,
      isConfirming: false
    }
    if (Number(nextProps.match.params.number) >= 0 && typeof nextProps.bookings === 'object' && nextProps.bookings.length > 0) {
      const booking = findByKey(nextProps.match.params.number, 'number', nextProps.bookings)
      if (typeof booking !== 'undefined') {
        newState.id = booking.id
        newState.number = booking.number
        newState.paydate1 = booking.paydate1
        newState.paydate2 = booking.paydate2
        newState.bookinggroup = booking.bookinggroup
        newState.customers = booking.customers
        newState.usepaydate1 = booking.paydate1 !== booking.paydate2 && (booking.paydate1 !== null || booking.paydate1 !== 'null' || typeof booking.paydate1 !== 'undefined')
        if (Number(booking.tourid) >= 0 && typeof nextProps.tours === 'object' && nextProps.tours.length > 0) {
          const tour = findByKey(booking.tourid, 'id', nextProps.tours)
          newState.tourSelected = [tour]
        }
      }
      this.setState(newState)
    }
  }

  handleSave = async () => {
    const {id, ...state} = this.state
    const {postItem, putItem, getItem} = this.props
    this.setState({isSubmitting: true})
    console.log(state)
    const data = state
    const reply = id === 'new' ? await postItem('bookings', data) : await putItem('bookings', id, data)
    if (reply !== false && !isNaN(reply)) {
      getItem('categories', 'all')
      getItem('tours', 'all')
      if (id === 'new') {
        this.setState({redirectTo: '/bokningar/bookings/' + reply}, () => { this.setState({redirectTo: false}) })
      }
    }
    this.setState({isSubmitting: false}) 
  }

  handleChange = (e) => {
    this.setState({ [e.name]: e.value })
  }

  handleChangePax = (e, i) => {
    const {customers} = this.state
    const newcustomer = update(customers, {[[i]]: {[e.name]: {$set: e.value}}})
    this.setState({customers: newcustomer})
  }

  handleChangeRoom = (room, i) => {
    const {customers = [], tourSelected = {}} = this.state
    const roomSpecs = typeof room[0] === 'undefined' || typeof tourSelected[0] === 'undefined' ? {price: 0} : findByKey(room[0].id, 'id', tourSelected[0].rooms)
    const roomid = typeof room[0] === 'undefined' || typeof tourSelected[0] === 'undefined' ? '' : room[0].id
    const newcustomer = update(customers, {[[i]]:
                                  {selectedRoom: {$set: room},
                                    roomid      : {$set: roomid},
                                    price       : {$set: roomSpecs.price}}
    })
    this.setState({customers: newcustomer})
  }

  handleChangeDeparture = (departurelocation, i) => {
    const {customers = []} = this.state
    const newDeparture = {departurelocation: departurelocation[0].departurelocation, departuretime: typeof departurelocation[0].departuretime === 'undefined' ? customers[i].departuretime : departurelocation[0].departuretime}
    const newcustomer = update(customers, {[[i]]:
      {departurelocation: {$set: newDeparture.departurelocation},
        departuretime    : {$set: newDeparture.departuretime}}
    })
    this.setState({customers: newcustomer})
  }

  handleSelectPerson = (selectedCustomer, i) => {
    const {customers = []} = this.state
    const {allCustomers = []} = this.props
    if (typeof selectedCustomer === 'object' && typeof selectedCustomer[0] === 'object') {
      if (selectedCustomer[0].customOption) {
        console.log('custom option')
        const mergeChange = _.merge(selectedCustomer[0], customers[i])
        const newcustomer = update(customers, {[[i]]:
          {id            : {$set: 'new'},
            firstname     : {$set: mergeChange.firstname},
            lastname      : {$set: mergeChange.lastname},
            street        : {$set: mergeChange.street},
            zip           : {$set: mergeChange.zip},
            city          : {$set: mergeChange.city},
            phone         : {$set: mergeChange.phone},
            email         : {$set: mergeChange.email},
            personalnumber: {$set: mergeChange.personalnumber}}
        })
        this.setState({customers: newcustomer})
      } else {
        const foundCustomer = allCustomers.find(o => { return o.id.toLowerCase() === selectedCustomer[0].id.toLowerCase() })
        if (typeof foundCustomer === 'object') {
          const newcustomer = update(customers, {[[i]]:
          {id            : {$set: foundCustomer.id},
            firstname     : {$set: foundCustomer.firstname},
            lastname      : {$set: foundCustomer.lastname},
            street        : {$set: foundCustomer.street},
            zip           : {$set: foundCustomer.zip},
            city          : {$set: foundCustomer.city},
            phone         : {$set: foundCustomer.phone},
            email         : {$set: foundCustomer.email},
            personalnumber: {$set: foundCustomer.personalnumber}}
          })
          this.setState({customers: newcustomer})
        }
      }
    }
  }

  /*
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
          if (await deleteItem('bookings', id)) {
            this.setState({isSubmitting: false, redirectTo: '/bokningar/'})
            return null
          }
        }
      }
    }
    this.setState({isSubmitting: false})
  }
*/
  addCustomer = () => {
    const {customers = []} = this.state
    const maxCustomer = Math.max(...customers.map(c => Number(c.custnumber)), -1)
    const emptyCustomer = {
      id               : 'new',
      custnumber       : typeof customers === 'object' ? Number(maxCustomer + 1) : 0,
      invoicenr        : '00',
      firstname        : '',
      lastname         : '',
      street           : '',
      zip              : '',
      city             : '',
      phone            : '',
      email            : '',
      priceadjustment  : 0,
      label            : '',
      price            : '',
      departurelocation: '',
      departuretime    : '',
      personalnumber   : '',
      requests         : '',
      roomid           : '',
      selectedRoom     : {label: ''}
    }
    const newcustomers = update(customers, {$push: [emptyCustomer]})
    this.setState({customers: newcustomers})
  }

  removeCustomer = (index) => {
    const {customers} = this.state
    if (customers.length > 0) {
      const newcustomers = update(customers, {$splice: [[index, 1]]})
      this.setState({customers: newcustomers})
    }
  }

  tourSelected = (tourSelected) => {
    const newState = { tourSelected: tourSelected }
    newState.usepaydate1 = true
    newState.paydate1 = moment().format('YYYY-MM-DD')
    newState.paydate2 = moment().format('YYYY-MM-DD')
    if (typeof tourSelected === 'object' && typeof tourSelected[0] === 'object' && typeof tourSelected[0].reservationfeeprice !== 'undefined') {
      newState.usepaydate1 = tourSelected[0].reservationfeeprice > 0
    }
    if (typeof tourSelected === 'object' && typeof tourSelected[0] === 'object' && typeof tourSelected[0].departuredate !== 'undefined') {
      if (moment().add(7, 'w').isBefore(tourSelected[0].departuredate)) {
        newState.paydate2 = moment(tourSelected[0].departuredate).subtract(1, 'm').format('YYYY-MM-DD')
      } else {
        const d = moment.duration(moment().diff(moment(tourSelected[0].departuredate)))
        newState.paydate2 = moment(tourSelected[0].departuredate).subtract(Math.abs(Number(d.asMilliseconds() / 2)), 'ms').format('YYYY-MM-DD')
      }
      if (newState.usepaydate1 === true) {
        newState.usepaydate1 = moment().add(7, 'w').isBefore(tourSelected[0].departuredate)
      }
      if (newState.usepaydate1 === true) {
        newState.paydate1 = moment().add(11, 'd').format('YYYY-MM-DD')
      } else {
        newState.paydate1 = newState.paydate2
      }
      // If trip was in the past paydates can be in the past, then reset dates
      if (moment(newState.paydate1).isBefore(moment())) {
        newState.usepaydate1 = false
        newState.paydate1 = newState.paydate2
      }
      if (moment(newState.paydate2).isBefore(moment())) {
        newState.usepaydate1 = false
        newState.paydate1 = moment().format('YYYY-MM-DD')
        newState.paydate2 = moment().format('YYYY-MM-DD')
      }
    }
    this.setState(newState)
  }

  toggleGroup = (b) => {
    this.setState({ bookinggroup: !!b })
  }

  togglePayDate1 = () => {
    const { usepaydate1, paydate2 } = this.state
    const newState = { usepaydate1: !usepaydate1 }
    if (usepaydate1) {
      newState.paydate1 = paydate2
    }
    this.setState(newState)
  }

  render () {
    const { id = 'new', isSubmitting, number = 'new', tourSelected, redirectTo, isConfirming, bookingdate, bookinggroup, usepaydate1, paydate1, paydate2, customers = [] } = this.state
    const { history, tours } = this.props
    const toursActivePlusSelected = [...getActivePlusSelectedTours(tours, tourSelected)]
    const tourIsSelected = typeof tourSelected === 'object' && tourSelected.length > 0 && typeof tourSelected[0].id !== 'undefined' && typeof tourSelected[0].label !== 'undefined' && Number(tourSelected[0].id).toString() === tourSelected[0].id
    toursActivePlusSelected.sort(dynamicSort('label'))

    if (redirectTo !== false) { return <Redirect to={redirectTo} /> }
    let odd = -1
    const customerForms = customers.map((c, i) => {
      odd++
      return (<BookingsCustomer
        index={i}
        customer={c}
        key={c.custnumber}
        id={c.id}
        tour={typeof tourSelected === 'object' ? tourSelected[0] : {}}
        number={Number(c.custnumber) + 1}
        isOdd={odd % 2 !== 0}
        handleChange={this.handleChangePax}
        removeCustomer={this.removeCustomer}
        handleChangeRoom={this.handleChangeRoom}
        handleChangeDeparture={this.handleChangeDeparture}
        handleSelectPerson={this.handleSelectPerson}
        isSubmitting={isSubmitting}
        maxInvoice={customers.length > Math.max(...customers.map(c => Number(c.invoicenr)), 0) ? customers.length : Math.max(...customers.map(c => Number(c.invoicenr)), 0)}
      />)
    })

    return (
      <div className="TourView NewTour">
        {isConfirming && <ConfirmPopup doAction={this.doDelete} message={`Vill du verkligen markulera bokning:\n${number} ${tourSelected[0].label}.\nBokningen makuleras för alla resenärer. Det går också att byta ut enskilda resenärer istället.`} />}

        <form autoComplete="off" autoCorrect="off">
          <input type="text" name="prevent_autofill" id="prevent_autofill" value="" style={{display:'none'}} />
          <input type="password" name="password_fake" id="password_fake" value="" style={{display:'none'}} />
          <button onClick={() => { history.goBack() }} disabled={isSubmitting} type="button" title="Tillbaka till meny" className="mr-4 btn btn-primary btn-sm custom-scale position-absolute" style={{right: 0}}>
            <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={faArrowLeft} size="1x" />&nbsp;Meny</span>
          </button>
          <fieldset disabled={isSubmitting} autoComplete="disabled">
            <div className="container text-left" style={{maxWidth: '1150px'}}>

              <h3 className="my-3 w-100 mx-auto text-center">{id !== 'new' ? `Ändra bokning: ${number} på ${tourSelected[0].label}.` : 'Skapa ny bokning'}</h3>
              <div className="container-fluid w-100">
                {tourIsSelected ? <div className="row">
                  <div className="text-center col-12 p-0 m-0">
                    {!isNaN(id) ? <button onClick={(e) => this.deleteConfirm(e)} disabled={isSubmitting} type="button" title="Makulera hela bokningen" className="btn btn-danger btn-sm custom-scale mr-5">
                      <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={isSubmitting ? faSpinner : faTrash} size="1x" />&nbsp;Makulera</span>
                    </button> : null }
                    <button onClick={this.handleSave} disabled={isSubmitting} type="button" title="Radera resan med alla bokningar" className="btn btn-primary btn-sm custom-scale ml-5">
                      <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={isSubmitting ? faSpinner : faSave} size="1x" />&nbsp;Spara</span>
                    </button>
                  </div>
                </div> : null}
                <div className="row">
                  <div className="col-12 ">
                    <label htmlFor="bookingTour" className="d-block small mt-1 mb-0">Resa</label>
                    <Typeahead className="rounded w-100 d-inline-block m-0"
                      id="bookingTour"
                      name="bookingTour"
                      minLength={0}
                      maxResults={30}
                      flip
                      disabled={isSubmitting || (id !== 'new' && number !== 'new')}
                      clearButton
                      paginationText="Visa fler resultat"
                      emptyLabel=""
                      onChange={(tourSelected) => { this.tourSelected(tourSelected) }}
                      labelKey="label"
                      filterBy={['label']}
                      options={toursActivePlusSelected}
                      selected={tourSelected}
                      placeholder="Välj resa"
                      allowNew={false}
                      // eslint-disable-next-line no-return-assign
                      ref={(ref) => this._Tour = ref}
                    />
                  </div>
                </div>
                {tourIsSelected
                  ? <React.Fragment>
                    <div className="row">
                      <div className="col-12">
                        <label htmlFor="bookingDate" className="d-block small mt-1 mb-0">Bokningsdatum (åååå-mm-dd)</label>
                        <input autoComplete="off" id="bookingDate" name="bookingdate" value={bookingdate} onChange={(e) => { this.handleChange(e.target) }} className="rounded" type="date" style={{width: '166px'}} min="2000-01-01" max="3000-01-01" placeholder="0" required />
                      </div>
                    </div>
                    <div className="row">
                      <div className="col-12">
                        <label className="d-block small mt-1 mb-0 p-0 col-12">Gruppresa</label>
                        <button type="button" name="bookingGroup" onClick={(e) => { e.preventDefault(); this.toggleGroup(false) }} className={bookinggroup ? 'btn btn-secondary mr-2' : 'btn btn-primary active mr-2'} aria-pressed={!bookinggroup}>{bookinggroup ? null : <FontAwesomeIcon icon={faCheck} size="1x" />}&nbsp;Individuell <p className="small m-0">(anger att bokningen skall bokföras på konto 3021)</p></button>
                        <button type="button" name="bookingGroup" onClick={(e) => { e.preventDefault(); this.toggleGroup(true) }} className={bookinggroup ? 'btn btn-primary active ml-2' : 'btn btn-secondary ml-2'} aria-pressed={bookinggroup}>{bookinggroup ? <FontAwesomeIcon icon={faCheck} size="1x" /> : null}&nbsp;Grupp <p className="small m-0">(anger att bokningen skall bokföras på konto 3020)</p></button>
                      </div>
                    </div>
                    <div className="row">
                      <div className="col-12">
                        <label htmlFor="payDate1" className="d-block small mt-1 mb-0">Anmälningsavgift, sista betalningsdatum (åååå-mm-dd)</label>
                        <input id="payDate1" name="paydate1" value={paydate1} onChange={(e) => { this.handleChange(e.target) }} className="rounded" type="date" style={{width: '166px'}} min="2000-01-01" max="3000-01-01" placeholder="0" disabled={!usepaydate1} />&nbsp;
                        <button type="button" name="usePayDate1" title={usepaydate1 ? 'Använder anmälningsavgift' : 'Använder inte anmälningsavgift'} onClick={(e) => { e.preventDefault(); this.togglePayDate1() }} className={usepaydate1 ? 'btn btn-primary active small btn-sm' : 'btn btn-secondary small btn-sm'} aria-pressed={usepaydate1}><FontAwesomeIcon icon={usepaydate1 ? faCheckSquare : faSquare} size="1x" /></button>
                      </div>
                    </div>
                    <div className="row">
                      <div className="col-12">
                        <label htmlFor="payDate2" className="d-block small mt-1 mb-0">Slutlikvid, sista betalningsdatum (åååå-mm-dd)</label>
                        <input id="payDate2" name="paydate2" value={paydate2} onChange={(e) => { this.handleChange(e.target) }} className="rounded" type="date" style={{width: '166px'}} min="2000-01-01" max="3000-01-01" placeholder="0" required />
                      </div>
                    </div>
                    <div className="row">
                      {customerForms}
                    </div>
                    <div className="row">
                      <div className="col-6 text-left">
                        <button onClick={this.addCustomer} disabled={isSubmitting} type="button" title="Lägg till flera resenärer" className="btn btn-primary custom-scale mt-3">
                          <span className="mt-1"><FontAwesomeIcon icon={faPlus} size="lg" /></span>
                        </button>

                      </div>
                      <div className="col-6 text-right">
                        <button onClick={this.handleSave} disabled={isSubmitting} type="button" title="Spara resan" className="btn btn-primary custom-scale mt-3">
                          <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={isSubmitting ? faSpinner : faSave} size="lg" />&nbsp;Spara</span>
                        </button>
                      </div>
                    </div>

                  </React.Fragment>
                  : <div className="row">
                    <div className="col-12 text-center my-5 py-2">
                      <h4>Välj en resa först</h4>
                      <p className="mt-5">Vill du skriva ut en manuell resebekräftelse?<br />
                        <a href={'https://' + window.location.hostname + '/boka-manuell.php?anmavg=1'} target="_blank">Manuell resebekräftelse med anmälningsavgift</a><br />
                        <a href={'https://' + window.location.hostname + '/boka-manuell.php?anmavg=0'} target="_blank">Manuell resebekräftelse utan anmälningsavgift</a>
                      </p>
                    </div>
                  </div>}
              </div>
            </div>
          </fieldset>
        </form>
      </div>
    )
  }
}

NewTourBooking.propTypes = {
  getItem     : PropTypes.func,
  postItem    : PropTypes.func,
  putItem     : PropTypes.func,
  deleteItem  : PropTypes.func,
  getItemWeb  : PropTypes.func,
  tours       : PropTypes.array,
  bookings    : PropTypes.array,
  match       : PropTypes.object,
  history     : PropTypes.object,
  allCustomers: PropTypes.array
}

const mapStateToProps = state => ({
  tours       : state.tours.tours,
  bookings    : state.tours.bookings,
  allCustomers: typeof state.lists.customers === 'object' ? state.lists.customers : []
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem,
  postItem,
  putItem,
  deleteItem,
  getItemWeb
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(NewTourBooking)
