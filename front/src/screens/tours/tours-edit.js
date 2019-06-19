import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import {faPlus, faSave, faMinus} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import {getItem, putItem, postItem, deleteItem} from '../../actions'
import { Typeahead, Menu, MenuItem } from 'react-bootstrap-typeahead'
import update from 'immutability-helper'
import { findByKey, getActivePlusSelectedCategories } from '../../utils'
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
      stateCategories    : [],
      insuranceprice     : 150,
      reservationfeeprice: 300,
      rooms              : [],
      catSelected        : [] }
  }

  componentWillMount () {
    const {getItem} = this.props
    getItem('categories', 'all')
    getItem('tours', 'all')
  }

  componentWillReceiveProps (nextProps) {
    const {match, tours} = this.props
    if (Number(match.params.id) >= 0 && tours !== nextProps.tours && typeof nextProps.tours === 'object' && nextProps.tours.length > 0) {
      const tour = findByKey(match.params.id, 'id', nextProps.tours)
      this.setState({
        id                 : tour.id,
        label              : tour.label,
        departuredate      : moment(tour.departuredate).format('YYYY-MM-DD'),
        stateCategories    : tour.categories,
        insuranceprice     : tour.insuranceprice,
        reservationfeeprice: tour.reservationfeeprice,
        rooms              : tour.rooms,
        catSelected        : tour.categories
      })
    }
  }

  handleSave = () => {}

  handleChange = (e) => {
    this.setState({ [e.name]: e.value })
  }

  handleChangeRoomRow = (e, i) => {
    const {rooms} = this.state
    const newroom = update(rooms, {[[i]]: {[e.name]: {$set: e.value}}})
    this.setState({rooms: newroom})
  }

  addRow = () => {
    const emptyRoom = {
      id             : 'new',
      label          : '',
      price          : '',
      numberavaliable: ''
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
    const {isSubmitting, label, departuredate, stateCategories, insuranceprice, reservationfeeprice, rooms, catSelected} = this.state
    const {categories, tours} = this.props
    const activecategories = getActivePlusSelectedCategories(categories, tours.id)

    const roomRows = rooms.map((item, i) => {
      return (<tr key={i}>
        <td className="p-2 w-50 align-middle"><input value={item.label} name="label" onChange={(e) => this.handleChangeRoomRow(e.target, i)} className="rounded w-100" placeholder="Rumstyp/Dagsresa" maxLength="99" type="text" required /></td>
        <td className="p-2 align-middle"><input value={Number(item.size).toFixed(0)} name="size" onChange={(e) => this.handleChangeRoomRow(e.target, i)} className="text-right rounded" placeholder="0" type="number" min="0" max="99" maxLength="2" step="1" style={{width: '75px'}} required /></td>
        <td className="pl-2 pr-1 text-nowrap align-middle"><input value={Number(item.price).toFixed(0)} name="price" onChange={(e) => this.handleChangeRoomRow(e.target, i)} className="text-right rounded mr-1" type="number" placeholder="0" min="0" max="99999" maxLength="5" step="1" style={{width: '75px'}} required />kr</td>
        <td className="p-2 align-middle"><input value={Number(item.numberavaliable).toFixed(0)} name="numberavaliable" onChange={(e) => this.handleChangeRoomRow(e.target, i)} className="text-right rounded" type="number" placeholder="0" min="0" max="999" maxLength="3" step="1" style={{width: '75px'}} required /></td>
      </tr>)
    })

    return (
      <div className="TourView NewTour">
        <form>
          <fieldset disabled={isSubmitting}>
            <div className="container text-left" style={{maxWidth: '850px'}}>
              <h3 className="my-3 w-50 mx-auto text-center">{label !== '' ? 'Ändra ' + label : 'Skapa ny resa'}</h3>
              <div className="container-fluid" style={{width: '85%'}}>
                <fieldset>
                  <div className="row m-0 p-0">
                    <div className="text-center col-12 px-1 py-0 m-0">
                      <label className="small w-100 text-left p-0 mx-0 mt-1 mb-0 d-block" htmlFor="tourName">Resans namn</label>
                      <input id="tourName" name="tourName" value={label} onChange={() => {}} className="rounded w-100 d-inline-block m-0" placeholder="Resans namn" maxLength="99" type="text" required />
                    </div>
                  </div>
                  <div className="row m-0 p-0">
                    <div className="text-center col-12 px-1 py-0 m-0">
                      <label className="small w-100 text-left p-0 mx-0 mt-1 mb-0 d-block" htmlFor="tourCategories">Resekategorier:</label>
                      <Typeahead className="rounded w-100 d-inline-block m-0"
                        id="tourCategories"
                        name="tourCategories"
                        minLength={0}
                        maxResults={30}
                        flip
                        multiple
                        emptyLabel=""
                        disabled={isSubmitting}
                        onChange={(catSelected) => { this.setState({ catSelected: catSelected }) }}
                        labelKey="label"
                        filterBy={['label']}
                        options={activecategories}
                        selected={catSelected}
                        placeholder="Kategorier"
                        // eslint-disable-next-line no-return-assign
                        ref={(ref) => this._Category = ref}
                      />
                    </div>
                  </div>
                  <div className="w-50 d-inline">
                    <label htmlFor="tourDate" className="d-block small mt-1 mb-0">Avresedatum (åååå-mm-dd)</label>
                    <input id="tourDate" name="tourDate" value={departuredate} onChange={() => {}} className="rounded" type="date" style={{width: '166px'}} min="2000-01-01" max="3000-01-01" placeholder="0" required />
                  </div>
                  <div className="w-25 d-inline">
                    <label htmlFor="tourReservation" className="d-block small mt-1 mb-0">Anmälningsavgift</label>
                    <input id="tourReservation" name="tourReservation" value={Number(insuranceprice).toFixed(0)} onChange={() => {}} className="rounded text-right" type="number" style={{width: '75px'}} min="0" max="9999" placeholder="0" maxLength="4" step="1" required /> kr
                  </div>
                  <div className="w-25 d-inline">
                    <label htmlFor="tourInsurance" className="d-block small mt-1 mb-0">Avbeställningskydd</label>
                    <input id="tourInsurance" name="tourInsurance" value={Number(reservationfeeprice).toFixed(0)} onChange={() => {}} className="rounded text-right" type="number" style={{width: '75px'}} min="0" max="9999" placeholder="0" maxLength="4" step="1" required /> kr
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
                          {rooms.length > 1 &&
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
  categories: PropTypes.array,
  tours     : PropTypes.array,
  match     : PropTypes.object
}

const mapStateToProps = state => ({
  categories: state.tours.categories,
  tours     : state.tours.tours
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem,
  postItem
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(NewTour)
