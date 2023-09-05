import { json } from "@sveltejs/kit";
import { backendServer } from "../../../services/config";
import { postServerApi } from "../../../services/api";
import { getCookie,setCookie,delCookie } from "../../../services/cookie";
import { decrypt } from "../../../services/encrypt";

export async function POST({request,cookies}){
    
    let result  = {msg:'no'};
    let status  = 400;
    let cart = [];

    let body = await request.json();
    body = JSON.parse(decrypt(body.body));
    
    const access_token = getCookie(cookies,'access_token');
    cart = JSON.parse(getCookie(cookies,'cart'+body.mobileLast));

    const res = await postServerApi({
        path:backendServer+"/api/register",
        data:{cart},
        auth:access_token
    });

    if(res.msg=='ok' && res.list){
        
        delCookie(cookies,'cart'+body.mobileLast);
        setCookie(cookies,'resNumber',JSON.stringify(res.list),60*15);

        result  = {msg:'ok'};
        status  = 200;
    }
    
    return json(result,{status:status})

}