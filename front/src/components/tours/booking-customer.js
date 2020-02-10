import React, { Component } from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import {faSpinner, faTrash, faInfoCircle} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import { Typeahead, Menu, MenuItem } from 'react-bootstrap-typeahead'
import moment from 'moment'
import 'moment/locale/sv'
import { infoPopup, getItem } from '../../actions'
import toursBooking from '../../screens/tours/tours-booking'
import { findByKey } from '../../utils'

class BookingsCustomer extends Component {
  constructor (props) {
    super(props)
    this.state = {
      departurelocationList: [],
      selectedRoom         : props.customer.selectedRoom,
      selectedCustomer     : props.customer,
      selectedDeparture    : props.customer.selectedDeparture,
      isRoomValid          : false
    }
  }

  componentDidMount () {
    const {...props} = this.props
    this.Initate(props)
  }

  UNSAFE_componentWillReceiveProps (nextProps) {
    const {bookings, tour} = this.props
    if (bookings !== nextProps.bookings || (typeof tour === 'object' ? tour.id : null) !== (typeof nextProps.tour === 'object' ? nextProps.tour.id : null)) {
      this.Initate(nextProps)
    }
  }

  Initate = (nextProps) => {
    nextProps.getItem('departurelists', typeof nextProps.tour === 'object' ? nextProps.tour.id : -1)
    this.setState({selectedRoom     : nextProps.customer.selectedRoom,
      selectedCustomer : nextProps.customer,
      selectedDeparture: nextProps.customer.selectedDeparture})
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

  handleRoomInput = (value) => {
    const {tour, index, handleChangeRoom} = this.props
    const selectedRoomMatch = typeof value === 'object' ? value[0] : typeof tour.rooms === 'object' ? tour.rooms.find(o => o.label.toLowerCase() === value.toLowerCase()) : undefined
    const isValid = typeof selectedRoomMatch === 'object'
    this.setState({isRoomValid: isValid})
    if (isValid) {
      handleChangeRoom([selectedRoomMatch], index)
    } else {
      handleChangeRoom([], index)
    }
  }

  handleRoomInput = (value) => {
    const {tour, index, handleChangeRoom} = this.props
    const selectedRoomMatch = typeof value === 'object' ? value[0] : typeof tour.rooms === 'object' ? tour.rooms.find(o => o.label.toLowerCase() === value.toLowerCase()) : undefined
    const isValid = typeof selectedRoomMatch === 'object'
    this.setState({isRoomValid: isValid})
    if (isValid) {
      handleChangeRoom([selectedRoomMatch], index)
    } else {
      handleChangeRoom([], index)
    }
  }

  render () {
    const {id, number, isOdd, handleChange, index, customer = {}, isSubmitting, removeCustomer, maxInvoice, tour, handleChangeRoom, allCustomers} = this.props
    const {departurelocationList, selectedRoom, selectedCustomer, selectedDeparture, isRoomValid } = this.state

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
              <Typeahead className="rounded w-100 d-inline-block m-0"
                id="firstname"
                name="firstname"
                minLength={2}
                maxResults={30}
                allowNew
                flip
                disabled={isSubmitting}
                clearButton
                paginationText="Visa fler resultat"
                emptyLabel=""
                labelKey="firstname"
                newSelectionPrefix="Lägg till: "
                onChange={() => { console.log('changeTriggered') }}
                options={allCustomers}
                renderMenu={(results, menuProps) => (
                  <Menu {...menuProps}>
                    {results.map((result, index) => (
                      <MenuItem key={index} option={result} position={index}>
                        <div key={index} className="small m-0 p-0">
                          <p className="m-0 p-0">{result.firstname} {result.lastname}</p>
                          <p className="m-0 p-0">{result.street}</p>
                          <p className="m-0 p-0">{result.email}</p>
                          <p className="m-0 p-0">{result.phone}</p>
                        </div>
                      </MenuItem>))}
                  </Menu>
                )}
                selected={[selectedCustomer]}
                placeholder="Förnamn"
                // eslint-disable-next-line no-return-assign
                ref={(ref) => this._customerfirstname = ref}
              />
            </div>
            <div className="col-7 pl-1">
              <label htmlFor="lastname" className="d-block small mt-1 mb-0">Efternamn</label>
              <Typeahead className="rounded w-100 d-inline-block m-0"
                id="lastname"
                name="lastname"
                minLength={2}
                maxResults={30}
                allowNew
                flip
                disabled={isSubmitting}
                clearButton
                paginationText="Visa fler resultat"
                emptyLabel=""
                labelKey="lastname"
                newSelectionPrefix="Lägg till: "
                onChange={() => { console.log('changeTriggeredlast') }}
                options={allCustomers}
                renderMenu={(results, menuProps) => (
                  <Menu {...menuProps}>
                    {results.map((result, index) => (
                      <MenuItem key={index} option={result} position={index}>
                        <div key={index} className="small m-0 p-0">
                          <p className="m-0 p-0">{result.firstname} {result.lastname}</p>
                          <p className="m-0 p-0">{result.street}</p>
                          <p className="m-0 p-0">{result.email}</p>
                          <p className="m-0 p-0">{result.phone}</p>
                        </div>
                      </MenuItem>))}
                  </Menu>
                )}
                selected={[selectedCustomer]}
                placeholder="Efternamn"
                // eslint-disable-next-line no-return-assign
                ref={(ref) => this._customerlastname = ref}
              />
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
              <Typeahead className="rounded w-100 d-inline-block m-0"
                id="phone"
                name="phone"
                minLength={2}
                maxResults={30}
                allowNew
                flip
                disabled={isSubmitting}
                clearButton
                paginationText="Visa fler resultat"
                emptyLabel=""
                labelKey="phone"
                newSelectionPrefix="Lägg till: "
                onChange={() => { console.log('changeTriggeredlast') }}
                options={allCustomers}
                renderMenu={(results, menuProps) => (
                  <Menu {...menuProps}>
                    {results.map((result, index) => (
                      <MenuItem key={index} option={result} position={index}>
                        <div key={index} className="small m-0 p-0">
                          <p className="m-0 p-0">{result.firstname} {result.lastname}</p>
                          <p className="m-0 p-0">{result.street}</p>
                          <p className="m-0 p-0">{result.email}</p>
                          <p className="m-0 p-0">{result.phone}</p>
                        </div>
                      </MenuItem>))}
                  </Menu>
                )}
                selected={[selectedCustomer]}
                placeholder="Telefonnr"
                inputProps={{type   : 'tel',
                  pattern: '^[^a-zA-Z]+$'}}
                // eslint-disable-next-line no-return-assign
                ref={(ref) => this._customerphone = ref}
              />
            </div>
          </div>
          <div className="row">
            <div className="col-12">
              <label htmlFor="email" className="d-block small mt-1 mb-0">E-post</label>
              <Typeahead className="rounded w-100 d-inline-block m-0"
                id="email"
                name="email"
                minLength={2}
                maxResults={30}
                allowNew
                flip
                disabled={isSubmitting}
                clearButton
                paginationText="Visa fler resultat"
                emptyLabel=""
                labelKey="email"
                newSelectionPrefix="Lägg till: "
                onChange={() => { console.log('changeTriggeredlast') }}
                options={allCustomers}
                renderMenu={(results, menuProps) => (
                  <Menu {...menuProps}>
                    {results.map((result, index) => (
                      <MenuItem key={index} option={result} position={index}>
                        <div key={index} className="small m-0 p-0">
                          <p className="m-0 p-0">{result.firstname} {result.lastname}</p>
                          <p className="m-0 p-0">{result.street}</p>
                          <p className="m-0 p-0">{result.email}</p>
                          <p className="m-0 p-0">{result.phone}</p>
                        </div>
                      </MenuItem>))}
                  </Menu>
                )}
                selected={[selectedCustomer]}
                placeholder="E-post"
                inputProps={{type   : 'email',
                  pattern: '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$'}}
                // eslint-disable-next-line no-return-assign
                ref={(ref) => this._customeremail = ref}
              />
            </div>
          </div>
          <div className="row">
            <div className="col-12">
              <label htmlFor="priceadjustment" className="d-block small mt-3 mb-0">Rabatt/Tillägg</label>
              <input placeholder="0" type="text" name="priceadjustment" value={Number(customer.priceadjustment).toFixed(0)} style={{width: '180px'}} onChange={e => handleChange(e.target, index)} className="rounded d-inline-block m-0" /> kr
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
                selectHintOnEnter
                emptyLabel=""
                labelKey="label"
                filterBy={['label']}
                onChange={value => this.handleRoomInput(value)}
                onInputChange={(value) => this.handleRoomInput(value)}
                options={tour.rooms}
                selected={[selectedRoom]}
                placeholder="Rumstyp"
                allowNew={false}
                isValid={isRoomValid}
                isInvalid={!isRoomValid}
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
                labelKey="label"
                filterBy={['label']}
                newSelectionPrefix="Lägg till plats: "
                selectHintOnEnter
                isValid
                onChange={e => handleChangeRoom(e, index)}
                options={departurelocationList}
                selected={[selectedDeparture]}
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
              <Typeahead className="rounded w-100 d-inline-block m-0"
                id="personalnumber"
                name="personalnumber"
                minLength={2}
                maxResults={30}
                allowNew
                flip
                disabled={isSubmitting}
                clearButton
                paginationText="Visa fler resultat"
                emptyLabel=""
                labelKey="personalnumber"
                newSelectionPrefix="Lägg till: "
                onChange={() => { console.log('changeTriggeredlast') }}
                options={allCustomers}
                renderMenu={(results, menuProps) => (
                  <Menu {...menuProps}>
                    {results.map((result, index) => (
                      <MenuItem key={index} option={result} position={index}>
                        <div key={index} className="small m-0 p-0">
                          <p className="m-0 p-0">{result.firstname} {result.lastname}</p>
                          <p className="m-0 p-0">{result.personalnumber}</p>
                          <p className="m-0 p-0">{result.email}</p>
                          <p className="m-0 p-0">{result.phone}</p>
                        </div>
                      </MenuItem>))}
                  </Menu>
                )}
                selected={[selectedCustomer]}
                placeholder="XXXXXX-XXXX"
                inputProps={{pattern: '^[0-9]{6}[-+]{1}[0-9]{4}$', maxLength: '11'}}
                // eslint-disable-next-line no-return-assign
                ref={(ref) => this._customerpersonalnumber = ref}
              />
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
  customer        : PropTypes.object,
  bookings        : PropTypes.array,
  id              : PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  number          : PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  isOdd           : PropTypes.bool,
  isSubmitting    : PropTypes.bool,
  handleChange    : PropTypes.func,
  removeCustomer  : PropTypes.func,
  index           : PropTypes.number,
  maxInvoice      : PropTypes.number,
  infoPopup       : PropTypes.func,
  tour            : PropTypes.object,
  handleChangeRoom: PropTypes.func
}

const mapStateToProps = state => ({
  allCustomers: typeof state.lists.customers === 'object' ? state.lists.customers : [],
  bookings    : typeof state.tours.bookings === 'object' ? state.tours.bookings : []
})

const mapDispatchToProps = dispatch => bindActionCreators({
  infoPopup,
  getItem
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(BookingsCustomer)
