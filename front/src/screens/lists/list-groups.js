import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import {faSave, faSpinner, faEraser, faTrash, faCalendarCheck, faSortDown, faSortUp, faArrowLeft} from '@fortawesome/free-solid-svg-icons'
import PropTypes from 'prop-types'
import {getItem, putItem, postItem, deleteItem} from '../../actions'
import { Typeahead, Menu, MenuItem } from 'react-bootstrap-typeahead'
import searchStyle from '../../styles/searchStyle'
import { looseObjectCompare, dynamicSort, getActivePlusSelectedCategories } from '../../utils'
import moment from 'moment'
import 'moment/locale/sv'
import ConfirmPopup from '../../components/global/confirm-popup'

class GroupList extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isSubmitting     : false,
      orgSelected      : [],
      catSelected      : [],
      firstname        : '',
      lastname         : '',
      organisation     : '',
      street           : '',
      city             : '',
      zip              : '',
      phone            : '',
      email            : '',
      personalnumber   : '',
      date             : moment().format('YYYY-MM-DD'),
      isConfirming     : false,
      showList         : false,
      sortBy           : 'organisation',
      sortIsDown       : true,
      catFilterSelected: []
    }
    this.initialstate = {
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
      personalnumber: '',
      date          : moment().format('YYYY-MM-DD')
    }
  }

  componentDidMount () {
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

  changeOrg = (org, callback = function () {}) => {
    const { catSelected, ...state } = this.state
    const categories = typeof org[0] === 'undefined' ? catSelected : org[0].categories
    const e = {}
    const properties = ['firstname', 'lastname', 'organisation', 'street', 'city', 'zip', 'phone', 'email', 'personalnumber', 'date']
    properties.forEach(p => {
      const value = typeof org[0] === 'undefined' ? state[p] : org[0][p]
      e.name = p
      e.value = value
      this.handleChange(e)
    })
    this.setState({ orgSelected: org, catSelected: categories }, () => callback())
  }

  handleChange = (e) => {
    this.setState({ [e.name]: e.value })
  }

  handleSetToday = (e) => {
    if (typeof e !== 'undefined') { e.preventDefault() }
    const now = moment().format('YYYY-MM-DD')
    this.setState({ date: now }, () => this.handleSaveDate())
  }

  handleSaveDate = async () => {
    const { orgSelected, date } = this.state
    const { putItem } = this.props
    const hasSelected = typeof orgSelected[0] !== 'undefined' && typeof Number(orgSelected[0].id) === 'number'
    if (hasSelected) {
      this.setState({isSubmitting: true})
      const data = Object.assign({}, orgSelected[0])
      data.date = date
      data.id = orgSelected[0].id
      if (await putItem('groupcustomers', orgSelected[0].id, data)) { this.setState({orgSelected: [data]}) }
      this.setState({isSubmitting: false})
    }
  }

  handleSave = async (e) => {
    e.preventDefault()
    const { orgSelected } = this.state
    const { putItem, postItem } = this.props
    const hasSelected = typeof orgSelected[0] !== 'undefined'
    this.setState({isSubmitting: true})
    const data = this.getStructuredState()
    if (hasSelected) {
      data.id = orgSelected[0].id
      if (await putItem('groupcustomers', orgSelected[0].id, data)) { this.setState({orgSelected: [data], catSelected: data.categories}) }
    }
    if (!hasSelected) {
      const post = await postItem('groupcustomers', data)
      if (post !== false && typeof Number(post) === 'number') {
        data.id = post
        this.setState({orgSelected: [data], catSelected: data.categories})
      }
    }
    this.setState({isSubmitting: false})
  }

  handleClear = (e) => {
    if (typeof e !== 'undefined') { e.preventDefault() }
    this._Organisation.getInstance().clear()
    this._Category.getInstance().clear()
    this.setState(this.initialstate)
  }

  getEditState = () => {
    const { orgSelected } = this.state
    const structuredState = this.getStructuredState()
    if (typeof orgSelected[0] !== 'object') { return false }
    return !looseObjectCompare(structuredState, orgSelected[0])
  }

  getEmptyState = () => {
    const structuredState = this.getStructuredState()
    const { firstname, lastname, organisation, street, phone, email } = this.state
    if ((firstname.length < 2 || lastname.length < 2) && organisation.length < 1) { return true }
    if (street.length < 2 && phone.length < 5 && email.length < 5) { return true }
    if (looseObjectCompare(this.initialstate, structuredState)) { return true }
    return false
  }

  getStructuredState = () => {
    const { catSelected, firstname, lastname, organisation, street, city, zip, phone, email, personalnumber, date } = this.state
    return {
      organisation  : organisation,
      firstname     : firstname,
      lastname      : lastname,
      street        : street,
      city          : city,
      zip           : zip,
      phone         : phone,
      email         : email,
      personalnumber: personalnumber,
      date          : moment(date).format('YYYY-MM-DD'),
      categories    : catSelected
    }
  }

  deleteConfirm = (e) => {
    if (typeof e !== 'undefined') { e.preventDefault() }
    this.setState({isSubmitting: true})
    this.setState({isConfirming: true})
  }

  doDelete = async (choice) => {
    const { orgSelected } = this.state
    this.setState({isConfirming: false})
    const { deleteItem } = this.props
    if (choice === true) {
      if (typeof orgSelected[0] !== 'undefined') {
        if (await deleteItem('groupcustomers', orgSelected[0].id)) {
          this.handleClear()
        }
      }
    }
    this.setState({isSubmitting: false})
  }

  sortBy = (order) => {
    const { sortBy, sortIsDown } = this.state
    if (sortBy === order) {
      this.setState({sortIsDown: !sortIsDown})
      return
    }
    this.setState({sortBy: order, sortIsDown: true})
  }

  render () {
    const { sortIsDown, showList, sortBy, isConfirming, isSubmitting, orgSelected, catSelected, firstname, lastname, organisation, street, city, zip, phone, email, personalnumber, date, catFilterSelected } = this.state
    const { groupcustomers, categories, history } = this.props

    const activecategories = [...getActivePlusSelectedCategories(categories, orgSelected[0])]
    activecategories.sort(dynamicSort('label'))

    const isEdited = this.getEditState()
    const isEmpty = this.getEmptyState()
    const hasSelected = typeof orgSelected[0] !== 'undefined'

    const groupcustomersSorted = [...groupcustomers]
    groupcustomersSorted.sort(dynamicSort(sortBy))
    if (!sortIsDown) { groupcustomersSorted.reverse() }
    const selectedFilterLabels = catFilterSelected.map(cf => { return cf.label })
    const orgRowsFiltered = groupcustomersSorted.filter(c => {
      const rowCatLabels = c.categories.map(l => { return l.label })
      return (catFilterSelected.length < 1) || selectedFilterLabels.some(r => rowCatLabels.includes(r))
    }).map(c => {
      return <tr key={c.id}><td>{c.organisation}</td>
        <td onClick={() => this.changeOrg([c])}>{`${c.firstname} ${c.lastname}`}</td>
        <td onClick={() => this.changeOrg([c])}>{c.street}</td>
        <td onClick={() => this.changeOrg([c])}>{c.zip}</td>
        <td onClick={() => this.changeOrg([c])}>{c.city}</td>
        <td onClick={() => this.changeOrg([c])}>{c.phone}</td>
        <td onClick={() => this.changeOrg([c])}>{c.email}</td>
        <td onClick={() => this.changeOrg([c])}>{c.date}</td>
        <td><FontAwesomeIcon onClick={() => this.changeOrg([c], () => this.handleSetToday())} className="text-primary" icon={faCalendarCheck} size="sm" /></td>
        <td><FontAwesomeIcon onClick={() => this.changeOrg([c], () => this.deleteConfirm())} className="text-danger" icon={faTrash} size="sm" /></td>
      </tr>
    })

    return (
      <div className="ListView GroupList">
        {isConfirming && typeof orgSelected[0] !== 'undefined' && <ConfirmPopup doAction={this.doDelete} message={`Vill du verkligen ta bort:\n${orgSelected[0].organisation}\n${orgSelected[0].firstname} ${orgSelected[0].lastname}`} />}
        <form autoComplete="off">
          <button onClick={() => { history.goBack() }} disabled={isSubmitting} type="button" title="Tillbaka till meny" className="mr-4 btn btn-primary btn-sm custom-scale position-absolute" style={{right: 0}}>
            <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={faArrowLeft} size="1x" />&nbsp;Meny</span>
          </button>
          <fieldset disabled={isSubmitting}>
            <div className="container text-left" style={{maxWidth: '850px'}}>

              <h3 className="my-3 w-50 mx-auto text-center">Gruppkunder</h3>
              <div className="container-fluid" style={{width: '85%'}}>
                <div className="row m-0 p-0">
                  <div className="text-center col-12 px-1 py-0 my-1 mx-0">
                    <Typeahead className="rounded w-100 d-inline-block"
                      inputProps={{style: searchStyle, type: 'search'}}
                      id="groupOrg"
                      name="groupOrg"
                      minLength={1}
                      maxResults={6}
                      flip
                      emptyLabel=""
                      paginationText="Visa fler resultat"
                      disabled={isSubmitting}
                      onChange={(orgSelected) => this.changeOrg(orgSelected)}
                      labelKey="organisation"
                      filterBy={['organisation', 'firstname', 'lastname', 'phone', 'email', 'personalnumber']}
                      options={groupcustomersSorted}
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
                    <label className="small w-100 text-left p-0 mx-0 mt-1 mb-0 d-block" htmlFor="personalnumber">Orgnr/Personnr (xxxxxx-xxxx):</label>
                    <input placeholder="XXXXXX-XXXX" pattern="^[0-9]{6}[-+]{1}[0-9]{4}$" maxLength="11" type="text" name="personalnumber" value={personalnumber} onChange={e => this.handleChange(e.target)} className="rounded w-100 d-inline-block m-0" />
                  </div>
                </div>
                <div className="row m-0 p-0">
                  <div className="text-center col-12 px-1 py-0 m-0">
                    <label className="small w-100 text-left p-0 mx-0 mt-1 mb-0 d-block" htmlFor="groupCategories">Resekategorier:</label>
                    <Typeahead className="rounded w-100 d-inline-block m-0"
                      id="groupCategories"
                      name="groupCategories"
                      minLength={0}
                      maxResults={30}
                      flip
                      multiple
                      emptyLabel=""
                      paginationText="Visa fler resultat"
                      disabled={isSubmitting}
                      onChange={(catSelected) => { this.setState({ catSelected: catSelected }) }}
                      labelKey="label"
                      filterBy={['label']}
                      options={activecategories}
                      selected={catSelected}
                      placeholder="Kategorier"
                      allowNew={false}
                      // eslint-disable-next-line no-return-assign
                      ref={(ref) => this._Category = ref}
                    />
                  </div>
                </div>
                <div className="row m-0 p-0">
                  <div className="text-left col-6 px-1 py-0 m-0">
                    <label className="small w-100 text-left p-0 mx-0 mt-1 mb-0 d-block" htmlFor="personalnumber">Senaste kontakt (åååå-mm-dd):</label>
                    <input placeholder="ÅÅÅÅ-MM-DD" type="date" name="date" value={date} onChange={e => this.handleChange(e.target)} className="rounded d-inline-block m-0 w-100" />
                  </div>
                  <div className="text-left col-2 px-1 py-0 m-0">
                    <label className="small w-100 text-left p-0 mx-0 mt-1 mb-0 d-block">Sätt idag:</label>
                    <button onClick={e => this.handleSetToday(e)} disabled={isSubmitting} type="button" title="Sätt till dagens datum" className="btn btn-primary custom-scale w-100 m-0">
                      <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={faCalendarCheck} size="lg" /></span>
                    </button>
                  </div>
                </div>
                <div className="row my-2 mb-0 mx-0 p-0">
                  <div className="small text-center col-12 px-1 py-0 m-0" style={{height: '54px'}}>
                    {isEdited && hasSelected
                      ? <div className="bg-danger text-light rounded p-1 my-1">Ändrar befintlig kund&nbsp;
                        {typeof orgSelected[0] !== 'undefined' ? orgSelected[0].organisation : null}
                    ({typeof orgSelected[0] !== 'undefined' ? orgSelected[0].firstname : null}
                    &nbsp;
                        {typeof orgSelected[0] !== 'undefined' ? orgSelected[0].lastname : null})
                        <br />Ändringana har inte sparats!
                      </div>
                      : null }
                    {!hasSelected
                      ? <div className="bg-success text-light rounded p-1 my-1">
                    Skapar ny gruppkund
                        { !hasSelected && isEmpty ? <span><br />Det behövs mer information innan det går att spara.</span> : null}
                      </div> : null}

                  </div>
                </div>
                <div className="row my-1 mx-0 p-0">
                  <div className="text-left col-6 pb-1 pt-2 px-1 m-0" style={{height: '50px'}}>
                    { !isEmpty
                      ? <button onClick={e => this.handleClear(e)} disabled={isSubmitting} type="button" title="Rensa formuläret" className="btn btn-warning custom-scale">
                        {isSubmitting ? <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={faSpinner} size="lg" />&nbsp;Rensa</span> : <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={faEraser} size="lg" />&nbsp;Rensa</span>}
                      </button>
                      : null }
                  </div>
                  <div className="text-right col-6 px-1 py-0 m-0" style={{height: '50px'}}>
                    {(hasSelected && isEdited) || (!hasSelected && !isEmpty)
                      ? <button onClick={e => this.handleSave(e)} disabled={isSubmitting} type="button" title="Spara gruppkund" className="btn btn-lg btn-primary custom-scale">
                        {isSubmitting ? <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={faSpinner} size="lg" />&nbsp;Spara</span> : <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={faSave} size="lg" />&nbsp;Spara</span>}
                      </button> : null }
                    { hasSelected && !isEdited
                      ? <button onClick={e => this.deleteConfirm(e)} disabled={isSubmitting} type="button" title="Ta bort gruppkunden" className="btn btn-danger custom-scale">
                        {isSubmitting ? <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={faSpinner} size="lg" />&nbsp;Radera</span> : <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={faTrash} size="lg" />&nbsp;Radera</span>}
                      </button>
                      : null }
                  </div>
                </div>
              </div>
            </div>
          </fieldset>
        </form>
        <div className="mt-3 mx-2 text-center w-100">
          <p>Det finns {groupcustomers.length} gruppkunder i systemet.</p>
          <button className="btn btn-primary m-2 mr-3" onClick={e => { e.preventDefault(); this.setState({showList: !showList}) }}>       {showList ? 'Dölj' : 'Visa'} lista</button>
        </div>
        <div className="mt-3 mx-auto w-100 text-center GruppKundLista text-center">
          {showList ? <div className="w-75 mx-auto my-5 text-center" style={{maxWidth: '850px'}}><h5>Filtrera på kategorier</h5>
            <Typeahead className="rounded w-100 d-inline-block m-0"
              id="groupFilterCategories"
              name="groupFilterCategories"
              minLength={0}
              maxResults={30}
              flip
              multiple
              emptyLabel=""
              onChange={(catFilterSelected) => { this.setState({ catFilterSelected: catFilterSelected }) }}
              labelKey="label"
              filterBy={['label']}
              options={activecategories}
              selected={catFilterSelected}
              placeholder="Kategorier"
              // eslint-disable-next-line no-return-assign
              ref={(ref) => this._FilterCategory = ref}
            />
          </div> : null}
          {showList ? <table className="mx-auto table table-sm table-hover" style={{width: '85%'}}>
            <thead>
              <tr>
                <th className="nowrap" scope="col" onClick={() => this.sortBy('organisation')}>Organisation {sortBy === 'organisation' ? <FontAwesomeIcon icon={sortIsDown ? faSortDown : faSortUp} size="sm" /> : null}</th>
                <th className="nowrap" scope="col" onClick={() => this.sortBy('lastname')}>Kontaktperson {sortBy === 'lastname' ? <FontAwesomeIcon icon={sortIsDown ? faSortDown : faSortUp} size="sm" /> : null}</th>
                <th className="nowrap" scope="col" onClick={() => this.sortBy('street')}>Adress {sortBy === 'street' ? <FontAwesomeIcon icon={sortIsDown ? faSortDown : faSortUp} size="sm" /> : null}</th>
                <th className="nowrap" scope="col" onClick={() => this.sortBy('zip')}>Postnr {sortBy === 'zip' ? <FontAwesomeIcon icon={sortIsDown ? faSortDown : faSortUp} size="sm" /> : null}</th>
                <th className="nowrap" scope="col" onClick={() => this.sortBy('city')}>Stad {sortBy === 'city' ? <FontAwesomeIcon icon={sortIsDown ? faSortDown : faSortUp} size="sm" /> : null}</th>
                <th className="nowrap" scope="col" onClick={() => this.sortBy('phone')}>Telefonnr {sortBy === 'phone' ? <FontAwesomeIcon icon={sortIsDown ? faSortDown : faSortUp} size="sm" /> : null}</th>
                <th className="nowrap" scope="col" onClick={() => this.sortBy('email')}>E-post {sortBy === 'email' ? <FontAwesomeIcon icon={sortIsDown ? faSortDown : faSortUp} size="sm" /> : null}</th>
                <th className="nowrap" scope="col" onClick={() => this.sortBy('date')}>Senast {sortBy === 'date' ? <FontAwesomeIcon icon={sortIsDown ? faSortDown : faSortUp} size="sm" /> : null}</th>
                <th className="nowrap small" scope="col">Sätt idag</th>
                <th className="nowrap small" scope="col">Radera</th>
              </tr>
            </thead>
            <tbody>
              {orgRowsFiltered}
            </tbody>
          </table> : null}
        </div>

      </div>
    )
  }
}

GroupList.propTypes = {
  getItem       : PropTypes.func,
  putItem       : PropTypes.func,
  postItem      : PropTypes.func,
  deleteItem    : PropTypes.func,
  groupcustomers: PropTypes.array,
  categories    : PropTypes.array,
  history       : PropTypes.object
}

const mapStateToProps = state => ({
  groupcustomers: state.lists.groupcustomers,
  categories    : state.tours.categories
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem,
  putItem,
  postItem,
  deleteItem
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(GroupList)
