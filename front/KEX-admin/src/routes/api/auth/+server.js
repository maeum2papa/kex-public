import { json } from '@sveltejs/kit';
import { postServerApi } from '../../../services/api';
import { getCookie,setCookie } from '../../../services/cookie'
import { backendServer } from '../../../services/config';
 
export async function POST({request,cookies}){
    
    let result = {msg:'no',email:''}
    let status = 400

    let access_token = getCookie(cookies,'access_token');
    let refresh_token = getCookie(cookies,'refresh_token');
    
    if(access_token!='' && access_token!=undefined && refresh_token!='' && refresh_token!=undefined && status==400){
        
        const res = await postServerApi({
            path:backendServer+'/admin/auth',
            data:{},
            auth : access_token
        });
        
        if(res.msg=='ok'){
            result = {msg:'ok',email:res.email}
            status = 200;
        }

    }

    if(status==400 && refresh_token!='' && refresh_token!=undefined ){
        
        const res = await postServerApi({
            path:backendServer+'/admin/retoken',
            data:{refresh_token:refresh_token}
        });
        
        if(res.msg=='ok'){
            setCookie(cookies,'access_token',res.access_token,(60*15))
            
            result = {msg:'ok',email:res.email}
            status = 200;
        }
    }
    
    return json(result,{status:status});
}