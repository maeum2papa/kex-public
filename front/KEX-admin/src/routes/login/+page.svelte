<script>
    import { cAlert,Loading } from "../../stores/store";
    import { postApi } from "../../services/api";
    import { onMount } from "svelte";

    let formData = {
        email : ''
    }

    let inputs = {

    }

    const handleLogin = async() => {

        if(formData.email==''){
            $cAlert = {flag:true,msg:'아이디를 입력해 주세요.',feedback:()=>{inputs.email.select()}};
            return false;
        }

        if(formData.password==''){
            $cAlert = {flag:true,msg:'비밀번호를 입력해 주세요.',feedback:()=>{inputs.password.select()}};
            return false;
        }
        
        const res = await postApi({
            path:'/api/login',
            data:formData
        });

        if(res.msg=='ok'){
            location.replace("/dashboard");
        }else{
            $cAlert = {flag:true,msg:'계정정보가 일치하지 않습니다.',feedback:()=>{inputs.password.select()}};
            return false;
        }
    }


    onMount(async()=>{

        const res = await postApi({
            path:'/api/auth'
        });

        if(res.msg=='ok'){
            location.replace("/dashboard");
        }

    });
</script>

<div class="box">
    <h1>SF-KEX 편의점 택배 관리자 페이지</h1>
    <input type="text" placeholder="이메일" bind:value={formData.email} bind:this={inputs.email}><br/>
    <input type="password" placeholder="비밀번호" bind:value={formData.password} bind:this={inputs.password}><br/>
    <button type='button' on:click={handleLogin}>로그인</button>
</div>

<style lang="scss">
    .box{
        width:380px;
        text-align: center;
        position: fixed;
        top:40%;
        left:50%;
        transform: translate(-50%,-50%);

        h1{
            margin-bottom:20px;
        }

        input,button{
            width:calc(100% - 20px);
            padding:10px;
            box-sizing: border-box;
            margin-top:10px;
        }

        button{
            font-weight: 500;
            letter-spacing: 2px;
        }
    }
</style>
