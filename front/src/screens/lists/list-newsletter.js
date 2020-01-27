import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import {faSave, faSpinner, faTrash, faArrowLeft} from '@fortawesome/free-solid-svg-icons'
import { Typeahead } from 'react-bootstrap-typeahead'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import {getItem, postItem, deleteItem} from '../../actions'
import { dynamicSort } from '../../utils'

class NewsletterList extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isSubmitting   : false,
      emailSelected  : [],
      validEmailEntry: true,
      existEmailEntry: true,
      showList       : false,
      showSepList    : false,
      wasDeleted     : false,
      wasSaved       : false
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
    getItem('newsletter', 'all')
  }

  handleEmailSave = async (e) => {
    e.preventDefault()
    const valid = /\S+@\S+\.\S+/.test(this._typeahead.getInstance().getInput().value)
    this.setState({wasDeleted: false, existEmailEntry: true, validEmailEntry: true, wasSaved: false})
    if (valid) {
      this.setState({isSubmitting: true})
      const {postItem} = this.props
      const data = {
        email: this._typeahead.getInstance().getInput().value
      }
      if (await postItem('newsletter', data)) {
        this.setState({emailSelected: [], wasSaved: data.email})
        this._typeahead.getInstance().clear()
      }
      this.setState({isSubmitting: false})
    } else {
      this.setState({validEmailEntry: false})
    }
  }

  doDelete = async (e) => {
    e.preventDefault()
    const { deleteItem, newsletter } = this.props
    const emailin = this._typeahead.getInstance().getInput().value
    const idArr = newsletter.filter(email => emailin === email.email)
    const id = idArr.length === 1 ? idArr[0].id : false
    this.setState({wasDeleted: false, existEmailEntry: true, validEmailEntry: true, wasSaved: false})
    if (id !== false) {
      this.setState({isSubmitting: true})
      if (await deleteItem('newsletter', id)) {
        this.setState({emailSelected: [], wasDeleted: emailin})
        this._typeahead.getInstance().clear()
      }
      this.setState({isSubmitting: false})
    } else {
      this.setState({existEmailEntry: false})
    }
  }

  render () {
    const {newsletter, history} = this.props
    const {isSubmitting, emailSelected, validEmailEntry, showList, wasDeleted, existEmailEntry, wasSaved, showSepList} = this.state
    const newsletterSorted = [...newsletter]
    newsletterSorted.sort(dynamicSort('email'))
    const EmailList = newsletterSorted.map(nl => { return <tr key={nl.id}><td>{nl.email}</td></tr> })
    const EmailSepList = newsletterSorted.map(nl => { return nl.email + '; ' })
    return (
      <div className="ListView NewsletterList">

        <form autoComplete="off">
          <button onClick={() => { history.goBack() }} disabled={isSubmitting} type="button" title="Tillbaka till meny" className="mr-4 btn btn-primary btn-sm custom-scale position-absolute" style={{right: 0}}>
            <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={faArrowLeft} size="1x" />&nbsp;Meny</span>
          </button>
          <fieldset disabled={isSubmitting}>
            <div className="container text-left" style={{maxWidth: '850px'}}>

              <h3 className="my-4 w-50 mx-auto text-center">Nyhetsbrev</h3>
              <h6 className="m-3 p-2 text-center">Sök efter eller lägg till e-post i nyhetsbrevslistan</h6>
              <div>
                <Typeahead className="rounded w-50 m-2 d-inline-block"
                  id="email"
                  name="email"
                  minLength={2}
                  maxResults={5}
                  flip
                  emptyLabel=""
                  disabled={isSubmitting}
                  onChange={(emailSelected) => { this.setState({ emailSelected: emailSelected, validEmailEntry: true, wasSaved: false, existEmailEntry: true }) }}
                  options={newsletterSorted.map(nl => { return nl.email })}
                  selected={emailSelected}
                  placeholder="Skriv en e-postadress..."
                  // eslint-disable-next-line no-return-assign
                  ref={(ref) => this._typeahead = ref}
                />
                <button onClick={(e) => this.handleEmailSave(e)} disabled={isSubmitting} type="button" title="Spara e-postaddressen." className="btn btn-primary custom-scale rounded mx-3 my-2">
                  <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={isSubmitting ? faSpinner : faSave} size="lg" />&nbsp;Spara</span>
                </button>
                <button onClick={(e) => this.doDelete(e)} disabled={isSubmitting} type="button" title="Ta bort e-postaddressen." className="btn btn-danger custom-scale rounded mx-3 my-2">
                  <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={isSubmitting ? faSpinner : faTrash} size="lg" />&nbsp;Ta bort</span>
                </button>
                {!validEmailEntry && <div className="w-100 m-1 d-block text-danger text-center">Du måste ange en giltig e-postadress.</div>}
                {!existEmailEntry && <div className="w-100 m-1 d-block text-danger text-center">E-postadressen verkar inte finnas i registret.</div>}
                {!!wasDeleted && <div className="w-100 m-1 d-block text-info text-center">E-postadressen {wasDeleted} har tagits bort!</div>}
                {!!wasSaved && <div className="w-100 m-1 d-block text-success text-center">E-postadressen {wasSaved} har sparats!</div>}
              </div>
              <div className="mt-5 text-center">
                <p>Det finns {newsletter.length} adresser i systemet.</p>
                <button className="btn btn-primary btn-sm m-2 mr-3" onClick={e => { e.preventDefault(); this.setState({showList: !showList}) }}>       {showList ? 'Dölj' : 'Visa'} lista</button>
                <button className="btn btn-primary btn-sm m-2 ml-3" onClick={e => { e.preventDefault(); this.setState({showSepList: !showSepList}) }}>{showSepList ? 'Dölj' : 'Visa'} separerad lista</button>
              </div>
              <div className="mt-3 EpostLista1">
                {showList ? <table>
                  <tbody>
                    {EmailList}
                  </tbody>
                </table> : null}
              </div>
              <div className="mt-3 EpostLista2">
                {showSepList ? <p>
                  {EmailSepList}
                </p> : null}
              </div>
            </div>
          </fieldset>
        </form>
      </div>
    )
  }
}

NewsletterList.propTypes = {
  getItem   : PropTypes.func,
  postItem  : PropTypes.func,
  deleteItem: PropTypes.func,
  newsletter: PropTypes.array,
  history   : PropTypes.object
}

const mapStateToProps = state => ({
  newsletter: state.lists.newsletter
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem,
  postItem,
  deleteItem
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(NewsletterList)
