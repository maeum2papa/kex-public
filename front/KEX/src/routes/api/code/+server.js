import { json } from "@sveltejs/kit";
import { backendServer } from "../../../services/config";
import { postServerApi } from "../../../services/api";
import { setCookie } from "../../../services/cookie";
import { decrypt } from "../../../services/encrypt";

export async function POST({request,cookies}){
    
    let result  = {msg:'no'};
    let status  = 400;

    let body = await request.json();
    body = JSON.parse(decrypt(body.body));

    const min = 100000;
    const max = 999999;
    const randomNumber = Math.floor(Math.random() * (max - min + 1)) + min;
    const code = randomNumber.toString();

    console.log('code',code);
    const unixTime = new Date().getTime();

    //카카오 알림톡으로 인증번호 전송
    let form = new FormData();
    form.append('plusFriendId','@에스에프인터내셔널');
    form.append('senderKey','de1cfb379dbca188e155001bd05b708be33eda57');
    form.append('templateCode','SJR_056722');
    form.append('contents','[SFKEX] 인증번호 ['+code+']를 입력 후 확인을 누르시기 바랍니다.');
    form.append('receiverTelNo',body.mobile);
    form.append('userKey',unixTime.toString().substring(0,12));
    form.append('resendType','SMS');
    form.append('resendCallback','0803931111');

    
    const response = await fetch("https://apimsg.wideshot.co.kr/api/v2/message/alimtalk",{
        method:"POST",
        body:form,
        headers:{
            "sejongApiKey":"eTZxT1QrU2JmTitmZWZQRWxOelMrQ2dhUEp5elFhQnBLSjNaTTMvaWFNSTA0a216c1RoRHdvTjZJc25EYzBhMQ=="
        }
    });
    

    if(response.status==200){
        //전송이 성공하면...
        setCookie(cookies,'code',code,(60 * 5));

        result = {msg:'ok',code:code};//code는 실제 서비스에서는 불필요해서 삭제 필요
        status = 200;
    }   

    return json(result,{status:status})

}