import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import {faSave, faSpinner, faPlus} from '@fortawesome/free-solid-svg-icons'
import { Typeahead } from 'react-bootstrap-typeahead'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import {getItem, postItem} from '../../actions'

class NewsletterList extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isSubmitting   : false,
      emailInput     : '',
      emailSelected  : [],
      validEmailEntry: true,
      showList       : false
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
    getItem('newsletter', 'all')
  }

  submitToggle = (b) => {
    const validatedb = !!b
    this.setState({isSubmitting: validatedb})
  }

  handleEmailSave = async (e) => {
    e.preventDefault()
    const valid = /\S+@\S+\.\S+/.test(this._typeahead.getInstance().getInput().value)
    if (valid) {
      this.setState({isSubmitting: true})
      const {postItem} = this.props
      const data = {
        email: this._typeahead.getInstance().getInput().value
      }
      if (await postItem('newsletter', data)) {
        this.setState({emailSelected: []})
        this._typeahead.getInstance().clear()
      }
      this.setState({isSubmitting: false})
    } else {
      this.setState({validEmailEntry: false})
    }
  }

  render () {
    const {newsletter} = this.props
    const {isSubmitting, emailSelected, validEmailEntry, showList} = this.state

    const EmailList = newsletter.map(nl => { return <tr><td>{nl.email}</td></tr> })

    return (
      <div className="ListView NewsletterList">

        <form>
          <fieldset disabled={isSubmitting}>
            <div className="container text-left" style={{maxWidth: '650px'}}>
              <h3 className="my-4 w-50 mx-auto text-center">Nyhetsbrev</h3>
              <h6 className="m-3 p-2 text-center">Sök efter eller lägg till e-post i nyhetsbrevslistan</h6>
              <div>
                <Typeahead className="rounded w-75 m-2 d-inline-block"
                  id="email"
                  name="email"
                  minLength={2}
                  maxResults={5}
                  flip
                  emptyLabel
                  disabled={isSubmitting}
                  onChange={(emailSelected) => { this.setState({ emailSelected: emailSelected, validEmailEntry: true }) }}
                  options={newsletter.map(nl => { return nl.email })}
                  selected={emailSelected}
                  placeholder="Skriv en e-postadress..."
                  // eslint-disable-next-line no-return-assign
                  ref={(ref) => this._typeahead = ref}
                />
                <button onClick={(e) => this.handleEmailSave(e)} disabled={isSubmitting} type="button" title="Spara e-postaddressen." className="btn btn-primary custom-scale rounded mx-3 my-2">
                  <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={faSave} size="lg" />&nbsp;Spara</span>
                </button>
                {!validEmailEntry && <div className="w-100 m-1 d-block text-danger text-center">Du måste ange en giltig e-postadress.</div>}
              </div>
              <div className="mt-5 text-center">
                <p>Det finns {newsletter.length} adresser i systemet.</p>
                <button className="btn btn-primary btn-sm" onClick={e => { e.preventDefault(); this.setState({showList: !showList}) }}>{showList ? 'Dölj' : 'Visa'} lista</button>
              </div>
              <div className="mt-3 EpostLista">
                {showList ? <table>
                  <tbody>
                    {EmailList}
                  </tbody>
                </table> : null}
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
  newsletter: PropTypes.array
}

const mapStateToProps = state => ({
  newsletter: state.lists.newsletter
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem,
  postItem
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(NewsletterList)
