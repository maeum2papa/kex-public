import { postApi } from '../services/api';

const auth = async()=>{

    const res = await postApi({
        path:'/api/auth'
    });
    
    if(res.msg=='ok'){
        return res;
    }else{
        return '';
    }

}

export{
    auth
}