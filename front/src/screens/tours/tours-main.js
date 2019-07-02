import React, { Component } from 'react'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import { getItem } from '../../actions'
import { faFilter } from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import { bindActionCreators } from 'redux'
import TourRow from '../../components/tours/tour-row'

class TourViewMain extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isSubmitting  : false,
      tourRowLimit  : 10,
      showOnlyActive: true
    }
  }

  componentWillMount () {
    const {getItem} = this.props
    getItem('tours')
  }

  submitToggle = (b) => {
    const validatedb = !!b
    this.setState({isSubmitting: validatedb})
  }

  toggleShowOnlyActive = (e) => {
    e.preventDefault()
    const {showOnlyActive} = this.state
    const inverted = !showOnlyActive
    this.setState({showOnlyActive: inverted})
  }

  render () {
    const {tours = []} = this.props
    const {isSubmitting, tourRowLimit, showOnlyActive} = this.state

    let temp
    try {
      temp = tours.slice(0, tourRowLimit).filter(tour => { return !(tour.isdisabled && showOnlyActive) }).map((tour) => {
        return <TourRow key={'tour' + tour.id}
          id={tour.id}
          label={tour.label}
          isDisabled={tour.isdisabled}
          departuredate={tour.departuredate}
          submitToggle={this.submitToggle}
          insuranceprice={tour.insuranceprice}
          reservationfeeprice={tour.reservationfeeprice}
          rooms={tour.rooms}
          categories={tour.categories}
        />
      })
    } catch (e) {
      temp = null
    }
    const tourRows = temp
    temp = undefined

    return (
      <div className="TourViewMain">
        <h3 className="my-4">Resor &amp; Bokningar</h3>
        <div className="container-fluid pt-2">
          <div className="row">
            <div className="col-lg-4 col-md-12">
              <h4 className="w-75 my-3 mx-auto">Bokningar</h4>
              <Link to={'/bokningar/bokning/ny'} className="btn w-75 btn-primary my-3 mx-auto py-2">Skapa ny bokning</Link>
              <button className="btn w-75 btn-primary my-3 mx-auto py-2">Skapa ny reservation</button>
              <button className="btn w-75 btn-primary my-4 mx-auto py-2">Spara programbeställningar</button>
              <p className="w-75 my-3 py-2 mx-auto px-1 text-justify d-block">Reservation används för att boka upp platser för förare och reseledare, samt för preliminärbokningar och grupper.</p>
            </div>
            <div className="col-lg-4 col-md-12">
              <h4 className="w-75 my-3 mx-auto">Betalningar</h4>
              <button className="btn w-75 btn-primary my-3 mx-auto py-2">Registrera betalning</button>
              <p className="w-75 my-3 py-2 mx-auto px-1 text-justify d-block">Senaste registrerade betalning:</p>
            </div>
            <div className="col-lg-4 col-md-12">
              <h4 className="w-75 my-3 mx-auto">Resor</h4>
              <Link to={'/bokningar/resa/ny'} className="btn w-75 btn-primary my-3 mx-auto py-2">Skapa ny resa</Link>
              <Link to={'/bokningar/kategorier'} className="btn w-75 btn-primary my-3 mx-auto py-2">Ändra resekategorier</Link>
              <form>
                <fieldset disabled={isSubmitting.tour}>
                  <table className="w-75 my-3 py-2 mx-auto px-1 text-justify d-block">
                    <thead>
                      <tr>
                        <th className="py-2" colSpan="2">Redigera resa</th>
                        <th className="align-middle text-center py-2">
                          {!showOnlyActive &&
                          <span title="Dölj inaktiva resor" className="seconday-color custom-scale cursor-pointer"><FontAwesomeIcon icon={faFilter} onClick={(e) => this.toggleShowOnlyActive(e)} size="lg" /></span> }
                          {showOnlyActive &&
                          <span title="Visa inaktiva resor" className="primary-color custom-scale  cursor-pointer"><FontAwesomeIcon icon={faFilter} onClick={(e) => this.toggleShowOnlyActive(e)} size="lg" /></span> }
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      {tourRows}
                      {tours.filter(tour => { return !(tour.isdisabled && showOnlyActive) }).length >= tourRowLimit ? <tr>
                        <td colSpan="3" className="py-3"><button className="btn btn-primary btn-sm mt-1 px-2 py-1 w-100" onClick={(e) => { e.preventDefault(); this.setState({tourRowLimit: tourRowLimit + 10}) }}>Visa fler resor</button></td>
                      </tr> : null}
                    </tbody>
                  </table>
                </fieldset>
              </form>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

TourViewMain.propTypes = {
  getItem: PropTypes.func,
  tours  : PropTypes.array
}

const mapStateToProps = state => ({
  tours: state.tours.tours
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(TourViewMain)
