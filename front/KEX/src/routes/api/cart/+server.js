import { json } from "@sveltejs/kit";
import { backendServer } from "../../../services/config";
import { postServerApi } from "../../../services/api";
import { setCookie,getCookie } from "../../../services/cookie";
import { decrypt } from "../../../services/encrypt";

export async function POST({request,cookies}){
    
    let result  = {msg:'no'};
    let status  = 400;
    let cart = [];

    let body = await request.json();
    body = JSON.parse(decrypt(body.body));
    
    let cookieCart = getCookie(cookies,'cart'+body.mobileLast);
    const access_token = getCookie(cookies,'access_token');
    cart = JSON.parse(cookieCart);
    
    
    const res = await postServerApi(
        {
            path:backendServer+"/api/cart",
            data:{cart:cart},
            auth:access_token
        }
    );

    if(res.msg=='ok'){
        result  = {msg:'ok',list:res.list};
        status  = 200;
    }

    return json(result,{status:status})

}