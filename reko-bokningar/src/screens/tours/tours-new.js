import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import faPlus from '@fortawesome/fontawesome-free-solid/faPlus'
import faSave from '@fortawesome/fontawesome-free-solid/faSave'
import FontAwesomeIcon from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import {getCategories, networkAction} from '../../actions'

class NewTour extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isSubmitting: false,
      tourName: '',
      tourDate: '',
      tourCategory: '',
      tourRoomOpt: [
        {
          roomType: '',
          roomSize: 0,
          roomPrice: 0,
          roomCount: 0
        }
      ]
    }
  }

  componentWillMount() {
    const {networkAction = () => {}, getCategories = () => {}, login = {user: 'anonymous', jwt: 'none'}} = this.props
    networkAction(1, 'updating categories')
    getCategories({
      user      : login.user,
      jwt       : login.jwt,
      categoryid: 'all'
    }).then(() => {networkAction(0, 'updating categories')})
  }

  handleChange = (target) => {
    console.log(target)
    console.log(target.value)
    console.log(target.name)
    if (target.name === 'tourName' || target.name === 'tourDate' || target.name === 'tourCategory')
      this.setState({[target.name]: target.value})
  }

  submitToggle = (b) => {
    const {networkAction} = this.props
    let validatedb
    try {
      validatedb = !!b
    } catch (e) {
      validatedb = false
    }
    networkAction(Number(validatedb), 'saving new tour')
    this.setState({isSubmitting: validatedb})
  }

  render () {
    const {isSubmitting = false, showStatus = false, showStatusMessage = ''} = this.state


    return (
      <div className="TourView NewTour">
        <form>
          <fieldset disabled={isSubmitting}>
            <div className="container text-left" style={{maxWidth: '650px'}}>
              <h3 className="my-4 w-50 mx-auto text-center">Skapa ny resa</h3>
              <fieldset>
                <div>
                  <label className="d-block small mt-1 mb-0">Resans namn</label>
                  <input name="tourName" onChange={e => this.handleChange(e.target)} className="rounded w-100" placeholder="Resans namn" maxLength="99" type="text" />
                </div>
                <div>
                  <label className="d-block small mt-1 mb-0">Avresedatum</label>
                  <input name="tourDate" onChange={e => this.handleChange(e.target)} className="rounded w-100" type="date" />
                </div>
                <div>
                  <label className="d-block small mt-1 mb-0">Kategori</label>
                  <input name="tourCategory" onChange={e => this.handleChange(e.target)} className="rounded w-100" type="text" />
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
                    <tr>
                      <td className="p-2 w-50 align-middle"><input name="roomType" onChange={e => this.handleChange(e.target)} className="rounded w-100" placeholder="Rumstyp/Dagsresa" maxLength="99" type="text" /></td>
                      <td className="p-2 align-middle"><input name="roomSize" onChange={e => this.handleChange(e.target)} className="text-right rounded" placeholder="0" type="number" min="0" max="99" maxLength="2" step="1" style={{width: '75px'}} /></td>
                      <td className="pl-2 pr-1 text-nowrap align-middle"><input name="roomPrice" onChange={e => this.handleChange(e.target)} className="text-right rounded mr-1" type="number" placeholder="0" min="0" max="99999" maxLength="5" step="1" style={{width: '75px'}} />kr</td>
                      <td className="p-2 align-middle"><input name="roomCount" onChange={e => this.handleChange(e.target)} className="text-right rounded" type="number" placeholder="0" min="0" max="999" maxLength="3" step="1" style={{width: '75px'}} /></td>
                    </tr>
                    <tr>
                      <td className="p-2 align-middle" colSpan="2">
                        <button onClick={this.addRow} disabled={isSubmitting} type="button" title="Lägg till flera boendealternativ" className="btn btn-primary custom-scale">
                          <span className="mt-1"><FontAwesomeIcon icon={faPlus} size="lg" /></span>
                        </button>
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
          </fieldset>
        </form>
      </div>
    )
  }
}

NewTour.propTypes = {
  networkAction: PropTypes.func,
  getCategories: PropTypes.func
}

const mapStateToProps = state => ({
  login            : state.login,
  showStatus       : state.errorPopup.visible,
  showStatusMessage: state.errorPopup.message,
  categories       : state.tours.categories
})

const mapDispatchToProps = dispatch => bindActionCreators({
  networkAction,
  getCategories
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(NewTour)
