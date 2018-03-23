import React, { Component } from 'react';
import faSyncAlt from '@fortawesome/fontawesome-free-solid/faSyncAlt';
import FontAwesomeIcon from '@fortawesome/react-fontawesome';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';


class SaveIcon extends Component {

  render() {

    const {isLoading = true} = this.props;

    const style = {
      display: 'none',
    };

    const styleShow = {
      display: 'block',
      position: 'fixed',
      bottom: '0',
      right: '0',
      color: '#0856fb',
      fontSize: '1.5rem',
      fontWeight: 'bold',
      zIndex: '90010',
      margin: '5px',
      marginBottom: '8px',
      opacity: '0.7',
      height: '30px',
      width: '30px',
    };


    return (
      <div className="SaveIcon text-center" style={isLoading ? styleShow : style}>
        <FontAwesomeIcon icon={faSyncAlt} size="1x" spin />
      </div>);


  }
}

SaveIcon.propTypes = {
  isLoading:            PropTypes.bool,
};

const mapStateToProps = state => ({
  loading: state.loading.inprogress,
});


export default connect(mapStateToProps, null)(SaveIcon);
