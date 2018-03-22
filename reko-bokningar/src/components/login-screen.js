import React, { Component } from 'react';
import fontawesome from '@fortawesome/fontawesome';
import faSpinner from '@fortawesome/fontawesome-free-solid/faSpinner';
import FontAwesomeIcon from '@fortawesome/react-fontawesome';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators }from 'redux';
import {Login,errorPopup} from '../actions';
import Config from '../config/config';
import Logo from '../img/logo.gif';


fontawesome.library.add(faSpinner);

class LoginScreen extends Component {
  constructor(props){
    super(props);
    this.state = {
      issending: true,
      servertime: '',
      logindata: {
        pwd: Config.AutoLoginPwd,
        user: Config.AutoUsername,
        auto: this.props.login.autoAttempt,
        isOnce: false,
        blockError: false
      }
    };
  }

  componentWillMount() {

    let logindata = {...this.state.logindata};
    let userObject = null;
    try {
      userObject = localStorage.getObject('user');
    } catch(e) {
      userObject = null;
    }

    const dateTime = +new Date();
    const timestamp = Math.floor(dateTime / 1000);

    //Check if userObject appears valid.
    //This is just a basic sanity filter. It allows a lot of leeway since the client and server could have timezone differences etc.
    try {
      if (userObject.expires-10000 >= timestamp || userObject.token.length < 10 || userObject.tokenid.length < 10 || userObject.user === '') {
        userObject = null;
      }
    } catch(e) {
      userObject = null;
    }

    if (userObject !== null) {
      logindata = {
        pwd: userObject.tokenid + Config.OnceLoginToken + userObject.token,
        user: userObject.user,
        auto: true,
        isOnce: true,
      };
      localStorage.setObject('user', null);
    }


    if (!this.props.login.login && logindata.auto) {
      this.setState({issending: true});
      //Will we try again if we fail?

      this.props.Login(logindata)
        .then(() => {
          //Component will unmount
        })
        .catch(() => {
          this.setState({issending: false});
        });
    }
    if (this.props.login.autoAttempt === false) {
      this.setState({issending: false});
    }
  }

  componentWillReceiveProps(nextProps) {

    if (!nextProps.login.login) {
      myAxios.get( '/timestamp')
      .then(response => {
        try {
          this.setState({servertime: response.data.servertime});
        } catch (e) {

        })
        .catch(() => {
          //try auto login
          this.setState({issending: true});
          this.props.Login(this.state.logindata)
            .then(() => {
            //Component will unmount
            })
            .catch(() => {
            this.setState({issending: false});
            });
        });




      let logindata = {...this.state.logindata};


      let userObject = null;
      try {
        userObject = localStorage.getObject('user');
      } catch(e) {
        userObject = null;
      }
      const dateTime = +new Date();
      const timestamp = Math.floor(dateTime / 1000);

      try {
        /* Check if userObject appears valid.
         This is just a basic sanity filter. It allows a lot of leeway since the client and server could have timezone differences etc. */
        if (userObject.expires-10000 >= timestamp || userObject.token.length < 10 || userObject.tokenid.length < 10 || userObject.user === '') {
          userObject = null;
        }
      } catch(e) {
        userObject = null;
      }

    }

    let userObject = null;
    try {
      userObject = localStorage.getObject('user');
    } catch(e) {
      userObject = null;
    }

    if (!nextProps.login.login && nextProps.login.autoAttempt && userObject === null && (typeof Config.AutoUsername !== 'undefined' && Config.AutoUsername) && (typeof Config.AutoLoginPwd !== 'undefined' && Config.AutoLoginPwd)) {
      this.setState({issending: true});
      this.props.Login({
        pwd: Config.AutoLoginPwd,
        user: Config.AutoUsername,
        auto: nextProps.login.autoAttempt,
        isOnce: false,
      })
        .then(() => {
          //Component will unmount
        })
        .catch(() => {
          this.setState({issending: false});
        });
    } else {
      this.setState({issending: false});
    }
  }


  handleUserChange = (event) => {
    this.setState({logindata: { ...this.state.logindata, user: event.target.value}});
  }

  handlePwdChange = (event) => {
    this.setState({logindata: { ...this.state.logindata, pwd: event.target.value}});
  }

  clearPwd = () => {
    this.setState({logindata: { ...this.state.logindata, pwd: ''}});
  }

  clearUser = () => {
    this.setState({logindata: { ...this.state.logindata, user: ''}});
  }

  handleSubmit = (event) => {
    event.preventDefault();
    this.setState({issending: true});
    this.props.Login(this.state.logindata)
      .then(() => {
        //Component will unmount
      })
      .catch(() => {
        this.setState({issending: false});
      });
  }


  render() {
    const style = {
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
            <h5 className="w-50 mx-auto my-3" style={{color: 'red'}}>{this.props.error.message}</h5>
            <h4 className="w-50 mx-auto mt-5 mb-3">Logga in</h4>
            <form onSubmit={this.handleSubmit}>
              <fieldset disabled={this.state.issending}>
                <div className="my-2 w-50 mx-auto"><label className="small d-block text-left pt-2 pl-3">Användarnamn:</label><input className="w-100 rounded" type="text" placeholder="Användarnamn" value={this.state.user} onFocus={this.clearUser} onChange={this.handleUserChange}/></div>
                <div className="my-2 w-50 mx-auto"><label className="small d-block text-left pt-2 pl-3">Lösenord:</label><input className="w-100 rounded" type="password" placeholder="Lösenord" value={this.state.pwd} onFocus={this.clearPwd} onChange={this.handlePwdChange}/></div>
                <div className="my-2 w-50 mx-auto"><input className="w-100 mt-4 rounded text-uppercase font-weight-bold btn btn-primary custom-wide-text" type="submit" value="Logga in"/></div>
              </fieldset>
            </form>
          </div>
        }
      </div>);


  }
}

LoginScreen.propTypes = {
  Login:              PropTypes.func,
  login:              PropTypes.object,
  error:              PropTypes.object,
  errorPopup:         PropTypes.func,
};

const mapStateToProps = state => ({
  login: state.login,
  error: state.errorPopup,
});

const mapDispatchToProps = dispatch => bindActionCreators({
  Login,
  errorPopup,
}, dispatch);


export default connect(mapStateToProps, mapDispatchToProps)(LoginScreen);
