<script>
    import { onMount } from "svelte";
    import Header from "../../components/Header.svelte";
    import { cAlert } from "../../stores/store";
    import { postApi } from "../../services/api";
    import { checkMobile } from "../../services/validate";


    let formData = {
        name : '',
        mobile : '',
        code : ''
    }

    let inputs = {
        name : {},
        mobile : {},
        code : {},
    }

    let inquire = '';

    let codeSendFlag = false;

    const handleHelper = (e) =>{
        const regex = /[^0-9-]/g;
        formData.mobile = formData.mobile.replace(regex, '');
    }

    const handleCodeSend = async()=>{
        if(formData.name==''){
            $cAlert = {flag:true,msg:'발송자명을 입력해 주세요.',feedback:()=>{inputs.name.select()}}
            return false;
        }

        if(formData.mobile==''){
            $cAlert = {flag:true,msg:'전화번호를 입력해 주세요.',feedback:()=>{inputs.mobile.select()}}
            return false;
        }

        if(formData.mobile.includes('-')){
            formData.mobile = formData.mobile.replace(/-/g,'');
        }

        if(!checkMobile(formData.mobile)){
            $cAlert = {flag:true,msg:'정상적인 전화번호가 아닙니다.',feedback:()=>{inputs.mobile.select()}}
            return false;
        }

        const res = await postApi({
            path:"/api/code",
            data : formData
        });

        if(res.msg=='ok'){
            codeSendFlag = true;
            setTimeout(()=>{
                inputs.code.focus();
            },1000);
            
            //실제 서비스에서는 불필요해서 삭제 필요
            // if(res.code){
            //     alert('TEST:'+res.code);
            //     formData.code = res.code;
            // }
        }else{
            $cAlert = {flag:true,msg:'네트워크 상의 오류가 발생하였습니다. 잠시 후 다시 시도해 주세요.',feedback:()=>{}}
            return false;
        }
    }

    const handleLogin = async()=>{
        if(codeSendFlag){

            if(formData.code==''){
                $cAlert = {flag:true,msg:'인증코드를 입력해 주세요.',feedback:()=>{inputs.code.focus()}}
                return false;
            }

            const res = await postApi({
                path:"/api/login",
                data : formData
            });
            

            if(res.msg=='ok'){
                if(inquire==1){
                    location.replace('/inquire');
                }else{
                    location.replace('/accept/search');
                }
            }else{
                $cAlert = {flag:true,msg:'인증코드가 일치하지 않습니다.',feedback:()=>{inputs.code.select()}}
                return false;
            }

        }
    }

    onMount(()=>{
        
        let params = new URL(location).searchParams;
        inquire = params.get('inquire');
        
    })

</script>

<Header/>


<div class="inner-wrap">
	<h1>로그인</h1>

	<div class="table">
        <div class="help"><span class="star">*</span> 는 필수입력입니다.</div>
		<div class="table-body">
            <div>
                <div>발송자명<span class="star">*</span></div>
                <div>
                    <input type="text" placeholder="한글입력" bind:value={formData.name} bind:this={inputs.name}>
                </div>
            </div>

            <div>
                <div>전화번호<span class="star">*</span></div>
                <div>
                    <input type="text" placeholder="010-0000-0000" bind:value={formData.mobile} bind:this={inputs.mobile} maxlength={13} on:keyup={handleHelper}>
                </div>
            </div> 


            {#if codeSendFlag}

            <div>
                <div>인증코드<span class="star">*</span></div>
                <div>
                    <input type="text" placeholder="숫자 6자리" bind:value={formData.code} bind:this={inputs.code} maxlength={6}> <span class="timer">유효시간 5분</span>
                </div>
            </div>

            {/if}
		</div>
	</div>

	<div class="button-area">
        <button type='button' on:click={handleCodeSend}>{#if codeSendFlag}문자 재발송{:else}문자인증{/if}</button>
        {#if codeSendFlag}
        <button type='button' on:click={handleLogin}>확인</button>
        {/if}
	</div>
</div>


<style lang="scss">
	.inner-wrap{
		margin-top:95px;
		margin-bottom:66px;
	}

	h1{
		font-size:var(--font-size-big-big);
		text-align: center;
	}

	.button-area{
		text-align: center;
		margin-top:67px;
	}

	button{
		margin:0 10px;
	}

    @media only screen and (max-width:425px){
		.inner-wrap{
			margin-top:53px;
			margin-bottom:122px;
		}

		h1{
			font-weight: 500;
		}

		.button-area{
			text-align: center;
			margin-top:45px;
		}

		button{
			margin:0;
			margin-bottom:10px;
		}

        .table{
            margin-top:44px;
        }

        span.timer{
            display: block;
            margin-top:2px;
        }

	}
</style>