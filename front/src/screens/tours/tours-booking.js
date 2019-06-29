import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import {faPlus, faSave, faMinus, faSpinner, faArrowLeft, faTrash} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import {getItem, putItem, postItem, deleteItem} from '../../actions'
import { Typeahead } from 'react-bootstrap-typeahead'
import update from 'immutability-helper'
import { findByKey, getActivePlusSelectedCategories } from '../../utils'
import { Redirect } from 'react-router-dom'
import ConfirmPopup from '../../components/global/confirm-popup'
import moment from 'moment'
import 'moment/locale/sv'

class NewTourBooking extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isSubmitting: false,
      id          : 'new',
      number      : 'new',
      tourid      : 'new',   
      tour        : {label: ''},       
      customers   : [], 
      redirectTo  : false,
      isConfirming: false
    }
  }

  componentWillMount () {
    const {getItem} = this.props
    getItem('bookings', 'all')
    getItem('categories', 'all')
    getItem('tours', 'all')
  }

  componentWillReceiveProps (nextProps) {
    const {match, bookings, tours} = this.props
    const $newState = {
      redirectTo         : false,
      isConfirming       : false
    }
    if (Number(match.params.number) >= 0 && bookings !== nextProps.bookings && typeof nextProps.bookings === 'object' && nextProps.bookings.length > 0) {
      const booking = findByKey(match.params.number, 'number', nextProps.bookings)
      $newState.id = booking.id
      $newState.number = booking.number
      if (Number(booking.tourid) >= 0 && typeof nextProps.tours === 'object' && nextProps.tours.length > 0) {
        const tour = findByKey(booking.tourid, 'id', nextProps.tours)
        $newState.tourid = booking.id
        $newState.tour = tour
      }
      this.setState($newState)
    }
  }

  handleSave = async () => {
    const {id} = this.state
    const {postItem, putItem, getItem} = this.props
    this.setState({isSubmitting: true})
    const data = {}
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

  handleChangePaxw = (e, i) => {
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
          if (await deleteItem('bookings', id)) {
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
    const { id, isSubmitting, number, tour, redirectTo, isConfirming } = this.state
    const { history } = this.props

    if (redirectTo !== false) { return <Redirect to={redirectTo} /> }

    

    return (
      <div className="TourView NewTour">
        {isConfirming && <ConfirmPopup doAction={this.doDelete} message={`Vill du verkligen markulera bokning:\n${number} ${tour.label}.\nBokningen makuleras för alla resenärer. Det går också att byta ut enskilda resenärer istället.`} />}

        <form>
          <button onClick={() => { history.goBack() }} disabled={isSubmitting} type="button" title="Tillbaka till meny" className="mr-4 btn btn-primary btn-sm custom-scale position-absolute" style={{right: 0}}>
            <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={faArrowLeft} size="1x" />&nbsp;Meny</span>
          </button>
          <fieldset disabled={isSubmitting}>
            <div className="container text-left" style={{maxWidth: '850px'}}>

              <h3 className="my-3 w-100 mx-auto text-center">{id !== 'new' ? 'Ändra bokning: ' + number + ' på ' + tour.label : 'Skapa ny bokning'}</h3>
              <div className="container-fluid" style={{width: '85%'}}>
                <fieldset>
                  
                </fieldset>
              </div>
            </div>
          </fieldset>
        </form>
      </div>
    )
  }
}

NewTourBooking.propTypes = {
  getItem   : PropTypes.func,
  postItem  : PropTypes.func,
  putItem   : PropTypes.func,
  deleteItem: PropTypes.func,
  tours     : PropTypes.array,
  bookings  : PropTypes.array,
  match     : PropTypes.object,
  history   : PropTypes.object
}

const mapStateToProps = state => ({
  tours   : state.tours.tours,
  bookings: state.tours.bookings
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem,
  postItem,
  putItem,
  deleteItem
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(NewTourBooking)
