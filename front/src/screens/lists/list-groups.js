import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import {faPlus} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import {getItem} from '../../actions'
import update from 'immutability-helper'

class GroupList extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isSubmitting   : false,
      extracategories: []
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
    const {isSubmitting} = this.state
    return (
      <div className="ListView GroupList">

        <form>
          <fieldset disabled={isSubmitting}>
      test
          </fieldset>
        </form>
      </div>
    )
  }
}

GroupList.propTypes = {
  getItem   : PropTypes.func,
  groupcustomers: PropTypes.array
}

const mapStateToProps = state => ({
  groupcustomers: state.lists.groupcustomers
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(GroupList)
