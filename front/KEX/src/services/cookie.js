import {chash,encrypt,decrypt} from "../services/encrypt";

const setCookie = (cookies = {},key='',value='',time=0) =>{
    cookies.set(chash(key), encrypt(value), {
        path: '/',
        httpOnly: true,
        secure: false,
        maxAge: time
    })
}

const getCookie = (cookies = {}, key='') => {
    
    if(cookies.get(chash(key))==undefined){
        return '';
    }else{
        return decrypt(cookies.get(chash(key)))
    }
}

const delCookie = (cookies = {}, key='') => {
    // cookies.delete(chash(key))
    cookies.set(chash(key), '', {
        path: '/',
        httpOnly: true,
        secure: false,
        maxAge: 0
    })
}

export{
    getCookie,
    setCookie,
    delCookie
}