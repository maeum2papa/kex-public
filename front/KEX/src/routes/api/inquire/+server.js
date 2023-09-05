import { json } from "@sveltejs/kit";
import { backendServer } from "../../../services/config";
import { postServerApi } from "../../../services/api";
import { getCookie } from "../../../services/cookie";

export async function POST({request,cookies}){
    
    let result  = {msg:'no'};
    let status  = 400;
    let id = [];

    const access_token = getCookie(cookies,'access_token');
    

    const res2 = await postServerApi({
        path:backendServer+'/api/auth',
        data:{},
        auth : access_token
    });

    if(res2.msg=='ok' && res2.id){
        id = res2.id.split("|");
    }
    

    const res = await postServerApi(
        {
            path:backendServer+"/api/inquire",
            data:{},
            auth:access_token
        }
    )

    if(res.msg=='ok'){       
        result  = {msg:'ok',list:res.list,name:id[0]};
        status  = 200;
    }
    
    return json(result,{status:status})

}