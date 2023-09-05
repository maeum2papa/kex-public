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

export{
    checkEmail,
    checkPassword
}