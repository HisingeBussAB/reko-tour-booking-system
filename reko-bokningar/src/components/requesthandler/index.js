import React, { Component } from 'react';
import axios from 'axios';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import Config from '../../config/config';

class Handler extends Component {


  Get = function(data) {

  }

  Send = function(data) {
    this.ExpirationCheck();
  }

  ExpirationCheck = () => {
    axios.get( Config.ApiUrl + '/timestamp')
      .then(response => {
        console.log(response.data.servertime)
      })
      .catch(() => {
        //do nothing here. API is probably down and if the error persists it will be handled on the actual request
      });
  }




/*
  axios.post( Config.ApiUrl + '/api/token/submit', {
    apitoken: Config.ApiToken,
    user: this.props.login.user,
  })
    .then(response => {
      axios.post( Config.ApiUrl + '/api/tours/category/' + operation, {
        submittoken: response.data.submittoken,
        apitoken: Config.ApiToken,
        user: this.props.login.user,
        jwt: this.props.login.jwt,
        task: operationin,
        categoryid: this.state.categoriesUnsaved[i].id,
        category: this.state.categoriesUnsaved[i].category,
        active: active,
      })
        .then(response => {
          if (response.data.modifiedid !== undefined) {
            this.getCategory(response.data.modifiedid);
          }
          console.log(response);
          this.setState({isSubmitting: false});
          this.setState({showStatus: true, showStatusMessage: response.data.response});
        })
        .catch(error => {
          console.log(error.response.data.response);
          console.log(error.response.data.login);
          this.setState({showStatus: true, showStatusMessage: error.response.data.response});
          this.setState({isSubmitting: false});
        });
    })
    .catch(error => {
      let message = 'Något har gått fel, får inget svar från API.';
      if (error.response !== undefined) {
        message = error.response.data.response;
      }
      this.setState({showStatus: true, showStatusMessage: message});
    });
*/
}

Handler.propTypes = {
  login:              PropTypes.object,
};

const mapStateToProps = state => ({
  login: state.login,
});

export default connect(mapStateToProps, null)(Handler);