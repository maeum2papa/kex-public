<script>
    import { getApi } from "../services/api";

    const handleLogout = async()=>{
        await getApi({
            path:'/api/logout'
        });
        
        location.replace("/");
    }
</script>

<header class="wrap">
    <div><h1>SF-KEX 편의점 택배 관리자 페이지</h1></div>
    <div><button type="button" on:click={handleLogout}>로그아웃</button></div>
</header>

<style lang="scss">
    header{
        display: flex;
        justify-content: space-between;
    }
</style>