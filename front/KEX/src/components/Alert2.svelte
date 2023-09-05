<script>
    import { cAlert } from '../stores/store'

    function closeAlert(){
        $cAlert.flag = false;
    }
</script>


{#if $cAlert.flag}
<div class="alert">
    <div>{$cAlert.msg}</div>
    <button type="button" class="black" on:click={()=>{ closeAlert(); $cAlert.feedback(); }}>확인</button>
</div>

<div class="mask"></div>
{/if}


<style lang="scss">
.alert{
    position:fixed;
    min-width: 300px;
    padding:40px;
    top:50%;
    left:50%;
    transform: translate(-50%,-50%);
    background: #fff;
    z-index: 2001;
    border-radius: 4px;
    text-align: center;
    div{
        font-size:var(--font-size-normal);
    }
}
.mask{
    position: fixed;
    width:100vw;
    height: 100vh;
    background: rgba(1,1,1,0.9);
    z-index: 2000;
    top:0;
    left:0;
}
button{
    margin-top:40px;
}

@media only screen and (max-width:425px){

    .alert{
        width: calc(100% - 110px);
        min-width: 0;
    }

}
</style>