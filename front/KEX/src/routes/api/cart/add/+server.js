import { json } from "@sveltejs/kit";
import { backendServer } from "../../../../services/config";
import { postServerApi } from "../../../../services/api";
import { setCookie,getCookie } from "../../../../services/cookie";
import { decrypt } from "../../../../services/encrypt";

export async function POST({request,cookies}){
    
    let result  = {msg:'no'};
    let status  = 400;
    let cart = [];

    let body = await request.json();
    body = JSON.parse(decrypt(body.body));
    
    let cookieCart = getCookie(cookies,'cart'+body.mobileLast);
    
    if(cookieCart==''){
        cart.push(body);
    }else{
        cart = JSON.parse(cookieCart);
        let overlap = false;

        cart.forEach(e => {
            if(e.SFNumber==body.SFNumber){
                overlap = true;
            }
        });

        if(overlap==false){
            cart.push(body);
        }
    }

    setCookie(cookies,'cart'+body.mobileLast,JSON.stringify(cart),(60*30));

    result  = {msg:'ok'};
    status  = 200;

    return json(result,{status:status})

}