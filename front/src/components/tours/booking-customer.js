import React, { Component } from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import {faPlus, faSave, faMinus, faSpinner, faTrash, faCheck, faCheckSquare, faSquare, faInfoCircle} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import { Typeahead } from 'react-bootstrap-typeahead'
import moment from 'moment'
import 'moment/locale/sv'
import { infoPopup } from '../../actions'
import toursBooking from '../../screens/tours/tours-booking'
import { findByKey } from '../../utils'

class BookingsCustomer extends Component {
  constructor (props) {
    super(props)
    this.state = {
      departurelocationList: [],
      selectedRoom: {label: ''}
    }
  }

  componentDidMount () {
    const {...props} = this.props
    this.Initate(props)
  }

  componentWillReceiveProps (nextProps) {
    const {bookings, tour} = this.props
    if (bookings !== nextProps.bookings || (typeof tour === 'object' ? tour.id : null) !== (typeof nextProps.tour === 'object' ? nextProps.tour.id : null)) {
      this.Initate(nextProps)
    }
  }

  Initate = (nextProps) => {
    const filteredBooking = nextProps.bookings.filter(b => { return (typeof b === 'object' ? Number(b.tourid) : false) === (typeof nextProps.tour === 'object' ? Number(nextProps.tour.id) : true) })
    const departureList = filteredBooking.map(booking => {
      return booking.customers.map(customer => { return customer.departurelocation })
    }
    ).flat()
    this.setState({departurelocationList: Array.from(new Set(departureList))})
  }

  showInvoiceInfo = () => {
    const {infoPopup} = this.props
    infoPopup({
      visible: true,
      message: `Välj nummer för att skicka separata fakturor.
                Resernärer med samma fakturanummer grupperas ihop på samma faktura, fakturan skickas till den första resenären på fakturan som har en adress angiven.

                Fakturanummret är de två sista siffrorna (efter bokningsnummret) av referensnummret för fakturan.
                Fakturanummren börjar på 00.`,
      suppressed: false})
  }

  render () {
    const {id, number, isOdd, handleChange, index, customer = {}, isSubmitting, removeCustomer, maxInvoice, tour, handleChangeRoom} = this.props
    const {departurelocationList, selectedRoom} = this.state

console.log(selectedRoom)

console.log(customer.departurelocation)


    const invoiceSelector = <select name="invoicenr" value={customer.invoicenr} onChange={e => handleChange(e.target, index)} disabled={isSubmitting} className="rounded d-inline m-0 p-1">
      {Array.from(Array(maxInvoice).keys()).map(i => { return (<option key={i} value={i}>{Number(i).toString().padStart(2, '0')}</option>) })}
    </select>

    return (

      <div className={(isOdd ? 'pl-5' : 'pr-5') + ' col-6'} key={number} id={'customer' + id}>

        <div className="container p-0 mt-2">
          <div className="row">
            <div className="col-7 pr-1">
              <h5 className="mb-1 p-0 mt-4 w-100">Resenär {number} på faktura {invoiceSelector} <FontAwesomeIcon icon={faInfoCircle} size="1x" className="mt-1 primary-color" onClick={this.showInvoiceInfo} /></h5>
            </div>
            <div className="col-5 pl-1 text-right">
              <button onClick={(e) => removeCustomer(index)} disabled={isSubmitting} type="button" title="Makulera hela bokningen" className="btn btn-danger btn-sm custom-scale mb-1 py-1 px-2 mt-4">
                <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={isSubmitting ? faSpinner : faTrash} size="1x" />&nbsp;Ta bort resenär {number}</span>
              </button>
            </div>
          </div>
          <div className="row">
            <div className="col-5 pr-1">
              <label htmlFor="firstname" className="d-block small mt-1 mb-0">Förnamn</label>
              <input placeholder="Förnamn" type="text" name="firstname" value={customer.firstname} onChange={e => handleChange(e.target, index)} className="rounded w-100 d-inline-block m-0" />
            </div>
            <div className="col-7 pl-1">
              <label htmlFor="lastname" className="d-block small mt-1 mb-0">Efternamn</label>
              <input placeholder="Efternamn" type="text" name="lastname" value={customer.lastname} onChange={e => handleChange(e.target, index)} className="rounded w-100 d-inline-block m-0" />
            </div>
          </div>
          <div className="row">
            <div className="col-12">
              <label htmlFor="street" className="d-block small mt-1 mb-0">Gatuadress</label>
              <input placeholder="Gatuadress" type="text" name="street" value={customer.street} onChange={e => handleChange(e.target, index)} className="rounded w-100 d-inline-block m-0" />
            </div>
          </div>
          <div className="row">
            <div className="col-4 pr-1">
              <label htmlFor="zip" className="d-block small mt-1 mb-0">Postnr</label>
              <input placeholder="Postnr" type="text" pattern="^[0-9]{3}[ ]?[0-9]{2}$" maxLength="6" name="zip" value={customer.zip} onChange={e => handleChange(e.target, index)} className="rounded w-100 d-inline-block m-0" />
            </div>
            <div className="col-8 pl-1">
              <label htmlFor="city" className="d-block small mt-1 mb-0">Postort</label>
              <input placeholder="Postort" type="text" name="city" value={customer.city} onChange={e => handleChange(e.target, index)} className="rounded w-100 d-inline-block m-0" />
            </div>
          </div>
          <div className="row">
            <div className="col-12">
              <label htmlFor="phone" className="d-block small mt-1 mb-0">Telefon</label>
              <input placeholder="Telefonnr" type="tel" pattern="^[^a-zA-Z]+$" name="phone" value={customer.phone} onChange={e => handleChange(e.target, index)} className="rounded w-100 d-inline-block m-0" />
            </div>
          </div>
          <div className="row">
            <div className="col-12">
              <label htmlFor="email" className="d-block small mt-1 mb-0">E-post</label>
              <input placeholder="E-post" type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" name="email" value={customer.email} onChange={e => handleChange(e.target, index)} className="rounded w-100 d-inline-block m-0" />
            </div>
          </div>
          <div className="row">
            <div className="col-12">
              <label htmlFor="priceadjustment" className="d-block small mt-3 mb-0">Rabatt/Tillägg</label>
              <input placeholder="0" type="number" name="priceadjustment" value={Number(customer.priceadjustment).toFixed(0)} style={{width: '180px'}} onChange={e => handleChange(e.target, index)} className="rounded d-inline-block m-0" /> kr
            </div>
          </div>
          <div className="row">
            <div className="col-8 pr-1">
              <label htmlFor="room" className="d-block small mt-1 mb-0">Rumstyp</label>
              <Typeahead className="rounded w-100 d-inline-block m-0"
                id="room"
                name="room"
                minLength={0}
                maxResults={30}
                flip
                disabled={isSubmitting}
                clearButton
                paginationText="Visa fler resultat"
                emptyLabel=""
                labelKey="label"
                filterBy={['label']}
                onChange={e => handleChangeRoom(e, index)}
                options={tour.rooms}
                selected={[selectedRoom]}
                highlightOnlyResult
                placeholder="Påstigningsplats"
                allowNew={false}
                // eslint-disable-next-line no-return-assign
                ref={(ref) => this._room = ref}
              />
            </div>
            <div className="col-4 pl-1">
              <label htmlFor="price" className="d-block small mt-1 mb-0">Pris efter rabatt</label>
              <span>{Number(Number(customer.price) + Number(customer.priceadjustment)).toFixed(0)} kr</span>
            </div>
          </div>
          <div className="row">
            <div className="col-8 pr-1">
              <label htmlFor="departurelocation" className="d-block small mt-1 mb-0">Avgångsplats</label>
              <Typeahead className="rounded w-100 d-inline-block m-0"
                id="departurelocation"
                name="departurelocation"
                minLength={0}
                maxResults={30}
                allowNew
                flip
                disabled={isSubmitting}
                clearButton
                paginationText="Visa fler resultat"
                emptyLabel=""
                newSelectionPrefix="Lägg till plats: "
                onChange={e => handleChange({name: 'departurelocation', value: typeof e[0] !== 'undefined' ? e[0].label : ''}, index)}
                options={departurelocationList}
                selected={[typeof customer.departurelocation === 'string' ? {label: customer.departurelocation} : {label: ''}]}
                placeholder="Påstigningsplats"
                // eslint-disable-next-line no-return-assign
                ref={(ref) => this._departurelocation = ref}
              />
            </div>
            <div className="col-4 pl-1">
              <label htmlFor="departuretime" className="d-block small mt-1 mb-0">Avgtid</label>
              <input id="departuretime" type="time" name="departuretime" value={customer.departuretime} onChange={(e) => { handleChange(e.target, index) }} className="rounded w-100 d-inline-block m-0" placeholder="00:00" required />
            </div>
          </div>
          <div className="row">
            <div className="col-12">
              <label htmlFor="personalnumber" className="d-block small mt-1 mb-0">Personnummer</label>
              <input placeholder="XXXXXX-XXXX" pattern="^[0-9]{6}[-+]{1}[0-9]{4}$" maxLength="11" type="text" name="personalnumber" value={customer.personalnumber} onChange={e => handleChange(e.target, index)} className="rounded w-100 d-inline-block m-0" />
            </div>
          </div>
          <div className="row">
            <div className="col-12">
              <label htmlFor="requests" className="d-block small mt-1 mb-0">Önskemål</label>
              <textarea value={customer.requests} onChange={e => handleChange(e.target, index)} className="rounded w-100 d-inline-block m-0 rbt-input-main" style={{height: '100px'}} />
            </div>
          </div>
        </div>

      </div>
    )
  }
}

BookingsCustomer.propTypes = {
  customer      : PropTypes.object,
  bookings      : PropTypes.array,
  id            : PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  number        : PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  isOdd         : PropTypes.bool,
  isSubmitting  : PropTypes.bool,
  handleChange  : PropTypes.func,
  removeCustomer: PropTypes.func,
  index         : PropTypes.number,
  maxInvoice    : PropTypes.number,
  infoPopup     : PropTypes.func,
  tour          : PropTypes.object
}

const mapStateToProps = state => ({
  allCustomers: typeof state.lists.customers === 'object' ? state.lists.customers : [],
  bookings    : typeof state.tours.bookings === 'object' ? state.tours.bookings : []
})

const mapDispatchToProps = dispatch => bindActionCreators({
  infoPopup
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(BookingsCustomer)
