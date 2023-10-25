import axios from 'axios'

/* global Laravel */

const hostparts = location.hostname.split('.')
const tenant = hostparts.shift()
const domain = hostparts.join('.')

import { initializeApp } from 'firebase/app'
import { getFirestore, doc, setDoc } from 'firebase/firestore'

const firebaseConfig = {
	apiKey: 'AIzaSyDL2Jc5h9l4v_8CLPyy9C5qUszPdiT2a60',
	authDomain: 'epicdb-80b1c.firebaseapp.com',
	databaseURL: 'https://epicdb-80b1c.firebaseio.com',
	projectId: 'epicdb-80b1c',
	storageBucket: 'epicdb-80b1c.appspot.com',
	messagingSenderId: '107958648782',
	appId: '1:107958648782:web:bf83e032a2038dfe63759f'
}
const FireApp = initializeApp(firebaseConfig)
const db = getFirestore(FireApp)

const originalHandler = window.onerror

function weblog(msg, cause){
	if(domain !== 'jobtron.org') return
	const date = new Date().toISOString().substring(0,10)
	const logdoc = doc(db, 'weblog', date);
	const result = {
		tenant,
		userId: Laravel.userId,
		msg: msg || '',
		cause: cause || '',
		location: location.pathname
	}
	console.error('weblog', result)
	setDoc(logdoc, {
		[Date.now()]: result
	},
	{ merge: true });
}

window.onerror = function(msg, url, line, col, error){
	weblog(msg, error?.cause)
	if(originalHandler) originalHandler(msg, url, line)
}

const AXIOS_SEPARATOR = '#️⃣'

axios.interceptors.response.use((response) => response, (error) => {
	const msg = error?.response?.data?.message || ''
	if(msg === 'Unauthenticated.') return location.assign('/')
	const reqData = error?.response?.config?.data
	const data = reqData instanceof FormData ? JSON.stringify(Object.fromEntries(reqData)) : JSON.stringify(reqData)
	weblog('axios: ' + (~msg.indexOf('logs/laravel') ? 'LARAVEL LOGS' : msg), (error?.response?.config?.method || '') + AXIOS_SEPARATOR + (error?.response?.config?.url || '') + AXIOS_SEPARATOR + data)
	throw error;
});
