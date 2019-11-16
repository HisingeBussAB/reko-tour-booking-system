import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import {faPlus, faSave, faMinus, faSpinner, faArrowLeft, faTrash, faFileImport} from '@fortawesome/free-solid-svg-icons'
import {faCaretSquareDown, faCaretSquareUp} from '@fortawesome/free-regular-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import PropTypes from 'prop-types'
import {getItem, putItem, postItem, deleteItem} from '../../actions'
import { Typeahead } from 'react-bootstrap-typeahead'
import update from 'immutability-helper'
import { findByKey, sumBy } from '../../utils'
import { Redirect } from 'react-router-dom'
import ConfirmPopup from '../../components/global/confirm-popup'
import moment from 'moment'
import _ from 'lodash'
import 'moment/locale/sv'

class NewBudget extends Component {
  constructor (props) {
    super(props)
    this.state = {
      isSubmitting    : false,
      id              : 'new',
      budgetgroupid   : null,
      label           : '',
      tourlabelcalc   : '',
      tourid          : null,
      estimatedpax    : '',
      actualpax       : '',
      estimatedsurplus: '',
      createddate     : moment().format('YYYY-MM-DD'),
      departuredate   : moment().format('YYYY-MM-DD'),
      sortdatecalc    : moment().format('YYYY-MM-DD'),
      isdisabled      : false,
      costs           : [
        {id             : 'new',
          budgetid       : null,
          label          : 'Buss inkl. moms',
          estimatedamount: '',
          actualamount   : '',
          isfixed        : true
        },
        {id             : 'new',
          budgetid       : null,
          label          : '',
          estimatedamount: '',
          actualamount   : '',
          isfixed        : false
        }],
      sales: [
        {id      : 'new',
          budgetid: null,
          label   : '',
          price   : '',
          amount  : 1}],

      redirectTo  : false,
      isConfirming: false,
      showSales   : false
    }
  }

  componentWillMount () {
    const {getItem} = this.props
    getItem('budgets', 'all')
    getItem('budgetgroups', 'all')
    getItem('bookings', 'all')
    getItem('tours', 'all')
  }

  componentDidMount () {
    const {...props} = this.props
    this.Initiate(props)
  }

  componentWillReceiveProps (nextProps) {
    const {budgets} = this.props
    if (!_.isEqual(budgets, nextProps.budgets)) {
      this.Initiate(nextProps)
    }
  }

  Initiate = (nextProps) => {
    try {
      const budgets = findByKey(nextProps.match.params.id, 'id', nextProps.budgets)
      if (typeof budgets !== 'undefined') {
        this.setState({
          isSubmitting  : false,
          id            : budgets.id,
          budgetgroupid : budgets.budgetgroupid,
          label         : budgets.label,
          tourlabelcalc : budgets.tourlabelcalc,
          tourid        : budgets.tourid,
          estimatedpax  : budgets.extimatedpax,
          actualpax     : budgets.actualpax,
          estimatedprice: budgets.estimatedprice,
          createddate   : budgets.createddate,
          departuredate : budgets.departuredate,
          sortdatecalc  : budgets.sortdatecalc,
          isdisabled    : budgets.isdisabled,
          costs         : budgets.actualcosts,
          sales         : budgets.sales,
          redirectTo    : false,
          isConfirming  : false,
          showSales     : false
        })
      }
    } catch (e) {
      // To early or bad imput, do nothing use default state.
    }
  }

  handleSave = async () => {
    const {} = this.state
    const {postItem, putItem, getItem} = this.props
    this.setState({isSubmitting: true})
    this.setState({isSubmitting: false})
  }

  handleChange = (e) => {
    this.setState({ [e.name]: e.value })
  }

  handleChangeCostRow = (e, i) => {
    const {costs} = this.state
    const newcost = e.value == 0 ? update(costs, {[[i]]: {[e.name]: {$set: ''}}}) : update(costs, {[[i]]: {[e.name]: {$set: e.value}}})
    this.setState({costs: newcost})
  }

  handleChangeSalesRow = (e, i) => {
    const {sales} = this.state
    const newsale = update(sales, {[[i]]: {[e.name]: {$set: e.value}}})
    this.setState({sales: newsale})
  }

  deleteConfirm = (e) => {
    if (typeof e !== 'undefined') { e.preventDefault() }
    this.setState({isSubmitting: true, isConfirming: true})
  }

  doDelete = async (choice) => {
    const { id } = this.state
    const { deleteItem } = this.props
    this.setState({ isConfirming: false })
    if (!isNaN(id)) {
      if (choice === true) {
        if (typeof id !== 'undefined') {
          if (await deleteItem('budgets', id)) {
            this.setState({isSubmitting: false, redirectTo: '/kalkyler/'})
            return null
          }
        }
      }
    }
    this.setState({isSubmitting: false})
  }

  addCost = (isfixed) => {
    const {budgetid} = this.state
    const emptyCost = {
      id             : 'new',
      budgetid       : !isNaN(budgetid) ? budgetid : null,
      label          : '',
      estimatedamount: '',
      actualamount   : '',
      isfixed        : isfixed
    }
    const {costs} = this.state
    const newcost = update(costs, {$push: [emptyCost]})
    this.setState({costs: newcost})
  }

  removeCost = (isfixed) => {
    const {costs} = this.state
    if (costs.length > 0) {
      const dropMe = _.findLastIndex(costs, ['isfixed', isfixed])
      const newcosts = update(costs, {$splice: [[dropMe, 1]]})
      this.setState({costs: newcosts})
    }
  }

  addSales = () => {
    const {budgetid} = this.state
    const emptySale = {
      id      : 'new',
      budgetid: !isNaN(budgetid) ? budgetid : null,
      label   : '',
      price   : '',
      amount  : 1
    }
    const {sales} = this.state
    const newsale = update(sales, {$push: [emptySale]})
    this.setState({sales: newsale})
  }

  removeSales = () => {
    const {sales} = this.state
    if (sales.length > 1) {
      const newsales = update(sales, {$splice: [[sales.length - 1, 1]]})
      this.setState({sales: newsales})
    }
  }

  render () {
    const {redirectTo, isConfirming, sales, showSales, isSubmitting, label, sortdatecalc, id, departuredate, tourlabelcalc, estimatedpax, estimatedsurplus, actualpax, costs} = this.state
    const {history} = this.props

    const salesTotal = sumBy(sales.map((sale) => { return { summed: isNaN(+sale.amount * +sale.price) ? '' : +sale.amount * +sale.price } }), 'summed')

    const actualprice = isNaN(+salesTotal / +(+actualpax === 0 ? 1 : +actualpax)) ? '' : +salesTotal / +(+actualpax === 0 ? 1 : +actualpax)

    const costsIndexed = costs.map((cost, i) => { cost.actindex = i; return cost })
    const fixedCostsRaw = costsIndexed.filter((cost) => { return cost.isfixed })
    const paxCostsRaw = costsIndexed.filter((cost) => { return !cost.isfixed })

    // Total gruppkostnad
    const sumgroup = isNaN(sumBy(fixedCostsRaw, 'estimatedamount')) ? '' : sumBy(fixedCostsRaw, 'estimatedamount')
    const sumgroupactual = isNaN(sumBy(fixedCostsRaw, 'actualamount')) ? '' : sumBy(fixedCostsRaw, 'actualamount')

    // Summa för gruppkostnader per passagerare
    const sumpax = isNaN(+sumgroup / +(+estimatedpax === 0 ? 1 : estimatedpax) ) ? '' : +sumgroup / +(+estimatedpax === 0 ? 1 : estimatedpax)
    const sumpaxactual = isNaN(+sumgroupactual / +(+actualpax === 0 ? 1 : actualpax)) ? '' : +sumgroupactual / +(+actualpax === 0 ? 1 : actualpax)


    // Total kostnad per person fixed and group
    const sumtotalperpax = isNaN(sumBy(paxCostsRaw, 'estimatedamount') + sumpax) ? '' : sumBy(paxCostsRaw, 'estimatedamount') + sumpax
    const sumtotalperpaxactual = isNaN(sumBy(paxCostsRaw, 'actualamount') + sumpaxactual) ? '' : sumBy(paxCostsRaw, 'actualamount') + sumpaxactual
    
    // Ber. kundpris:
    const estimatedPrice = isNaN(+sumtotalperpax + +estimatedsurplus) ? '' : +sumtotalperpax + +estimatedsurplus
    // Ber. marginalskatt/pers:
    const marginTaxPerPax = isNaN(+estimatedPrice * 0.026) ? '' : -(+estimatedPrice * 0.026)

    // Ber. bruttoöverskott/pers:
    const estSurplusPerPax = isNaN(+estimatedsurplus + +marginTaxPerPax) ? '' : +estimatedsurplus + +marginTaxPerPax

    // Ber. intäkter totalt:
    const estTotIncome = isNaN(+estimatedPrice * +estimatedpax) ? '' : +estimatedPrice * +estimatedpax

    // Ber. kostander totalt:
    const estTotCost = isNaN((+sumtotalperpax - +marginTaxPerPax) * +estimatedpax) ? '' : (+sumtotalperpax - +marginTaxPerPax) * +estimatedpax

    // Ber. bruttoöverskott:
    const estTotalSurplus = isNaN(+estTotIncome - +estTotCost) ? '' : +estTotIncome - +estTotCost

    // % of turnover beräknad
    const estOfRevenue = isNaN(+estTotalSurplus / +estTotIncome) ? 0 : (+estTotalSurplus / +estTotIncome) * 100

    // EFTERKALKYL
    // Bruttoöverskott/pers:
    const actEstSurplusPerPax = isNaN(+actualprice - +sumtotalperpaxactual) ? '' : +actualprice - +sumtotalperpaxactual

    // Resulat marginalskatt/pers
    const actMarginTaxPerPax = isNaN(+actualprice * 0.026) ? '' : -(+actualprice * 0.026)

    // Resulat bruttoöverskott/pers
    const actSurplusPerPax = isNaN(+actEstSurplusPerPax + +actMarginTaxPerPax) ? '' : +actEstSurplusPerPax + +actMarginTaxPerPax
    
    // Resultat kostnader:
    const actTotCost = isNaN((+sumtotalperpaxactual - +actMarginTaxPerPax) * +actualpax) ? '' : (+sumtotalperpaxactual - +actMarginTaxPerPax) * +actualpax

    // Resulat bruttoöverskott
    const actTotalSurplus = isNaN(+salesTotal - +actTotCost) ? '' : +salesTotal - +actTotCost

    // % of turnover final
    const actOfRevenue = isNaN(+actTotalSurplus / +salesTotal) ? 0 : (+actTotalSurplus / +salesTotal) * 100

    const diffpax = isNaN(+actualpax - +estimatedpax) ? '' : +actualpax - +estimatedpax

    const fixedCosts = fixedCostsRaw.map((cost, i) => {
      return <tr key={'fixedCosts' + i}>
        <td className="text-left">
          <input id="costLabel" name="label" value={cost.label} onChange={(e) => this.handleChangeCostRow(e.target, cost.actindex)} className="rounded w-100 d-inline-block m-0" placeholder="Kostnad" maxLength="99" type="text" />
        </td>
        <td className="text-right">
          <input id="estimatedPrice" name="estimatedamount" value={cost.estimatedamount} onChange={(e) => this.handleChangeCostRow(e.target, cost.actindex)} className="rounded d-inline-block m-0 text-right" placeholder="0" min="-999999" max="999999" type="number" /> kr
        </td>
        <td className="text-right">
          <input id="costLabel" name="actualamount" value={cost.actualamount} onChange={(e) => this.handleChangeCostRow(e.target, cost.actindex)} className="rounded d-inline-block m-0 text-right" placeholder="0" min="-999999" max="999999" type="number" /> kr
        </td>
      </tr>
    })

    const paxCosts = paxCostsRaw.map((cost, i) => {
      return <tr key={'paxCosts' + i}>
        <td className="text-left">
          <input id="costLabel" name="label" value={cost.label} onChange={(e) => this.handleChangeCostRow(e.target, cost.actindex)} className="rounded w-100 d-inline-block m-0" placeholder="Kostnad" maxLength="99" type="text" />
        </td>
        <td className="text-right">
          <input id="estimatedPrice" name="estimatedamount" value={cost.estimatedamount} onChange={(e) => this.handleChangeCostRow(e.target, cost.actindex)} className="rounded d-inline-block m-0 text-right" placeholder="0" min="-999999" max="999999" type="number" /> kr
        </td>
        <td className="text-right">
          <input id="costLabel" name="actualamount" value={cost.actualamount} onChange={(e) => this.handleChangeCostRow(e.target, cost.actindex)} className="rounded d-inline-block m-0 text-right" placeholder="0" min="-999999" max="999999" type="number" /> kr
        </td>
      </tr>
    })

    const salesTable = sales.map((sale, i) => {
      return <tr key={'sales' + i}>
        <td className="text-left pr-4">
          <input id="saleLabel" name="label" value={sale.label} onChange={(e) => this.handleChangeSalesRow(e.target, i)} className="rounded w-100 d-inline-block m-0" placeholder="Försäljningsrad (person,rum,artikel eller summering)" maxLength="99" type="text" />
        </td>
        <td className="text-right" style={{width: '10%', minWidth: '120px'}}>
          <input id="salePrice" name="amount" value={sale.amount} onChange={(e) => this.handleChangeSalesRow(e.target, i)} className="rounded d-inline-block m-0 text-right" placeholder="0" min="-999999" max="999999" type="number" /> kr
        </td>
        <td className="text-right" style={{width: '10%', minWidth: '120px'}}>
          <input id="salePrice" name="price" value={sale.price} onChange={(e) => this.handleChangeSalesRow(e.target, i)} className="rounded d-inline-block m-0 text-right" placeholder="0" min="-999999" max="999999" type="number" /> kr
        </td>
        <td className="text-right" style={{width: '10%', minWidth: '120px'}}>
          {isNaN(+sale.amount * +sale.price) ? null : +sale.amount * +sale.price} kr
        </td>
      </tr>
    })

    if (redirectTo !== false) { return <Redirect to={redirectTo} /> }

    return (
      <div className="BudgetView NewBudget">
        {isConfirming && <ConfirmPopup doAction={this.doDelete} message={`Vill du verkligen ta bort kalkylen:\n${label} ${moment(sortdatecalc).format('D/M - Y')}.\n`} />}

        <form autoComplete="off">
          <button onClick={() => { history.goBack() }} disabled={isSubmitting} type="button" title="Tillbaka till meny" className="d-print-none mr-4 btn btn-primary btn-sm custom-scale position-absolute" style={{right: 0}}>
            <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={faArrowLeft} size="1x" />&nbsp;Meny</span>
          </button>
          <fieldset disabled={isSubmitting}>
            <div className="container">
              <div className="row">
                <h3 className="mt-4 mb-3 py-1 col-12 text-center">{id !== 'new' ? 'Ändra kalkyl: ' + label + ' ' + moment(sortdatecalc).format('D/M - Y') : 'Skapa ny resekalkyl'}</h3>
              </div>
              <div className="row d-print-none">
                <div className="text-center col-12">
                  {!isNaN(id) ? <button onClick={(e) => this.deleteConfirm(e)} disabled={isSubmitting} type="button" title="Radera kalkylen helt" className="btn btn-danger btn-sm custom-scale mr-5">
                    <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={isSubmitting ? faSpinner : faTrash} size="1x" />&nbsp;Radera</span>
                  </button> : null }
                  <button onClick={this.handleSave} disabled={isSubmitting} type="button" title="Spara kalkylen" className={'btn btn-primary btn-sm custom-scale' + (isNaN(id) ? null : ' ml-5')}>
                    <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={isSubmitting ? faSpinner : faSave} size="1x" />&nbsp;Spara</span>
                  </button>
                </div>
              </div>
              <div className="row">
                <div className="col-5">
                  <label className="small w-100 text-left d-block m-0 pt-4" htmlFor="budgetName">Kalkylens namn</label>
                  <input id="budgetName" name="label" value={label} onChange={(e) => { this.handleChange(e.target) }} className="rounded w-100 d-inline-block m-0" placeholder="Kalkylens namn" maxLength="99" type="text" required />
                </div>
                <div className="col-3">
                  <label className="small w-100 text-left d-block m-0 pt-4" htmlFor="departureDate">Resedatum</label>
                  <input id="departureDate" name="departuredate" value={departuredate} onChange={(e) => { this.handleChange(e.target) }} className="rounded w-100 d-inline-block m-0" placeholder="YYYY-MM-DD" type="date" />
                </div>
                <div className="col-4">
                  <label className="small w-100 text-left d-block m-0 pt-4" htmlFor="connectedTour">Koppla resa</label>
                  <input id="connectedTour" name="tourlabelcalc" value={tourlabelcalc} className="rounded w-100 d-inline-block m-0" placeholder="Koppla resa (inaktiverad)" maxLength="99" type="text" />
                </div>
              </div>
              <div className="row d-print-none">
                <div className="col-6 text-left">
                  <button onClick={this.handleSave} disabled={isSubmitting} type="button" title="Spara kalkylen" className={'mt-5 btn btn-primary btn custom-scale' + (isNaN(id) ? null : ' ml-5')}>
                    <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={isSubmitting ? faSpinner : faSave} size="1x" />&nbsp;Spara</span>
                  </button>
                </div>
                <div className="col-6 text-right">
                  <button disabled={isSubmitting} type="button" title={showSales ? 'Dölj försäljning' : 'Registera försäljning'} onClick={() => this.setState({showSales: !showSales})} className="mt-5 btn btn-primary btn custom-scale">
                    <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={showSales ? faCaretSquareUp : faCaretSquareDown} size="1x" />&nbsp;{showSales ? 'Dölj försäljning' : 'Registera försäljning'}</span>
                  </button>
                </div>
              </div>
              <div className={'row ' + (showSales ? null : 'd-none')}>
                <div className="col-12">
                  <div className="table-responsive">
                    <table className="table table-sm table-hover d-print-table text-left">
                      <caption style={{captionSide: 'top', fontWeight: 'normal', color: 'black', fontSize: '1.21em'}}>Försäljning</caption>
                      <thead>
                        <tr>
                          <th scope="col" className="align-middle">Beskrivning</th>
                          <th scope="col" className="text-center align-middle" style={{width: '10%', minWidth: '120px'}}>antal</th>
                          <th scope="col" className="text-center align-middle" style={{width: '10%', minWidth: '120px'}}>pris</th>
                          <th scope="col" className="text-center align-middle" style={{width: '10%', minWidth: '120px'}}>total</th>
                        </tr>
                      </thead>
                      <tbody>
                        {salesTable}
                        <tr>
                          <td className="align-middle">Total:</td>
                          <td style={{width: '10%', minWidth: '120px'}} />
                          <td style={{width: '10%', minWidth: '120px'}} />
                          <td className="text-right align-middle">{Math.round(salesTotal)} kr</td>
                        </tr>

                        <tr className="d-print-none">
                          <td className="text-left d-print-none">
                            <button onClick={this.addSales} disabled={isSubmitting} type="button" title="Lägg till rad" className="btn btn-primary custom-scale m-2">
                              <span className="mt-1"><FontAwesomeIcon icon={faPlus} /></span>
                            </button>
                            {(typeof fixedCosts === 'object') && fixedCosts.length > 0 &&
                            <button onClick={this.removeSales} disabled={isSubmitting} type="button" title="Ta bort sista raden" className="btn btn-danger custom-scale m-2">
                              <span className="mt-1"><FontAwesomeIcon icon={faMinus} size="sm" /></span>
                            </button>}
                          </td>
                          <td className="text-right d-print-none" colSpan="3">
                            <button disabled type="button" title="Importera från kopplad resa" className="btn btn-secondary custom-scale m-2">
                              <span className="mt-1 text-uppercase"><FontAwesomeIcon icon={faFileImport} size="sm" />&nbsp;Importera från kopplad resa</span>
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div className="row">
                <div className="col-12 text-left">
                  <div className="table-responsive">
                    <table className="table table-sm table-hover align-middle">
                      <caption style={{captionSide: 'top', fontWeight: 'normal', color: 'black', fontSize: '1.21em'}}>Summering</caption>
                      <thead>
                        <tr>
                          <th scope="col" className="align-middle">Förkalkyl</th>
                          <th scope="col" className="text-right align-middle">kr</th>
                          <th scope="col" className="pl-5 align-middle">Efterkalkyl</th>
                          <th scope="col" className="text-right align-middle">kr</th>
                          <th scope="col" className="text-right align-middle">+/- kr</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td className="align-middle">Prel. antal reseande:</td>
                          <td className="text-right align-middle"><input id="estimatedpax" name="estimatedpax" value={estimatedpax} onChange={(e) => this.handleChange(e.target)} className="rounded d-inline-block m-0 text-right" placeholder="0" min="-999999" max="999999" type="number" /> pers</td>
                          <td className="pl-5 align-middle">Resultat antal reseande:</td>
                          <td className="text-right align-middle"><input id="actualpax" name="actualpax" value={actualpax} onChange={(e) => this.handleChange(e.target)} className="rounded d-inline-block m-0 text-right" placeholder="0" min="-999999" max="999999" type="number" /> pers</td>
                          <td className={'text-right align-middle ' + (diffpax >= 0 ? '' : 'text-danger')}>{ isNaN(diffpax) ? null : diffpax } +/-</td>
                        </tr>
                        <tr>
                          <td colSpan="5" style={{height: '20px'}} />
                        </tr>
                        <tr>
                          <td className="align-middle">Prel. bruttoöverskott/pers:</td>
                          <td className="text-right align-middle"><input id="estimatedsurplus" name="estimatedsurplus" value={estimatedsurplus} onChange={(e) => this.handleChange(e.target)} className="rounded d-inline-block m-0 text-right" placeholder="0" min="-999999" max="999999" type="number" /> kr</td>
                          <td className="pl-5 align-middle">Bruttoöverskott/pers:</td>
                          <td className="text-right align-middle">{Math.round(actEstSurplusPerPax)} kr</td>
                          <td className="text-right align-middle">+/-</td>
                        </tr>
                        <tr>
                          <td className="align-middle">Ber. kundpris:</td>
                          <td className="text-right align-middle">{Math.round(estimatedPrice)} kr</td>
                          <td className="pl-5 align-middle">Genomsnitt kundpris:</td>
                          <td className="text-right align-middle">{Math.round(actualprice)} kr</td>
                          <td className="text-right align-middle">+/-</td>
                        </tr>
                        <tr>
                          <td className="align-middle">Ber. marginalskatt/pers:</td>
                          <td className="text-right align-middle">{Math.round(marginTaxPerPax)} kr</td>
                          <td className="pl-5 align-middle">Resulat marginalskatt/pers</td>
                          <td className="text-right align-middle">{Math.round(actMarginTaxPerPax)} kr</td>
                          <td className="text-right align-middle">+/-</td>
                        </tr>
                        <tr>
                          <td className="align-middle">Ber. bruttoöverskott/pers:</td>
                          <td className="text-right align-middle">{Math.round(estSurplusPerPax)} kr</td>
                          <td className="pl-5 align-middle">Resulat bruttoöverskott/pers:</td>
                          <td className="text-right align-middle">{Math.round(actSurplusPerPax)} kr</td>
                          <td className="text-right align-middle">+/-</td>
                        </tr>
                        <tr>
                          <td colSpan="5" style={{height: '20px'}} />
                        </tr>
                        <tr>
                          <td className="align-middle">Ber. intäkter totalt:</td>
                          <td className="text-right align-middle">{Math.round(estTotIncome)} kr</td>
                          <td className="pl-5 align-middle">Resultat intäkter:</td>
                          <td className="text-right align-middle">{Math.round(salesTotal)} kr</td>
                          <td className="text-right align-middle">+/-</td>
                        </tr>
                        <tr>
                          <td className="align-middle">Ber. kostander totalt:</td>
                          <td className="text-right align-middle">{Math.round(estTotCost)} kr</td>
                          <td className="pl-5 align-middle">Resultat kostnader:</td>
                          <td className="text-right align-middle">{Math.round(actTotCost)} kr</td>
                          <td className="text-right align-middle">+/-</td>
                        </tr>
                        <tr>
                          <td className="align-middle">Ber. bruttoöverskott:</td>
                          <td className="text-right align-middle">{Math.round(estTotalSurplus)} kr</td>
                          <td className="pl-5 align-middle font-weight-bolder">Resulat bruttoöverskott</td>
                          <td className="text-right align-middle font-weight-bolder">{Math.round(actTotalSurplus)} kr</td>
                          <td className="text-right align-middle font-weight-bolder">+/-</td>
                        </tr>
                        <tr>
                          <td className="align-middle">Ber. % av omsättningen</td>
                          <td className="text-right align-middle">{Number(estOfRevenue).toFixed(1)} %</td>
                          <td className="pl-5 align-middle font-weight-bolder">% av omsättningen</td>
                          <td className="text-right align-middle font-weight-bolder">{Number(actOfRevenue).toFixed(1)} %</td>
                          <td className="text-right align-middle font-weight-bolder">+/-</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div className="row">
                <div className="col-12">
                  <div className="table-responsive">
                    <table className="table table-sm table-hover d-print-table text-left">
                      <caption style={{captionSide: 'top', fontWeight: 'normal', color: 'black', fontSize: '1.21em'}}>Fasta kostander</caption>
                      <thead>
                        <tr>
                          <th scope="col" className="align-middle">Beskrivning</th>
                          <th scope="col" className="text-right align-middle">bedömt kr</th>
                          <th scope="col" className="text-right align-middle">resultat kr</th>
                        </tr>
                      </thead>
                      <tbody>
                        {fixedCosts}
                        <tr>
                          <td className="align-middle">Summa gruppkostnad:</td>
                          <td className="text-right align-middle">{Math.round(sumgroup)} kr</td>
                          <td className="text-right align-middle">{Math.round(sumgroupactual)} kr</td>
                        </tr>

                        <tr className="d-print-none">
                          <td className="text-left d-print-none" colSpan="3">
                            <button onClick={() => this.addCost(true)} disabled={isSubmitting} type="button" title="Lägg till rad" className="btn btn-primary custom-scale m-2">
                              <span className="mt-1"><FontAwesomeIcon icon={faPlus} /></span>
                            </button>
                            {(typeof fixedCosts === 'object') && fixedCosts.length > 0 &&
                            <button onClick={() => this.removeCost(true)} disabled={isSubmitting} type="button" title="Ta bort sista raden" className="btn btn-danger custom-scale m-2">
                              <span className="mt-1"><FontAwesomeIcon icon={faMinus} size="sm" /></span>
                            </button>}
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <table className="table table-sm table-hover d-print-table text-left">
                      <caption style={{captionSide: 'top', fontWeight: 'normal', color: 'black', fontSize: '1.21em'}}>Resenärbundna kostnader</caption>
                      <thead>
                        <tr>
                          <th scope="col" className="align-middle">Kostnad beskrivning</th>
                          <th scope="col" className="text-right align-middle">bedömt kr/pers</th>
                          <th scope="col" className="text-right align-middle">resultat kr/pers</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td className="align-middle">Gruppkostnad/person:</td>
                          <td className="text-right align-middle">{Math.round(sumpax)} kr</td>
                          <td className="text-right align-middle">{Math.round(sumpaxactual)} kr</td>
                        </tr>
                        {paxCosts}
                        <tr>
                          <td className="align-middle">Summa personkostnad:</td>
                          <td className="text-right align-middle">{Math.round(sumtotalperpax)} kr</td>
                          <td className="text-right align-middle">{Math.round(sumtotalperpaxactual)} kr</td>
                        </tr>
                        <tr className="d-print-none">
                          <td className="text-left d-print-none" colSpan="3">
                            <button onClick={() => this.addCost(false)} disabled={isSubmitting} type="button" title="Lägg till rad" className="btn btn-primary custom-scale m-2">
                              <span className="mt-1"><FontAwesomeIcon icon={faPlus} /></span>
                            </button>
                            {(typeof paxCosts === 'object') && paxCosts.length > 0 &&
                            <button onClick={() => this.removeCost(false)} disabled={isSubmitting} type="button" title="Ta bort sista raden" className="btn btn-danger custom-scale m-2">
                              <span className="mt-1"><FontAwesomeIcon icon={faMinus} size="sm" /></span>
                            </button>}
                          </td>
                        </tr>
                      </tbody>
                    </table>
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

NewBudget.propTypes = {
  getItem    : PropTypes.func,
  postItem   : PropTypes.func,
  putItem    : PropTypes.func,
  deleteItem : PropTypes.func,
  match      : PropTypes.object,
  history    : PropTypes.object,
  budgets    : PropTypes.object,
  budgetgrops: PropTypes.object,
  tours      : PropTypes.object,
  bookings   : PropTypes.object
}

const mapStateToProps = state => ({
  budgets     : state.budgets.budgets,
  budgetgroups: state.budgets.budgetgroups,
  tours       : state.tours.tours,
  bookings    : state.tours.bookings
})

const mapDispatchToProps = dispatch => bindActionCreators({
  getItem,
  postItem,
  putItem,
  deleteItem
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(NewBudget)
