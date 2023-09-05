const checkEmail = (email='')=>{
    const req = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return req.test(email)
}

const checkPassword = (password = '')=>{

    if(password.length < 4){
        return 'length-err'
    }
    
    return 'ok'
}

const checkMobile = (mobile='')=>{
    const req = /^01[0-9]{1}[0-9]{3,4}[0-9]{4}$/;
    return req.test(mobile)
}

export{
    checkEmail,
    checkPassword,
    checkMobile
}