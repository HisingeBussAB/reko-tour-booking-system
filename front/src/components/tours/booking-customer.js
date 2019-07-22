import React, { Component } from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import {faPlus, faSave, faMinus, faSpinner, faTrash, faCheck, faCheckSquare, faSquare} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'

class BookingsCustomer extends Component {
  constructor (props) {
    super(props)
    this.state = {

    }
  }

  render () {
    const {id, number, isOdd, handleChange, index, customer, isSubmitting, removeCustomer} = this.props

    return (

      <div className={(isOdd ? 'pl-5' : 'pr-5') + ' col-6'} key={number} id={'customer' + id}>

        <div className="container p-0 mt-2">
          <div className="row">
            <div className="col-6 pr-1">
            <h5 className="mb-1 p-0 mt-4 w-100">Resenär nr <b>{number}</b></h5>            </div>
            <div className="col-6 pl-1 text-right">
              <button onClick={(e) => this.removeCustomer(index)} disabled={isSubmitting} type="button" title="Makulera hela bokningen" className="btn btn-danger btn-sm custom-scale mb-1 py-1 px-2 mt-4">
                <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={isSubmitting ? faSpinner : faTrash} size="1x" />&nbsp;Ta bort resenär {number}</span>
              </button>
            </div>
          </div>
          <div className="row">
                      <div className="col-12">
                        <label htmlFor="payDate1" className="d-block small mt-1 mb-0">Anmälningsavgift, sista betalningsdatum (åååå-mm-dd)</label>
                        <input id="payDate1" name="paydate1" value={paydate1} onChange={(e) => { this.handleChange(e.target) }} className="rounded" type="date" style={{width: '166px'}} min="2000-01-01" max="3000-01-01" placeholder="0" disabled={!usepaydate1} />&nbsp;
                        <button type="button" name="usePayDate1" title={seperateinvoice ? 'Använder anmälningsavgift' : 'Använder inte anmälningsavgift'} onClick={(e) => { e.preventDefault(); this.togglePayDate1() }} className={usepaydate1 ? 'btn btn-primary active small btn-sm' : 'btn btn-secondary small btn-sm'} aria-pressed={usepaydate1}><FontAwesomeIcon icon={usepaydate1 ? faCheckSquare : faSquare} size="1x" /></button>
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
              {customer.roomLabel}
            </div>
            <div className="col-4 pl-1">
              <label htmlFor="price" className="d-block small mt-1 mb-0">Pris efter rabatt</label>
              <span>{Number(Number(customer.price) + Number(customer.priceadjustment)).toFixed(0)} kr</span>
            </div>
          </div>
          <div className="row">
            <div className="col-8 pr-1">
              <label htmlFor="departurelocation" className="d-block small mt-1 mb-0">Avgångsplats</label>
              {customer.departurelocation}
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
              <textarea value={customer.requests} className="rounded w-100 d-inline-block m-0 rbt-input-main" style={{height: '100px'}} />
            </div>
          </div>
        </div>

      </div>
    )
  }
}

BookingsCustomer.propTypes = {
  customer      : PropTypes.object,
  id            : PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  number        : PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  isOdd         : PropTypes.bool,
  isSubmitting  : PropTypes.bool,
  handleChange  : PropTypes.func,
  removeCustomer: PropTypes.func,
  index         : PropTypes.number

}

const mapStateToProps = state => ({
  allCustomers: typeof state.lists.customers === 'object' ? state.lists.customers : []
})

export default connect(mapStateToProps, null)(BookingsCustomer)
