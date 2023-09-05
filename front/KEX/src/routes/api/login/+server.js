import { json } from "@sveltejs/kit";
import { backendServer } from "../../../services/config";
import { postServerApi } from "../../../services/api";
import { setCookie,getCookie } from "../../../services/cookie";
import { decrypt } from "../../../services/encrypt";

export async function POST({request,cookies}){
    
    let result  = {msg:'no'};
    let status  = 400;

    let body = await request.json();
    let deCodeBody = JSON.parse(decrypt(body.body));

    const code = getCookie(cookies,'code');
    
    if(deCodeBody.code==code){
        
        
        const res = await postServerApi(
            {
                path:backendServer+"/api/login",
                data:body
            }
        );

        if(res.msg=='ok'){
            setCookie(cookies,'access_token',res.access_token,(60*30));

            result = {msg:'ok'};
            status = 200;
        }
    }

    return json(result,{status:status})

}