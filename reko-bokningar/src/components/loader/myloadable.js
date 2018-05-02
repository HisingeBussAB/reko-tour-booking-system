import Loadable from 'react-loadable'
import Loading from './loader'

export default function MyLoadable (opts) {
  return Loadable(Object.assign({
    loading: Loading,
    delay  : 300,
    timeout: 60000
  }, opts))
}
