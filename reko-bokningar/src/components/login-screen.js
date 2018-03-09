import React, { Component } from 'react';
import fontawesome from '@fortawesome/fontawesome';
import faSpinner from '@fortawesome/fontawesome-free-solid/faSpinner';
import FontAwesomeIcon from '@fortawesome/react-fontawesome';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators }from 'redux';
import {Login} from '../actions';
import Config from '../config/config';
import axios from 'axios';
import Logo from '../img/logo.gif';


fontawesome.library.add(faSpinner);

class LoginScreen extends Component {
  constructor(props){
    super(props);
    this.state = {
      servertime: '',
      logindata: {
        pwd: Config.AutoLoginPwd,
        user: Config.AutoUsername,
      }
    };    
  }

  componentWillMount() {
    if (!this.props.login.login && this.props.login.autoAttempt) 
      this.props.Login(this.state.logindata);
  }

  componentWillReceiveProps(nextProps) {
    if (!nextProps.login.login && !nextProps.login.autoAttempt) {

    }
  }

  handleUserChange = (event) => {
    this.setState({logindata: {user: event.target.value}});
  }

  handlePwdChange = (event) => {
    this.setState({logindata: {pwd: event.target.value}});
  }

  clearPwd = () => {
    this.setState({logindata: {pwd: ''}});
  }

  clearUser = () => {
    this.setState({logindata: {user: ''}});
  }

  handleSubmit = (event) => {
    event.preventDefault();
    this.props.Login(this.state.logindata);
  }


  

  render() {
    let style = {
      color: '#0856fb',
      height: '650px',
      margin: '0 auto',
      position: 'absolute',
      top: '50%',
      transform: 'translateY(-50%)',
      textAlign: 'center',
      width: '100%',
      zIndex: '19999',
    };

    
    return (
      <div className="Login" style={style}>
        <p><img src={Logo} alt="Logo" className="rounded my-4" title="Till Startsida" id="mainLogo"/></p>
        <h1 className="my-4">Resesystem</h1>
        {this.props.login.autoAttempt ?
          <div>
            <h3 className="mb-4">Försöker automatisk inloggning...</h3>
            <FontAwesomeIcon className="my-4" icon="spinner" pulse size="4x" />
          </div> :
          <div>
            {!this.props.login.autoAttempt ? <h5 className="w-50 mx-auto my-3" style={{color: 'red'}}>Automatisk inlogging misslyckades!</h5> : null}         
            <h5 className="w-50 mx-auto my-3" style={{color: 'red'}}>{this.state.showErrorMessage}</h5>
            <h4 className="w-50 mx-auto mt-5 mb-3">Logga in</h4>
            <form onSubmit={this.handleSubmit}>
              <div className="my-2 w-50 mx-auto"><label className="small d-block text-left pt-2 pl-3">Användarnamn:</label><input className="w-100 rounded" type='text' placeholder='Användarnamn' value={this.state.user} onFocus={this.clearUser} onChange={this.handleUserChange}/></div>
              <div className="my-2 w-50 mx-auto"><label className="small d-block text-left pt-2 pl-3">Lösenord:</label><input className="w-100 rounded" type='password' placeholder='Lösenord' value={this.state.pwd} onFocus={this.clearPwd} onChange={this.handlePwdChange}/></div>
              <div className="my-2 w-50 mx-auto"><input className="w-100 mt-4 rounded text-uppercase font-weight-bold btn btn-primary custom-wide-text" type='submit' value='Logga in'/></div>
            </form>
          </div>
        }
      </div>);


  }
}

LoginScreen.propTypes = {
  Login:              PropTypes.func,
  login:              PropTypes.object,
};

const mapStateToProps = state => ({
  login: state.login,
});

const mapDispatchToProps = dispatch => bindActionCreators({
  Login,
}, dispatch);


export default connect(mapStateToProps, mapDispatchToProps)(LoginScreen);