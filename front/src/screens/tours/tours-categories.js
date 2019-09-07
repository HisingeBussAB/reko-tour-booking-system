import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import {faPlus, faArrowLeft, faFilter} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import {getItem, getItemWeb} from '../../actions'
import CategoriesRow from '../../components/tours/category-row'
import update from 'immutability-helper'
import { findByKey, dynamicSort } from '../../utils'

class Categories extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isSubmitting   : false,
      extracategories: [],
      showOnlyActive : false
    }
  }

  componentWillMount () {
    this.reduxGetAllUpdate()
  }

  componentWillUnmount () {
    this.reduxGetAllUpdate()
  }

  componentDidMount () {
    const {...props} = this.props
    this.Initiate(props)
  }

  componentWillReceiveProps (nextProps) {
    const {webcategories, categories} = this.props
    if (webcategories !== nextProps.webcategories || categories !== nextProps.categories) {
      this.Initiate(nextProps)
    }
  }

  Initiate = (nextProps) => {
    try {
      const {extracategories = []} = this.state
      const allcategories = nextProps.categories.concat(extracategories)
      const webcategoriesFiltered = nextProps.webcategories
        .filter(cat => { return typeof findByKey(cat.kategori, 'label', allcategories) === 'undefined' })
        .map(cat => { return {id: 'new', label: cat.kategori, isdisabled: false, forceSave: true, title: 'Förslag hämtat från hemsidan'} })
      const newextracategories = update(extracategories, {$push: webcategoriesFiltered})
      this.setState({extracategories: newextracategories})
    } catch (e) {
      // something was undefined we want no changes to happen. ignore
    }
  }

  reduxGetAllUpdate = () => {
    const {getItem, getItemWeb} = this.props
    getItem('categories', 'all')
    getItemWeb('kategorier')
  }

  addRow = () => {
    const {extracategories = []} = this.state
    const newcategory = {
      id        : 'new',
      label     : '',
      isdisabled: false,
      title     : '',
      forceSave : false
    }
    const newextracategories = update(extracategories, {$push: [newcategory]})

    this.setState({extracategories: newextracategories})
  }

  removeExtraCategory = (i) => {
    const {extracategories} = this.state
    delete extracategories[i]  //Statemutation!
  }

  submitToggle = (b) => {
    const validatedb = !!b
    this.setState({isSubmitting: validatedb})
  }

  toggleShowOnlyActive = (e) => {
    const {showOnlyActive = true} = this.state
    this.setState({showOnlyActive: !showOnlyActive})
  }

  render () {
    const {categories: categoriesUnsorted = [], history, webcategories: webcategoriesUnsorted = []} = this.props
    const {extracategories = [], isSubmitting = false, showOnlyActive = false} = this.state
    const categories = [...categoriesUnsorted]
    const webcategories = [...webcategoriesUnsorted]
    categories.sort(dynamicSort('label'))
    webcategories.sort(dynamicSort('kategori'))
    let categoryRows
    try {
      categoryRows = categories.filter(category => { return !(showOnlyActive && category.isdisabled) }).map((category) => {
        return <CategoriesRow key={category.id} isNew={false} id={category.id} category={category.label} isDisabled={category.isdisabled} submitToggle={this.submitToggle} />
      })
    } catch (e) {
      categoryRows = null
    }

    extracategories.forEach((item, i) => {
      categoryRows.push(<CategoriesRow key={'new' + i} isNew index={i} id={item.id} remove={this.removeExtraCategory} title={item.title} forceSave={item.forceSave} category={item.label} isDisabled={item.isdisabled} submitToggle={this.submitToggle} />)
    })

    return (
      <div className="TourView Categories">

        <form autoComplete="off">
          <button onClick={() => { history.goBack() }} disabled={isSubmitting} type="button" title="Tillbaka till meny" className="mr-4 btn btn-primary btn-sm custom-scale position-absolute" style={{right: 0}}>
            <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={faArrowLeft} size="1x" />&nbsp;Meny</span>
          </button>
          <fieldset disabled={isSubmitting}>
            <div className="container text-left" style={{maxWidth: '850px'}}>

              <h3 className="my-4 w-50 mx-auto text-center">Resekategorier</h3>
              <table className="table table-hover w-100">
                <thead>
                  <tr>
                    <th span="col" className="pr-3 py-2 text-center w-50">Kategori</th>
                    <th span="col" className="px-3 py-2 text-center">Spara</th>
                    <th span="col" className="px-3 py-2 text-center">Aktiv
                      {!showOnlyActive &&
                      <span title="Dölj inaktiva kategorier" className="seconday-color custom-scale cursor-pointer"><FontAwesomeIcon icon={faFilter} onClick={(e) => this.toggleShowOnlyActive(e)} size="lg" /></span> }
                      {showOnlyActive &&
                      <span title="Visa inaktiva kategorier" className="primary-color custom-scale  cursor-pointer"><FontAwesomeIcon icon={faFilter} onClick={(e) => this.toggleShowOnlyActive(e)} size="lg" /></span> }
                    </th>
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
  getItem      : PropTypes.func,
  categories   : PropTypes.array,
  history      : PropTypes.object,
  getItemWeb   : PropTypes.func,
  webcategories: PropTypes.array
}

const mapStateToProps = state => ({
  categories   : state.tours.categories,
  webcategories: state.web.webcategories
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem,
  getItemWeb
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(Categories)
