import { json } from "@sveltejs/kit";
import { backendServer } from "../../../services/config";
import { postServerApi } from "../../../services/api";
import { getCookie } from "../../../services/cookie";

export async function POST({request,cookies}){
    
    let result  = {msg:'no'};
    let status  = 400;
    let id = [];

    let resNumber = JSON.parse(getCookie(cookies,'resNumber'));
    let access_token = getCookie(cookies,'access_token');

    if(access_token!='' && access_token!=undefined){
        
        const res = await postServerApi({
            path:backendServer+'/api/auth',
            data:{},
            auth : access_token
        });
        
        if(res.msg=='ok' && res.id){
            id = res.id.split("|");
        }

    }
    
    for (const item of resNumber.entries()) { 
        
        let unixTime = new Date().getTime();

        //카카오 알림톡으로 인증번호 전송
        let form = new FormData();
        form.append('plusFriendId','@에스에프인터내셔널');
        form.append('senderKey','de1cfb379dbca188e155001bd05b708be33eda57');
        form.append('templateCode','SJR_056743');
        form.append('contents','고객님! KEX EXPRESS입니다.\n고객님이 예약하신 편의점 택배 화물 승인번호는 아래와 같습니다.\n가까운 GS 편의점에서 발송 진행 부탁 드립니다. POSTBOX 장비 첫화면의 <쇼핑몰 거래 사전예약/선결제> 탭 눌러서 진행 가능합니다.\n\nSF 송장번호 : '+item[1].SFNumber+'\n승인번호 : '+item[1].reservationNumber);
        form.append('receiverTelNo',id[1]);
        form.append('userKey',unixTime.toString().substring(0,12));
        form.append('resendType','SMS');
        form.append('resendCallback','0803931111');
        
        await fetch("https://apimsg.wideshot.co.kr/api/v2/message/alimtalk",{
            method:"POST",
            body:form,
            headers:{
                "sejongApiKey":"eTZxT1QrU2JmTitmZWZQRWxOelMrQ2dhUEp5elFhQnBLSjNaTTMvaWFNSTA0a216c1RoRHdvTjZJc25EYzBhMQ=="
            }
        });
    }

    result  = {msg:'ok',list:resNumber};
    status  = 200;
    
    return json(result,{status:status})

}