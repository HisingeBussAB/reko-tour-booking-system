import React, { Component } from 'react'
import { connect } from 'react-redux'
import PropTypes from 'prop-types'
import {getItem} from '../../actions'
import { bindActionCreators } from 'redux'
import { Link } from 'react-router-dom'

class Pending extends Component {
  componentWillMount () {
    this.reduxGetAllUpdate()
  }

  reduxGetAllUpdate = () => {
    const {getItem} = this.props
    getItem('pendingcount', 'all')
  }

  render () {
    const {pendingfromweb: {count: {bookings = 0, leads = 0, newsletter = 0}}} = this.props
    return (
      <div className="Pending text-center">
        <h3>Från Hemsidan</h3>
        <ul className="list-group">
          <Link className="list-group-item list-group-item-action d-flex justify-content-between align-items-center" to={'/pending'}>Nya resebokningar<span className={'badge ' + (bookings > 0 ? 'badge-primary' : 'badge-secondary') + ' badge-pill'} style={{fontSize: '105%'}}>{bookings}</span></Link>
          <Link className="list-group-item list-group-item-action d-flex justify-content-between align-items-center" to={'/pending/leads'}>Nya programbeställningar<span className={'badge ' + (leads > 0 ? 'badge-primary' : 'badge-secondary') + ' badge-pill'} style={{fontSize: '105%'}}>{leads}</span></Link>
          <Link className="list-group-item list-group-item-action d-flex justify-content-between align-items-center" to={'/pending/newsletter'}>Nya nyhetsbrevspenumerationer<span className={'badge ' + (newsletter > 0 ? 'badge-primary' : 'badge-secondary') + ' badge-pill'} style={{fontSize: '105%'}}>{newsletter}</span></Link>
        </ul>
      </div>
    )
  }
}

Pending.propTypes = {
  getItem       : PropTypes.func,
  pendingfromweb: PropTypes.object
}

const mapStateToProps = state => ({
  pendingfromweb: state.pendingfromweb
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(Pending)
