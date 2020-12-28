import React, { Component, useState, useRef } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import JoditEditor from 'jodit-react'
import {postItem} from '../../actions'

function NewsletterSend () {
  const editor = useRef(null)
  const [content, setContent] = useState('')

  const config = {
    readonly: false,
    minHeight: 800,
    minWidth: 700
  }
  console.log(content)
  console.log(content.target === undefined ? '' : content.target.innerHTML)
  console.log(content.target)
  
  return (
    <div className="ListView NewsletterSend">

      <form autoComplete="off">
        <fieldset>
          <div className="container text-left" style={{maxWidth: '850px'}}>
            <h3 className="my-4 w-50 mx-auto text-center">Skicka nyhetsbrev (ej klart)</h3>
            <div dangerouslySetInnerHTML={{__html: content}} />
            <div>
              <JoditEditor
                ref={editor}
                value={content}
                config={config}
                tabIndex={1} // tabIndex of textarea
                onBlur={newContent => setContent(newContent.target.innerHTML)}
                onChange={newContent => {}}
              />
            </div>
          </div>
          <input type="file"></input>
        </fieldset>
      </form>
    </div>
  )
}

const mapStateToProps = state => ({})

const mapDispatchToProps = dispatch => bindActionCreators({
  postItem
}, dispatch)

export default connect(mapStateToProps, mapDispatchToProps)(NewsletterSend)
