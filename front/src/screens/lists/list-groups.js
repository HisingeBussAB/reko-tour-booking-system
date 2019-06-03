import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import {getItem} from '../../actions'
import { Typeahead } from 'react-bootstrap-typeahead'

class GroupList extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isSubmitting: false
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
  }

  submitToggle = (b) => {
    const validatedb = !!b
    this.setState({isSubmitting: validatedb})
  }

  render () {
    const { isSubmitting } = this.state
    const { groupcustomers } = this.props
    return (
      <div className="ListView GroupList">

        <form>
          <fieldset disabled={isSubmitting}>
            <div className="container text-left" style={{maxWidth: '650px'}}>
              <h3 className="my-4 w-50 mx-auto text-center">Gruppkunder</h3>
              <h6 className="m-3 p-2 text-center">Sök efter eller lägg till gruppkunder</h6>
              <div>
                <Typeahead className="rounded w-50 m-2 d-inline-block" /*
                  id="email"
                  name="email"
                  minLength={2}
                  maxResults={5}
                  flip
                  emptyLabel=""
                  disabled={isSubmitting}
                  onChange={(emailSelected) => { this.setState({ emailSelected: emailSelected, validEmailEntry: true, wasSaved: false, existEmailEntry: true }) }}
                  options={newsletter.map(nl => { return nl.email })}
                  selected={emailSelected}
                  placeholder="Skriv en e-postadress..."
                  // eslint-disable-next-line no-return-assign
                  ref={(ref) => this._typeahead = ref} */
                />

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
  groupcustomers: PropTypes.array
}

const mapStateToProps = state => ({
  groupcustomers: state.lists.groupcustomers
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(GroupList)
