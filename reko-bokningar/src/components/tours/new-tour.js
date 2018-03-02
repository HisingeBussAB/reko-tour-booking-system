import React, { Component } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import { toggleMenuCompression } from '../../actions';


class NewTour extends Component {
  
  componentDidMount() {
    this.props.toggleMenuCompression(true);
  }


  render() {

   
    return (
      <div className="TourViewNewTour">


        <h3 className="my-4">Skapa ny resa</h3>
        
      </div>
    );
  }
}


const mapDispatchToProps = dispatch => bindActionCreators({
  toggleMenuCompression,
}, dispatch);


export default connect(null, mapDispatchToProps)(NewTour);