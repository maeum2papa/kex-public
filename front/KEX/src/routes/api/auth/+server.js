import { json } from '@sveltejs/kit';
import { postServerApi } from '../../../services/api';
import { getCookie,setCookie } from '../../../services/cookie'
import { backendServer } from '../../../services/config';
 
export async function POST({request,cookies}){
    
    let result = {msg:'no',email:''}
    let status = 400

    let access_token = getCookie(cookies,'access_token');
    
    if(access_token!='' && access_token!=undefined){
        
        const res = await postServerApi({
            path:backendServer+'/api/auth',
            data:{},
            auth : access_token
        });

        if(res.msg=='ok' && res.id){
            let id = res.id.split("|");
            result = {msg:'ok',name:id[0],mobile:id[1]}
            status = 200;
        }

    }
    
    return json(result,{status:status});
}