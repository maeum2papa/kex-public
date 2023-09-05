import { json } from "@sveltejs/kit";
import { backendServer } from "../../../services/config";
import { postServerApi } from "../../../services/api";
import { setCookie } from "../../../services/cookie";

export async function POST({request,cookies}){
    
    let result  = {msg:'no'};
    let status  = 400;

    const body = await request.json();

    
    const res = await postServerApi({
        path:backendServer+'/admin/login',
        data:body
    });
    
    if(res.msg=='ok' && res.access_token && res.refresh_token){

        setCookie(cookies,'access_token',res.access_token,(60 * 15));
        setCookie(cookies,'refresh_token',res.refresh_token,(60 * 60 * 24 * 180));

        result = {msg:'ok'};
        status = 200;
    }

    return json(result,{status:status})

}