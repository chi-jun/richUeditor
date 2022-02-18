import axios from 'axios'

axios.interceptors.response.use(res => {
    if (res.status === 200) {
        return res.data
    } else {
        Promise.reject()
    }
},error => {
    console.log(error)
    return Promise.reject()
})

export default axios