import React, { Component } from 'react'
import { connect } from 'react-redux'
import myAxios from '../config/axios'
import { unregister } from '../registerServiceWorker'

class UpdateFirewall extends Component {
  constructor (props) {
    super(props)
    this.state = {
      response: 'Väntar på svar.'
    }
  }

  componentDidMount () {
    myAxios.get('/updatefirewall')
      .then((reply) => {
        this.setState({'response': reply.data})
        unregister()
      })
      .catch(() => {
        this.setState({'response': 'Failed to update Cloudflare filters.'})
        unregister()
      })
  }

  render () {
    const {response} = this.state
    return (
      <div style={{margin: '50px'}}>
        <div dangerouslySetInnerHTML={{__html: response}} style={{margin: '30px'}} />
        <h1 style={{margin: '30px'}}><a href="https://bokningar.rekoresor.app" target="_top" style={{margin: '30px'}}>Tillbaka till huvudsida</a></h1>
      </div>
    )
  }
}

export default connect(null, null)(UpdateFirewall)
