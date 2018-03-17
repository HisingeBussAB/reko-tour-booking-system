import React, { Component } from 'react';
import fontawesome from '@fortawesome/fontawesome';
import faSpinner from '@fortawesome/fontawesome-free-solid/faSpinner';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators }from 'redux';
import {Login} from '../actions';
import {errorPopup} from '../actions';
import Config from '../config/config';
import {myAxios} from '../config/axios';


fontawesome.library.add(faSpinner);

class ExpireChecker extends Component {
  constructor(props){
    super(props);
    this.state = {
      displayWarning: false,
      warningMessage: false,
      servertime: '',
    };    
    this.interval = false;
  }

  componentDidMount() {
    this.checkTime();
    this.interval = setInterval(()=> {
      this.checkTime();
    }, 600000);
  }


  componentWillUnmount() {
    clearInterval(this.interval);
  }

  checkTime = () => {
    myAxios.get( '/timestamp')
      .then(response => {
        try {
          this.setState({servertime: response.data.servertime});
          if (this.state.servertime > this.props.login.expires-1800) {
            this.setState({displayWarning: true});
            //attempt auto relog if once token is found in local storage or we are AutoUser
            let userObject = null;
            let logindata = null
            if (this.props.login.user === Config.AutoUsername && Config.AutoLogin) {
              logindata = { pwd: Config.AutoLoginPwd,
                user: Config.AutoUsername,
                auto: true,
                isOnce: false,
              };
            }
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
              if ((userObject.expires-10000) >= timestamp || userObject.token.length < 10 || userObject.tokenid.length < 10 || userObject.user === '') {
                userObject = null;
              }
            } catch(e) {
              userObject = null;
            }

            if (userObject !== null) {
              //change logindata i once is found and appears valid
              logindata = {
                pwd: userObject.tokenid + Config.OnceLoginToken + userObject.token,
                user: userObject.user,
                auto: true,
                isOnce: true,
              };
              localStorage.setObject('user', null);
            }
            if (logindata !== null) {
              this.props.Login(logindata);
            }
          } else {
            this.setState({displayWarning: false});
          }
        } catch(e) {
          //Malformatted answer or client not logged.
          try {
            var expire = this.props.login.expires - 1000; //Set expire within window to show
            this.setState({servertime: expire});
          } catch(e) {/*not logged in, probably*/}
          this.props.errorPopup('Kunde inte hämta tid från server. Något är fel i APIn. Spara arbetet.');
        }
      })
      .catch(() => {
        try {
          var expire = this.props.login.expires - 1000; //Set expire within window to show
          this.setState({servertime: expire});
        } catch(e) {/*not logged in, probably*/}
        this.props.errorPopup('Kunde inte hämta tid från server. Något är fel i APIn. Spara arbetet.');
      });
  }
  
  
  closeMe = () => {
    this.setState({displayWarning: false});
  }

  render() {
    let style = {
      display: 'none',
    };

    let styleShow = {
      display: 'block',
      position: 'fixed',
      top: '20px',
      left: '50%',
      transform: 'translateX(-50%)',
      color: 'red',
      fontSize: '1.5rem',
      fontWeight: 'bold',
      zIndex: '50000',
      backgroundColor: 'white',
      padding: '25px',
      border: '1px solid black',
      borderRadius: '5px',
    };

    let minutesLeft;
    try {
      minutesLeft = Math.round((this.props.login.expires - this.state.servertime)/60);
    } catch(e) {
      minutesLeft = 'okänt antal';
    }

    
    
    return (
      <div className="ExpireChecker text-center" style={this.state.displayWarning ? styleShow : style}><p>
        {!this.state.warningMessage ? 'Inloggningen går ut om ' + minutesLeft + ' minuter.' 
          : 
          this.state.warningMessage} </p>
      <p>Spara arbetet och ladda om appen (tryck F5).</p>
      <button className="btn btn-primary text-uppercase py-1 px-3 m-1" onClick={this.closeMe}>Stäng</button>
      </div>);


  }
}

ExpireChecker.propTypes = {
  Login:              PropTypes.func,
  errorPopup:         PropTypes.func,
  login:              PropTypes.object,
  error:              PropTypes.object,
};

const mapStateToProps = state => ({
  login: state.login,
  error: state.errorPopup,
});

const mapDispatchToProps = dispatch => bindActionCreators({
  Login,
  errorPopup,
}, dispatch);


export default connect(mapStateToProps, mapDispatchToProps)(ExpireChecker);