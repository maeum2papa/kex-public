import { json } from "@sveltejs/kit";
import { backendServer } from "../../../../services/config";
import { postServerApi } from "../../../../services/api";

export async function POST({request,cookies}){
    
    let result  = {msg:'no'};
    let status  = 400;

    const body = await request.json();

    
    const res = await postServerApi({
        path:backendServer+'/admin/exchange/del',
        data:body
    });
    
    if(res.msg=='ok'){
        result = {msg:'ok'};
        status = 200;
    }

    return json(result,{status:status})

}