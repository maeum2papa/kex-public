import { json } from "@sveltejs/kit";
import { delCookie } from "../../../../services/cookie";
import { decrypt } from "../../../../services/encrypt";

export async function POST({request,cookies}){
    
    let result  = {msg:'no'};
    let status  = 400;

    let body = await request.json();
    body = JSON.parse(decrypt(body.body));
    
    delCookie(cookies,'cart'+body.mobileLast);

    result  = {msg:'ok'};
    status  = 200;

    return json(result,{status:status})

}