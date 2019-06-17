import React, { Component } from 'react'
import { connect } from 'react-redux'
import {getItem, postItem, putItem} from '../../actions'
import { bindActionCreators } from 'redux'
import PropTypes from 'prop-types'
import { Link } from 'react-router-dom'
import {faSave, faSpinner, faTrash} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import moment from 'moment'
import 'moment/locale/sv'

class PendingNewsletter extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isSubmitting : false,
      showExtended : false,
      showProcessed: false
    }
  }
  componentWillMount () {
    this.reduxGetAllUpdate()
  }

  reduxGetAllUpdate = () => {
    const {getItem} = this.props
    getItem('pendingcount', 'all')
    getItem('pendingnewsletter', 'all')
    getItem('newsletter', 'all')
  }

  handleIgnore = async (id) => {
    const {putItem} = this.props
    this.setState({isSubmitting: true})
    await putItem('pendingnewsletter', id, {})
    this.reduxGetAllUpdate()
    this.setState({isSubmitting: false})
  }

  handleSave = async (id) => {
    this.setState({isSubmitting: true})
    const {putItem, postItem, pendingfromweb: {newsletter = []}} = this.props
    const postEmail = newsletter.find(email => {
      return email.id === id
    })
    const data = {
      email: postEmail.email
    }
    if (await postItem('newsletter', data)) {
      await putItem('pendingnewsletter', id, {})
      this.reduxGetAllUpdate()
    } else {
      getItem('newsletter', 'all')
    }
    this.setState({isSubmitting: false})
  }

  render () {
    const {pendingfromweb: {newsletter = []}, existingEmails = []} = this.props
    const {isSubmitting, showExtended, showProcessed} = this.state
    const processed = newsletter.filter(row => { return Number(row.processed) === 1 }).map(row => {
      const found = existingEmails.includes(row.email)
      return <tr key={row.id} className={'table-warning'}>
        <td className="mr-4">{row.email}</td>
        <td className={showExtended ? 'text-center' : 'd-none'}>{moment(row.arrived).format('YY-MM-DD')}</td>
        <td className={showExtended ? 'text-center' : 'd-none'}>{row.ip}</td>
        <td className="text-center">{Number(row.processed) === 0 ? 'Nej' : 'Ja'}</td>
        <td className="text-center mr-3" style={{width: '130px'}}>{found ? <button className="w-100 btn btn-primary btn-sm text-uppercase" disabled>Sparad</button> : <button className=" w-100 btn btn-warning btn-sm text-uppercase" disabled>Ignorerad</button>}</td>
        <td className="text-center" style={{width: '130px'}}>{found ? <button className="w-100 btn btn-primary btn-sm text-uppercase" disabled>Sparad</button>
          : <button onClick={() => this.handleSave(row.id)} disabled={isSubmitting} type="button" title="Spara e-postaddressen." className=" w-100 btn btn-primary btn-sm custom-scale rounded">
            <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={isSubmitting ? faSpinner : faSave} size="1x" />&nbsp;Spara</span>
          </button>}
        </td>
      </tr>
    })
    const unprocessed = newsletter.filter(row => { return Number(row.processed) === 0 }).map(row => {
      const found = existingEmails.includes(row.email)
      return <tr key={row.id} className={found ? 'table-danger' : ''}>
        <td className="mr-4">{row.email}</td>
        <td className={showExtended ? 'text-center' : 'd-none'}>{moment(row.arrived).format('YY-MM-DD')}</td>
        <td className={showExtended ? 'text-center' : 'd-none'}>{row.ip}</td>
        <td className="text-center">{Number(row.processed) === 0 ? 'Nej' : 'Ja'}</td>
        <td className="text-center mr-3" style={{width: '130px'}}>{found ? <button onClick={() => this.handleIgnore(row.id)} disabled={isSubmitting} type="button" title="Dölj dubletten." className=" w-100 btn btn-danger btn-sm custom-scale rounded">
          <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={isSubmitting ? faSpinner : faTrash} size="1x" />&nbsp;Dölj</span>
        </button>
          : <button onClick={() => this.handleIgnore(row.id)} disabled={isSubmitting} type="button" title="Dölj utan att spara e-postaddressen." className=" w-100 btn btn-danger btn-sm custom-scale rounded">
            <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={isSubmitting ? faSpinner : faTrash} size="1x" />&nbsp;Ignorera</span>
          </button>}
        </td>
        <td className="text-center" style={{width: '130px'}}>{found ? <button className="btn btn-danger btn-sm text-uppercase w-100" disabled>Dublett</button>
          : <button onClick={() => this.handleSave(row.id)} disabled={isSubmitting} type="button" title="Spara e-postaddressen." className=" w-100 btn btn-primary btn-sm custom-scale rounded">
            <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={isSubmitting ? faSpinner : faSave} size="1x" />&nbsp;Spara</span>
          </button>}
        </td>
      </tr>
    })

    return (
      <div className="PendingNewsletter">
        <div className="container text-left" style={{maxWidth: '850px'}}>
          <h3 className="mt-3 mb-2 w-100 mx-auto text-center">Nyhetsbrevsprenumerationer</h3>
          <h4 className="mb-3 w-100 mx-auto text-center">från hemsidan</h4>
          <div className="text-center mb-3">
            <Link className="btn btn-primary btn-sm m-3 mr-5 rounded" to={'/utskick/nyhetsbrev'}>Till nyhetsbrevshantering</Link>
            <button className="btn btn-primary btn-sm m-3 ml-4 rounded" onClick={() => { this.setState({showExtended: !showExtended}) }}>{showExtended ? 'Dölj' : 'Visa'} detaljer</button>
            <button className="btn btn-primary btn-sm m-3 rounded" onClick={() => { this.setState({showProcessed: !showProcessed}) }}>{showProcessed ? 'Dölj' : 'Visa'} sparade</button>
          </div>
          <div>
            <form>
              <fieldset disabled={isSubmitting}>
                <table className="table table-hover w-100">
                  <thead>
                    <tr>
                      <th scope="col">E-post</th>
                      <th className={showExtended ? 'text-center' : 'd-none'} scope="col">Ankom</th>
                      <th className={showExtended ? 'text-center' : 'd-none'} scope="col">IP</th>
                      <th className="text-center" scope="col">Hanterad</th>
                      <th className="text-center mr-3" style={{width: '130px'}} scope="col">Ignorera</th>
                      <th className="text-center" style={{width: '130px'}} scope="col">Spara</th>
                    </tr>
                  </thead>
                  <tbody>
                    {unprocessed}
                    {showProcessed ? processed : null}
                  </tbody>
                </table>
                {showProcessed ? <footer className="text-center small">Sparade/Ignorerade försvinner efter fem veckor.</footer> : null}
              </fieldset>
            </form>
          </div>  
        </div>
      </div>
    )
  }
}

PendingNewsletter.propTypes = {
  getItem       : PropTypes.func,
  postItem      : PropTypes.func,
  putItem       : PropTypes.func,
  pendingfromweb: PropTypes.object,
  existingEmails: PropTypes.array
}

const mapStateToProps = state => ({
  pendingfromweb: state.pendingfromweb,
  existingEmails: state.lists.newsletter.map(email => { return email.email })
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem,
  postItem,
  putItem
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(PendingNewsletter)
