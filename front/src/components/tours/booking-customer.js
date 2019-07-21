import React, { Component } from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { faAssistiveListeningSystems } from '@fortawesome/free-solid-svg-icons'

class BookingsCustomer extends Component {
  constructor (props) {
    super(props)
    this.emptyState = {
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
      requests         : ''
    }
    const c = Object.assign({}, props.id !== 'new' && typeof props.customer === 'object' && typeof props.customer.firstname !== 'undefined' ? props.customer : this.emptyState)
    this.state = {
      firstname        : c.firstname,
      lastname         : c.lastname,
      street           : c.street,
      zip              : c.zip,
      city             : c.city,
      phone            : c.phone,
      email            : c.email,
      priceadjustment  : c.priceadjustment,
      roomLabel        : c.label,
      price            : c.price,
      departurelocation: c.departurelocation,
      departuretime    : c.departuretime,
      personalnumber   : c.personalnumber,
      requests         : c.requests
    }
  }

  componentWillReceiveProps (nextProps) {
    const {...props} = this.props
    if (nextProps.id !== props.id) {
      // for some reason id changed, component state needs reset.
      const c = Object.assign({}, nextProps.id !== 'new' && typeof props.customer === 'object' && typeof props.customer.firstname !== 'undefined' ? props.customer : this.emptyState)
      this.setState({
        firstname        : c.firstname,
        lastname         : c.lastname,
        street           : c.street,
        zip              : c.zip,
        city             : c.city,
        phone            : c.phone,
        email            : c.email,
        priceadjustment  : c.priceadjustment,
        roomLabel        : c.label,
        price            : c.price,
        departurelocation: c.departurelocation,
        departuretime    : c.departuretime,
        personalnumber   : c.personalnumber,
        requests         : c.requests
      })
    }
  }

  handleChange = (e) => {
    this.setState({ [e.name]: e.value })
  }

  render () {
    const {id, number, odd} = this.props
    const {
      firstname,
      lastname,
      street,
      zip,
      city,
      phone,
      email,
      priceadjustment,
      roomLabel,
      price,
      departurelocation,
      departuretime,
      personalnumber,
      requests} = this.state
    return (

      <div className={(odd ? 'pl-5' : 'pr-5') + ' col-6'} key={number} id={'customer' + id}>
        <h5 className="mb-1 p-0 mt-4 w-100">Resenär nr <b>{number}</b></h5>
        <div className="container p-0 mt-2">
          <div className="row">
            <div className="col-5 pr-1">
              <label htmlFor="firstname" className="d-block small mt-1 mb-0">Förnamn</label>
              <input placeholder="Förnamn" type="text" name="firstname" value={firstname} onChange={e => this.handleChange(e.target)} className="rounded w-100 d-inline-block m-0" />
            </div>
            <div className="col-7 pl-1">
              <label htmlFor="lastname" className="d-block small mt-1 mb-0">Efternamn</label>
              <input placeholder="Efternamn" type="text" name="lastname" value={lastname} onChange={e => this.handleChange(e.target)} className="rounded w-100 d-inline-block m-0" />
            </div>
          </div>
          <div className="row">
            <div className="col-12">
              <label htmlFor="street" className="d-block small mt-1 mb-0">Gatuadress</label>
              <input placeholder="Gatuadress" type="text" name="street" value={street} onChange={e => this.handleChange(e.target)} className="rounded w-100 d-inline-block m-0" />
            </div>
          </div>
          <div className="row">
            <div className="col-4 pr-1">
              <label htmlFor="zip" className="d-block small mt-1 mb-0">Postnr</label>
              <input placeholder="Postnr" type="text" pattern="^[0-9]{3}[ ]?[0-9]{2}$" maxLength="6" name="zip" value={zip} onChange={e => this.handleChange(e.target)} className="rounded w-100 d-inline-block m-0" />
            </div>
            <div className="col-8 pl-1">
              <label htmlFor="city" className="d-block small mt-1 mb-0">Postort</label>
              <input placeholder="Postort" type="text" name="city" value={city} onChange={e => this.handleChange(e.target)} className="rounded w-100 d-inline-block m-0" />
            </div>
          </div>
          <div className="row">
            <div className="col-12">
              <label htmlFor="phone" className="d-block small mt-1 mb-0">Telefon</label>
              <input placeholder="Telefonnr" type="tel" pattern="^[^a-zA-Z]+$" name="phone" value={phone} onChange={e => this.handleChange(e.target)} className="rounded w-100 d-inline-block m-0" />
            </div>
          </div>
          <div className="row">
            <div className="col-12">
              <label htmlFor="email" className="d-block small mt-1 mb-0">E-post</label>
              <input placeholder="E-post" type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" name="email" value={email} onChange={e => this.handleChange(e.target)} className="rounded w-100 d-inline-block m-0" />
            </div>
          </div>
          <div className="row">
            <div className="col-12">
              <label htmlFor="priceadjustment" className="d-block small mt-3 mb-0">Rabatt/Tillägg</label>
              <input placeholder="0" type="number" name="priceadjustment" value={Number(priceadjustment).toFixed(0)} style={{width: '180px'}} onChange={e => this.handleChange(e.target)} className="rounded d-inline-block m-0" /> kr
            </div>
          </div>
          <div className="row">
            <div className="col-8 pr-1">
              <label htmlFor="room" className="d-block small mt-1 mb-0">Rumstyp</label>
              {roomLabel}
            </div>
            <div className="col-4 pl-1">
              <label htmlFor="price" className="d-block small mt-1 mb-0">Pris efter rabatt</label>
              <span>{Number(Number(price) + Number(priceadjustment)).toFixed(0)} kr</span>
            </div>
          </div>
          <div className="row">
            <div className="col-8 pr-1">
              <label htmlFor="departurelocation" className="d-block small mt-1 mb-0">Avgångsplats</label>
              {departurelocation}
            </div>
            <div className="col-4 pl-1">
              <label htmlFor="departuretime" className="d-block small mt-1 mb-0">Avgtid</label>
              <input id="departuretime" type="time" name="departuretime" value={departuretime} onChange={(e) => { this.handleChange(e.target) }} className="rounded w-100 d-inline-block m-0" placeholder="00:00" required />
            </div>
          </div>
          <div className="row">
            <div className="col-12">
              <label htmlFor="personalnumber" className="d-block small mt-1 mb-0">Personnummer</label>
              <input placeholder="XXXXXX-XXXX" pattern="^[0-9]{6}[-+]{1}[0-9]{4}$" maxLength="11" type="text" name="personalnumber" value={personalnumber} onChange={e => this.handleChange(e.target)} className="rounded w-100 d-inline-block m-0" />
            </div>
          </div>
          <div className="row">
            <div className="col-12">
              <label htmlFor="requests" className="d-block small mt-1 mb-0">Önskemål</label>
              <textarea value={requests} className="rounded w-100 d-inline-block m-0 rbt-input-main" style={{height: '100px'}} />
            </div>
          </div>
        </div>

      </div>
    )
  }
}

BookingsCustomer.propTypes = {
  customer: PropTypes.object,
  id      : PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  number  : PropTypes.oneOfType([PropTypes.string, PropTypes.number])
}

const mapStateToProps = state => ({
  allCustomers: typeof state.lists.customers !== 'undefined' ? state.lists.customers : []
})

export default connect(mapStateToProps, null)(BookingsCustomer)
