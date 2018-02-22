import React, { Component } from 'react';
import MainMenu from './components/main-menu';
import { connect } from 'react-redux';


class App extends Component {
  render() {
    return (
      <div className="App">
        <MainMenu />
      </div>
    );
  }
}

export default connect(null, null)(App);
