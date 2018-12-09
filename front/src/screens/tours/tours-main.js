import React, { Component } from 'react'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import { getItem } from '../../actions'
import PropTypes from 'prop-types'
import { bindActionCreators } from 'redux'
import TourRow from '../../components/tours/tour-row'

class TourViewMain extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isSubmitting: {
        tour: false
      }
    }
  }

  componentWillMount () {
    const {getItem} = this.props
    getItem('tours')
  }

  submitToggle = (b, type) => {
    const {isSubmitting} = this.state
    isSubmitting[type] = !!b
    this.setState({isSubmitting})
  }

  render () {
    const {tours = []} = this.props
    const {isSubmitting} = this.state

    let temp
    try {
      temp = tours.map((tour) => {
        return <TourRow key={'tour' + tour.id} id={tour.id} label={tour.label} isActive={tour.active} departure={tour.departure} />
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
              <Link to={'/bokningar/nybokning'} className="btn w-75 btn-primary my-3 mx-auto py-2">Skapa ny bokning</Link>
              <button className="btn w-75 btn-primary my-3 mx-auto py-2">Skapa ny reservation</button>
              <button className="btn w-75 btn-primary my-4 mx-auto py-2">Spara programbeställningar</button>
              <p className="w-75 my-3 py-2 mx-auto px-1 text-justify d-block">Reservation används för att boka upp platser för förare/reseledare, preliminärbokningar &amp; grupper.</p>
            </div>
            <div className="col-lg-4 col-md-12">
              <h4 className="w-75 my-3 mx-auto">Betalningar</h4>
              <button className="btn w-75 btn-primary my-3 mx-auto py-2">Registrera betalning</button>
              <p className="w-75 my-3 py-2 mx-auto px-1 text-justify d-block">Senaste registrerad betalning:</p>
            </div>
            <div className="col-lg-4 col-md-12">
              <h4 className="w-75 my-3 mx-auto">Resor</h4>
              <Link to={'/bokningar/resa/ny'} className="btn w-75 btn-primary my-3 mx-auto py-2">Skapa ny resa</Link>
              <Link to={'/bokningar/kategorier'} className="btn w-75 btn-primary my-3 mx-auto py-2">Ändra resekategorier</Link>
              <form>
                <fieldset disabled={isSubmitting.tour}>
                  <table className="w-75 my-3 py-2 mx-auto px-1 text-justify d-block">
                    <tbody>
                      {tourRows}
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
