import React, { Component } from 'react'
import { connect } from 'react-redux'
import Logo from '../../img/logo.gif'
import { Link, Redirect } from 'react-router-dom'
import {getItem} from '../../actions'
import { bindActionCreators } from 'redux'
import { Typeahead, Menu, MenuItem } from 'react-bootstrap-typeahead'
import searchStyle from '../../styles/searchStyle'
import PropTypes from 'prop-types'
import moment from 'moment'
import 'moment/locale/sv'

class MainMenu extends Component {
  constructor (props) {
    super(props)
    this.state = {
      shortcutToggle: false,
      tourToggle    : false,
      searchResult  : [],
      redirect: null
    }
  }

  componentDidMount () {
    const {getItem} = this.props
    getItem('bookingssearchlist', 'all')
  }

  shortcutToggler = (flag) => {
    if (flag === null) {
      const {shortcutToggle = false} = this.state
      shortcutToggle ? flag = false : flag = true
    }
    this.setState({shortcutToggle: flag})
  }

  tourToggler = (flag) => {
    if (flag === null) {
      const {tourToggle = false} = this.state
      tourToggle ? flag = false : flag = true
    }
    this.setState({tourToggle: flag})
  }

  onSearchSelect = (selected) => {
    const redirect = typeof selected[0] === 'object' && selected[0].bookingnr !== 'undefined' ? selected[0].bookingnr : null
    this.setState({searchResult: selected, redirect: redirect})
  }

  render () {
    const {shortcutToggle = false, searchResult = [], searchOptions = [], redirect = null} = this.state
    const {bookingssearchlist} = this.props

    console.log(bookingssearchlist)

    const tours =
      <div className="m-2 dropdown-wrapper custom-order-sm-10">
        <div className="dropdown">
          <div className="list-group text-uppercase font-weight-bold dropdown-menu custom-dropdown" onMouseEnter={() => this.tourToggler(true)} onMouseLeave={() => this.tourToggler(false)}>
            <div className="list-group-item dropdown-toggle active custom-wide-text cursor-pointer" onClick={() => this.tourToggler(null)}>Bokningsläge</div>

          </div>
        </div>
      </div>

    const shortcuts = shortcutToggle ? [
      'Skapa bokning',
      'Programbeställningar',
      'Lägg in betalningar',
      'Ny resa',
      'Ny kalkyl'
    ] : []
console.log(this.state)
console.log(this.props)
    if (redirect) {
      return <Redirect to={redirect} />
    }
    return (
      <div className="MainMenu d-print-none">

        <nav className="my-1 mx-1">
          <div className="d-flex flex-wrap flex-row justify-content-between align-items-baseline my-2 py-1">
            <Link to={'/'}><img src={Logo} alt="Logo" className="m-2 rounded custom-scale" title="Startsida" id="mainLogo" /></Link>
            <Typeahead className="m-2 rounded"
              inputProps={{style: searchStyle, type: 'search', title: 'Sök på bokningsnr, resa, namn, e-post eller telefonnr.'}}
              id="searchBox"
              name="searchBox"
              minLength={1}
              maxResults={20}
              flip
              emptyLabel=""
              paginationText="Visa fler resultat"
              onChange={(searchResult) => this.onSearchSelect(searchResult)}
              labelKey="bookingnr"
              filterBy={['bookingnr', 'lastname', 'fullname', 'phone', 'email', 'tour']}
              options={bookingssearchlist}
              selected={searchResult}
              placeholder="Sök bokning..."
              renderMenu={(results, menuProps) => (
                <Menu {...menuProps}>
                  {results.map((result, index) => (
                    <MenuItem key={index} option={result} position={index}>
                      <div key={index} className="small m-0 p-0">
                        <p className="m-0 p-0">{result.bookingnr} {result.tour} {moment(result.departuredate).format('D/M-YY')}</p>
                        <p className="m-0 p-0">{result.firstname} {result.lastname}</p>
                        <p className="m-0 p-0">{result.email}</p>
                        <p className="m-0 p-0">{result.phone}</p>
                      </div>
                    </MenuItem>))}
                </Menu>
              )}
              // eslint-disable-next-line no-return-assign
              ref={(ref) => this._SearchBox = ref}
            />
            <div className="m-2 dropdown-wrapper dropdown-wrapper-smaller custom-order-sm-9">
              <div className="dropdown">
                <div className="list-group text-uppercase font-weight-bold dropdown-menu dropdown-menu-smaller custom-dropdown" onMouseEnter={() => this.shortcutToggler(true)} onMouseLeave={() => this.shortcutToggler(false)}>
                  <div className="list-group-item dropdown-toggle active custom-wide-text cursor-pointer" onClick={() => this.shortcutToggler(null)} >Genvägar</div>
                  {shortcuts.map((shortcut, i) => {
                    return <div className="list-group-item dropdown-item cursor-pointer" key={i}>{shortcut}</div>
                  })
                  }

                </div>
              </div>
            </div>
            {tours}
            <span className="m-2 "><Link to={'/bokningar/'}><button style={{minWidth: '140px'}} type="button" title="Resor, bokningar och betalningar" className="text-uppercase font-weight-bold btn btn-primary custom-scale custom-wide-text">Bokningar</button></Link></span>
            <span className="m-2 "><Link to={'/kalkyler/'}><button style={{minWidth: '140px'}} type="button" title="Resekalkyler" className="text-uppercase font-weight-bold btn btn-primary custom-scale custom-wide-text">Kalkyler</button></Link></span>
            <span className="m-2 "><Link to={'/utskick/'}><button style={{minWidth: '180px'}} title="Adresslistor för utskick" type="button" className="text-uppercase font-weight-bold btn btn-primary custom-scale custom-wide-text">Utskick/Register</button></Link></span>
          </div>
        </nav>

      </div>
    )
  }
}

MainMenu.propTypes = {
  getItem           : PropTypes.func,
  bookingssearchlist: PropTypes.array
}

const mapStateToProps = state => ({
  bookingssearchlist: state.lists.bookingssearchlist
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(MainMenu)
