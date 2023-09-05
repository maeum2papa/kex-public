<script>
    import { postApi } from "../services/api";
    import { auth } from "../services/auth";
    import { cAlert } from "../stores/store";
    import { onMount } from "svelte";

    let autoPrintShipCode = '';
    let iframFlag = false;
    let iframSrc = '';

    let inputs = {
        print:{}
    }

    const autoPrintShipCodeChange = async(e)=>{//12자리

        if(e.keyCode==13){

            await auth();

            // const res = await postApi({
            //     path:"/api/scan",
            //     data:{d:autoPrintShipCode}
            // });

            const response = await fetch("https://api.sf-kex.com/admin/scan?d="+autoPrintShipCode);

            let res = await response.json();
            
            if(res.msg=='ok'){
                
                iframFlag = true;
                iframSrc = "/print?d="+res.data.d+'&o='+res.data.o;
                setTimeout(()=>{
                    iframFlag = false;
                    iframSrc = '';
                },200)
                // window.open("/print?d="+res.data.d+'&o='+res.data.o);
            }else{
                alert("맵핑된 택배사 운송장 번호가 없습니다.");
            }
            
            setTimeout(()=>{
                autoPrintShipCode = ''
            },20)
        }
    }

    export let handleMask;

    onMount(()=>{
        setTimeout(()=>{
            inputs.print.focus();
        },100);
    });

</script>

<div id="print">
    <h2>SF운송장출력</h2>
    <input type="text" placeholder="택배사운송장번호를 스캔해주세요." bind:value={autoPrintShipCode} bind:this={inputs.print} on:keyup={autoPrintShipCodeChange} maxlength={15}>
    <div class="button-area">
        <button type="button" on:click={handleMask}>닫기</button>
    </div>

    <div id="guide">
        <b>유의사항</b><br/>
        1. 크롬 바로가기 아이콘에서 마우스 오른쪽 클릭<br/>
        2. 속성 클릭<br/>
        3. 대상(T) 맨위에 <code>...₩chrome.exe <span>--kiosk-printing</span></code> 와 같이 추가<br/>
        4. 크롬 브라우저 모두 종료 후 다시 실행<br/>

        * 사전에 프린터는 바코트 프린터로 세팅되어 있어야 합니다.
    </div>

    {#if iframFlag}
    <iframe src={iframSrc}></iframe>
    {/if}
</div>

<style lang="scss">
    
    .button-area{
        margin-top:10px;
        padding-left: 5px;
        button{
            margin-right:10px;
        }
    }

     #print{
        position: fixed;
        background: #fff;
        width:400px;
        top:50%;
        left:50%;
        transform: translate(-50%,-50%);
        z-index: 1100;
        text-align: center;
        padding:20px;

        h2{
            font-size:24px;
            margin-bottom:20px;
        }

        input{
            width:calc(100% - 30px);
            padding:15px;
        }

    }

    #guide{
        margin-top:40px;
        text-align: left;
        line-height: 1.5rem;

        code{
            background: #efefef;
            padding:2px 4px;
            border-radius: 2px;
        }

        span{
            font-weight: bold;
        }
    }

    iframe{
        position: absolute;
        top:0;
        left:0;
        opacity: 0;
    }
</style>