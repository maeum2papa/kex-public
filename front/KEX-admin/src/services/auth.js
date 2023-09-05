import { postApi } from '../services/api';

const auth = async()=>{

    const res = await postApi({
        path:'/api/auth'
    });
    
    if(res.email!='' && res.email!=undefined){
        // userEmail = res.email;
        return res.email;
    }else{
        return '';
    }

}

export{
    auth
}