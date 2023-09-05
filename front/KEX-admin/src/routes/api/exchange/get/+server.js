import { json } from "@sveltejs/kit";
import { backendServer } from "../../../../services/config";
import { postServerApi } from "../../../../services/api";
import { getCookie } from "../../../../services/cookie";

export async function POST({request,cookies}){
    
    let result  = {msg:'no'};
    let status  = 400;

    let access_token = getCookie(cookies,'access_token');
    
    const res = await postServerApi({
        path:backendServer+'/admin/exchange/get',
        data:{},
        auth : access_token
    });
    
    if(res.msg=='ok'){
        result = {msg:'ok',data:res.data};
        status = 200;
    }

    return json(result,{status:status})

}