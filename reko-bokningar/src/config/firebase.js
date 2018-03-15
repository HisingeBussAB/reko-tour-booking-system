import firebase from 'firebase';
import {firebaseconfig} from './config';

firebase.initializeApp(firebaseconfig); 

export default firebase;