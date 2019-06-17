import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import {faPlus} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import {getItem} from '../../actions'
import CategoriesRow from '../../components/tours/category-row'
import update from 'immutability-helper'

class Categories extends Component {
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
    getItem('categories', 'all')
  }

  addRow = () => {
    const {extracategories = []} = this.state
    const newcategory = {
      id        : 'new',
      category  : '',
      isdisabled: false
    }
    const newextracategories = update(extracategories, {$push: [newcategory]})

    this.setState({extracategories: newextracategories})
  }

  removeExtraCategory = (i) => {
    const {extracategories} = this.state
    delete extracategories[i]
  }

  submitToggle = (b) => {
    const validatedb = !!b
    this.setState({isSubmitting: validatedb})
  }

  render () {
    const {categories = []} = this.props
    const {extracategories = [], isSubmitting = false} = this.state

    let categoryRows
    try {
      categoryRows = categories.map((category) => {
        return <CategoriesRow key={category.id} isNew={false} id={category.id} category={category.label} isDisabled={category.isdisabled} submitToggle={this.submitToggle} />
      })
    } catch (e) {
      categoryRows = null
    }
    extracategories.forEach((item, i) => {
      categoryRows.push(<CategoriesRow key={('new' + i)} isNew index={i} id={item.id} remove={this.removeExtraCategory} category={item.label} isDisabled={item.isdisabled} submitToggle={this.submitToggle} />)
    })

    return (
      <div className="TourView Categories">

        <form>
          <fieldset disabled={isSubmitting}>
            <div className="container text-left" style={{maxWidth: '850px'}}>
              <h3 className="my-4 w-50 mx-auto text-center">Resekategorier</h3>
              <table className="table table-hover w-100">
                <thead>
                  <tr>
                    <th span="col" className="pr-3 py-2 text-center w-50">Kategori</th>
                    <th span="col" className="px-3 py-2 text-center">Spara</th>
                    <th span="col" className="px-3 py-2 text-center">Aktiv</th>
                    <th span="col" className="pl-3 py-2 text-center">Ta bort</th>
                  </tr>
                </thead>
                <tbody>
                  {categoryRows}
                  <tr>
                    <td colSpan="4" className="py-2">
                      <button onClick={this.addRow} disabled={isSubmitting} type="button" title="Lägg till flera kategorier" className="btn btn-primary custom-scale">
                        <span className="mt-1"><FontAwesomeIcon icon={faPlus} size="lg" /></span>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </fieldset>
        </form>
      </div>
    )
  }
}

Categories.propTypes = {
  getItem   : PropTypes.func,
  categories: PropTypes.array
}

const mapStateToProps = state => ({
  categories: state.tours.categories
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(Categories)
