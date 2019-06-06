import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import {getItem} from '../../actions'
import { Typeahead, Menu, MenuItem } from 'react-bootstrap-typeahead'
import searchStyle from '../../styles/searchStyle'

class GroupList extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isSubmitting  : false,
      orgSelected   : [],
      catSelected   : [],
      firstname     : '',
      lastname      : '',
      organisation  : '',
      street        : '',
      city          : '',
      zip           : '',
      phone         : '',
      email         : '',
      personalnumber: ''
    }
  }

  componentWillMount () {
    this.reduxGetAllUpdate()
  }

  componentWillUnmount () {
    this.reduxGetAllUpdate()
  }

  reduxGetAllUpdate = () => {
    const {getItem} = this.props
    getItem('groupcustomers', 'all')
    getItem('categories', 'all')
  }

  submitToggle = (b) => {
    const validatedb = !!b
    this.setState({isSubmitting: validatedb})
  }

  changeOrg = (org) => {
    const { catSelected, ...state } = this.state
    const categories = typeof org[0] === 'undefined' ? catSelected : org[0].categories
    const e = {}
    const properties = ['firstname', 'lastname', 'organisation', 'street', 'city', 'zip', 'phone', 'email', 'personalnumber']
    properties.map(p => {
      const value = typeof org[0] === 'undefined' ? state[p] : org[0][p]
      e.name = p
      e.value = value
      this.handleChange(e)
    })
    this.setState({ orgSelected: org, catSelected: categories })
  }

  handleChange = (e) => {
    this.setState({ [e.name]: e.value })
  }

  render () {
    const { isSubmitting, orgSelected, catSelected, firstname, lastname, organisation, street, city, zip, phone, email, personalnumber } = this.state
    const { groupcustomers, categories } = this.props

    const allactivecategories = categories.filter(category => !category.isdisabled)
    const activecategoriesandselected = (typeof orgSelected[0] !== 'undefined') ? allactivecategories.concat(orgSelected[0].categories) : allactivecategories
    const activecategories = []
    if (typeof activecategoriesandselected === 'object') {
      const map = new Map()
      for (const item of activecategoriesandselected) {
        if (!map.has(item.id)) {
          map.set(item.id, true)
          activecategories.push({
            id   : item.id,
            label: item.label
          })
        }
      }
    }
    console.log(catSelected)

    return (
      <div className="ListView GroupList">

        <form>
          <fieldset disabled={isSubmitting}>
            <div className="container text-left" style={{maxWidth: '650px'}}>
              <h3 className="my-4 w-50 mx-auto text-center">Gruppkunder</h3>
              <h6 className="m-3 p-2 text-center">Sök efter eller lägg till gruppkunder</h6>
              <div className="container-fluid" style={{width: '85%'}}>
                <div className="row m-0 p-0">
                  <div className="text-center col-12 px-1 py-0 my-1 mx-0">
                    <Typeahead className="rounded w-100 d-inline-block"
                      inputProps={{style: searchStyle, type: 'search'}}
                      id="groupOrg"
                      name="groupOrg"
                      minLength={2}
                      maxResults={5}
                      flip
                      emptyLabel=""
                      disabled={isSubmitting}
                      onChange={(orgSelected) => this.changeOrg(orgSelected) }
                      labelKey="organisation"
                      filterBy={['organisation', 'firstname', 'lastname', 'phone', 'email', 'personalnumber']}
                      options={groupcustomers}
                      selected={orgSelected}
                      placeholder="Sök gruppkund"
                      renderMenu={(results, menuProps) => (
                        <Menu {...menuProps}>
                          {results.map((result, index) => (
                            <MenuItem key={index} option={result} position={index}>
                              <div key={index} className="small m-0 p-0">
                                <p className="m-0 p-0">{result.organisation}</p>
                                <p className="m-0 p-0">{result.firstname} {result.lastname}</p>
                                <p className="m-0 p-0">{result.street}</p>
                                <p className="m-0 p-0">{result.phone}</p>
                              </div>
                            </MenuItem>))}
                        </Menu>
                      )}
                      // eslint-disable-next-line no-return-assign
                      ref={(ref) => this._Organisation = ref}
                    />

                  </div>
                </div>
                <div className="row mx-0 mb-0 mt-2 p-0">
                  <div className="text-center col-12 px-1 py-0 m-0">
                    <label className="small w-100 text-left p-0 mx-0 mt-1 mb-0 d-block" htmlFor="organisation">Organisation:</label>
                    <input placeholder="Organisation" type="text" name="organisation" value={organisation} onChange={e => this.handleChange(e.target)} className="rounded w-100 d-inline-block m-0" />
                  </div>
                </div>
                <div className="row m-0 p-0">
                  <div className="text-center col-5 px-1 py-0 m-0">
                    <label className="small w-100 text-left p-0 mx-0 mt-1 mb-0 d-block" htmlFor="firstname">Förnamn:</label>
                    <input placeholder="Förnamn" type="text" name="firstname" value={firstname} onChange={e => this.handleChange(e.target)} className="rounded w-100 d-inline-block m-0" />
                  </div>
                  <div className="text-center col-7 px-1 py-0 m-0">
                    <label className="small w-100 text-left p-0 mx-0 mt-1 mb-0 d-block" htmlFor="lastname">Efternamn:</label>
                    <input placeholder="Efternamn" type="text" name="lastname" value={lastname} onChange={e => this.handleChange(e.target)} className="rounded w-100 d-inline-block m-0" />
                  </div>
                </div>
                <div className="row m-0 p-0">
                  <div className="text-center col-12 px-1 py-0 m-0">
                    <label className="small w-100 text-left p-0 mx-0 mt-1 mb-0 d-block" htmlFor="street">Gatuadress:</label>
                    <input placeholder="Gatuadress" type="text" name="street" value={street} onChange={e => this.handleChange(e.target)} className="rounded w-100 d-inline-block m-0" />
                  </div>
                </div>
                <div className="row m-0 p-0">
                  <div className="text-center col-5 px-1 py-0 m-0">
                    <label className="small w-100 text-left p-0 mx-0 mt-1 mb-0 d-block" htmlFor="zip">Postnr:</label>
                    <input placeholder="Postnr" type="text" pattern="^[0-9]{3}[ ]?[0-9]{2}$" maxLength="6" name="zip" value={zip} onChange={e => this.handleChange(e.target)} className="rounded w-100 d-inline-block m-0" />
                  </div>
                  <div className="text-center col-7 px-1 py-0 m-0">
                    <label className="small w-100 text-left p-0 mx-0 mt-1 mb-0 d-block" htmlFor="city">Postort:</label>
                    <input placeholder="Postort" type="text" name="city" value={city} onChange={e => this.handleChange(e.target)} className="rounded w-100 d-inline-block m-0" />
                  </div>
                </div>
                <div className="row m-0 p-0">
                  <div className="text-center col-12 px-1 py-0 m-0">
                    <label className="small w-100 text-left p-0 mx-0 mt-1 mb-0 d-block" htmlFor="phone">Telefonnr:</label>
                    <input placeholder="Telefonnr" type="tel" pattern="^[^a-zA-Z]+$" name="phone" value={phone} onChange={e => this.handleChange(e.target)} className="rounded w-100 d-inline-block m-0" />
                  </div>
                </div>
                <div className="row m-0 p-0">
                  <div className="text-center col-12 px-1 py-0 m-0">
                    <label className="small w-100 text-left p-0 mx-0 mt-1 mb-0 d-block" htmlFor="email">E-post:</label>
                    <input placeholder="E-post" type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" name="email" value={email} onChange={e => this.handleChange(e.target)} className="rounded w-100 d-inline-block m-0" />
                  </div>
                </div>
                <div className="row m-0 p-0">
                  <div className="text-center col-12 px-1 py-0 m-0">
                    <label className="small w-100 text-left p-0 mx-0 mt-1 mb-0 d-block" htmlFor="personalnumber">Orgnr/Personnr:</label>
                    <input placeholder="XXXXXX-XXXX" pattern="^[0-9]{6}[-+]{1}[0-9]{4}$" maxLength="11" type="text" name="personalnumber" value={personalnumber} onChange={e => this.handleChange(e.target)} className="rounded w-100 d-inline-block m-0" />
                  </div>
                </div>
                <div className="row m-0 p-0">
                  <div className="text-center col-12 px-1 py-0 m-0">
                    <label className="small w-100 text-left p-0 mx-0 mt-1 mb-0 d-block" htmlFor="groupCategories">Kategorier:</label>
                    <Typeahead className="rounded w-100 d-inline-block m-0"
                      id="groupCategories"
                      name="groupCategories"
                      minLength={2}
                      maxResults={5}
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
              </div>
            </div>
          </fieldset>
        </form>
      </div>
    )
  }
}

GroupList.propTypes = {
  getItem       : PropTypes.func,
  groupcustomers: PropTypes.array,
  categories    : PropTypes.array
}

const mapStateToProps = state => ({
  groupcustomers: state.lists.groupcustomers,
  categories    : state.tours.categories
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(GroupList)
