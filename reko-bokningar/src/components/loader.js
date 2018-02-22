import React, { Component } from 'react';
import fontawesome from '@fortawesome/fontawesome';
import faSpinner from '@fortawesome/fontawesome-free-solid/faSpinner';
import FontAwesomeIcon from '@fortawesome/react-fontawesome';
import PropTypes from 'prop-types';

fontawesome.library.add(faSpinner);

class Loader extends Component {
  
  

  render() {
    let style;
    let text;
    let size;
    if (this.props.fullScreen === true) {
      style = {
        color: '#0856fb',
        margin: '0 auto',
        position: 'absolute',
        top: '50%',
        transform: 'translateY(-50%)',
        textAlign: 'center',
        width: '100%',
      };
      const textstyle = {
        paddingTop: '12px',
      }
      text = <p style={textstyle}>Laddar...</p>;
      size = '6x';
    } else {
      style = {
        color: '#0856fb',
        margin: '0 auto',
        textAlign: 'center',
        width: '100%',
      };
      text = '';
      size = '1x';
    }
    
    return (
      <div className="Loader" style={style}>
        <FontAwesomeIcon icon="spinner" pulse size={size} />
        {text}
      </div>
    );
  }
}

Loader.propTypes = {
  fullScreen: PropTypes.bool,
};

export default (Loader);